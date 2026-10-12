<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Essential SEO Meta Tags --}}
    <title>{{ $teacher->getSeoMetaTitle($schoolName) }}</title>
    <meta name="description" content="{{ $teacher->getSeoMetaDescription($schoolName) }}">
    <meta name="keywords" content="{{ $teacher->getSeoKeywords($schoolName) }}">
    <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph / Facebook / WhatsApp / LinkedIn Preview --}}
    <meta property="og:type" content="profile">
    <meta property="og:site_name" content="{{ $schoolName }}">
    <meta property="og:title" content="{{ $teacher->getSeoMetaTitle($schoolName) }}">
    <meta property="og:description" content="{{ $teacher->getSeoMetaDescription($schoolName) }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    @if($teacher->photo_url)
        <meta property="og:image" content="{{ url($teacher->photo_url) }}">
        <meta property="og:image:alt" content="Foto {{ $teacher->display_name }}">
    @elseif($setting?->logo)
        <meta property="og:image" content="{{ asset('storage/' . ltrim($setting->logo, '/')) }}">
    @endif
    <meta property="profile:username" content="{{ $teacher->slug }}">

    {{-- Twitter Cards --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $teacher->getSeoMetaTitle($schoolName) }}">
    <meta name="twitter:description" content="{{ $teacher->getSeoMetaDescription($schoolName) }}">
    @if($teacher->photo_url)
        <meta name="twitter:image" content="{{ url($teacher->photo_url) }}">
    @endif

    {{-- Google Structured Data JSON-LD Schema.org (ProfilePage & Person) --}}
    <script type="application/ld+json">
    {!! json_encode($schemaJsonLd, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
    </script>

    {{-- BreadcrumbList Schema --}}
    <script type="application/ld+json">
    {
        "@@context": "https://schema.org",
        "@type": "BreadcrumbList",
        "itemListElement": [
            {
                "@type": "ListItem",
                "position": 1,
                "name": "Beranda",
                "item": "{{ url('/') }}"
            },
            {
                "@type": "ListItem",
                "position": 2,
                "name": "Direktori Pegawai",
                "item": "{{ route('pegawai.index') }}"
            },
            {
                "@type": "ListItem",
                "position": 3,
                "name": "{{ $teacher->display_name }}",
                "item": "{{ $canonicalUrl }}"
            }
        ]
    }
    </script>

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

        /* Header Full-Width Navigation on Detail Page (Clean White Glass) */
        .landing-navbar-bar {
            background: rgba(255, 255, 255, 0.96);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border-bottom: 1px solid #E2E8F0;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.04);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .landing-navbar-bar.scrolled {
            box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.08), 0 2px 6px -1px rgba(226, 31, 38, 0.06);
            border-bottom: 1px solid #CBD5E1;
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

        .profile-banner {
            background-color: #0b0f19;
            background-image: radial-gradient(circle at 10% 20%, rgba(220, 38, 38, 0.28) 0%, transparent 45%),
                              radial-gradient(circle at 90% 10%, rgba(99, 102, 241, 0.24) 0%, transparent 45%),
                              linear-gradient(to bottom, #0f172a, #0b0f19);
        }
    </style>
</head>
<body class="min-h-screen antialiased flex flex-col justify-between" x-data="{ copied: false, scrolled: false, copyLink() { navigator.clipboard.writeText(window.location.href); this.copied = true; setTimeout(() => this.copied = false, 2500); } }" @scroll.window="scrolled = (window.pageYOffset > 50)">

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
                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center overflow-hidden p-1 shadow-md shrink-0 border border-slate-200">
                            @if($setting?->logo)
                                <img src="{{ Storage::url($setting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                            @else
                                <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="object-contain w-full h-full">
                            @endif
                        </div>
                        <div class="flex flex-col leading-none hidden sm:flex text-left">
                            <span class="font-outfit font-black text-base tracking-tight text-slate-900 leading-none">SISFO</span>
                            <span class="font-outfit font-black text-sm tracking-tight text-red-600 leading-none mt-0.5">TS</span>
                        </div>
                    </a>
                </div>

                {{-- Menu Desktop (1 Baris, Jelas & Terlihat di Latar Putih) --}}
                <div class="hidden md:flex items-center gap-5 lg:gap-7 font-jakarta text-xs font-semibold whitespace-nowrap">
                    <a 
                        href="{{ route('welcome') }}#features" 
                        class="nav-link text-slate-700 hover:text-red-600 transition-colors duration-200"
                    >
                        Ekosistem Digital
                    </a>
                    <a 
                        href="{{ route('pengaduan.create') }}" 
                        class="nav-link text-slate-700 hover:text-red-600 transition-colors duration-200"
                    >
                        Layanan Aduan
                    </a>
                    <a 
                        href="{{ route('digireligi') }}" 
                        target="_blank" 
                        class="nav-link text-slate-700 hover:text-emerald-600 flex items-center gap-1.5 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z"/></svg>
                        <span>DigiReligi</span>
                    </a>
                    <a 
                        href="{{ route('gallery-photo.index') }}" 
                        class="nav-link text-slate-700 hover:text-red-600 flex items-center gap-1.5 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2 1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <span>Galery Photo</span>
                    </a>
                    <a 
                        href="{{ route('forum-stella.index') }}" 
                        class="nav-link text-slate-700 hover:text-cyan-600 flex items-center gap-1.5 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"/></svg>
                        <span>Forum Stella</span>
                    </a>
                    <a 
                        href="{{ route('public.showcase.index') }}" 
                        class="nav-link text-slate-700 hover:text-purple-600 flex items-center gap-1.5 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"/></svg>
                        <span>Showcase Siswa</span>
                    </a>
                    <a 
                        href="{{ route('pegawai.index') }}" 
                        class="nav-link font-bold text-red-600 hover:text-red-700 flex items-center gap-1.5 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4 shrink-0 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        <span>Pegawai</span>
                    </a>
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary px-5 py-2.5 rounded-xl font-bold text-xs shadow-xl text-white text-center whitespace-nowrap">
                            Dashboard Utama
                        </a>
                    @else
                        <a 
                            href="{{ route('login') }}" 
                            class="text-slate-800 hover:text-red-600 transition-colors duration-200 font-bold text-xs"
                        >
                            Masuk
                        </a>
                        <a href="{{ route('register') }}" class="btn-primary px-4 py-2.5 rounded-xl font-bold text-xs shadow-xl text-white text-center whitespace-nowrap">
                            Daftar Akun
                        </a>
                    @endauth
                </div>

                {{-- Mobile Menu --}}
                <div class="md:hidden flex items-center gap-2">
                    <a 
                        href="{{ route('pegawai.index') }}" 
                        class="text-xs font-bold text-red-600 flex items-center gap-1 transition-colors duration-200 mr-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        Pegawai
                    </a>
                    <a 
                        href="{{ route('public.showcase.index') }}" 
                        class="text-xs font-bold text-slate-700 hover:text-purple-600 flex items-center gap-1 transition-colors duration-200 mr-2"
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

        {{-- Top Spacer for Fixed Header --}}
        <div class="h-16 sm:h-20"></div>

        {{-- Breadcrumb Nav (Soft Gray Background, Teks Berada di Tengah Area) --}}
        <nav aria-label="Breadcrumb" class="bg-slate-100/90 border-y border-slate-200/80 text-xs py-3.5 transition-colors">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between text-slate-500 min-h-[1.75rem]">
                <div class="flex items-center gap-2 flex-wrap">
                    <a href="{{ route('welcome') }}" class="hover:text-red-600 transition font-medium">Beranda</a>
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    <a href="{{ route('pegawai.index') }}" class="hover:text-red-600 transition font-medium">Pegawai</a>
                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/></svg>
                    <span class="text-slate-900 font-bold truncate">{{ $teacher->display_name }}</span>
                </div>
                <a href="{{ route('pegawai.index') }}" class="hidden sm:inline-flex items-center gap-1.5 text-red-600 hover:text-red-700 font-bold text-xs transition shrink-0 ml-4">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    <span>Kembali ke Direktori</span>
                </a>
            </div>
        </nav>

        {{-- Profile Hero Banner --}}
        <section class="profile-banner text-white pt-10 pb-20 relative overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-8">
                    {{-- Left: Photo + Name + Details --}}
                    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6">
                        {{-- Photo --}}
                        <div class="relative w-32 h-32 sm:w-40 sm:h-40 rounded-3xl bg-white p-1.5 shadow-2xl border-4 border-slate-700/60 overflow-hidden shrink-0">
                            @if($teacher->photo_url)
                                <img src="{{ $teacher->photo_url }}" alt="{{ $teacher->display_name }}" class="w-full h-full object-cover rounded-2xl">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-red-600 to-rose-700 rounded-2xl flex items-center justify-center text-white font-black text-4xl font-roboto">
                                    {{ strtoupper(substr($teacher->nama_lengkap, 0, 2)) }}
                                </div>
                            @endif
                            <div class="absolute bottom-3 right-3 bg-emerald-500 text-white p-1 rounded-full shadow-lg" title="Pendidik Terverifikasi">
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            </div>
                        </div>

                        {{-- Main Titles in Roboto --}}
                        <div>
                            <div class="flex flex-wrap items-center gap-2 mb-2">
                                <span class="rounded-full bg-red-500/20 text-red-400 border border-red-500/30 px-3 py-0.5 text-xs font-bold uppercase tracking-wider">
                                    {{ $teacher->kategori_label }}
                                </span>
                                @if($teacher->is_featured)
                                    <span class="rounded-full bg-amber-400/20 text-amber-300 border border-amber-400/30 px-2.5 py-0.5 text-xs font-bold flex items-center gap-1">
                                        <svg class="w-3 h-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                        Pendidik Unggulan
                                    </span>
                                @endif
                            </div>

                            <h1 class="font-roboto text-2xl sm:text-4xl lg:text-5xl font-black text-white tracking-tight leading-tight">
                                {{ $teacher->display_name }}
                            </h1>

                            <p class="mt-2 text-sm sm:text-base font-bold text-red-400">
                                {{ $teacher->jabatan ?: ($teacher->bidang_studi ? 'Guru ' . $teacher->bidang_studi : 'Tenaga Pendidik') }}
                            </p>

                            <p class="mt-1 text-xs text-slate-300 flex items-center gap-2">
                                <svg class="w-4 h-4 text-slate-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                <span>{{ $schoolName }}</span>
                                @if($teacher->nuptk)
                                    <span>·</span>
                                    <span>NUPTK: {{ $teacher->nuptk }}</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    {{-- Right: Quick Action & Share Buttons --}}
                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3 w-full lg:w-auto shrink-0">
                        {{-- Social Profiles --}}
                        <div class="flex items-center gap-2">
                            @if(!empty($teacher->sosial_media['linkedin']))
                                <a href="{{ $teacher->sosial_media['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl bg-slate-800 hover:bg-blue-600 text-white transition-colors shadow-sm" title="Profil LinkedIn">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                                </a>
                            @endif
                            @if(!empty($teacher->sosial_media['github']))
                                <a href="{{ $teacher->sosial_media['github'] }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white transition-colors shadow-sm" title="Profil GitHub">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 0c-6.626 0-12 5.373-12 12 0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23.957-.266 1.983-.399 3.003-.404 1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576 4.765-1.589 8.199-6.086 8.199-11.386 0-6.627-5.373-12-12-12z"/></svg>
                                </a>
                            @endif
                            @if(!empty($teacher->sosial_media['instagram']))
                                <a href="{{ $teacher->sosial_media['instagram'] }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl bg-slate-800 hover:bg-pink-600 text-white transition-colors shadow-sm" title="Instagram">
                                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </a>
                            @endif
                            @if(!empty($teacher->sosial_media['website']))
                                <a href="{{ $teacher->sosial_media['website'] }}" target="_blank" rel="noopener noreferrer" class="p-2.5 rounded-xl bg-slate-800 hover:bg-emerald-600 text-white transition-colors shadow-sm" title="Website Pribadi">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                                </a>
                            @endif
                        </div>

                        {{-- Quick Share Actions --}}
                        <div class="flex items-center gap-2">
                            <a 
                                href="https://api.whatsapp.com/send?text={{ urlencode('Lihat profil dan portofolio guru ' . $teacher->display_name . ' di ' . $schoolName . ': ' . $canonicalUrl) }}" 
                                target="_blank" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white px-3.5 py-2 text-xs font-bold shadow-md transition"
                                title="Bagikan ke WhatsApp"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981zm11.387-5.464c-.074-.124-.272-.198-.57-.347-.297-.149-1.758-.868-2.031-.967-.272-.099-.47-.149-.669.149-.198.297-.768.967-.941 1.165-.173.198-.347.223-.644.074-.297-.149-1.255-.462-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.297-.347.446-.521.151-.172.2-.296.3-.495.099-.198.05-.372-.025-.521-.075-.148-.669-1.611-.916-2.206-.242-.579-.487-.501-.669-.51l-.57-.01c-.198 0-.52.074-.792.372s-1.04 1.016-1.04 2.479 1.065 2.876 1.213 3.074c.149.198 2.095 3.2 5.076 4.487.709.306 1.263.489 1.694.626.712.226 1.36.194 1.872.118.571-.085 1.758-.719 2.006-1.413.248-.695.248-1.29.173-1.414z"/></svg>
                                <span>Bagikan</span>
                            </a>
                            <button 
                                type="button" 
                                @click="copyLink()" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 px-3.5 py-2 text-xs font-bold transition"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3"/></svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin Link'"></span>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Teaching Philosophy Quote Banner --}}
                @if($teacher->filosofi_mengajar)
                    <div class="mt-8 rounded-2xl bg-white/5 border border-white/10 p-5 backdrop-blur-md max-w-4xl">
                        <div class="flex items-start gap-3">
                            <span class="text-3xl font-serif text-amber-400 leading-none">“</span>
                            <blockquote class="text-sm sm:text-base italic text-slate-200 font-medium leading-relaxed">
                                {{ $teacher->filosofi_mengajar }}
                            </blockquote>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Main Content Grid (Warna Putih & Soft Gray) --}}
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-10">
                
                {{-- Left Column: Bio, Skills, Education, Certifications (8 cols) --}}
                <div class="lg:col-span-8 space-y-10">
                    
                    {{-- Section 1: Biografi & Profil Singkat --}}
                    @if($teacher->bio)
                        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
                            <h2 class="font-roboto text-xl font-bold text-slate-900 flex items-center gap-2.5 pb-4 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-red-50 text-red-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </span>
                                <span>Tentang & Rekam Jejak</span>
                            </h2>
                            <div class="mt-5 prose prose-slate max-w-none text-slate-600 text-sm leading-relaxed whitespace-pre-line font-normal">
                                {{ $teacher->bio }}
                            </div>
                        </section>
                    @endif

                    {{-- Section 2: Portofolio & Karya Unggulan --}}
                    <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm" id="portofolio">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 gap-4">
                            <h2 class="font-roboto text-xl font-bold text-slate-900 flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                </span>
                                <span>Portofolio & Karya Nyata ({{ $teacher->portfolios->count() }})</span>
                            </h2>
                        </div>

                        @if($teacher->portfolios->isNotEmpty())
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                @foreach($teacher->portfolios as $port)
                                    <div class="group bg-white rounded-2xl border border-slate-200/90 hover:border-purple-300 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between overflow-hidden">
                                        {{-- Image / Header --}}
                                        <div>
                                            @if($port->image_url)
                                                <div class="h-44 w-full overflow-hidden bg-slate-200 relative">
                                                    <img src="{{ $port->image_url }}" alt="{{ $port->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                                    <span class="absolute top-3 left-3 rounded-full bg-slate-900/80 backdrop-blur-md px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-white">
                                                        {{ $port->kategori ?: 'Proyek' }}
                                                    </span>
                                                </div>
                                            @else
                                                <div class="h-28 bg-gradient-to-r from-slate-100 via-slate-50 to-gray-200/70 p-4 flex items-end border-b border-slate-100">
                                                    <span class="rounded-full bg-white shadow-xs border border-slate-200 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-slate-700">
                                                        {{ $port->kategori ?: 'Karya & Proyek' }}
                                                    </span>
                                                </div>
                                            @endif

                                            {{-- Details --}}
                                            <div class="p-5">
                                                <div class="flex items-center justify-between text-xs text-slate-400 mb-1 font-semibold">
                                                    <span>{{ $port->tahun ?: 'Karya' }}</span>
                                                    @if($port->is_featured)
                                                        <span class="text-amber-600 font-bold">Unggulan</span>
                                                    @endif
                                                </div>

                                                <h3 class="font-roboto font-bold text-base text-slate-900 group-hover:text-purple-600 transition-colors leading-snug">
                                                    {{ $port->judul }}
                                                </h3>

                                                @if($port->deskripsi)
                                                    <p class="mt-2 text-xs text-slate-600 leading-relaxed line-clamp-3">
                                                        {{ $port->deskripsi }}
                                                    </p>
                                                @endif

                                                {{-- Tech Stack Tags --}}
                                                @if(!empty($port->teknologi))
                                                    <div class="mt-4 flex flex-wrap gap-1.5">
                                                        @foreach($port->teknologi as $tech)
                                                            <span class="rounded-lg bg-slate-50 border border-slate-200/80 px-2.5 py-0.5 text-[10px] font-medium text-slate-600">
                                                                {{ $tech }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Link Action --}}
                                        @if($port->tautan_proyek)
                                            <div class="p-5 pt-0">
                                                <a 
                                                    href="{{ $port->tautan_proyek }}" 
                                                    target="_blank" 
                                                    rel="noopener noreferrer" 
                                                    class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-600 hover:text-purple-700"
                                                >
                                                    <span>Buka Tautan Karya</span>
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="mt-6 rounded-2xl bg-slate-50 border border-slate-200 p-8 text-center">
                                <p class="text-xs text-slate-500 font-medium">Portofolio karya guru ini sedang dalam tahap dokumentasi dan pembaharuan berkala.</p>
                            </div>
                        @endif
                    </section>

                    {{-- Section 3: Sertifikasi Profesi & Lisensi --}}
                    @if(!empty($teacher->sertifikasi))
                        <section class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/90 shadow-sm">
                            <h2 class="font-roboto text-xl font-bold text-slate-900 flex items-center gap-2.5 pb-4 border-b border-slate-100">
                                <span class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/></svg>
                                </span>
                                <span>Sertifikasi & Lisensi Profesi</span>
                            </h2>

                            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @foreach($teacher->sertifikasi as $cert)
                                    <div class="rounded-2xl border border-slate-200 bg-slate-50/60 p-4 flex items-start gap-3.5 hover:bg-white hover:border-amber-300 transition-colors">
                                        <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-700 flex items-center justify-center shrink-0">
                                            <svg class="w-5 h-5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                        </div>
                                        <div>
                                            <h3 class="font-roboto font-bold text-sm text-slate-900 leading-tight">
                                                {{ $cert['nama'] ?? '-' }}
                                            </h3>
                                            <p class="text-xs text-slate-500 font-normal mt-1">
                                                Penerbit: {{ $cert['penerbit'] ?? $schoolName }}
                                            </p>
                                            @if(!empty($cert['tahun']))
                                                <span class="inline-block mt-2 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5">
                                                    Tahun {{ $cert['tahun'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @endif

                </div>

                {{-- Right Sidebar: Skills, Education, Quick Info (4 cols) --}}
                <div class="lg:col-span-4 space-y-8">
                    
                    {{-- Keahlian / Core Competencies --}}
                    @if(!empty($teacher->keahlian))
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm">
                            <h2 class="font-roboto text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                                <span>Bidang Keahlian & Spesialisasi</span>
                            </h2>
                            <div class="mt-4 flex flex-wrap gap-2">
                                @foreach($teacher->keahlian as $sk)
                                    <span class="rounded-xl bg-slate-50 hover:bg-red-50 hover:text-red-600 hover:border-red-200 transition-colors border border-slate-200/80 px-3 py-1.5 text-xs font-bold text-slate-800">
                                        {{ $sk }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Riwayat Pendidikan Formal --}}
                    @if(!empty($teacher->riwayat_pendidikan))
                        <div class="bg-white rounded-3xl p-6 sm:p-7 border border-slate-200/90 shadow-sm">
                            <h2 class="font-roboto text-base font-bold text-slate-900 pb-3 border-b border-slate-100 flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                <span>Riwayat Pendidikan Formal</span>
                            </h2>
                            <div class="mt-4 space-y-4">
                                @foreach($teacher->riwayat_pendidikan as $edu)
                                    <div class="relative pl-6 before:content-[''] before:absolute before:left-1 before:top-2 before:w-2 before:h-2 before:rounded-full before:bg-indigo-600 border-l border-slate-200 pb-2 last:border-0 last:pb-0">
                                        <p class="font-roboto font-bold text-xs text-indigo-700 uppercase tracking-wide">
                                            {{ $edu['jenjang'] ?? 'Gelar' }}
                                        </p>
                                        <p class="font-bold text-sm text-slate-900 leading-snug mt-0.5">
                                            {{ $edu['institusi'] ?? '-' }}
                                        </p>
                                        @if(!empty($edu['jurusan']))
                                            <p class="text-xs text-slate-500 font-normal mt-0.5">
                                                {{ $edu['jurusan'] }}
                                            </p>
                                        @endif
                                        @if(!empty($edu['tahun_lulus']))
                                            <span class="inline-block mt-1 text-[10px] text-slate-400 font-medium">
                                                Lulus {{ $edu['tahun_lulus'] }}
                                            </span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Info Sekolah & Kolaborasi --}}
                    <div class="bg-gradient-to-br from-slate-900 to-slate-950 text-white rounded-3xl p-6 sm:p-7 shadow-lg">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-10 h-10 bg-white rounded-xl flex items-center justify-center p-1 shadow-md">
                                @if($setting?->logo)
                                    <img src="{{ Storage::url($setting->logo) }}" alt="Logo" class="w-full h-full object-contain">
                                @else
                                    <div class="w-full h-full bg-red-600 rounded-lg flex items-center justify-center text-white font-black text-xs">TS</div>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-roboto font-bold text-sm leading-none">{{ $schoolName }}</h3>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mt-1">Telkom Schools</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-300 leading-relaxed font-normal">
                            Pendidik kami aktif membimbing siswa, memimpin proyek teknologi berbasis industri, serta menjalin kolaborasi dengan dunia usaha & dunia industri (DUDI).
                        </p>

                        <div class="mt-5 pt-4 border-t border-slate-800 flex items-center justify-between text-xs font-bold text-slate-400">
                            <span>Status: Aktif Mengajar</span>
                            <span class="text-emerald-400 flex items-center gap-1">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Terverifikasi
                            </span>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Related Teachers (Internal Link Equity for SEO) --}}
            @if(!empty($relatedTeachers) && $relatedTeachers->isNotEmpty())
                <div class="mt-16 pt-10 border-t border-slate-200">
                    <div class="flex items-center justify-between gap-4 mb-6">
                        <div>
                            <h2 class="font-roboto font-bold text-2xl text-slate-900">Guru Lainnya di {{ $schoolName }}</h2>
                            <p class="text-xs text-slate-500 mt-1">Jelajahi profil rekan pendidik inspiratif lainnya.</p>
                        </div>
                        <a href="{{ route('pegawai.index') }}" class="text-xs font-bold text-red-600 hover:underline">
                            Lihat Semua
                        </a>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($relatedTeachers as $rel)
                            <a href="{{ route('pegawai.show', $rel->slug) }}" class="group bg-white rounded-2xl border border-slate-200 p-5 flex items-center gap-4 hover:shadow-lg hover:border-red-300 transition-all">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 overflow-hidden shrink-0">
                                    @if($rel->photo_url)
                                        <img src="{{ $rel->photo_url }}" alt="{{ $rel->display_name }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full bg-red-600 text-white flex items-center justify-center font-bold text-sm">
                                            {{ strtoupper(substr($rel->nama_lengkap, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <h3 class="font-roboto font-bold text-sm text-slate-900 group-hover:text-red-600 transition truncate">
                                        {{ $rel->display_name }}
                                    </h3>
                                    <p class="text-xs text-slate-500 truncate mt-0.5">
                                        {{ $rel->jabatan ?: $rel->kategori_label }}
                                    </p>
                                    <span class="inline-block text-[10px] text-red-600 font-bold mt-1">
                                        Lihat Portofolio →
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

        </main>
    </div>

    {{-- Footer --}}
    <footer class="bg-white text-slate-600 text-xs py-8 border-t border-slate-200 shadow-xs">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-2">
                <span class="font-roboto font-black text-sm text-slate-900">SISFO <span class="text-red-600">TS</span></span>
                <span>&copy; {{ date('Y') }} {{ $schoolName }}. Seluruh hak cipta dilindungi.</span>
            </div>
            <div class="flex items-center gap-4 font-semibold text-slate-600">
                <a href="{{ route('welcome') }}" class="hover:text-red-600 transition">Beranda</a>
                <a href="{{ route('pegawai.index') }}" class="text-red-600 font-bold transition">Direktori Pegawai</a>
                <a href="{{ route('public.showcase.index') }}" class="hover:text-red-600 transition">Showcase Siswa</a>
                <a href="{{ url('/sitemap.xml') }}" target="_blank" class="hover:text-red-600 transition">Sitemap XML</a>
            </div>
        </div>
    </footer>

</body>
</html>
