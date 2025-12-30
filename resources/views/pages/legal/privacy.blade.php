<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi — SISFO SMK Telkom Lampung</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0F172A; color: #F8FAFC; }
        .glass { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); }
    </style>
</head>
<body class="antialiased">
    <div class="min-h-screen bg-[#0F172A] pt-32 pb-20 px-6 overflow-hidden relative">
        {{-- Background Blobs --}}
        <div class="absolute top-[-100px] left-[-100px] w-[500px] h-[500px] bg-red-600 blur-[120px] opacity-10 rounded-full"></div>
        <div class="absolute bottom-[10%] right-[-100px] w-[500px] h-[500px] bg-blue-600 blur-[120px] opacity-10 rounded-full"></div>

        <div class="max-w-4xl mx-auto relative z-10">
            <div class="glass p-8 md:p-12 rounded-[40px] border-white/10 space-y-10">
                <div class="space-y-4">
                    <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-500 hover:text-white transition-colors text-sm font-bold uppercase tracking-widest">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                        Kembali ke Beranda
                    </a>
                    <h1 class="text-4xl md:text-6xl font-extrabold text-white leading-tight">Kebijakan Privasi</h1>
                    <p class="text-slate-500 font-bold uppercase tracking-[0.2em] text-xs">Terakhir Diperbarui: {{ date('d F Y') }}</p>
                </div>

                <div class="prose prose-invert prose-slate max-w-none space-y-8">
                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-red-600/20 flex items-center justify-center text-red-500 text-sm">01</span>
                            Pendahuluan
                        </h2>
                        <p class="text-slate-400 leading-relaxed font-medium">
                            Selamat datang di SISFO SMK Telkom Lampung. Kami sangat menghargai kepercayaan Anda dan berkomitmen untuk melindungi informasi pribadi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi data Anda saat menggunakan layanan kami.
                        </p>
                    </section>

                    <section class="space-y-4">
                        <h2 class="text-2xl font-bold text-white flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-blue-600/20 flex items-center justify-center text-blue-500 text-sm">02</span>
                            Informasi yang Kami Kumpulkan
                        </h2>
                        <p class="text-slate-400 leading-relaxed font-medium">
                            Kami mengumpulkan informasi yang diperlukan untuk operasional pendidikan, termasuk namun tidak terbatas pada:
                        </p>
                        <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 list-none p-0">
                            <li class="glass p-4 rounded-2xl flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-red-500"></div> Data Identitas (Nama, NIS/NIP)
                            </li>
                            <li class="glass p-4 rounded-2xl flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div> Informasi Kontak (Email, Nomor HP)
                            </li>
                            <li class="glass p-4 rounded-2xl flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-emerald-500"></div> Data Akademik & Kehadiran
                            </li>
                            <li class="glass p-4 rounded-2xl flex items-center gap-3 text-sm text-slate-300">
                                <div class="w-2 h-2 rounded-full bg-amber-500"></div> Log Aktivitas Sistem
                            </li>
                        </ul>
                    </section>

                    <section class="space-y-4 pt-8 border-t border-white/5">
                        <h2 class="text-2xl font-bold text-white">Hubungi Kami</h2>
                        <p class="text-slate-400 leading-relaxed font-medium">
                            Jika Anda memiliki pertanyaan mengenai Kebijakan Privasi ini, silakan hubungi tim IT Support SMK Telkom Lampung.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
