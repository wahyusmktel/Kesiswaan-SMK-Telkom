<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kesiswaan — SMK Telkom</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
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
<body class="antialiased overflow-x-hidden">
    <!-- Blobs -->
    <div class="blob top-[-100px] left-[-100px]"></div>
    <div class="blob bottom-[10%] right-[-100px]" style="background: #3B82F6; opacity: 0.1;"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 px-6 py-4">
        <div class="max-w-7xl mx-auto flex items-center justify-between glass py-3 px-6 rounded-2xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-lg">
                    <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                </div>
                <span class="font-outfit font-bold text-xl tracking-tight hidden sm:block">Kesiswaan <span class="text-[#E21F26]">TS</span></span>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="#features" class="nav-link text-slate-300 hover:text-white">Fitur Layanan</a>
                <a href="{{ route('pengaduan.create') }}" class="nav-link text-slate-300 hover:text-white">Pusat Pengaduan</a>
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="text-white hover:text-slate-300 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="btn-primary px-6 py-2.5 rounded-xl font-bold text-sm shadow-xl">Daftar Akun</a>
                @endauth
            </div>

            <!-- Mobile Menu Toggle (Simplified) -->
            <div class="md:hidden">
                <a href="{{ route('login') }}" class="btn-primary px-4 py-2 rounded-lg font-bold text-xs">LOGIN</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 px-6 overflow-hidden min-h-screen flex items-center">
        <div class="max-w-7xl mx-auto grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="space-y-8 text-center lg:text-left">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-600/10 border border-red-600/20 text-red-500 text-sm font-semibold tracking-wide animate-pulse">
                    ✨ Transformasi Digital Kesiswaan
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold leading-[1.1] text-gradient">
                    Solusi Cerdas Kelola <span class="text-gradient-red italic">Kesiswaan</span> Masa Kini
                </h1>
                <p class="text-lg text-slate-400 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                    Sistem informasi terpadu SMK Telkom untuk manajemen perizinan, monitoring kehadiran, hingga layanan pengaduan orang tua dalam satu platform digital.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center lg:justify-start">
                    <a href="{{ route('login') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl flex items-center justify-center gap-2">
                        Mulai Sekarang
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                    </a>
                    <a href="{{ route('pengaduan.create') }}" class="glass py-4 px-10 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all text-center">
                        Pengaduan Orang Tua
                    </a>
                </div>
                <div class="flex items-center justify-center lg:justify-start gap-6 pt-4">
                    <div class="flex -space-x-3">
                        <img src="https://ui-avatars.com/api/?name=Siswa+1&background=random" class="w-10 h-10 rounded-full border-2 border-[#0F172A]" alt="user">
                        <img src="https://ui-avatars.com/api/?name=Siswa+2&background=random" class="w-10 h-10 rounded-full border-2 border-[#0F172A]" alt="user">
                        <img src="https://ui-avatars.com/api/?name=Siswa+3&background=random" class="w-10 h-10 rounded-full border-2 border-[#0F172A]" alt="user">
                        <div class="w-10 h-10 rounded-full bg-slate-800 border-2 border-[#0F172A] flex items-center justify-center text-[10px] font-bold">+1000</div>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">Dipercaya oleh ribuan siswa & guru</p>
                </div>
            </div>

            <div class="relative hidden lg:block">
                <div class="animate-float">
                    <div class="glass p-4 rounded-[40px] rotate-3 shadow-2xl border-white/20">
                        <div class="bg-slate-900 rounded-[30px] overflow-hidden aspect-video relative group">
                            <!-- Placeholder for Dashboard Image -->
                            <img src="https://img.freepik.com/free-vector/gradient-ui-ux-elements-background_23-2149021422.jpg" alt="Dashboard Preview" class="w-full h-full object-cover opacity-80 group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute inset-0 flex items-center justify-center bg-black/40 group-hover:bg-black/20 transition-all">
                                <div class="w-20 h-20 bg-red-600 rounded-full flex items-center justify-center shadow-red-600/50 shadow-2xl cursor-pointer">
                                    <svg class="w-8 h-8 text-white fill-current" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Additional floating elements -->
                <div class="absolute -top-10 -right-10 glass p-6 rounded-3xl animate-float" style="animation-delay: 1s;">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-green-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Perizinan</p>
                            <p class="text-lg font-bold">Terverifikasi</p>
                        </div>
                    </div>
                </div>
                <div class="absolute -bottom-5 -left-10 glass p-6 rounded-3xl animate-float" style="animation-delay: 2s;">
                    <div class="flex items-center gap-4">
                        <div class="p-3 bg-red-500/20 rounded-xl">
                            <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2"/></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-bold uppercase tracking-wider">Terlambat</p>
                            <p class="text-lg font-bold">Terpantau</p>
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
                <h2 class="text-3xl md:text-5xl font-extrabold text-gradient">Fitur Unggulan Sistem</h2>
                <p class="text-slate-400 max-w-2xl mx-auto">Dirancang untuk memudahkan koordinasi antara sekolah, siswa, dan orang tua secara transparan dan efisien.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Perizinan -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-red-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Izin Digital</h3>
                        <p class="text-slate-400 leading-relaxed">Pengajuan izin meninggalkan kelas atau sekolah kini sepenuhnya digital dengan sistem approval bertingkat.</p>
                    </div>
                </div>

                <!-- Keterlambatan -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-orange-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Monitoring Terlambat</h3>
                        <p class="text-slate-400 leading-relaxed">Pendataan siswa terlambat yang terintegrasi dengan pengiriman notifikasi otomatis kepada wali kelas.</p>
                    </div>
                </div>

                <!-- Pengaduan -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-blue-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Aduan Orang Tua</h3>
                        <p class="text-slate-400 leading-relaxed">Wadah resmi bagi orang tua/wali siswa untuk menyampaikan aspirasi atau saran secara rahasia dan responsif.</p>
                    </div>
                </div>

                <!-- Dispensasi -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-purple-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Dispensasi Lomba</h3>
                        <p class="text-slate-400 leading-relaxed">Pengelolaan dispensasi untuk siswa yang mewakili sekolah dalam berbagai ajang kompetisi.</p>
                    </div>
                </div>

                <!-- Prakerin -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-green-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Jurnal Prakerin</h3>
                        <p class="text-slate-400 leading-relaxed">Pencatatan kegiatan harian siswa saat menjalankan Praktek Kerja Industri (Prakerin).</p>
                    </div>
                </div>

                <!-- Dashboard -->
                <div class="glass-card p-8 rounded-3xl space-y-6">
                    <div class="w-14 h-14 bg-red-600/10 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 01-2 2h22a2 2 0 01-2-2v-6a2 2 0 00-2-2h-2a2 2 0 00-2 2v6"/></svg>
                    </div>
                    <div class="space-y-2">
                        <h3 class="text-xl font-bold">Analitik Real-time</h3>
                        <p class="text-slate-400 leading-relaxed">Dashboard interaktif untuk guru dan kesiswaan untuk memantau tren kedisiplinan siswa.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Call to action -->
    <section class="py-24 px-6 relative">
        <div class="max-w-5xl mx-auto glass p-12 md:p-20 rounded-[40px] text-center space-y-8 relative overflow-hidden">
            <div class="blob opacity-10 top-[-20%] left-[-20%]"></div>
            <h2 class="text-4xl md:text-6xl font-extrabold text-gradient leading-tight">Siap Untuk Mendigitalisasi <br>Kesiswaan Anda?</h2>
            <p class="text-lg text-slate-400 max-w-2xl mx-auto">Gabung bersama ribuan pengguna lainnya yang telah merasakan kemudahan sistem kesiswaan masa kini.</p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                 @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl">Masuk ke Dashboard</a>
                @else
                    <a href="{{ route('register') }}" class="btn-primary px-10 py-4 rounded-2xl font-bold text-lg shadow-2xl">Daftar Akun Sekarang</a>
                    <a href="{{ route('login') }}" class="glass py-4 px-10 rounded-2xl font-bold text-lg hover:bg-white/10 transition-all">Login Sistem</a>
                @endauth
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 border-t border-white/5 px-6">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="space-y-2 text-center md:text-left">
                <div class="flex items-center gap-3 justify-center md:justify-start">
                    <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="w-8 h-8 border-r border-slate-700 pr-3">
                    <span class="font-outfit font-bold text-lg">Kesiswaan SMK Telkom</span>
                </div>
                <p class="text-sm text-slate-500">© 2025 SMK Telkom. All rights reserved.</p>
            </div>
            
            <div class="flex gap-6 text-sm font-medium text-slate-400">
                <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                <a href="#" class="hover:text-white transition-colors">Help Center</a>
            </div>

            <div class="flex gap-4">
                <div class="w-10 h-10 glass rounded-full flex items-center justify-center hover:bg-red-600 transition-all cursor-pointer">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                </div>
                <div class="w-10 h-10 glass rounded-full flex items-center justify-center hover:bg-red-600 transition-all cursor-pointer">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Smooth reveal on scroll (optional lightweight way)
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
