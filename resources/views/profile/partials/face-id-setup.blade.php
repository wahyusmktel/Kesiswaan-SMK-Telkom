<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Pengaturan Face ID') }}
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            {{ __('Daftarkan wajah Anda untuk bisa login tanpa memasukkan password.') }}
        </p>
    </header>

    @if (session('status') === 'face-id-updated')
        <div class="mt-4 p-4 bg-emerald-50 rounded-xl border border-emerald-200"
             x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3500)">
            <p class="text-sm font-medium text-emerald-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Face ID berhasil diperbarui. Model wajah Anda sudah disimpan dengan teknologi AI.
            </p>
        </div>
    @endif

    @if ($errors->has('face_image'))
        <div class="mt-4 p-4 bg-red-50 rounded-xl border border-red-200">
            <p class="text-sm font-medium text-red-600 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                {{ $errors->first('face_image') }}
            </p>
        </div>
    @endif

    <div class="mt-6 space-y-6" x-data="faceIdSetup()">

        @if(auth()->user()->face_descriptor)
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl flex items-start gap-4">
                @if(auth()->user()->face_photo)
                <div class="w-16 h-16 rounded-lg overflow-hidden border-2 border-white shadow-md flex-shrink-0">
                    <img src="{{ asset('storage/' . auth()->user()->face_photo) }}" alt="Face ID" class="w-full h-full object-cover">
                </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                        </svg>
                        Face ID Aktif (128-dimensi AI)
                    </h3>
                    <p class="text-sm text-gray-600 mt-1">Wajah Anda sudah terdaftar dengan model AI. Ingin memperbarui?</p>
                </div>
            </div>
        @elseif(auth()->user()->face_photo)
            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl">
                <p class="text-sm font-semibold text-amber-700 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Face ID lama perlu diperbarui ke sistem AI baru. Silakan daftarkan ulang.
                </p>
            </div>
        @else
            <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                <p class="text-sm text-gray-600 flex items-center gap-2">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Belum ada Face ID yang terdaftar.
                </p>
            </div>
        @endif

        {{-- Tombol Daftar --}}
        <div x-show="!isCapturing">
            <button type="button" @click="startRegistration()"
                :disabled="isLoading"
                class="w-full sm:w-auto px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-blue-400 text-white font-bold rounded-xl transition-all shadow-md flex items-center justify-center gap-2">
                <template x-if="!isLoading">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </template>
                <template x-if="isLoading">
                    <div class="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                </template>
                <span x-text="isLoading ? loadingMessage : ({{ auth()->user()->face_photo ? 'true' : 'false' }} ? 'Perbarui Face ID' : 'Daftarkan Face ID')"></span>
            </button>
        </div>

        {{-- Kamera --}}
        <div x-show="isCapturing" style="display:none;" class="border border-gray-200 rounded-2xl p-6 bg-gray-50">

            {{-- Status Deteksi Wajah --}}
            <div class="mb-4 text-center">
                <div x-show="faceDetected" class="inline-flex items-center gap-2 px-3 py-1.5 bg-emerald-100 text-emerald-700 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                    Wajah terdeteksi
                </div>
                <div x-show="!faceDetected && isCapturing" class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-100 text-amber-700 rounded-full text-xs font-bold">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                    Mendeteksi wajah...
                </div>
            </div>

            {{-- Video --}}
            <div class="relative w-full max-w-sm mx-auto aspect-square overflow-hidden rounded-full border-4 shadow-xl bg-black"
                 :class="faceDetected ? 'border-emerald-500' : 'border-blue-500'">
                <video x-ref="video" autoplay playsinline muted class="w-full h-full object-cover" style="transform: scaleX(-1);"></video>
                <div class="absolute inset-0 border-8 border-transparent border-t-blue-500/50 border-b-blue-500/50 rounded-full animate-spin pointer-events-none" style="animation-duration: 3s;"></div>
            </div>

            <p class="text-center text-sm font-bold mt-4" :class="faceDetected ? 'text-emerald-600' : 'text-gray-700'" x-text="statusText"></p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="captureAndSubmit()"
                    :disabled="!faceDetected || isProcessing"
                    class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 disabled:bg-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl transition-all shadow-md flex items-center gap-2">
                    <template x-if="isProcessing">
                        <div class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                    </template>
                    <span x-text="isProcessing ? 'Menyimpan...' : 'Ambil Foto & Simpan'"></span>
                </button>
                <button type="button" @click="cancelRegistration()"
                    class="px-6 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-bold rounded-xl transition-all shadow-sm">
                    Batal
                </button>
            </div>

            <canvas x-ref="canvas" style="display:none;"></canvas>

            <form id="face-form" action="{{ route('profile.face.update') }}" method="POST" class="hidden">
                @csrf
                @method('PATCH')
                <input type="hidden" name="face_image" x-ref="faceInput">
                <input type="hidden" name="face_descriptor" x-ref="descriptorInput">
            </form>

            {{-- Tips --}}
            <div class="mt-5 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs text-blue-700 font-semibold mb-1">Tips untuk hasil terbaik:</p>
                <ul class="text-xs text-blue-600 space-y-0.5 list-disc list-inside">
                    <li>Pastikan wajah terlihat jelas di tengah lingkaran</li>
                    <li>Gunakan pencahayaan yang cukup dan merata</li>
                    <li>Hindari topi atau kacamata hitam</li>
                    <li>Lihat langsung ke kamera</li>
                </ul>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/face-api.min.js') }}"></script>
    <script>
        const FACE_MODELS_URL = '{{ asset('models') }}';
        let faceApiModelsLoaded = window._faceApiModelsLoaded || false;

        async function loadFaceApiModels() {
            if (faceApiModelsLoaded) return;
            await Promise.all([
                faceapi.nets.tinyFaceDetector.loadFromUri(FACE_MODELS_URL),
                faceapi.nets.faceLandmark68TinyNet.loadFromUri(FACE_MODELS_URL),
                faceapi.nets.faceRecognitionNet.loadFromUri(FACE_MODELS_URL),
            ]);
            faceApiModelsLoaded = true;
            window._faceApiModelsLoaded = true;
        }

        function faceIdSetup() {
            return {
                isCapturing:   false,
                isLoading:     false,
                isProcessing:  false,
                loadingMessage:'',
                faceDetected:  false,
                stream:        null,
                detectInterval: null,
                statusText:    'Posisikan wajah Anda dengan jelas di tengah',

                async startRegistration() {
                    this.isLoading      = true;
                    this.loadingMessage = 'Memuat AI...';

                    try {
                        await loadFaceApiModels();
                    } catch (err) {
                        alert('Gagal memuat model AI. Periksa koneksi internet dan refresh halaman.');
                        this.isLoading = false;
                        return;
                    }

                    this.isLoading    = false;
                    this.isCapturing  = true;
                    this.faceDetected = false;
                    this.statusText   = 'Menghubungkan ke kamera...';

                    try {
                        this.stream = await navigator.mediaDevices.getUserMedia({
                            video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 640 } },
                            audio: false,
                        });
                        this.$refs.video.srcObject = this.stream;
                        await new Promise(r => { this.$refs.video.onloadedmetadata = r; });
                        this.statusText = 'Posisikan wajah Anda dengan jelas di tengah';
                        this.startLiveDetection();
                    } catch (err) {
                        this.statusText = 'Kamera tidak dapat diakses. Pastikan Anda telah memberikan izin.';
                        setTimeout(() => this.cancelRegistration(), 3000);
                    }
                },

                startLiveDetection() {
                    this.detectInterval = setInterval(async () => {
                        if (!this.$refs.video || !this.stream) return;
                        try {
                            const result = await faceapi
                                .detectSingleFace(this.$refs.video, new faceapi.TinyFaceDetectorOptions({ minConfidence: 0.5, inputSize: 224 }));
                            this.faceDetected = !!result;
                            if (this.faceDetected) {
                                this.statusText = 'Wajah terdeteksi! Klik "Ambil Foto & Simpan" untuk mendaftar.';
                            } else {
                                this.statusText = 'Posisikan wajah Anda dengan jelas di tengah';
                            }
                        } catch(_) {}
                    }, 600);
                },

                cancelRegistration() {
                    this.isCapturing  = false;
                    this.faceDetected = false;
                    if (this.detectInterval) { clearInterval(this.detectInterval); this.detectInterval = null; }
                    if (this.stream) {
                        this.stream.getTracks().forEach(t => t.stop());
                        this.stream = null;
                        this.$refs.video.srcObject = null;
                    }
                },

                async captureAndSubmit() {
                    if (!this.stream || !this.faceDetected || this.isProcessing) return;
                    this.isProcessing = true;

                    if (this.detectInterval) { clearInterval(this.detectInterval); this.detectInterval = null; }

                    const video  = this.$refs.video;
                    const canvas = this.$refs.canvas;
                    const ctx    = canvas.getContext('2d');

                    canvas.width  = video.videoWidth  || 640;
                    canvas.height = video.videoHeight || 640;

                    // Draw un-mirrored for face detection
                    ctx.drawImage(video, 0, 0);

                    this.statusText = 'Mengekstrak fitur wajah...';

                    try {
                        const detection = await faceapi
                            .detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions({ minConfidence: 0.5, inputSize: 416 }))
                            .withFaceLandmarks(true)
                            .withFaceDescriptor();

                        if (!detection) {
                            this.statusText   = 'Wajah tidak terdeteksi saat pengambilan. Coba lagi.';
                            this.isProcessing = false;
                            this.startLiveDetection();
                            return;
                        }

                        // Save mirrored image (natural-looking for user)
                        ctx.clearRect(0, 0, canvas.width, canvas.height);
                        ctx.translate(canvas.width, 0);
                        ctx.scale(-1, 1);
                        ctx.drawImage(video, 0, 0);

                        const descriptor = JSON.stringify(Array.from(detection.descriptor));
                        this.$refs.faceInput.value      = canvas.toDataURL('image/jpeg', 0.9);
                        this.$refs.descriptorInput.value = descriptor;

                        this.statusText = 'Menyimpan Face ID...';
                        this.stream.getTracks().forEach(t => t.stop());
                        document.getElementById('face-form').submit();

                    } catch (err) {
                        console.error(err);
                        this.statusText   = 'Gagal memproses wajah. Coba lagi.';
                        this.isProcessing = false;
                        this.startLiveDetection();
                    }
                }
            }
        }
    </script>
</section>
