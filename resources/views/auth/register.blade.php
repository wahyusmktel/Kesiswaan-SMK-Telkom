<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - Aplikasi Izin</title>
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

        .input-label {
            @apply block text-sm font-semibold text-gray-700 mb-2;
        }

        .form-input {
            @apply w-full px-4 py-3 border border-gray-300 rounded-lg bg-white text-gray-900 placeholder-gray-400 transition-all duration-300 focus:border-red-500 focus:ring-1 focus:ring-red-500;
        }

        .form-input:hover {
            @apply border-red-400;
        }

        .btn-register {
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
    </style>
</head>

<body class="bg-gray-50">
    <div class="flex h-full min-h-screen">
        <!-- Hero Section -->
        <div class="hidden lg:flex lg:w-1/2 hero-gradient relative flex-col justify-center items-center p-12">
            <div class="floating-shape shape-1 w-64 h-64 rounded-full"></div>
            <div class="floating-shape shape-2 w-96 h-96 rounded-full"></div>
            <div class="floating-shape shape-3 w-48 h-48 rounded-full"></div>

            <div class="relative z-10 text-center animate-fade-in-down">
                <div class="mb-8">
                    <div
                        class="inline-flex items-center justify-center w-20 h-20 bg-white/20 rounded-2xl backdrop-blur-md border border-white/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-5xl font-bold text-white mb-4">Bergabunglah</h1>
                <p class="text-xl text-red-100 mb-8">dengan Aplikasi Izin</p>
                <p class="text-red-100 text-lg max-w-md mx-auto leading-relaxed">
                    Daftarkan akun Anda dan mulai mengelola perizinan dengan sistem yang modern dan terpercaya. Proses
                    pendaftaran cepat dan mudah.
                </p>
                <div class="mt-12 pt-12 border-t border-white/20">
                    <div class="grid grid-cols-3 gap-6 text-center">
                        <div>
                            <div class="text-2xl font-bold text-white">100+</div>
                            <p class="text-red-100 text-sm mt-1">Sekolah</p>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">10K+</div>
                            <p class="text-red-100 text-sm mt-1">Pengguna</p>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-white">24/7</div>
                            <p class="text-red-100 text-sm mt-1">Support</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Register Form Section -->
        <div
            class="w-full lg:w-1/2 flex flex-col justify-center items-center px-6 py-8 sm:px-12 lg:px-16 overflow-y-auto">
            <div class="w-full max-w-md animate-fade-in-up">
                <!-- Debug: Registration status: {{ var_export($appSetting?->allow_registration, true) }} -->
                @if($appSetting && !$appSetting->allow_registration)
                    <!-- Registration Closed Message -->
                    <div class="text-center">
                        <div class="mb-8 inline-flex items-center justify-center w-24 h-24 bg-red-100 rounded-full text-red-600 animate-pulse">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                            </svg>
                        </div>
                        <h2 class="text-3xl font-extrabold text-gray-900 mb-4">Pendaftaran Ditutup</h2>
                        <div class="bg-white border-2 border-dashed border-red-200 rounded-2xl p-6 shadow-sm">
                            <p class="text-gray-600 leading-relaxed mb-6">
                                Mohon maaf, saat ini pendaftaran akun baru telah ditutup oleh sistem.
                            </p>
                            <div class="bg-red-50 rounded-xl p-4 text-left border border-red-100 mb-6">
                                <p class="text-red-800 text-sm font-semibold flex items-center gap-2 mb-2">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                                    Informasi Lebih Lanjut:
                                </p>
                                <p class="text-red-700 text-sm">
                                    Silahkan hubungi administrator sekolah atau tim kesiswaan untuk mendapatkan akses atau informasi pembukaan pendaftaran kembali.
                                </p>
                            </div>
                            <a href="{{ route('login') }}" class="btn-primary w-full flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                Kembali ke Halaman Login
                            </a>
                        </div>
                        <p class="mt-8 text-gray-400 text-xs">
                             &copy; {{ date('Y') }} {{ $appSetting?->school_name ?? 'SMK Telkom' }}
                        </p>
                    </div>
                @else
                    <!-- Header -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">Buat Akun Baru</h2>
                        <p class="text-gray-600">Isi data berikut untuk mendaftar</p>
                    </div>

                    <!-- Register Form -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-5">
                        @csrf

                        <!-- Name -->
                        <div class="form-group">
                            <label for="name" class="input-label">Nama Lengkap</label>
                            <div class="relative">
                                <input id="name" type="text" name="name" value="{{ old('name') }}"
                                    class="form-input @error('name') border-red-500 ring-1 ring-red-500 @enderror"
                                    placeholder="Masukkan nama lengkap" required autofocus autocomplete="name" />
                                <svg class="input-icon w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            @error('name')
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

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="input-label">Email</label>
                            <div class="relative">
                                <input id="email" type="email" name="email" value="{{ old('email') }}"
                                    class="form-input @error('email') border-red-500 ring-1 ring-red-500 @enderror"
                                    placeholder="nama@sekolah.id" required autocomplete="email" />
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
                                    <svg class="eye-closed w-5 h-5 hidden" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.477 0-8.268-2.943-9.542-7a9.96 9.96 0 012.768-4.724m3.546-2.232A9.955 9.955 0 0112 5c4.477 0 8.268 2.943 9.542 7a9.958 9.958 0 01-4.043 5.306M15 12a3 3 0 00-3-3m0 0a2.996 2.996 0 00-2.815 2.01m0 0L3 21m6-9a3 3 0 003 3m0 0a2.996 2.996 0 002.815-2.01m0 0L21 3" />
                                    </svg>
                                </button>
                            </div>
                            <div class="password-strength strength-medium w-full"></div>
                            <p class="text-xs text-gray-500 mt-2">Minimal 8 karakter dengan kombinasi huruf, angka, dan
                                simbol</p>
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

                        <!-- Confirm Password -->
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

                        <!-- Terms Checkbox -->
                        <label for="terms" class="flex items-start gap-3 cursor-pointer group">
                            <input id="terms" type="checkbox" name="terms"
                                class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded cursor-pointer focus:ring-red-500"
                                required />
                            <span class="text-sm text-gray-600 group-hover:text-red-600 transition-colors">
                                Saya setuju dengan <a href="#"
                                    class="text-red-600 hover:text-red-700 font-medium">Syarat dan Ketentuan</a> dan <a
                                    href="#" class="text-red-600 hover:text-red-700 font-medium">Kebijakan
                                    Privasi</a>
                            </span>
                        </label>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-primary w-full shadow-lg" data-submit-button>
                            <div class="flex items-center justify-center gap-2">
                                <span class="btn-text">Daftar Sekarang</span>
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

                    <!-- Login Link -->
                    <p class="text-center text-gray-600">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            class="font-semibold text-red-600 hover:text-red-700 transition-colors">
                            Masuk di sini
                        </a>
                    </p>
                @endif
            </div>

            <!-- Mobile Logo -->
            <div class="lg:hidden mt-8 text-center">
                <div
                    class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-red-500 to-red-700 rounded-2xl mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <p class="text-gray-500 text-sm">Aplikasi Izin &copy; 2025</p>
            </div>
        </div>
    </div>

    <script>
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
