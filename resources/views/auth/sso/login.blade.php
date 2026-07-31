<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Masuk dengan SISFO</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-50 font-sans text-slate-900 antialiased">
    <main class="grid min-h-screen lg:grid-cols-[minmax(0,1fr)_minmax(420px,560px)]">
        <section class="relative hidden overflow-hidden bg-slate-950 px-12 py-14 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="absolute inset-x-0 top-0 h-1 bg-red-600"></div>
            <div class="relative flex items-center gap-3">
                <div class="flex h-12 w-12 items-center justify-center border border-white/20 bg-white/10 text-lg font-black">TS</div>
                <div>
                    <p class="font-extrabold">SISFO TS</p>
                    <p class="text-xs text-slate-400">SMK Telkom Lampung</p>
                </div>
            </div>

            <div class="relative max-w-xl">
                <p class="mb-5 text-xs font-bold uppercase tracking-[0.22em] text-red-400">Single Sign-On Sekolah</p>
                <h1 class="text-5xl font-black leading-tight">Satu akun untuk layanan digital sekolah.</h1>
                <p class="mt-6 max-w-lg text-base leading-7 text-slate-300">Masuk menggunakan akun SISFO yang sudah terdaftar. Kata sandi Anda hanya diproses oleh SISFO dan tidak pernah dibagikan kepada aplikasi tujuan.</p>
            </div>

            <div class="relative flex items-center gap-3 text-xs text-slate-400">
                <svg class="h-5 w-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5-4.5A11.95 11.95 0 0112 3a11.95 11.95 0 01-8 2.5C4 13 7.5 18 12 21c4.5-3 8-8 8-15.5z"/></svg>
                OAuth 2.0 Authorization Code + PKCE
            </div>
        </section>

        <section class="flex items-center justify-center px-5 py-10 sm:px-10">
            <div class="w-full max-w-md">
                <div class="mb-9 flex items-center gap-3 lg:hidden">
                    <div class="flex h-11 w-11 items-center justify-center bg-red-600 font-black text-white">TS</div>
                    <div><p class="font-extrabold">SISFO TS</p><p class="text-xs text-slate-500">SMK Telkom Lampung</p></div>
                </div>

                @if($application)
                    <div class="mb-6 flex items-center gap-3 border-l-4 border-red-600 bg-white px-4 py-3 shadow-sm">
                        @if($application->logo_url)
                            <img src="{{ $application->logo_url }}" alt="" class="h-10 w-10 object-contain">
                        @else
                            <span class="flex h-10 w-10 items-center justify-center bg-slate-100 font-black text-slate-600">{{ strtoupper(substr($application->client->name, 0, 1)) }}</span>
                        @endif
                        <div><p class="text-xs text-slate-500">Masuk untuk melanjutkan ke</p><p class="font-bold">{{ $application->client->name }}</p></div>
                    </div>
                @endif

                <h2 class="text-3xl font-black">Masuk dengan SISFO</h2>
                <p class="mt-2 text-sm leading-6 text-slate-500">Gunakan akun yang sama dengan aplikasi SISFO sekolah.</p>

                @if(session('error'))
                    <div class="mt-6 border border-red-200 bg-red-50 px-4 py-3 text-sm font-semibold text-red-700">{{ session('error') }}</div>
                @endif

                <form method="POST" action="{{ route('sso.login.store') }}" class="mt-7 space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-sm font-bold">Email SISFO</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                            class="h-12 w-full border bg-white px-3 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100 {{ $errors->has('email') ? 'border-red-500' : 'border-slate-300' }}">
                        @error('email')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password" class="mb-2 block text-sm font-bold">Kata sandi</label>
                        <input id="password" name="password" type="password" required autocomplete="current-password"
                            class="h-12 w-full border bg-white px-3 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100 {{ $errors->has('password') ? 'border-red-500' : 'border-slate-300' }}">
                        @error('password')<p class="mt-1.5 text-xs font-semibold text-red-600">{{ $message }}</p>@enderror
                    </div>
                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                        Ingat sesi di perangkat ini
                    </label>
                    <button type="submit" class="flex h-12 w-full items-center justify-center gap-2 bg-red-600 px-4 text-sm font-extrabold text-white transition hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-200">
                        Masuk dan Lanjutkan
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </form>

                <div class="my-6 flex items-center gap-3"><span class="h-px flex-1 bg-slate-200"></span><span class="text-xs font-bold text-slate-400">ATAU</span><span class="h-px flex-1 bg-slate-200"></span></div>

                <a href="{{ route('sso.google.redirect') }}" class="flex h-12 w-full items-center justify-center gap-3 border border-slate-300 bg-white px-4 text-sm font-bold transition hover:border-slate-400 hover:bg-slate-50">
                    <span class="flex h-6 w-6 items-center justify-center rounded-full bg-white text-base font-black text-blue-600">G</span>
                    Masuk dengan Google Workspace
                </a>

                <p class="mt-8 text-center text-xs leading-5 text-slate-400">Pastikan alamat yang terbuka adalah <strong class="text-slate-600">{{ config('sso.domain') }}</strong> sebelum memasukkan akun.</p>
            </div>
        </section>
    </main>
</body>
</html>
