<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Scan Stella Access Card — SISFO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/html5-qrcode"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .scanner-container {
            position: relative;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            overflow: hidden;
            border-radius: 2rem;
            border: 8px solid white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        #reader {
            width: 100% !important;
            border: none !important;
        }

        #reader__scan_region {
            background: #000;
        }

        #reader video {
            object-fit: cover !important;
        }

        .success-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(34, 197, 94, 0.9);
            z-index: 50;
            display: flex;
            flex-direction: column;
            items-center: center;
            justify-content: center;
            backdrop-blur: sm;
        }

        @keyframes pulse-border {

            0%,
            100% {
                border-color: rgba(220, 38, 38, 0.5);
            }

            50% {
                border-color: rgba(220, 38, 38, 1);
            }
        }

        .scanning-border {
            position: absolute;
            top: 20px;
            left: 20px;
            right: 20px;
            bottom: 20px;
            border: 2px dashed rgba(255, 255, 255, 0.5);
            pointer-events: none;
            z-index: 10;
            border-radius: 1rem;
        }
    </style>
</head>

<body class="min-h-screen flex flex-col items-center justify-center p-6 bg-slate-50">

    <!-- Background Decoration -->
    <div class="fixed top-0 left-0 w-full h-1/2 hero-gradient -skew-y-6 transform origin-top-left -translate-y-24">
    </div>

    <div class="relative w-full max-w-lg z-10">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-3xl shadow-xl mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                </svg>
            </div>
            <h1 class="text-3xl font-black text-white mb-2">Stella Access Card</h1>
            <p class="text-red-100 font-medium opacity-90">Arahkan barcode kartu Anda ke kamera</p>
        </div>

        <!-- Scanner Card -->
        <div class="bg-white rounded-[2.5rem] shadow-2xl p-8 border border-white/20">
            <div class="scanner-container relative bg-slate-900 mb-8" id="scanner-wrapper">
                <div id="reader"></div>
                <div class="scanning-border"></div>

                <!-- Success State Overlay -->
                <div id="success-overlay" class="success-overlay hidden">
                    <div
                        class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-4 shadow-lg scale-110">
                        <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <p class="text-white font-black text-xl text-center px-6" id="success-message">MENGONTROL AKSES...
                    </p>
                </div>
            </div>

            <!-- Manual Input Link (Fallback) -->
            <div class="flex flex-col items-center gap-4">
                <p class="text-slate-400 text-sm font-medium">Bermasalah dengan kamera?</p>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}"
                        class="px-6 py-2 bg-slate-100 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-200 transition-all">Kembali</a>
                </div>
            </div>
        </div>

        <!-- Instructions -->
        <div class="mt-8 grid grid-cols-3 gap-4">
            <div class="bg-white/50 backdrop-blur-sm p-4 rounded-2xl text-center border border-white/40">
                <div
                    class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-2 font-black text-xs">
                    1</div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Pegang Kartu</p>
            </div>
            <div class="bg-white/50 backdrop-blur-sm p-4 rounded-2xl text-center border border-white/40">
                <div
                    class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-2 font-black text-xs">
                    2</div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Arahkan Barcode</p>
            </div>
            <div class="bg-white/50 backdrop-blur-sm p-4 rounded-2xl text-center border border-white/40">
                <div
                    class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center mx-auto mb-2 font-black text-xs">
                    3</div>
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-tighter">Login Otomatis</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html5QrCode = new Html5Qrcode("reader");
            const successOverlay = document.getElementById('success-overlay');
            const successMessage = document.getElementById('success-message');
            let isProcessing = false;

            const config = {
                fps: 10,
                qrbox: { width: 300, height: 150 },
                aspectRatio: 1.0
            };

            const onScanSuccess = async (decodedText, decodedResult) => {
                if (isProcessing) return;
                isProcessing = true;

                // Visual Feedback
                successOverlay.classList.remove('hidden');
                successOverlay.classList.add('flex');
                playBeep('success');

                try {
                    const response = await fetch('{{ route('stella-login.submit') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ nipd: decodedText })
                    });

                    const data = await response.json();

                    if (response.ok) {
                        successMessage.innerText = data.message || 'LOGIN BERHASIL';
                        setTimeout(() => {
                            window.location.href = data.redirect;
                        }, 1000);
                    } else {
                        throw new Error(data.message || 'Login gagal.');
                    }
                } catch (error) {
                    isProcessing = false;
                    successOverlay.classList.add('hidden');
                    successOverlay.classList.remove('flex');
                    alert(error.message);
                    // Continue scanning after a short delay
                    setTimeout(() => { isProcessing = false; }, 2000);
                }
            };

            const onScanFailure = (error) => {
                // Ignore failure to find barcode in frame
            };

            html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
                .catch(err => {
                    console.error("Camera detection error:", err);
                    alert("Gagal mengakses kamera. Pastikan izin kamera telah diberikan.");
                });

            function playBeep(type) {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                if (audioCtx.state === 'suspended') audioCtx.resume();

                const oscillator = audioCtx.createOscillator();
                const gainNode = audioCtx.createGain();
                oscillator.connect(gainNode);
                gainNode.connect(audioCtx.destination);

                if (type === 'success') {
                    oscillator.frequency.setValueAtTime(880, audioCtx.currentTime); // A5
                    gainNode.gain.setValueAtTime(0.1, audioCtx.currentTime);
                    oscillator.start();
                    oscillator.stop(audioCtx.currentTime + 0.1);
                    setTimeout(() => {
                        const osc2 = audioCtx.createOscillator();
                        osc2.connect(gainNode);
                        osc2.frequency.setValueAtTime(1108.73, audioCtx.currentTime); // C#6
                        osc2.start();
                        osc2.stop(audioCtx.currentTime + 0.2);
                    }, 100);
                }
            }
        });
    </script>
</body>

</html>