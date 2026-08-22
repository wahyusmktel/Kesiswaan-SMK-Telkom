import Hls from 'hls.js/dist/hls.light.mjs';

const root = document.querySelector('[data-cctv-live-root]');
const dataElement = document.getElementById('cctv-live-data');

if (root && dataElement) {
    const config = JSON.parse(dataElement.textContent);
    const video = root.querySelector('[data-cctv-video]');
    const overlay = root.querySelector('[data-player-overlay]');
    const overlayMessage = root.querySelector('[data-overlay-message]');
    const status = root.querySelector('[data-live-status]');
    let currentCamera = null;
    let hls = null;
    let bearerToken = '';
    let refreshTimer = null;
    let networkRecoveryAttempts = 0;

    const setStatus = (label, state = 'idle') => {
        status.textContent = label;
        status.className = 'rounded-full px-3 py-1 text-xs font-bold';
        status.classList.add(...({
            live: ['bg-emerald-50', 'text-emerald-700'],
            error: ['bg-red-50', 'text-red-700'],
            loading: ['bg-amber-50', 'text-amber-700'],
            idle: ['bg-slate-100', 'text-slate-600'],
        }[state]));
    };

    const showOverlay = (message, visible = true) => {
        overlayMessage.textContent = message;
        overlay.classList.toggle('hidden', !visible);
    };

    const destroyPlayer = () => {
        window.clearTimeout(refreshTimer);
        refreshTimer = null;
        if (hls) {
            hls.destroy();
            hls = null;
        }
        video.pause();
        video.removeAttribute('src');
        video.load();
    };

    const requestToken = async (camera) => {
        const response = await fetch(camera.tokenUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                Accept: 'application/json',
                'X-CSRF-TOKEN': config.csrfToken,
            },
        });

        if (!response.ok) {
            throw new Error(response.status === 403
                ? 'Akses kamera tidak lagi tersedia.'
                : 'Token tayangan tidak dapat dibuat.');
        }

        return response.json();
    };

    const scheduleTokenRefresh = (camera, expiresAt) => {
        window.clearTimeout(refreshTimer);
        const refreshIn = Math.max(30000, ((expiresAt * 1000) - Date.now()) - 60000);
        refreshTimer = window.setTimeout(async () => {
            try {
                const refreshed = await requestToken(camera);
                bearerToken = refreshed.token;
                scheduleTokenRefresh(camera, refreshed.expires_at);
            } catch {
                setStatus('Token kedaluwarsa', 'error');
            }
        }, refreshIn);
    };

    const startPlayer = async (camera) => {
        currentCamera = camera;
        destroyPlayer();
        document.querySelectorAll('[data-camera-button]').forEach((button) => {
            button.classList.toggle('is-active', Number(button.dataset.cameraButton) === camera.id);
        });
        root.querySelector('[data-player-title]').textContent = camera.name;
        root.querySelector('[data-player-location]').textContent = camera.location || 'Lokasi tidak dicantumkan';
        root.querySelector('[data-player-description]').textContent = camera.description || 'Tidak ada keterangan tambahan.';
        setStatus('Menghubungkan', 'loading');
        showOverlay('Menghubungkan ke kamera...');
        networkRecoveryAttempts = 0;

        try {
            const issued = await requestToken(camera);
            bearerToken = issued.token;
            scheduleTokenRefresh(camera, issued.expires_at);

            if (Hls.isSupported()) {
                hls = new Hls({
                    lowLatencyMode: true,
                    backBufferLength: 10,
                    liveSyncDurationCount: 2,
                    xhrSetup(xhr) {
                        xhr.setRequestHeader('Authorization', `Bearer ${bearerToken}`);
                    },
                });
                hls.loadSource(issued.manifest_url);
                hls.attachMedia(video);
                hls.on(Hls.Events.MANIFEST_PARSED, () => {
                    video.play().catch(() => {});
                });
                hls.on(Hls.Events.ERROR, (_event, error) => {
                    if (!error.fatal) return;
                    if (error.type === Hls.ErrorTypes.NETWORK_ERROR) {
                        if (networkRecoveryAttempts < 2) {
                            networkRecoveryAttempts += 1;
                            window.setTimeout(() => hls?.startLoad(), networkRecoveryAttempts * 1000);
                            return;
                        }

                        setStatus('Gateway tidak tersedia', 'error');
                        showOverlay('Gateway CCTV tidak dapat dijangkau. Periksa Cloudflare Tunnel dan konfigurasi CORS MediaMTX.');
                        return;
                    }
                    if (error.type === Hls.ErrorTypes.MEDIA_ERROR) {
                        hls.recoverMediaError();
                        return;
                    }
                    setStatus('Stream bermasalah', 'error');
                    showOverlay('Kamera tidak dapat ditayangkan. Periksa koneksi atau codec kamera.');
                });
            } else {
                throw new Error('Browser ini belum mendukung player CCTV aman. Gunakan Chrome, Edge, atau Firefox versi terbaru.');
            }
        } catch (error) {
            setStatus('Tidak tersedia', 'error');
            showOverlay(error.message || 'Tayangan tidak dapat dimuat.');
        }
    };

    video.addEventListener('playing', () => {
        networkRecoveryAttempts = 0;
        setStatus('Live', 'live');
        showOverlay('', false);
    });
    video.addEventListener('waiting', () => setStatus('Buffering', 'loading'));
    video.addEventListener('error', () => {
        setStatus('Stream bermasalah', 'error');
        showOverlay('Tayangan terputus. Gunakan tombol muat ulang.');
    });

    root.querySelectorAll('[data-camera-button]').forEach((button) => {
        button.addEventListener('click', () => {
            const camera = config.cameras.find((item) => item.id === Number(button.dataset.cameraButton));
            if (camera) startPlayer(camera);
        });
    });
    root.querySelector('[data-reload-camera]').addEventListener('click', () => {
        if (currentCamera) startPlayer(currentCamera);
    });
    document.addEventListener('visibilitychange', () => {
        if (!hls) return;
        document.hidden ? hls.stopLoad() : hls.startLoad();
    });
    window.addEventListener('beforeunload', destroyPlayer);

    if (config.cameras.length > 0) {
        startPlayer(config.cameras[0]);
    }
}
