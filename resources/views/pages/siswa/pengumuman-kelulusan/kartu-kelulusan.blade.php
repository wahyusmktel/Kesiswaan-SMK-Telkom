<x-app-layout>
    @push('styles')
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&display=swap" rel="stylesheet">
        <style>
            /* ============================================================
               PAGE BACKGROUND & WRAPPER
            ============================================================ */
            .kartu-page-bg {
                background: #0d0d1a;
                min-height: 100vh;
            }
            .kartu-scene {
                background: radial-gradient(ellipse at 20% 20%, rgba(168,85,247,0.18) 0%, transparent 55%),
                            radial-gradient(ellipse at 80% 80%, rgba(6,182,212,0.15) 0%, transparent 55%),
                            radial-gradient(ellipse at 50% 50%, rgba(245,158,11,0.06) 0%, transparent 60%),
                            #0d0d1a;
                padding: 32px 16px 48px;
                display: flex;
                flex-direction: column;
                align-items: center;
            }

            /* ============================================================
               CARD WRAPPER — displayed at 360×640, download at 1080×1920
            ============================================================ */
            .card-shell {
                position: relative;
                width: 360px;
                height: 640px;
                flex-shrink: 0;
                border-radius: 28px;
                overflow: hidden;
                box-shadow:
                    0 0 0 1px rgba(168,85,247,0.35),
                    0 0 60px rgba(168,85,247,0.2),
                    0 32px 80px rgba(0,0,0,0.7);
            }

            /* ============================================================
               CARD INNER — actual content captured by html2canvas
            ============================================================ */
            #graduation-card {
                position: relative;
                width: 360px;
                height: 640px;
                overflow: hidden;
                font-family: 'Sora', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
                background: #060614;
            }

            /* --- Gradient orbs (background blobs) --- */
            .orb {
                position: absolute;
                border-radius: 50%;
                pointer-events: none;
            }
            .orb-purple {
                width: 320px; height: 320px;
                top: -110px; left: -90px;
                background: radial-gradient(circle, #7c3aed 0%, transparent 68%);
                opacity: 0.55;
            }
            .orb-cyan {
                width: 280px; height: 280px;
                bottom: -90px; right: -70px;
                background: radial-gradient(circle, #0891b2 0%, transparent 68%);
                opacity: 0.5;
            }
            .orb-gold {
                width: 220px; height: 220px;
                top: 50%; left: 50%;
                margin-top: -110px; margin-left: -110px;
                background: radial-gradient(circle, #d97706 0%, transparent 68%);
                opacity: 0.12;
            }
            .orb-pink {
                width: 180px; height: 180px;
                top: 55%; right: -50px;
                background: radial-gradient(circle, #ec4899 0%, transparent 68%);
                opacity: 0.22;
            }

            /* --- Dot grid texture --- */
            .dot-grid {
                position: absolute;
                inset: 0;
                background-image: radial-gradient(rgba(255,255,255,0.06) 1px, transparent 1px);
                background-size: 22px 22px;
                pointer-events: none;
            }

            /* --- Sparkle stars --- */
            .star-shape {
                position: absolute;
                pointer-events: none;
            }
            .star-shape::before,
            .star-shape::after {
                content: '';
                position: absolute;
                background: currentColor;
                border-radius: 2px;
            }
            .star-shape::before { top: 50%; left: 0; right: 0; height: 2px; margin-top: -1px; }
            .star-shape::after  { left: 50%; top: 0; bottom: 0; width: 2px; margin-left: -1px; }

            /* ============================================================
               CARD SECTIONS
            ============================================================ */

            /* Top badge */
            .card-top-badge {
                position: absolute;
                top: 26px;
                left: 0; right: 0;
                text-align: center;
            }
            .badge-pill {
                display: inline-block;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.16);
                border-radius: 100px;
                padding: 5px 16px;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: 2px;
                color: rgba(255,255,255,0.8);
                text-transform: uppercase;
            }
            .card-school-name {
                margin-top: 8px;
                font-size: 11px;
                font-weight: 800;
                color: rgba(255,255,255,0.55);
                letter-spacing: 3px;
                text-transform: uppercase;
            }

            /* Center celebration block */
            .card-center {
                position: absolute;
                top: 102px;
                left: 20px; right: 20px;
                background: rgba(255,255,255,0.05);
                border: 1px solid rgba(255,255,255,0.1);
                border-radius: 24px;
                padding: 22px 20px 20px;
                text-align: center;
            }
            .card-emoji-big {
                font-size: 54px;
                line-height: 1;
                margin-bottom: 10px;
                display: block;
            }
            .card-headline-sub {
                font-size: 11px;
                font-weight: 700;
                color: rgba(255,255,255,0.5);
                letter-spacing: 3px;
                text-transform: uppercase;
                margin-bottom: 4px;
            }
            .card-headline-main {
                font-size: 26px;
                font-weight: 800;
                line-height: 1.15;
                color: #ffffff;
                margin-bottom: 14px;
            }
            .card-headline-main span.grad {
                /* fallback: solid gold */
                color: #f59e0b;
            }
            .lulus-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: rgba(16,185,129,0.15);
                border: 1.5px solid rgba(16,185,129,0.55);
                border-radius: 100px;
                padding: 5px 16px;
                font-size: 12px;
                font-weight: 800;
                color: #34d399;
                letter-spacing: 2px;
                text-transform: uppercase;
            }
            .lulus-dot {
                width: 7px; height: 7px;
                border-radius: 50%;
                background: #34d399;
                display: inline-block;
                flex-shrink: 0;
            }

            /* Student name block */
            .card-name-block {
                position: absolute;
                top: 342px;
                left: 0; right: 0;
                text-align: center;
                padding: 0 20px;
            }
            .name-divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(168,85,247,0.6), rgba(6,182,212,0.6), transparent);
                margin-bottom: 12px;
            }
            .card-student-name {
                font-size: 21px;
                font-weight: 800;
                color: #ffffff;
                line-height: 1.2;
                letter-spacing: 0.5px;
                word-break: break-word;
            }
            .card-class-info {
                margin-top: 7px;
                font-size: 11.5px;
                font-weight: 600;
                color: rgba(255,255,255,0.45);
                letter-spacing: 1.5px;
                text-transform: uppercase;
            }
            .card-year {
                margin-top: 3px;
                font-size: 10.5px;
                color: rgba(255,255,255,0.3);
                font-weight: 600;
                letter-spacing: 1px;
            }

            /* Tagline */
            .card-tagline {
                position: absolute;
                top: 465px;
                left: 20px; right: 20px;
                text-align: center;
                background: rgba(168,85,247,0.08);
                border: 1px solid rgba(168,85,247,0.2);
                border-radius: 16px;
                padding: 12px 16px;
            }
            .tagline-quote {
                font-size: 11.5px;
                font-weight: 600;
                color: rgba(255,255,255,0.72);
                line-height: 1.55;
                font-style: italic;
            }
            .tagline-emoji {
                font-size: 14px;
                font-style: normal;
            }

            /* Bottom branding */
            .card-bottom {
                position: absolute;
                bottom: 18px;
                left: 0; right: 0;
                text-align: center;
            }
            .bottom-divider {
                height: 1px;
                background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
                margin: 0 40px 10px;
            }
            .bottom-brand {
                font-size: 9px;
                font-weight: 700;
                color: rgba(255,255,255,0.25);
                letter-spacing: 2.5px;
                text-transform: uppercase;
            }
            .bottom-hashtag {
                margin-top: 3px;
                font-size: 9px;
                font-weight: 600;
                color: rgba(168,85,247,0.55);
                letter-spacing: 1px;
            }

            /* ============================================================
               PAGE UI (OUTSIDE CARD)
            ============================================================ */
            .page-title {
                font-family: 'Sora', sans-serif;
                font-size: 20px;
                font-weight: 800;
                color: #fff;
                text-align: center;
                margin-bottom: 4px;
            }
            .page-sub {
                font-size: 13px;
                color: rgba(255,255,255,0.45);
                text-align: center;
                margin-bottom: 24px;
            }
            .action-area {
                width: 100%;
                max-width: 360px;
                margin-top: 24px;
                display: flex;
                flex-direction: column;
                gap: 12px;
            }
            .btn-download {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 8px;
                padding: 14px;
                background: linear-gradient(135deg, #7c3aed, #06b6d4);
                border: none;
                border-radius: 14px;
                color: #fff;
                font-family: 'Sora', sans-serif;
                font-size: 14px;
                font-weight: 700;
                cursor: pointer;
                transition: opacity 0.2s, transform 0.15s;
                box-shadow: 0 8px 24px rgba(124,58,237,0.4);
            }
            .btn-download:hover { opacity: 0.9; transform: translateY(-1px); }
            .btn-download:active { transform: translateY(0); opacity: 0.85; }
            .btn-download:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
            .btn-back {
                width: 100%;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
                padding: 12px;
                background: rgba(255,255,255,0.06);
                border: 1px solid rgba(255,255,255,0.12);
                border-radius: 14px;
                color: rgba(255,255,255,0.6);
                font-family: 'Sora', sans-serif;
                font-size: 13px;
                font-weight: 600;
                text-decoration: none;
                transition: background 0.2s;
            }
            .btn-back:hover { background: rgba(255,255,255,0.1); color: #fff; }
            .tips-box {
                width: 100%;
                max-width: 360px;
                margin-top: 20px;
                background: rgba(255,255,255,0.04);
                border: 1px solid rgba(255,255,255,0.08);
                border-radius: 16px;
                padding: 16px;
            }
            .tips-title {
                font-size: 11px;
                font-weight: 700;
                color: rgba(255,255,255,0.35);
                letter-spacing: 2px;
                text-transform: uppercase;
                margin-bottom: 10px;
            }
            .tips-item {
                display: flex;
                align-items: flex-start;
                gap: 10px;
                font-size: 12px;
                color: rgba(255,255,255,0.55);
                margin-bottom: 7px;
                line-height: 1.5;
            }
            .tips-icon {
                flex-shrink: 0;
                width: 20px;
                height: 20px;
                background: rgba(168,85,247,0.15);
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 11px;
            }
            .progress-ring {
                display: inline-block;
                width: 18px; height: 18px;
                border: 2.5px solid rgba(255,255,255,0.2);
                border-top-color: #a855f7;
                border-radius: 50%;
                animation: spin 0.7s linear infinite;
                vertical-align: middle;
            }
            @keyframes spin { to { transform: rotate(360deg); } }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Kartu Kelulusan</h2>
    </x-slot>

    {{-- Dark page wrapper --}}
    <div class="kartu-page-bg -mx-4 sm:-mx-0">
        <div class="kartu-scene">

            <p class="page-title">Kartu Kelulusanmu 🎓</p>
            <p class="page-sub">Download &amp; flex di sosmed, bestie! ✨</p>

            {{-- ==================== KARTU ==================== --}}
            <div class="card-shell">
                <div id="graduation-card">

                    {{-- BG decorations --}}
                    <div class="orb orb-purple"></div>
                    <div class="orb orb-cyan"></div>
                    <div class="orb orb-gold"></div>
                    <div class="orb orb-pink"></div>
                    <div class="dot-grid"></div>

                    {{-- Sparkle stars --}}
                    <div class="star-shape" style="width:12px;height:12px;top:80px;right:38px;color:#a855f7;opacity:0.8;"></div>
                    <div class="star-shape" style="width:8px;height:8px;top:65px;left:55px;color:#f59e0b;opacity:0.9;"></div>
                    <div class="star-shape" style="width:10px;height:10px;top:430px;left:26px;color:#06b6d4;opacity:0.7;"></div>
                    <div class="star-shape" style="width:7px;height:7px;bottom:68px;right:44px;color:#ec4899;opacity:0.75;"></div>
                    <div class="star-shape" style="width:9px;height:9px;top:310px;right:28px;color:#a855f7;opacity:0.6;"></div>
                    <div class="star-shape" style="width:6px;height:6px;top:190px;left:22px;color:#34d399;opacity:0.65;"></div>

                    {{-- TOP BADGE --}}
                    <div class="card-top-badge">
                        <div class="badge-pill">✨ Official Announcement ✨</div>
                        <div class="card-school-name">{{ config('app.name', 'SMK Telkom') }}</div>
                    </div>

                    {{-- CENTER CELEBRATION --}}
                    <div class="card-center">
                        <span class="card-emoji-big">🎓</span>
                        <div class="card-headline-sub">Pengumuman Kelulusan</div>
                        <div class="card-headline-main">
                            Nggak nyangka<br>sampe sini juga, <span class="grad">frens!</span>
                        </div>
                        <div class="lulus-badge">
                            <span class="lulus-dot"></span>
                            RESMI LULUS
                        </div>
                    </div>

                    {{-- STUDENT NAME --}}
                    <div class="card-name-block">
                        <div class="name-divider"></div>
                        <div class="card-student-name">{{ strtoupper($siswa->nama_lengkap) }}</div>
                        <div class="card-class-info">{{ $rombel->kelas->nama_kelas }}</div>
                        <div class="card-year">T.A. {{ $tahunPelajaran->tahun }}</div>
                    </div>

                    {{-- TAGLINE --}}
                    <div class="card-tagline">
                        <div class="tagline-quote">
                            "Dari semua struggle, ujian dadakan &amp; tugas numpuk,<br>
                            ini hasilnya bestie — <span class="tagline-emoji">🔥</span> lo udah buktiin!"
                        </div>
                    </div>

                    {{-- BOTTOM BRANDING --}}
                    <div class="card-bottom">
                        <div class="bottom-divider"></div>
                        <div class="bottom-brand">{{ config('app.name', 'SMK Telkom') }} · Kesiswaan</div>
                        <div class="bottom-hashtag">#LulusBestie #{{ str_replace(' ', '', config('app.name', 'SMKTelkom')) }}2025</div>
                    </div>

                </div>{{-- #graduation-card --}}
            </div>{{-- .card-shell --}}

            {{-- ==================== ACTION AREA ==================== --}}
            <div class="action-area">
                <button id="btn-download" class="btn-download" onclick="downloadCard()">
                    <svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Download Kartu JPG
                </button>

                <a href="{{ route('siswa.pengumuman-kelulusan.index') }}" class="btn-back">
                    <svg style="width:16px;height:16px" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Kembali ke Pengumuman
                </a>
            </div>

            {{-- ==================== TIPS ==================== --}}
            <div class="tips-box">
                <div class="tips-title">📲 Tips Bagikan ke Sosmed</div>
                <div class="tips-item">
                    <div class="tips-icon">📱</div>
                    <span>Download kartu, lalu upload ke <strong style="color:rgba(255,255,255,0.7)">Instagram Story / Reels</strong> atau TikTok.</span>
                </div>
                <div class="tips-item">
                    <div class="tips-icon">🎨</div>
                    <span>Tambahkan sticker, musik, atau efek favorit kamu di IG/TikTok sebelum posting.</span>
                </div>
                <div class="tips-item">
                    <div class="tips-icon">🏷️</div>
                    <span>Tag sekolah dan pakai hashtag <strong style="color:rgba(168,85,247,0.8)">#LulusBestie</strong> biar makin viral!</span>
                </div>
                <div class="tips-item">
                    <div class="tips-icon">✨</div>
                    <span>Format JPG 1080×1920px — pas banget buat <strong style="color:rgba(255,255,255,0.7)">Instagram Story, Reels, TikTok</strong>!</span>
                </div>
            </div>

        </div>{{-- .kartu-scene --}}
    </div>{{-- .kartu-page-bg --}}

    @push('scripts')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"
                integrity="sha512-BNaRQnYJYiPSqHHDb58B0yaPfCu+Wgds8Gp/gU33kqBtgNS4tSPHuGibyoeqMV/TJlSKda6FXzoEyYGjTe+vXA=="
                crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <script>
            const STUDENT_NAME = @json(str_replace(' ', '-', $siswa->nama_lengkap));

            async function downloadCard() {
                const btn  = document.getElementById('btn-download');
                const card = document.getElementById('graduation-card');

                // Loading state
                btn.disabled = true;
                btn.innerHTML = '<span class="progress-ring"></span>&nbsp; Menyiapkan kartu...';

                try {
                    // Wait for fonts
                    await document.fonts.ready;

                    const canvas = await html2canvas(card, {
                        scale           : 3,          // 360×640 → 1080×1920
                        useCORS         : true,
                        allowTaint      : false,
                        backgroundColor : '#060614',
                        logging         : false,
                        imageTimeout    : 5000,
                        width           : 360,
                        height          : 640,
                    });

                    canvas.toBlob(blob => {
                        const url  = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.download = `kartu-kelulusan-${STUDENT_NAME}.jpg`;
                        link.href = url;
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);
                    }, 'image/jpeg', 0.96);

                } catch (err) {
                    console.error(err);
                    alert('Gagal generate kartu. Coba lagi ya bestie! 🙏');
                } finally {
                    btn.disabled = false;
                    btn.innerHTML = `
                        <svg style="width:18px;height:18px;flex-shrink:0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download Kartu JPG`;
                }
            }
        </script>
    @endpush
</x-app-layout>
