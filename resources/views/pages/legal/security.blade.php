<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Keamanan Sistem — SISFO SMK Telkom Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; color: #F8FAFC; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-[#0F172A] pt-32 pb-20 px-6 overflow-hidden relative">
        <div class="absolute top-[-100px] left-[-100px] w-[500px] h-[500px] bg-red-600 blur-[120px] opacity-10 rounded-full"></div>
        <div class="absolute bottom-[10%] right-[-100px] w-[500px] h-[500px] bg-emerald-600 blur-[120px] opacity-10 rounded-full"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div class="glass p-8 md:p-12 rounded-[40px] border-white/10 space-y-10">
                <div class="space-y-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-white transition-colors text-sm font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Beranda
                    </a>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">Keamanan Sistem</h1>
                    <p class="text-slate-500 font-bold uppercase tracking-[0.2em] text-xs">Standard Keamanan Digital SMK Telkom Lampung</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="glass p-6 rounded-3xl space-y-4 border-emerald-500/20">
                        <div class="w-12 h-12 bg-emerald-500/20 rounded-2xl flex items-center justify-center text-emerald-500">
                             <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-white">Enkripsi Data</h3>
                        <p class="text-sm text-slate-400 font-medium">Data Anda dilindungi oleh enkripsi SSL/TLS 256-bit.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
