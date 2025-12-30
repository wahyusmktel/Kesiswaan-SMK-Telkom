<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Syarat & Ketentuan — SISFO SMK Telkom Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; color: #F8FAFC; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-[#0F172A] pt-32 pb-20 px-6 overflow-hidden relative">
        <div class="absolute top-[-100px] right-[-100px] w-[500px] h-[500px] bg-red-600 blur-[120px] opacity-10 rounded-full"></div>
        <div class="absolute bottom-[10%] left-[-100px] w-[500px] h-[500px] bg-blue-600 blur-[120px] opacity-10 rounded-full"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div class="glass p-8 md:p-12 rounded-[40px] border-white/10 space-y-10">
                <div class="space-y-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-white transition-colors text-sm font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Beranda
                    </a>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">Syarat & Ketentuan</h1>
                    <p class="text-slate-500 font-bold uppercase tracking-[0.2em] text-xs">Versi 1.0 — SMK Telkom Lampung</p>
                </div>

                <div class="prose prose-invert prose-slate max-w-none space-y-8 text-slate-400 font-medium">
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-white">1. Penggunaan Layanan</h2>
                        <p>Dengan mengakses SISFO SMK Telkom Lampung, Anda setuju untuk menggunakan sistem ini sesuai dengan Kode Etik Sekolah dan peraturan perundang-undangan yang berlaku di Indonesia.</p>
                    </section>
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-white">2. Akurasi Data</h2>
                        <p>Pengguna bertanggung jawab penuh atas kebenaran data yang dimasukkan ke dalam sistem.</p>
                    </section>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
