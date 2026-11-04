<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Login Ujian CBT Seleksi SPMB | SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-100 via-slate-50 to-blue-50/40 min-h-screen flex flex-col justify-between">

    {{-- TOP CBT HEADER (KEMDIKBUD / PUSMENDIK CBT STYLE) --}}
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-red-600 flex items-center justify-center text-white font-black text-xl shadow-sm tracking-tighter">
                    TL
                </div>
                <div>
                    <h1 class="text-sm font-extrabold text-slate-900 tracking-tight leading-none uppercase">
                        SMK Telkom Lampung
                    </h1>
                    <p class="text-[11px] font-semibold text-red-600 tracking-wide uppercase mt-0.5">
                        Computer Based Test (CBT) SPMB
                    </p>
                </div>
            </div>
            <div class="hidden sm:flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Server Ujian Aktif
                </span>
            </div>
        </div>
    </header>

    {{-- MAIN CONTENT WRAPPER --}}
    <main class="flex-1 flex items-center justify-center p-4 sm:p-6 lg:p-8">
        <div class="w-full max-w-md sm:max-w-lg space-y-4">

            {{-- OFFICIAL TKA CBT BANNER --}}
            <div class="bg-gradient-to-r from-red-600 via-rose-600 to-red-700 rounded-2xl p-5 text-white shadow-lg shadow-red-500/10 relative overflow-hidden">
                <div class="absolute -right-4 -bottom-6 opacity-10 pointer-events-none">
                    <svg class="w-40 h-40" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L1 21h22L12 2zm0 4l7.53 13H4.47L12 6zm-1 4v4h2v-4h-2zm0 6v2h2v-2h-2z"/></svg>
                </div>
                <div class="relative z-10">
                    <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider bg-white/20 text-white inline-block mb-1">
                        Portal Seleksi Calon Siswa Baru
                    </span>
                    <h2 class="text-lg sm:text-xl font-black tracking-tight leading-snug">
                        Tes Kemampuan & Potensi Akademik
                    </h2>
                    <p class="text-xs text-red-100 mt-1 leading-relaxed">
                        Selamat datang calon siswa baru SMK Telkom Lampung. Silakan masuk menggunakan akun seleksi yang telah diberikan panitia.
                    </p>
                </div>
            </div>

            {{-- LOGIN CARD --}}
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xl shadow-slate-200/50 p-6 sm:p-8" x-data="{ showPass: false, loading: false }">
                
                {{-- Flash Notifications --}}
                @if(session('success'))
                    <div class="mb-5 p-3.5 rounded-xl text-xs font-semibold bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->has('auth_failed'))
                    <div class="mb-5 p-3.5 rounded-xl text-xs font-medium bg-rose-50 text-rose-800 border border-rose-200 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ $errors->first('auth_failed') }}</span>
                    </div>
                @endif

                <div class="text-center pb-5 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900 tracking-tight">MASUK RUANG UJIAN</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Masukkan Nomor Peserta & Kata Sandi Ujian Anda</p>
                </div>

                <form method="POST" action="{{ route('seleksi.login.submit') }}" @submit="loading = true" class="mt-6 space-y-4">
                    @csrf

                    {{-- Username / No Peserta --}}
                    <div>
                        <label for="username" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Username / Nomor Peserta <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                                placeholder="Contoh: 12345 atau REG-2027..."
                                class="w-full pl-10 pr-3.5 py-2.5 text-sm rounded-xl border border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all font-medium text-slate-900 placeholder:text-slate-400">
                        </div>
                        @error('username')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password / Token --}}
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Kata Sandi / Token <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                            <input :type="showPass ? 'text' : 'password'" name="password" id="password" required
                                placeholder="Masukkan kata sandi..."
                                class="w-full pl-10 pr-11 py-2.5 text-sm rounded-xl border border-slate-300 focus:border-red-500 focus:ring-2 focus:ring-red-200 transition-all font-medium text-slate-900 placeholder:text-slate-400">
                            
                            {{-- Toggle Password Visibility --}}
                            <button type="button" @click="showPass = !showPass"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 focus:outline-none">
                                <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                </svg>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-[11px] text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit Button --}}
                    <div class="pt-2">
                        <button type="submit" :disabled="loading"
                            class="w-full flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-sm font-bold text-white bg-red-600 hover:bg-red-700 active:bg-red-800 shadow-md shadow-red-600/20 transition-all disabled:opacity-60 disabled:cursor-not-allowed">
                            <span x-show="!loading" class="flex items-center gap-2">
                                <span>MASUK UJIAN (LOGIN)</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </span>
                            <span x-show="loading" x-cloak class="flex items-center gap-2">
                                <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memverifikasi Akun...</span>
                            </span>
                        </button>
                    </div>
                </form>

                {{-- PETUNJUK RESMI PESERTA --}}
                <div class="mt-6 pt-5 border-t border-slate-100 text-slate-500 text-xs space-y-2">
                    <p class="font-bold text-slate-700 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Petunjuk Pelaksanaan Ujian:
                    </p>
                    <ul class="list-disc pl-5 space-y-1 text-[11px] text-slate-600 leading-relaxed">
                        <li>Tahap 1: Peserta wajib menyelesaikan <strong>Tes Buta Warna</strong>.</li>
                        <li>Tahap 2: Peserta melanjutkan <strong>Tes Potensi Akademik (TPA)</strong> secara berurutan: Matematika, Bahasa Inggris, Bahasa Indonesia, dan Dasar TIK.</li>
                        <li>Gunakan perangkat smartphone atau laptop dengan browser modern dan koneksi internet yang stabil.</li>
                    </ul>
                </div>

            </div>

        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="py-4 text-center text-xs text-slate-500">
        <p>&copy; {{ date('Y') }} Panitia SPMB SMK Telkom Lampung &bull; Sistem Seleksi Terpadu</p>
    </footer>

</body>
</html>
