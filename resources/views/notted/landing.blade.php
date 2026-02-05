<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NOTTED - Connect. Share. Be Noted.</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: #0f172a;
            color: #f8fafc;
            overflow-x: hidden;
        }

        .glass {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-text {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 50%, #ec4899 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-mesh {
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            height: 100%;
            background:
                radial-gradient(circle at 20% 30%, rgba(99, 102, 241, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 80% 70%, rgba(236, 72, 153, 0.15) 0%, transparent 40%);
            z-index: -1;
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0px);
            }

            50% {
                transform: translateY(-20px);
            }
        }

        .nav-blur {
            backdrop-filter: blur(8px);
            background: rgba(15, 23, 42, 0.8);
        }

        .cta-btn {
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .cta-btn::after {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: all 0.5s ease;
        }

        .cta-btn:hover::after {
            left: 100%;
        }

        .feature-card {
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .feature-card:hover {
            transform: translateY(-10px) scale(1.02);
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(99, 102, 241, 0.4);
        }
    </style>
</head>

<body class="antialiased">
    <div class="hero-mesh"></div>

    <!-- Navigation -->
    <nav class="fixed top-0 w-full z-50 nav-blur border-b border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex items-center gap-2">
                    <div
                        class="w-10 h-10 bg-gradient-to-tr from-indigo-600 to-pink-600 rounded-xl flex items-center justify-center font-bold text-xl shadow-lg shadow-indigo-500/20">
                        N</div>
                    <span class="text-2xl font-extrabold tracking-tighter">NOTTED</span>
                </div>
                <div class="hidden md:flex items-center gap-8 font-medium text-slate-400">
                    <a href="#features" class="hover:text-white transition-colors">Fitur</a>
                    <a href="#about" class="hover:text-white transition-colors">Tentang</a>
                    @auth
                        <a href="{{ route('notted.app') }}"
                            class="cta-btn px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-full font-bold shadow-lg shadow-indigo-500/25 transition-all">Masuk
                            Aplikasi</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="px-6 py-2.5 border border-white/10 hover:bg-white/5 rounded-full transition-all">Login
                            Sisfo</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="pt-40 pb-20 px-4 relative">
        <div class="max-w-5xl mx-auto text-center">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full glass mb-8 animate-bounce">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span class="text-sm font-semibold text-indigo-300">New: Social Hub for SMK Telkom Lampung</span>
            </div>
            <h1 class="text-6xl md:text-8xl font-black mb-6 tracking-tighter leading-tight">
                Connect. <br>
                Share. <span class="gradient-text">Be Noted.</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-400 mb-12 max-w-2xl mx-auto leading-relaxed">
                Network of Telkom Digital Minds. Jejak digital positif yang tercatat selama masa sekolah.
            </p>

            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                @auth
                    <a href="{{ route('notted.app') }}"
                        class="cta-btn w-full sm:w-auto px-10 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl font-extrabold text-lg shadow-2xl shadow-indigo-500/40 hover:scale-105 transition-transform">
                        Masuk ke NOTTED App
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="cta-btn w-full sm:w-auto px-10 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-2xl font-extrabold text-lg shadow-2xl shadow-indigo-500/40 hover:scale-105 transition-transform">
                        Gabung Sekarang
                    </a>
                    <p class="text-slate-500 text-sm mt-4 sm:mt-0 sm:ml-4">*Gunakan akun Sisfo SMK Telkom Lampung</p>
                @endauth
            </div>
        </div>

        <!-- Floating Elements Mockup -->
        <div class="max-w-6xl mx-auto mt-24 relative">
            <div class="glass rounded-3xl p-4 overflow-hidden shadow-2xl border-white/10 relative z-10">
                <div class="aspect-video bg-[#1e293b] rounded-2xl flex items-center justify-center relative group">
                    <div
                        class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent flex flex-col justify-end p-8 opacity-0 group-hover:opacity-100 transition-opacity">
                        <h3 class="text-2xl font-bold mb-2">Social Feed</h3>
                        <p class="text-slate-300">Koneksi tanpa batas antar siswa digital.</p>
                    </div>
                    <!-- Mockup UI Element -->
                    <div class="w-4/5 h-4/5 flex gap-4">
                        <div class="w-1/3 glass rounded-2xl flex flex-col p-4 gap-4 animate-pulse">
                            <div class="h-8 w-8 rounded-lg bg-indigo-500/20"></div>
                            <div class="h-4 w-full bg-white/5 rounded"></div>
                            <div class="h-4 w-2/3 bg-white/5 rounded"></div>
                        </div>
                        <div class="w-2/3 flex flex-col gap-4">
                            <div class="h-2/3 glass rounded-2xl p-6 relative">
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-10 h-10 rounded-full bg-gradient-to-tr from-pink-500 to-orange-500">
                                    </div>
                                    <div class="h-4 w-32 bg-white/10 rounded"></div>
                                </div>
                                <div class="h-4 w-full bg-white/5 rounded mb-2"></div>
                                <div class="h-max aspect-video bg-white/5 rounded-xl"></div>
                            </div>
                            <div class="h-1/3 flex gap-4">
                                <div class="w-1/2 glass rounded-2xl"></div>
                                <div class="w-1/2 glass rounded-2xl"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Decorative Blobs -->
            <div class="absolute -top-10 -left-10 w-40 h-40 bg-indigo-600/30 rounded-full blur-3xl floating"></div>
            <div class="absolute -bottom-10 -right-10 w-60 h-60 bg-pink-600/20 rounded-full blur-3xl floating"
                style="animation-delay: -3s;"></div>
        </div>
    </section>

    <!-- Features Grid -->
    <section id="features" class="py-32 px-4 bg-slate-900/50">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-20">
                <h2 class="text-4xl md:text-5xl font-black mb-4">Mulai <span class="gradient-text">Petualangan</span>
                    Digitalmu</h2>
                <p class="text-slate-400 max-w-xl mx-auto">Kami menyiapkan segalanya untuk mendukung kreativitas dan
                    kolaborasi positif di masa sekolah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Posts -->
                <div class="feature-card glass p-8 rounded-3xl">
                    <div
                        class="w-14 h-14 bg-indigo-500/10 rounded-2xl flex items-center justify-center mb-6 text-indigo-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10l4 4v10a2 2 0 01-2 2zM14 4v4h4" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Posts</h3>
                    <p class="text-slate-400">Bagikan ide, karya, dan pemikiran positifmu kepada seluruh warga sekolah.
                    </p>
                </div>

                <!-- Reels -->
                <div class="feature-card glass p-8 rounded-3xl border-indigo-500/20 bg-indigo-500/5">
                    <div
                        class="w-14 h-14 bg-pink-500/10 rounded-2xl flex items-center justify-center mb-6 text-pink-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Reels</h3>
                    <p class="text-slate-400">Tampilkan bakat dan momen seru dalam format video pendek yang dinamis.</p>
                </div>

                <!-- Story -->
                <div class="feature-card glass p-8 rounded-3xl">
                    <div
                        class="w-14 h-14 bg-purple-500/10 rounded-2xl flex items-center justify-center mb-6 text-purple-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Story</h3>
                    <p class="text-slate-400">Ceritakan aktivitas harianmu yang seru dan inspiratif selama di sekolah.
                    </p>
                </div>

                <!-- Kelas -->
                <div class="feature-card glass p-8 rounded-3xl">
                    <div
                        class="w-14 h-14 bg-emerald-500/10 rounded-2xl flex items-center justify-center mb-6 text-emerald-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Kelas</h3>
                    <p class="text-slate-400">Ruang kolaborasi khusus antar teman sekelas yang terintegrasi.</p>
                </div>

                <!-- Pengumuman -->
                <div class="feature-card glass p-8 rounded-3xl">
                    <div
                        class="w-14 h-14 bg-amber-500/10 rounded-2xl flex items-center justify-center mb-6 text-amber-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Pengumuman</h3>
                    <p class="text-slate-400">Informasi ter-update dari sekolah yang dikemas secara modern.</p>
                </div>

                <!-- Keamanan -->
                <div class="feature-card glass p-8 rounded-3xl">
                    <div
                        class="w-14 h-14 bg-blue-500/10 rounded-2xl flex items-center justify-center mb-6 text-blue-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold mb-3">Safe Space</h3>
                    <p class="text-slate-400">Moderasi otomatis untuk memastikan lingkungan sekolah digital tetap
                        positif.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-20 px-4 border-t border-white/5">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="flex items-center gap-2">
                <div
                    class="w-8 h-8 bg-gradient-to-tr from-indigo-600 to-pink-600 rounded-lg flex items-center justify-center font-bold text-sm">
                    N</div>
                <span class="text-xl font-extrabold tracking-tighter">NOTTED</span>
            </div>
            <p class="text-slate-500 text-sm">© 2026 SMK Telkom Lampung. Part of Sisfo Ecosystem.</p>
            <div class="flex gap-6">
                <a href="#" class="text-slate-400 hover:text-white transition-colors">Privacy</a>
                <a href="#" class="text-slate-400 hover:text-white transition-colors">Terms</a>
                <a href="#" class="text-slate-400 hover:text-white transition-colors">Support</a>
            </div>
        </div>
    </footer>
</body>

</html>