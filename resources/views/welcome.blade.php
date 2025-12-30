<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SISFO — SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif
    
    <style>
        :root {
            --telkom-red: #E21F26;
            --telkom-red-dark: #B91319;
            --telkom-red-light: #FF4D54;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #0F172A; /* Darker Slate */
            color: #F8FAFC;
        }

        h1, h2, h3, h4, .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--telkom-red);
            transform: translateY(-5px);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--telkom-red) 0%, var(--telkom-red-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px -5px rgba(226, 31, 38, 0.4);
        }

        .text-gradient {
            background: linear-gradient(135deg, #FFF 0%, #AAA 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .text-gradient-red {
            background: linear-gradient(135deg, var(--telkom-red-light) 0%, var(--telkom-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .blob {
            position: absolute;
            width: 500px;
            height: 500px;
            background: var(--telkom-red);
            filter: blur(120px);
            opacity: 0.15;
            z-index: -1;
            border-radius: 50%;
        }

        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
            100% { transform: translateY(0px); }
        }

        .animate-float {
            animation: float 6s ease-in-out infinite;
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }

        .nav-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--telkom-red);
            transition: width 0.3s ease;
        }

        .nav-link:hover::after {
            width: 100%;
        }
    </style>
</head>
<body class="antialiased overflow-x-hidden" x-data="{ showVideo: false }">
    <!-- Blobs -->
    <div class="blob top-[-100px] left-[-100px]"></div>
    <div class="blob bottom-[10%] right-[-100px]" style="background: #3B82F6; opacity: 0.1;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between glass py-3 px-6 rounded-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-lg">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                    @endif
                </div>
                <div class="flex flex-col leading-tight hidden sm:block">
                    <span class="font-outfit font-black text-xl tracking-tighter">SISFO <span class="text-red-500">TS</span></span>
                </div>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#features" class="nav-link text-slate-300 hover:text-white">Ekosistem Digital</a>
                <a href="{{ route('pengaduan.create') }}" class="nav-link text-slate-300 hover:text-white">Layanan Aduan</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Dashboard Utama</a>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-slate-300 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Daftar Akun</a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle -->
            <div class="md:hidden flex items-center gap-2">
                 @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-tighter">DASHBOARD</a>
                @else
                    <a href="{{ route('login') }}" class="btn-primary px-4 py-2 rounded-lg font-bold text-xs">LOGIN</a>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 px-6 overflow-hidden min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-10 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-600/10 border border-red-600/20 text-red-500 text-sm font-bold tracking-wide">
                    🚀 <span class="animate-pulse">One Gateway Solution</span>
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.05] text-gradient">
                    Ekosistem Digital <br>
                    <span class="text-gradient-red italic">SMK Telkom</span> Lampung
                </h1>
                <p class="text-lg text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed font-medium">
                    Pusat integrasi seluruh layanan pendidikan, mulai dari Akademik, Kesiswaan, Kepegawaian (HR), hingga Manajemen Prakerin dalam satu platform yang cerdas dan transparan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('login') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl flex items-center justify-center gap-2">
                        Masuk ke Sistem
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </a>
                    <a href="#features" class="glass py-4 px-10 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all text-center">
                        Pelajari Fitur
                    </a>
                </div>
                <div class="grid grid-cols-3 gap-8 pt-4 border-t border-white/5 opacity-80">
                    <div>
                        <p class="text-2xl font-black text-white">100%</p>
                        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Digitalized</p>
                    </div>
                    <div class="border-l border-white/10 pl-8">
                        <p class="text-2xl font-black text-white">24/7</p>
                        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Availability</p>
                    </div>
                    <div class="border-l border-white/10 pl-8">
                        <p class="text-2xl font-black text-white">Cloud</p>
                        <p class="text-[10px] uppercase font-bold text-slate-500 tracking-widest">Integrated</p>
                    </div>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="animate-float">
                    <div class="glass p-4 rounded-[40px] rotate-3 shadow-2xl border-white/20">
                        <div class="bg-slate-900 rounded-[30px] overflow-hidden aspect-video relative group border border-white/10">
                            <!-- Placeholder for SISFO Overview Image -->
                            <img src="https://images.unsplash.com/photo-1551288049-bebda4e38f71?auto=format&fit=crop&q=80&w=1200" alt="SISFO Overview" class="w-full h-full object-cover opacity-60 group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition-all">
                                <div class="glass p-8 rounded-full border-red-500/30">
                                    <button @click="showVideo = true" class="w-16 h-16 bg-red-600 rounded-full flex items-center justify-center shadow-red-600/50 shadow-2xl hover:scale-110 active:scale-95 transition-all outline-none">
                                        <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Additional floating elements -->
                <div class="absolute -top-10 -right-10 glass p-6 rounded-3xl animate-float" style="animation-delay: 1s;">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-blue-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Akademik</p>
                            <p class="text-lg font-bold">Terpadu</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-5 -left-10 glass p-6 rounded-3xl animate-float" style="animation-delay: 2s;">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-500 font-black uppercase tracking-widest">Kesiswaan</p>
                            <p class="text-lg font-bold">Cerdas</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features -->
    <section id="features" class="py-24 px-6 relative">
        <div class="max-w-7xl mx-auto space-y-20">
            <div class="text-center space-y-4">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-600/10 border border-blue-600/20 text-blue-500 text-xs font-black uppercase tracking-widest mb-2">
                    Ecosystem Modules
                </div>
                <h2 class="text-3xl md:text-5xl font-extrabold text-gradient leading-tight">Layanan Sekolah Terintegrasi</h2>
                <p class="text-slate-400 max-w-2xl mx-auto font-medium">Platform tunggal yang menghubungkan seluruh aspek operasional sekolah untuk efisiensi dan transparansi maksimal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Academic -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group">
                    <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center group-hover:bg-blue-600/20 transition-colors">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">Portal Akademik</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Manajemen kurikulum, jadwal pelajaran, hingga monitoring absensi guru kelas secara real-time.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-blue-500 rounded-full"></div> Presensi Guru</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-blue-500 rounded-full"></div> Jadwal Mengajar</li>
                        </ul>
                    </div>
                </div>

                <!-- Kesiswaan -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group border-red-500/20">
                    <div class="w-14 h-14 bg-red-600/10 rounded-2xl flex items-center justify-center group-hover:bg-red-600/20 transition-colors">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">Manajemen Kesiswaan</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Digitalisasi perizinan, poin kedisiplinan, hingga tracking keterlambatan siswa yang terintegrasi.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-red-500 rounded-full"></div> Kartu Pelajar Digital</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-red-500 rounded-full"></div> Izin Keluar Sekolah</li>
                        </ul>
                    </div>
                </div>

                <!-- SDM / Kepegawaian -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group border-emerald-500/20">
                    <div class="w-14 h-14 bg-emerald-600/10 rounded-2xl flex items-center justify-center group-hover:bg-emerald-600/20 transition-colors">
                        <svg class="w-8 h-8 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">SDM & Kepegawaian</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Pengelolaan data guru dan staf, pengajuan izin/cuti pegawai, hingga penilaian kinerja internal.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-emerald-500 rounded-full"></div> Izin Dinas Luar</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-emerald-500 rounded-full"></div> Monitoring Kehadiran</li>
                        </ul>
                    </div>
                </div>

                <!-- Prakerin -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group border-orange-500/20">
                    <div class="w-14 h-14 bg-orange-600/10 rounded-2xl flex items-center justify-center group-hover:bg-orange-600/20 transition-colors">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">Hubin & Prakerin</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Monitoring kegiatan Praktek Kerja Industri (Prakerin) dan manajemen kemitraan dunia usaha.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-orange-500 rounded-full"></div> Jurnal Mingguan</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-orange-500 rounded-full"></div> Penempatan Magang</li>
                        </ul>
                    </div>
                </div>

                <!-- BK / Konseling -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group border-purple-500/20">
                    <div class="w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center group-hover:bg-purple-600/20 transition-colors">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">Layanan Konseling</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Wadah komunikasi antara siswa, guru BK, dan orang tua untuk bimbingan karir dan sosial.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-purple-500 rounded-full"></div> Konsultasi Online</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-purple-500 rounded-full"></div> Tracking Prestasi</li>
                        </ul>
                    </div>
                </div>

                <!-- Admin & Pengaduan -->
                <div class="glass-card p-8 rounded-[32px] space-y-6 group border-slate-500/20">
                    <div class="w-14 h-14 bg-slate-600/10 rounded-2xl flex items-center justify-center group-hover:bg-slate-600/20 transition-colors">
                        <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <div class="space-y-3">
                        <h3 class="text-xl font-bold">Layanan Terpadu</h3>
                        <p class="text-slate-400 leading-relaxed text-sm">Pusat pengaduan orang tua, manajemen data dapodik, hingga laporan analitik sistem kesiswaan.</p>
                        <ul class="text-xs space-y-2 text-slate-500 font-bold">
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-slate-500 rounded-full"></div> Pengaduan Wali Murid</li>
                            <li class="flex items-center gap-2"><div class="w-1 h-1 bg-slate-500 rounded-full"></div> Export Data Excel/PDF</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Digital Ecosystem Integration -->
    <section class="py-24 px-6 bg-slate-900/50">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="space-y-8 order-2 lg:order-1">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-600/10 border border-red-600/20 text-red-500 text-xs font-black uppercase tracking-widest">
                    Connected Architecture
                </div>
                <h2 class="text-4xl md:text-5xl font-extrabold text-gradient leading-tight">Satu Data, Sejuta Solusi <br>Untuk SMK Telkom</h2>
                <p class="text-lg text-slate-400 leading-relaxed font-medium">SISFO TS bukan sekadar aplikasi, melainkan platform yang menghubungkan data siswa dari Dapodik dengan monitoring real-time di sekolah. Memastikan koordinasi antara Guru, Siswa, dan Orang Tua berjalan harmonis.</p>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="flex items-start gap-4 p-4 glass rounded-2xl">
                        <div class="w-10 h-10 bg-blue-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Real-time Sync</h4>
                            <p class="text-xs text-slate-500">Data terupdate secara otomatis ke seluruh dashboard.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-4 p-4 glass rounded-2xl">
                        <div class="w-10 h-10 bg-amber-500/20 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                        </div>
                        <div>
                            <h4 class="font-bold">Keamanan Data</h4>
                            <p class="text-xs text-slate-500">Enkripsi standar industri untuk data sensitif sekolah.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative order-1 lg:order-2">
                <div class="grid grid-cols-2 gap-4">
                    <div class="space-y-4">
                        <div class="glass p-6 rounded-[32px] text-center space-y-2 translate-y-8 animate-float">
                            <p class="text-3xl font-black text-white">1.2K+</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Siswa Aktif</p>
                        </div>
                        <div class="glass p-6 rounded-[32px] text-center space-y-2 animate-float" style="animation-delay: 1.5s;">
                            <p class="text-3xl font-black text-white">80+</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Guru & Staf</p>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="glass p-6 rounded-[32px] text-center space-y-2 animate-float" style="animation-delay: 0.5s;">
                            <p class="text-3xl font-black text-white">25+</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Aplikasi Modul</p>
                        </div>
                        <div class="glass p-6 rounded-[32px] text-center space-y-2 translate-y-8 animate-float" style="animation-delay: 2s;">
                            <p class="text-3xl font-black text-white">100%</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">Cloud Ready</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="py-24 px-6 relative">
        <div class="max-w-5xl mx-auto glass p-12 md:p-20 rounded-[40px] text-center space-y-8 relative overflow-hidden">
            <div class="blob opacity-10 top-[-20%] left-[-20%]"></div>
            <h2 class="text-4xl md:text-6xl font-extrabold text-gradient leading-tight">Digitalisasikan <br>Sekolah Anda Sekarang</h2>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto font-medium">Gabung bersama ekosistem digital SMK Telkom Lampung untuk transparansi dan kemudahan manajemen pendidikan.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                 @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl flex items-center gap-2">
                        Dashboard Utama
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl">Daftar Akun Baru</a>
                    <a href="{{ route('login') }}" class="glass py-4 px-10 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all flex items-center justify-center gap-2">
                        Masuk Sistem
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
                    </a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Cookie Consent Popup -->
    <div x-data="{ 
            show: false,
            accept() {
                localStorage.setItem('cookieConsent', 'accepted');
                this.show = false;
            }
        }" 
        x-init="if(!localStorage.getItem('cookieConsent')) { setTimeout(() => show = true, 2000) }"
        x-show="show"
        x-transition:enter="transition ease-out duration-500"
        x-transition:enter-start="opacity-0 translate-y-10"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-300"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-10"
        class="fixed bottom-6 left-6 right-6 md:left-auto md:right-6 md:w-[400px] z-[100]"
        style="display: none;">
        <div class="glass p-6 rounded-[32px] border-white/10 shadow-2xl space-y-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-600/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 15.546c-.523 0-1.046.151-1.5.454a2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-3 0 2.704 2.704 0 00-3 0 2.704 2.704 0 01-1.5-.454M3 13V5a2 2 0 012-2h14a2 2 0 012 2v8M3 13l3.293-3.293a1 1 0 011.414 0l1.293 1.293a1 1 0 001.414 0l1.293-1.293a1 1 0 011.414 0l1.293 1.293a1 1 0 001.414 0L18 13M3 13l1.293 1.293a1 1 0 001.414 0l1.293-1.293a1 1 0 011.414 0l1.293 1.293a1 1 0 001.414 0L18 13"/></svg>
                </div>
                <h4 class="font-bold text-lg text-white">Privasi Anda Penting</h4>
            </div>
            <p class="text-xs text-slate-400 leading-relaxed font-medium">Kami menggunakan cookie untuk meningkatkan pengalaman Anda dan menganalisis trafik sistem. Dengan melanjutkan, Anda menyetujui kebijakan cookie kami.</p>
            <div class="flex gap-3">
                <button @click="accept()" class="flex-1 py-3 bg-white text-slate-900 font-bold rounded-2xl text-xs hover:bg-slate-200 transition-all">Terima Semua</button>
                <a href="{{ route('privacy') }}" class="px-6 py-3 glass hover:bg-white/5 text-xs text-white font-bold rounded-2xl transition-all">Detail</a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="space-y-3 text-center md:text-left">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    @if($appSetting?->logo)
                        <img src="{{ Storage::url($appSetting->logo) }}" alt="Logo" class="w-8 h-8 border-r border-slate-700 pr-3 object-contain">
                    @else
                        <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="w-8 h-8 border-r border-slate-700 pr-3 object-contain">
                    @endif
                    <div class="flex flex-col leading-tight">
                        <span class="font-outfit font-black text-lg">SISFO <span class="text-red-500">TS</span></span>
                        <span class="text-[8px] font-bold text-slate-500 uppercase tracking-widest leading-none">Powered by SMK Telkom Lampung</span>
                    </div>
                </div>
                <p class="text-xs text-slate-500 font-medium tracking-tight">© {{ date('Y') }} Sistem Informasi SMK Telkom Lampung. <br class="hidden sm:block"> Built with passion for better education.</p>
            </div>
            
            <div class="flex gap-8 text-[10px] font-bold uppercase tracking-[0.2em] text-slate-500">
                <a href="{{ route('privacy') }}" class="hover:text-red-500 transition-colors">Privacy</a>
                <a href="{{ route('terms') }}" class="hover:text-red-500 transition-colors">Terms</a>
                <a href="{{ route('security') }}" class="hover:text-red-500 transition-colors">Security</a>
            </div>

            <div class="flex gap-4">
                <div class="w-10 h-10 glass rounded-2xl flex items-center justify-center hover:bg-blue-600 transition-all cursor-pointer group">
                    <svg class="w-5 h-5 fill-slate-400 group-hover:fill-white" viewBox="0 0 24 24"><path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"/></svg>
                </div>
                <div class="w-10 h-10 glass rounded-2xl flex items-center justify-center hover:bg-red-600 transition-all cursor-pointer group">
                    <svg class="w-5 h-5 fill-slate-400 group-hover:fill-white" viewBox="0 0 24 24"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 4-8 4z"/></svg>
                </div>
                <div class="w-10 h-10 glass rounded-2xl flex items-center justify-center hover:bg-slate-700 transition-all cursor-pointer group">
                    <svg class="w-5 h-5 fill-slate-400 group-hover:fill-white" viewBox="0 0 24 24"><path d="M19.589 6.686a4.793 4.793 0 0 1-3.77-4.245V2h-3.445v13.672a2.896 2.896 0 0 1-5.201 1.743l-.002-.001.002.001a2.895 2.895 0 0 1 3.183-4.51v-3.5a6.329 6.329 0 0 0-5.394 10.692 6.33 6.33 0 0 0 10.857-4.424V8.617a8.171 8.171 0 0 0 5.429 2.046v-3.414l-.658-.563z"/></svg>
                </div>
                <div class="w-10 h-10 glass rounded-2xl flex items-center justify-center hover:bg-gradient-to-tr from-yellow-500 via-red-600 to-purple-600 transition-all cursor-pointer group">
                    <svg class="w-5 h-5 fill-slate-400 group-hover:fill-white" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </div>
            </div>
        </div>
    </footer>
    
    {{-- Video Modal Overlay --}}
    <div x-show="showVideo" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-950/90 backdrop-blur-xl p-4 sm:p-6"
         style="display: none;"
         @keydown.escape.window="showVideo = false">
        
        <div class="relative w-full max-w-5xl aspect-video glass rounded-[40px] overflow-hidden border-white/10 shadow-2xl shadow-red-600/10"
             @click.away="showVideo = false">
            
            <button @click="showVideo = false" 
                    class="absolute top-6 right-6 z-10 w-12 h-12 glass rounded-full flex items-center justify-center text-white hover:bg-red-600 transition-all group">
                <svg class="w-6 h-6 group-hover:rotate-90 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

            <template x-if="showVideo">
                <iframe class="w-full h-full" 
                        src="https://www.youtube.com/embed/IKdjAMatgtE?autoplay=1&rel=0&modestbranding=1" 
                        title="SISFO SMK Telkom Lampung Overview" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen>
                </iframe>
            </template>
        </div>
    </div>

    <script>
        // Smooth reveal on scroll
        window.addEventListener('scroll', reveal);
        function reveal() {
            var reveals = document.querySelectorAll(".glass-card");
            for (var i = 0; i < reveals.length; i++) {
                var windowHeight = window.innerHeight;
                var elementTop = reveals[i].getBoundingClientRect().top;
                var elementVisible = 150;
                if (elementTop < windowHeight - elementVisible) {
                    reveals[i].classList.add("active");
                }
            }
        }
    </script>
</body>
</html>
