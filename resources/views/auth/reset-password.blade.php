<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atur Password Baru — SISFO SMK Telkom Lampung</title>
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

        .password-strength {
            @apply h-2 rounded-full mt-2 transition-all duration-300;
        }

        .strength-weak {
            @apply bg-red-500;
        }

        .strength-medium {
            @apply bg-yellow-500;
        }

        .strength-strong {
            @apply bg-green-500;
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
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                            </path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-4xl font-extrabold text-white mb-4 tracking-tighter">SISFO <span class="text-red-500">TS</span></h1>
                <p class="text-xl text-red-100 mb-6 font-medium">Keamanan Akun Utama</p>
                <p class="text-red-100/80 text-base leading-relaxed font-medium">Langkah terakhir untuk mengamankan kembali akses Anda. Gunakan kombinasi password yang kuat.</p>
            </div>
        </div>
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-12 sm:px-12 lg:px-16">
            <div class="w-full max-w-md animate-fade-in-up">
                <div class="text-center mb-8">
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Atur Password Baru</h2>
                    <p class="text-gray-600">Masukkan password baru untuk akun Anda</p>
                </div>
                <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    <div class="form-group">
                        <label for="email" class="input-label">Alamat Email</label>
                        <div class="relative">
                            <input id="email" type="email" name="email"
                                value="{{ old('email', $request->email) }}"
                                class="form-input @error('email') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="nama@sekolah.id" required autofocus autocomplete="email" />
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
                    <div class="form-group">
                        <label for="password" class="input-label">Password Baru</label>
                        <div class="relative">
                            <input id="password" type="password" name="password"
                                class="form-input has-eye @error('password') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="••••••••" required autocomplete="new-password" />
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
                        <div class="password-strength strength-medium w-full"></div>
                        <p class="text-xs text-gray-500 mt-2">Minimal 8 karakter dengan kombinasi huruf, angka, dan
                            simbol</p>
                        @error('password')
                            <div class="error-message"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z"
                                        clip-rule="evenodd"></path>
                                </svg>{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="password_confirmation" class="input-label">Konfirmasi Password</label>
                        <div class="relative">
                            <input id="password_confirmation" type="password" name="password_confirmation"
                                class="form-input has-eye @error('password_confirmation') border-red-500 ring-1 ring-red-500 @enderror"
                                placeholder="••••••••" required autocomplete="new-password" />
                            <svg class="input-icon right-10 w-5 h-5" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            <button type="button" data-toggle="password" data-target="password_confirmation"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                <svg class="eye-open w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.477 0 8.268 2.943 9.542 7-1.274 4.057-5.065 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg class="eye-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.96 9.96 0 012.768-4.724m3.546-2.232A9.955 9.955 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.958 9.958 0 01-4.043 5.306M15 12a3 3 0 00-3-3m0 0a2.996 2.996 0 00-2.815 2.01m0 0L3 21m6-9a3 3 0 003 3m0 0a2.996 2.996 0 002.815-2.01m0 0L21 3" />
                                </svg>
                            </button>
                        </div>
                        @error('password_confirmation')
                            <div class="error-message"><svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M18.101 12.93a1 1 0 00-1.414-1.414L10 14.586l-6.687-6.687a1 1 0 00-1.414 1.414l8 8a1 1 0 001.414 0l8-8z"
                                        clip-rule="evenodd"></path>
                                </svg>{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn-primary w-full shadow-lg" data-submit-button>
                        <div class="flex items-center justify-center gap-2">
                            <span class="btn-text">Atur Password Baru</span>
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
                <p class="text-center text-gray-600"><a href="{{ route('login') }}"
                        class="text-red-600 hover:text-red-700 font-medium transition-colors">Kembali ke halaman
                        login</a></p>
            </div>
            <div class="lg:hidden mt-12 text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
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

        const passwordInput = document.getElementById('password');
        const passwordStrengthBar = document.querySelector('.password-strength');
        if (passwordInput) {
            passwordInput.addEventListener('input', function() {
                const password = this.value;
                let strength = 0;
                if (password.length >= 8) strength++;
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
                if (/\d/.test(password)) strength++;
                if (/[^a-zA-Z\d]/.test(password)) strength++;
                passwordStrengthBar.className = 'password-strength';
                if (password.length === 0) {
                    passwordStrengthBar.classList.add('strength-medium');
                } else if (strength <= 2) {
                    passwordStrengthBar.classList.add('strength-weak');
                } else if (strength === 3) {
                    passwordStrengthBar.classList.add('strength-medium');
                } else {
                    passwordStrengthBar.classList.add('strength-strong');
                }
            });
        }


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
    </script>
</body>

</html>
