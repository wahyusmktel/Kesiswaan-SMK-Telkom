<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password — SISFO SMK Telkom Lampung</title>
    <script src="https://cdn.jsdelivr.net/particles.js/2.0.0/particles.min.js"></script>
    @vite(['resources/css/app.css', 'resources/css/authentication.css', 'resources/js/app.js'])
    <style>
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.8s ease-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .gradient-red {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .gradient-red-hover {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        }

        .input-label {
            @apply block text-sm font-semibold text-gray-700 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 transition-all duration-300 focus:border-red-500 focus:ring-1 focus:ring-red-500;
        }

        .form-input:hover {
            @apply border-red-400;
        }

        .btn-submit {
            @apply w-full py-3 px-4 font-semibold text-white rounded-lg transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1;
        }

        .hero-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 50%, #7f1d1d 100%);
            position: relative;
            overflow: hidden;
        }

        .hero-gradient::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: float 20s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translate(0px, 0px);
            }

            50% {
                transform: translate(30px, -30px);
            }
        }

        .floating-shape {
            position: absolute;
            opacity: 0.1;
            animation: float 20s ease-in-out infinite;
        }

        .shape-1 {
            animation-delay: 0s;
        }

        .shape-2 {
            animation-delay: 5s;
        }

        .shape-3 {
            animation-delay: 10s;
        }

        .input-icon {
            @apply absolute right-4 top-1/2 transform -translate-y-1/2 text-gray-400 transition-colors duration-300;
        }

        .form-group:focus-within .input-icon {
            @apply text-red-500;
        }

        .error-message {
            @apply text-red-500 text-sm mt-1 flex items-center gap-1;
        }

        .success-message {
            @apply bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2;
        }

        .info-box {
            @apply bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg flex items-start gap-3 mb-6;
        }
        #particles-js {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex h-screen">
        <div class="hidden lg:flex lg:w-1/2 hero-gradient relative flex-col justify-center items-center p-12">
            <div id="particles-js"></div>
            <div class="floating-shape shape-1 w-64 h-64 rounded-full"></div>
            <div class="floating-shape shape-2 w-96 h-96 rounded-full"></div>
            <div class="floating-shape shape-3 w-48 h-48 rounded-full"></div>
            <div class="relative z-10 text-center animate-fade-in-down max-w-md">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl backdrop-blur-md border border-white/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-4 tracking-tighter">SISFO <span class="text-red-500">TS</span></h1>
                <p class="text-xl text-red-100 mb-6 font-medium">Pemulihan Akun</p>
                <p class="text-red-100/80 text-base leading-relaxed font-medium">Bantu kami mengembalikan akses ke ekosistem digital Anda dengan aman dan cepat.</p>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="w-full max-w-md animate-fade-in-up">
                <div class="text-center mb-8">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Lupa Password?</h2>
                    <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">SISFO TS &copy; {{ date('Y') }}</p>
                    <p class="text-gray-600">Masukkan email Anda untuk mendapatkan link reset</p>
                </div>
                @if (session('status'))
                    <div class="success-message">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                clip-rule="evenodd"></path>
                        </svg>
                        <span>{{ session('status') }}</span>
                    </div>
                @endif
                <div class="info-box">
                    <svg class="w-5 h-5 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div>
                        <p class="font-medium mb-1">Kami akan mengirimkan email ke Anda</p>
                        <p class="text-sm">Link reset password akan berlaku selama 1 jam.</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="input-label">Alamat Email</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-input @error('email') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="nama@sekolah.id" required autofocus />
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        @error('email')
                            <div class="error-message"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z"
                                        clip-rule="evenodd"></path>
                                </svg>{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full shadow-lg" data-submit-button>
                        <div class="flex items-center justify-center gap-2">
                            <span class="btn-text">Kirim Link Reset</span>
                            <span class="spinner" aria-hidden="true"></span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </button>
                </form>
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-gray-300"></div><span class="text-sm text-gray-500">atau</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>
                <div class="space-y-3 text-center">
                    <p class="text-gray-600"><a href="{{ route('login') }}"
                            class="text-red-600 hover:text-red-700 font-medium transition-colors">Kembali ke halaman
                            login</a></p>
                </div>
            </div>
        </div>
    </div>
    <script>
        particlesJS("particles-js", {
            "particles": {
                "number": { "value": 80, "density": { "enable": true, "value_area": 800 } },
                "color": { "value": "#ffffff" },
                "shape": { "type": "circle" },
                "opacity": { "value": 0.2, "random": false },
                "size": { "value": 3, "random": true },
                "line_linked": { "enable": true, "distance": 150, "color": "#ffffff", "opacity": 0.2, "width": 1 },
                "move": { "enable": true, "speed": 2, "direction": "none", "random": false, "straight": false, "out_mode": "out", "bounce": false }
            },
            "interactivity": {
                "detect_on": "canvas",
                "events": { "onhover": { "enable": true, "mode": "repulse" }, "onclick": { "enable": true, "mode": "push" }, "resize": true },
                "modes": { "repulse": { "distance": 100, "duration": 0.4 }, "push": { "particles_nb": 4 } }
            },
            "retina_detect": true
        });

        // Submit loading state (disable buttons only)
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', () => {
                const button = form.querySelector('[data-submit-button]');
                if (button) {
                    button.classList.add('loading');
                    button.disabled = true;
                }
                form.querySelectorAll('button').forEach((el) => {
                    if (el !== button) {
                        el.disabled = true;
                    }
                });
            });
        }
    </script>
</body>

</html>
