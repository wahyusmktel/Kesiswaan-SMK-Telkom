<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — SISFO SMK Telkom Lampung</title>
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

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes pulse-glow {

            0%,
            100% {
                box-shadow: 0 0 20px rgba(220, 38, 38, 0.3);
            }

            50% {
                box-shadow: 0 0 40px rgba(220, 38, 38, 0.5);
            }
        }

        .animate-fade-in-down {
            animation: fadeInDown 0.8s ease-out;
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out;
        }

        .animate-slide-in-left {
            animation: slideInLeft 0.8s ease-out;
        }

        .gradient-red {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }

        .gradient-red-hover {
            background: linear-gradient(135deg, #b91c1c 0%, #7f1d1d 100%);
        }

        .form-input-focus:focus {
            @apply border-red-500 ring-1 ring-red-500;
        }

        .input-label {
            @apply block text-sm font-semibold text-gray-700 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 transition-all duration-300 form-input-focus;
        }

        .form-input:hover {
            @apply border-red-400;
        }

        .btn-login {
            @apply w-full py-3 px-4 font-semibold text-white rounded-lg transition-all duration-300 hover:shadow-lg transform hover:-translate-y-1;
        }

        .glass-effect {
            @apply bg-white/95 backdrop-blur-md border border-white/20;
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
            @apply bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-4 flex items-center gap-2;
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
        <!-- Hero Section -->
        <div class="hidden lg:flex lg:w-1/2 hero-gradient relative flex-col justify-center items-center p-12">
            <div id="particles-js"></div>
            <div class="floating-shape shape-1 w-64 h-64 rounded-full"></div>
            <div class="floating-shape shape-2 w-96 h-96 rounded-full"></div>
            <div class="floating-shape shape-3 w-48 h-48 rounded-full"></div>

            <div class="relative z-10 text-center animate-fade-in-down">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl backdrop-blur-md border border-white/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-5xl font-extrabold text-white mb-4 tracking-tighter">SISFO <span class="text-red-500">TS</span></h1>
                <p class="text-xl text-red-100 mb-8 font-medium">Sistem Informasi SMK Telkom Lampung</p>
                <p class="text-red-100/80 text-lg max-w-md mx-auto leading-relaxed transition-all duration-300">
                    Ekosistem digital terintegrasi untuk mewujudkan manajemen sekolah yang cerdas, efisien, dan transparan.
                </p>
            </div>
        </div>

        <!-- Login Form Section -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="w-full max-w-md animate-fade-in-up">
                <!-- Header -->
                <div class="text-center mb-8">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-gray-900 mb-2 tracking-tight">Selamat Datang</h2>
                    <p class="text-gray-600 font-medium">Silakan masuk ke akun SISFO Anda</p>
                </div>

                <!-- Session Status -->
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

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div class="form-group">
                        <label for="email" class="input-label">Email</label>
                        <div class="relative">
                            <input id="email" type="email" name="email" value="{{ old('email') }}"
                                class="form-input @error('email') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="nama@sekolah.id" required autofocus autocomplete="username" />
                            <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z">
                                </path>
                            </svg>
                        </div>
                        @error('email')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="form-group">
                        <label for="password" class="input-label">Password</label>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                class="form-input has-eye @error('password') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="••••••••" required autocomplete="current-password" />
                            <svg class="input-icon right-10 w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" data-toggle="password" data-target="password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="eye-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="eye-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.96 9.96 0 012.768-4.724m3.546-2.232A9.955 9.955 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.958 9.958 0 01-4.043 5.306M15 12a3 3 0 00-3-3m0 0a2.996 2.996 0 00-2.815 2.01m0 0L3 21m6-9a3 3 0 003 3m0 0a2.996 2.996 0 002.815-2.01m0 0L21 3" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <div class="error-message">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z"
                                        clip-rule="evenodd"></path>
                                </svg>
                                {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Remember Me & Forgot Password -->
                    <div class="flex items-center justify-between">
                        <label for="remember" class="flex items-center gap-2 cursor-pointer group">
                            <input id="remember" type="checkbox" name="remember"
                                class="w-4 h-4 text-red-600 border-gray-300 rounded cursor-pointer focus:ring-red-500" />
                            <span class="text-sm text-gray-600 group-hover:text-red-600 transition-colors">Ingat
                                saya</span>
                        </label>
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}"
                                class="text-sm text-red-600 hover:text-red-700 font-medium transition-colors">
                                Lupa password?
                            </a>
                        @endif
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" class="btn-primary w-full shadow-lg" data-submit-button>
                        <div class="flex items-center justify-center gap-2">
                            <span class="btn-text">Masuk</span>
                            <span class="spinner" aria-hidden="true"></span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                            </svg>
                        </div>
                    </button>
                </form>

                <!-- Divider -->
                <div class="flex items-center gap-4 my-6">
                    <div class="flex-1 h-px bg-gray-300"></div>
                    <span class="text-sm text-gray-500">atau</span>
                    <div class="flex-1 h-px bg-gray-300"></div>
                </div>

                @if (session('error'))
                    <div class="error-message mb-4 bg-red-50 border border-red-200 p-3 rounded-lg">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Google Login Button -->
                <a href="{{ route('auth.google') }}" class="flex items-center justify-center gap-3 w-full py-3 px-4 bg-white border border-gray-300 rounded-lg font-semibold text-gray-700 hover:bg-gray-50 hover:shadow-md transition-all duration-300 transform hover:-translate-y-1 mb-6">
                    <svg class="w-5 h-5" viewBox="0 0 24 24">
                        <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                        <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                        <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                        <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                    </svg>
                    <span>Masuk dengan Google</span>
                </a>

                <!-- Register Link -->
                <p class="text-center text-gray-600">
                    Belum punya akun?
                    <a href="{{ route('register') }}"
                        class="font-semibold text-red-600 hover:text-red-700 transition-colors">
                        Daftar sekarang
                    </a>
                </p>
            </div>

            <!-- Mobile Logo -->
            <div class="lg:hidden mt-12 text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-[10px] font-bold uppercase tracking-widest">SISFO TS &copy; {{ date('Y') }}</p>
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

        // Toggle password visibility
        document.querySelectorAll('[data-toggle="password"]').forEach((btn) => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                const eyeOpen = btn.querySelector('.eye-open');
                const eyeClosed = btn.querySelector('.eye-closed');
                if (eyeOpen && eyeClosed) {
                    eyeOpen.classList.toggle('hidden', show);
                    eyeClosed.classList.toggle('hidden', !show);
                }
            });
        });

        // Submit loading state (disable buttons only, keep inputs to submit values)
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
