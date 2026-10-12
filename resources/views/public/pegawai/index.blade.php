<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="keywords" content="guru smk telkom, portofolio guru, profil pegawai smk telkom lampung, pendidik smk telkom, direktori guru">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph / Facebook --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($setting?->logo)
        <meta property="og:image" content="{{ asset('storage/' . ltrim($setting->logo, '/')) }}">
    @endif

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">

    {{-- Fonts: Outfit & Plus Jakarta Sans for Landing Nav, Roboto for Content --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Roboto:ital,wght@0,300;0,400;0,500;0,700;0,900;1,400;1,700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if ($setting?->favicon)
        <link rel="icon" href="{{ asset('storage/' . ltrim($setting->favicon, '/')) }}">
    @endif

    <style>
        :root {
            --telkom-red: #E21F26;
            --telkom-red-dark: #B91319;
            --telkom-red-light: #FF4D54;
        }

        [x-cloak] { display: none !important; }

        body, input, button, select, textarea, .font-roboto {
            font-family: 'Roboto', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        .font-outfit {
            font-family: 'Outfit', sans-serif;
        }

        .font-jakarta {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }

        body {
            background: linear-gradient(180deg, #FFFFFF 0%, #F8FAFC 40%, #F1F5F9 100%);
            color: #1E293B;
            min-height: 100vh;
        }

        /* Header Full-Width Navigation */
        .landing-navbar-bar {
            background: transparent;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Ketika di-scroll: Full width, background putih, border bawah putih/halus, bayangan halus */
        .landing-navbar-bar.scrolled {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1.5px solid rgba(255, 255, 255, 1);
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.06), 0 2px 6px -1px rgba(226, 31, 38, 0.08);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--telkom-red) 0%, var(--telkom-red-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: scale(1.04);
            box-shadow: 0 10px 20px -5px rgba(226, 31, 38, 0.4);
        }

        .nav-link {
            position: relative;
            transition: color 0.3s ease;
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col justify-between" x-data="{ scrolled: false }" @scroll.window="scrolled = (window.pageYOffset > 50)">

    <div>
        <!-- Navigation (Full Page / Full Width Edge-to-Edge, Menyatu Transparan dengan Hero -> Background Putih & Teks Merah saat Scroll) -->
        <header 
            class="fixed top-0 left-0 right-0 w-full z-50 landing-navbar-bar px-4 sm:px-8 lg:px-12 py-3.5 transition-all duration-500 ease-out"
            :class="{ 'scrolled': scrolled }"
        >
            <div class="w-full flex items-center justify-between">
                {{-- Logo Kiri --}}
                <div class="flex items-center gap-3">
                    <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-md shrink-0 border border-slate-100">
                            @if($setting?->logo)
                                <img src="{{ Storage::url($setting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                            @else
                                <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                            @endif
                        </div>
                        <div class="flex flex-col leading-none hidden sm:flex text-left">
                            <span 
                                class="font-outfit font-black text-base tracking-tight transition-colors duration-400 leading-none"
                                :class="scrolled ? 'text-red-600' : 'text-white'"
                            >SISFO</span>
                            <span class="font-outfit font-black text-sm tracking-tight text-red-500 leading-none mt-0.5">TS</span>
                        </div>
                    </a>
                </div>

                {{-- Menu Desktop (1 Baris, Menyesuaikan Warna Saat Scroll dengan Animasi Halus) --}}
                <div class="hidden md:flex items-center gap-5 lg:gap-7 font-jakarta text-xs font-semibold whitespace-nowrap">
                    <a 
                        href="{{ route('welcome') }}#features" 
                        class="nav-link transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-white'"
                    >
                        Ekosistem Digital
                    </a>
                    <a 
                        href="{{ route('pengaduan.create') }}" 
                        class="nav-link transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-white'"
                    >
                        Layanan Aduan
                    </a>
                    <a 
                        href="{{ route('digireligi') }}" 
                        target="_blank" 
                        class="nav-link flex items-center gap-1.5 transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-emerald-400'"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                        <span>DigiReligi</span>
                    </a>
                    <a 
                        href="{{ route('gallery-photo.index') }}" 
                        class="nav-link flex items-center gap-1.5 transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-red-400'"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2 1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Galery Photo</span>
                    </a>
                    <a 
                        href="{{ route('forum-stella.index') }}" 
                        class="nav-link flex items-center gap-1.5 transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-cyan-300'"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Forum Stella</span>
                    </a>
                    <a 
                        href="{{ route('public.showcase.index') }}" 
                        class="nav-link flex items-center gap-1.5 transition-colors duration-300"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-purple-400'"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Showcase Siswa</span>
                    </a>
                    <a 
                        href="{{ route('pegawai.index') }}" 
                        class="nav-link font-bold flex items-center gap-1.5 transition-colors duration-300"
                        :class="scrolled ? 'text-red-600 hover:text-red-800' : 'text-amber-400 hover:text-amber-300'"
                    >
                        <svg class="w-4 h-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pegawai</span>
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-5 py-2.5 rounded-xl font-bold text-xs shadow-xl text-white text-center whitespace-nowrap">
                            Dashboard Utama
                        </a>
                    @else
                        <a 
                            href="{{ route('login') }}" 
                            class="transition-colors duration-300 font-medium text-xs"
                            :class="scrolled ? 'text-red-700 hover:text-red-900 font-bold' : 'text-white hover:text-slate-300'"
                        >
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary px-4 py-2.5 rounded-xl font-bold text-xs shadow-xl text-white text-center whitespace-nowrap">
                            Daftar Akun
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu (Persis Sama dengan Landing Page) --}}
                <div class="md:hidden flex items-center gap-2">
                    <a 
                        href="{{ route('pegawai.index') }}" 
                        class="text-xs font-bold flex items-center gap-1 transition-colors duration-300 mr-2"
                        :class="scrolled ? 'text-red-600' : 'text-amber-400'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Pegawai
                    </a>
                    <a 
                        href="{{ route('public.showcase.index') }}" 
                        class="text-xs font-bold flex items-center gap-1 transition-colors duration-300 mr-2"
                        :class="scrolled ? 'text-red-700 hover:text-red-900' : 'text-slate-300 hover:text-purple-400'"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        Showcase
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-4 py-2 rounded-lg font-bold text-xs uppercase tracking-tighter text-white">DASHBOARD</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary px-4 py-2 rounded-lg font-bold text-xs text-white">LOGIN</a>
                    @endauth
                </div>
            </div>
        </header>

        {{-- Hero Header Section: Menggunakan Background Foto yang Diunggah --}}
        <section class="relative pt-36 pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden bg-cover bg-center" style="background-image: url('{{ asset('images/pegawai-hero.jpg') }}');">
            {{-- Dark tint at top to protect navbar, transitioning seamlessly to clean soft gray at the bottom --}}
            <div class="absolute inset-0 bg-gradient-to-b from-slate-950/85 via-slate-900/75 to-slate-50"></div>

            <div class="max-w-7xl mx-auto relative z-10 text-center">
                {{-- Eyebrow Badge --}}
                <div class="inline-flex items-center gap-2 rounded-full bg-white/15 border border-white/25 px-4 py-1.5 text-xs font-bold uppercase tracking-widest text-white mb-6 backdrop-blur-md shadow-lg">
                    <span class="h-2 w-2 rounded-full bg-red-500 animate-ping"></span>
                    <span>Pendidik & Tenaga Kependidikan Berkompeten</span>
                </div>

                {{-- Main Heading in Roboto --}}
                <h1 class="font-roboto text-3xl sm:text-5xl lg:text-6xl font-black tracking-tight leading-tight max-w-4xl mx-auto text-white drop-shadow-md">
                    Kenali Profil & Portofolio <span class="bg-gradient-to-r from-red-400 via-rose-300 to-amber-300 bg-clip-text text-transparent">Guru Hebat</span> Kami
                </h1>

                <p class="mt-4 text-base sm:text-lg text-slate-200 max-w-2xl mx-auto leading-relaxed font-normal drop-shadow-sm">
                    Daftar seluruh pendidik dan tenaga kependidikan {{ $schoolName }} lengkap dengan bidang keahlian, sertifikasi industri, serta portofolio karya nyata.
                </p>

                {{-- Quick Stats Counter --}}
                <div class="mt-8 flex flex-wrap items-center justify-center gap-4 sm:gap-8 text-white text-sm font-semibold">
                    <div class="flex items-center gap-2.5 bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-sm">
                        <span class="font-roboto text-2xl font-black text-white">{{ $totalTeachers }}</span>
                        <span class="text-slate-200 text-xs sm:text-sm">Tenaga Pendidik</span>
                    </div>
                    <div class="flex items-center gap-2.5 bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-sm">
                        <span class="font-roboto text-2xl font-black text-amber-300">{{ $totalPortfolios }}</span>
                        <span class="text-slate-200 text-xs sm:text-sm">Karya & Portofolio</span>
                    </div>
                    <div class="flex items-center gap-2.5 bg-white/10 backdrop-blur-md px-4 py-2 rounded-xl border border-white/20 shadow-sm">
                        <span class="font-roboto text-2xl font-black text-emerald-400">100%</span>
                        <span class="text-slate-200 text-xs sm:text-sm">Standar Industri</span>
                    </div>
                </div>

                {{-- Interactive Search Form (Clean White Card) --}}
                <div class="mt-10 max-w-2xl mx-auto">
                    <form method="GET" action="{{ route('pegawai.index') }}" class="relative flex items-center shadow-2xl rounded-2xl overflow-hidden border border-slate-200 bg-white">
                        @if($kategori)
                            <input type="hidden" name="kategori" value="{{ $kategori }}">
                        @endif
                        <div class="pl-4 sm:pl-5 text-slate-400">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $search }}" 
                            placeholder="Cari nama guru, mata pelajaran, atau keahlian (contoh: Laravel, Budi, IoT)..." 
                            class="w-full bg-transparent border-0 py-4 px-3 sm:px-4 text-sm sm:text-base text-slate-800 placeholder-slate-400 focus:ring-0 focus:outline-none font-roboto"
                        >
                        @if($search)
                            <a href="{{ route('pegawai.index', array_filter(['kategori' => $kategori])) }}" class="pr-3 text-xs text-slate-400 hover:text-slate-700 transition font-medium">Hapus</a>
                        @endif
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-bold text-xs sm:text-sm px-6 sm:px-8 py-4 transition-colors shrink-0">
                            Cari
                        </button>
                    </form>
                </div>
            </div>
        </section>

        {{-- Filter Categories & Main Directory Section (Warna Putih Gradiasi Soft Gray) --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 sm:py-14">
            
            {{-- Category Filter Tabs --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-8 border-b border-slate-200">
                <div class="flex flex-wrap items-center gap-2">
                    <a 
                        href="{{ route('pegawai.index', array_filter(['q' => $search])) }}" 
                        class="rounded-xl px-4 py-2.5 text-xs font-bold transition-all {{ empty($kategori) ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-sm' }}"
                    >
                        Semua Kategori ({{ $totalTeachers }})
                    </a>
                    @foreach(\App\Models\TeacherProfile::KATEGORI_LABELS as $key => $label)
                        <a 
                            href="{{ route('pegawai.index', array_filter(['q' => $search, 'kategori' => $key])) }}" 
                            class="rounded-xl px-4 py-2.5 text-xs font-bold transition-all {{ $kategori === $key ? 'bg-red-600 text-white shadow-md shadow-red-600/20' : 'bg-white text-slate-700 hover:bg-slate-50 border border-slate-200 shadow-sm' }}"
                        >
                            {{ $label }}
                        </a>
                    @endforeach
                </div>

                {{-- Skill Tag Filter --}}
                @if($allSkills->isNotEmpty())
                    <div class="flex items-center gap-1.5 overflow-x-auto text-xs py-1">
                        <span class="text-slate-400 font-bold shrink-0">Keahlian:</span>
                        @foreach($allSkills as $s)
                            <a 
                                href="{{ route('pegawai.index', array_filter(['q' => $search, 'kategori' => $kategori, 'skill' => $s === $skill ? null : $s])) }}" 
                                class="rounded-lg px-2.5 py-1 text-[11px] font-medium transition {{ $skill === $s ? 'bg-indigo-600 text-white font-bold shadow-sm' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200 shadow-xs' }}"
                            >
                                #{{ $s }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Filter Status Notice if active --}}
            @if($search || $kategori || $skill)
                <div class="mt-6 flex items-center justify-between bg-amber-50 border border-amber-200 rounded-xl px-4 py-3 text-xs text-amber-900 font-medium">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>
                            Menampilkan hasil untuk: 
                            @if($search) <strong>"{{ $search }}"</strong> @endif
                            @if($kategori) kategori <strong>"{{ \App\Models\TeacherProfile::KATEGORI_LABELS[$kategori] ?? $kategori }}"</strong> @endif
                            @if($skill) keahlian <strong>"#{{ $skill }}"</strong> @endif
                            ({{ $teachers->total() }} ditemukan)
                        </span>
                    </div>
                    <a href="{{ route('pegawai.index') }}" class="font-bold text-red-600 hover:underline">Reset Filter</a>
                </div>
            @endif

            {{-- Teacher Cards Grid --}}
            @if($teachers->isNotEmpty())
                <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($teachers as $teacher)
                        <article class="group bg-white rounded-3xl border border-slate-200/90 shadow-sm hover:shadow-xl hover:border-red-300 hover:-translate-y-1.5 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                            {{-- Top Header / Avatar Area --}}
                            <div>
                                <div class="relative h-28 bg-gradient-to-r from-slate-100 via-slate-50 to-gray-200/70 border-b border-slate-100 p-4">
                                    {{-- Category Badge --}}
                                    <div class="flex items-center justify-between">
                                        <span class="rounded-full bg-white/90 shadow-xs border border-slate-200 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700">
                                            {{ $teacher->kategori_label }}
                                        </span>
                                        @if($teacher->is_featured)
                                            <span class="flex items-center gap-1 rounded-full bg-amber-400 text-slate-950 px-2 py-0.5 text-[10px] font-bold shadow-xs">
                                                <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                                Unggulan
                                            </span>
                                        @endif
                                    </div>

                                    {{-- Teacher Photo --}}
                                    <div class="absolute -bottom-8 left-6">
                                        <div class="w-20 h-20 rounded-2xl bg-white p-1 shadow-md border-2 border-white overflow-hidden group-hover:scale-105 transition-transform duration-300">
                                            @if($teacher->photo_url)
                                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->display_name }}" class="w-full h-full object-cover rounded-xl">
                                            @else
                                                <div class="w-full h-full bg-gradient-to-br from-red-600 to-rose-700 rounded-xl flex items-center justify-center text-white font-bold text-xl">
                                                    {{ strtoupper(substr($teacher->nama_lengkap, 0, 2)) }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Body Info --}}
                                <div class="pt-11 px-6 pb-4">
                                    {{-- Name in Roboto Bold --}}
                                    <h2 class="font-roboto font-bold text-base sm:text-lg text-slate-900 group-hover:text-red-600 transition-colors leading-snug line-clamp-2">
                                        {{ $teacher->display_name }}
                                    </h2>

                                    {{-- Position / Subject --}}
                                    <p class="mt-1 text-xs font-semibold text-red-600">
                                        {{ $teacher->jabatan ?: ($teacher->bidang_studi ? 'Guru ' . $teacher->bidang_studi : 'Tenaga Pendidik') }}
                                    </p>

                                    @if($teacher->bidang_studi && $teacher->jabatan)
                                        <p class="mt-0.5 text-[11px] text-slate-400 font-medium">
                                            Bidang: {{ $teacher->bidang_studi }}
                                        </p>
                                    @endif

                                    {{-- Bio snippet --}}
                                    @if($teacher->bio)
                                        <p class="mt-3 text-xs text-slate-600 line-clamp-2 leading-relaxed">
                                            {{ $teacher->bio }}
                                        </p>
                                    @endif

                                    {{-- Skills Badges --}}
                                    @if(!empty($teacher->keahlian))
                                        <div class="mt-4 flex flex-wrap gap-1.5">
                                            @foreach(array_slice($teacher->keahlian, 0, 3) as $sk)
                                                <span class="rounded-lg bg-slate-50 border border-slate-200/80 px-2.5 py-0.5 text-[10px] font-medium text-slate-700">
                                                    {{ $sk }}
                                                </span>
                                            @endforeach
                                            @if(count($teacher->keahlian) > 3)
                                                <span class="rounded-lg bg-slate-50 border border-slate-200/80 px-1.5 py-0.5 text-[10px] font-medium text-slate-400">
                                                    +{{ count($teacher->keahlian) - 3 }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Footer / Portfolio Counter & Action Button --}}
                            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/70 flex items-center justify-between gap-3">
                                <div class="flex items-center gap-1.5 text-xs text-slate-600 font-medium">
                                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    <span>{{ $teacher->portfolios->count() }} Karya</span>
                                </div>

                                <a 
                                    href="{{ route('pegawai.show', $teacher->slug) }}" 
                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600 hover:text-red-700 group-hover:translate-x-1 transition-all"
                                >
                                    <span>Lihat Portofolio</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- Pagination --}}
                <div class="mt-12">
                    {{ $teachers->links() }}
                </div>
            @else
                {{-- Empty State --}}
                <div class="mt-12 bg-white rounded-3xl border border-slate-200 p-12 text-center max-w-lg mx-auto shadow-sm">
                    <div class="w-16 h-16 bg-red-50 text-red-600 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="font-roboto font-bold text-xl text-slate-900">Pegawai Tidak Ditemukan</h3>
                    <p class="mt-2 text-xs text-slate-500 leading-relaxed font-normal">
                        Tidak ada data profil guru atau pegawai yang cocok dengan kata kunci pencarian Anda. Silakan coba kata kunci lain.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('pegawai.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-xs font-bold text-white hover:bg-red-700 shadow-sm transition">
                            Lihat Seluruh Pegawai
                        </a>
                    </div>
                </div>
            @endif
        </main>
    </div>

    {{-- Footer (Clean White / Soft Gray with Slate Border) --}}
    <footer class="bg-white text-slate-600 text-xs py-8 border-t border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-outfit font-black text-sm text-slate-900">SISFO <span class="text-red-600">TS</span></span>
                <span>&copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak cipta dilindungi.</span>
            </div>
            <div class="flex items-center gap-4 font-semibold text-slate-600 font-jakarta text-xs">
                <a href="{{ route('welcome') }}" class="hover:text-red-600 transition">Beranda</a>
                <a href="{{ route('pegawai.index') }}" class="text-red-600 font-bold transition">Pegawai</a>
                <a href="{{ route('public.showcase.index') }}" class="hover:text-red-600 transition">Showcase</a>
                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="hover:text-red-600 transition">Sitemap XML</a>
            </div>
        </div>
    </footer>

</body>
</html>
