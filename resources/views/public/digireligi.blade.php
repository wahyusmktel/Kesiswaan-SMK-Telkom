<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DigiReligi — Al-Qur'an Digital & Jadwal Sholat | SMK Telkom Lampung</title>
    <meta name="description" content="Platform Al-Qur'an digital lengkap dengan terjemahan Bahasa Indonesia dan jadwal sholat otomatis untuk wilayah Kabupaten Pringsewu, Lampung.">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }

        * { box-sizing: border-box; }

        body {
            background: #0b1a14;
            color: #ecfdf5;
            font-family: 'Plus Jakarta Sans', sans-serif;
            min-height: 100vh;
        }

        .arabic { font-family: 'Noto Naskh Arabic', 'Traditional Arabic', serif; }

        /* Islamic star tile pattern */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image:
                radial-gradient(circle at 25% 25%, rgba(16,185,129,0.06) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(245,158,11,0.04) 0%, transparent 50%),
                url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M40 0L47 30H80L54 48L64 80L40 62L16 80L26 48L0 30H33Z' fill='%2310b981' fill-opacity='0.04'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 0;
        }

        .glass {
            background: rgba(5, 30, 20, 0.55);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(52, 211, 153, 0.12);
        }

        .glass-gold {
            background: rgba(30, 20, 5, 0.55);
            backdrop-filter: blur(24px);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .gold-text {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 50%, #d97706 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .green-text {
            background: linear-gradient(135deg, #34d399 0%, #10b981 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .tab-btn {
            padding: 0.5rem 1.25rem;
            border-radius: 0.75rem;
            font-size: 0.875rem;
            font-weight: 700;
            transition: all 0.2s ease;
            color: #6ee7b7;
            border: 1px solid transparent;
            cursor: pointer;
            white-space: nowrap;
        }
        .tab-btn:hover { background: rgba(52, 211, 153, 0.1); color: #fff; }
        .tab-btn.active {
            background: linear-gradient(135deg, #059669, #047857);
            color: #fff;
            border-color: rgba(52, 211, 153, 0.3);
            box-shadow: 0 4px 15px rgba(5, 150, 105, 0.35);
        }

        .surah-card {
            transition: all 0.2s ease;
            cursor: pointer;
            display: block;
            width: 100%;
            text-align: left;
        }
        .surah-card:hover {
            transform: translateY(-2px);
            border-color: rgba(52, 211, 153, 0.35) !important;
            box-shadow: 0 8px 25px rgba(5, 150, 105, 0.2);
        }

        .prayer-card {
            transition: all 0.3s ease;
            border-radius: 1.25rem;
            padding: 1.25rem;
            border: 1px solid rgba(52, 211, 153, 0.1);
            background: rgba(5, 30, 20, 0.5);
            text-align: center;
        }
        .prayer-card.next-prayer {
            background: linear-gradient(135deg, rgba(5, 150, 105, 0.25), rgba(4, 120, 87, 0.2));
            border-color: rgba(52, 211, 153, 0.5);
            box-shadow: 0 0 25px rgba(52, 211, 153, 0.2), inset 0 0 20px rgba(52, 211, 153, 0.05);
        }
        .prayer-card.past-prayer {
            opacity: 0.45;
        }
        .prayer-card.non-main {
            background: rgba(5, 30, 20, 0.3);
            border-color: rgba(52, 211, 153, 0.06);
        }

        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 15px rgba(52,211,153,0.2), inset 0 0 20px rgba(52,211,153,0.05); }
            50% { box-shadow: 0 0 35px rgba(52,211,153,0.4), inset 0 0 30px rgba(52,211,153,0.1); }
        }
        .next-prayer { animation: pulse-glow 2.5s ease-in-out infinite; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-12px) rotate(5deg); }
        }
        .float-moon { animation: float 5s ease-in-out infinite; }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }
        .skeleton {
            background: linear-gradient(90deg, rgba(52,211,153,0.05) 25%, rgba(52,211,153,0.12) 50%, rgba(52,211,153,0.05) 75%);
            background-size: 200% 100%;
            animation: shimmer 1.8s infinite;
            border-radius: 0.75rem;
        }

        .ayah-item:nth-child(even) {
            background: rgba(52, 211, 153, 0.025);
        }

        .bismillah-display {
            font-family: 'Noto Naskh Arabic', serif;
            font-size: 2rem;
            line-height: 1.8;
            direction: rtl;
            text-align: center;
            color: #fbbf24;
            text-shadow: 0 0 30px rgba(251,191,36,0.3);
        }

        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: rgba(0,0,0,0.2); }
        ::-webkit-scrollbar-thumb { background: rgba(52,211,153,0.3); border-radius: 2px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(52,211,153,0.5); }

        .countdown-ring {
            font-variant-numeric: tabular-nums;
            letter-spacing: 0.05em;
        }

        .quick-prayer-item { white-space: nowrap; }

        /* ── Audio Player ── */
        .audio-bar {
            background: rgba(3, 18, 12, 0.97);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border-top: 1px solid rgba(52, 211, 153, 0.2);
        }
        .ayah-playing {
            background: linear-gradient(90deg, rgba(52,211,153,0.06) 0%, transparent 100%) !important;
            border-left: 3px solid rgba(52, 211, 153, 0.45) !important;
            padding-left: calc(1.25rem - 3px) !important;
        }
        @keyframes soundwave {
            0%, 100% { transform: scaleY(0.35); }
            50%       { transform: scaleY(1); }
        }
        .sw-bar {
            display: inline-block;
            width: 2.5px;
            border-radius: 2px;
            background: currentColor;
            transform-origin: bottom;
            animation: soundwave 0.75s ease-in-out infinite;
        }
        .sw-bar:nth-child(2) { animation-delay: 0.15s; }
        .sw-bar:nth-child(3) { animation-delay: 0.3s;  }
        .sw-bar:nth-child(4) { animation-delay: 0.12s; }

        @media (max-width: 640px) {
            .bismillah-display { font-size: 1.5rem; }
        }
    </style>
</head>

<body x-data="digiReligi()" x-init="init()">
<div class="relative z-10 min-h-screen flex flex-col">

    {{-- ═══════════════════════════════════════
         STICKY NAVBAR
    ═══════════════════════════════════════ --}}
    <nav class="sticky top-0 z-50 glass border-b border-emerald-900/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">

            {{-- Logo --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-emerald-900/60 border border-emerald-700/40 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 3a6.5 6.5 0 1 0 6.5 6.5A6.5 6.5 0 0 0 12 3zm0 11a4.5 4.5 0 1 1 4.5-4.5A4.5 4.5 0 0 1 12 14zm0-7a2.5 2.5 0 1 0 2.5 2.5A2.5 2.5 0 0 0 12 7zm7.07-2.07a1 1 0 0 0-1.41 1.41 8.5 8.5 0 1 1-12.02-.02 1 1 0 1 0-1.41-1.41 10.5 10.5 0 1 0 14.84.02z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <div class="font-black text-base tracking-tight">
                        <span class="text-white">Digi</span><span class="text-emerald-400">Religi</span>
                    </div>
                    <div class="text-[10px] text-emerald-600 hidden sm:block">SMK Telkom Lampung</div>
                </div>
            </div>

            {{-- Tab Navigation (desktop) --}}
            <div class="hidden sm:flex items-center gap-1 bg-black/30 rounded-xl p-1 border border-emerald-900/30">
                <button @click="activeTab = 'quran'" :class="activeTab === 'quran' ? 'active' : ''" class="tab-btn flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Al-Qur'an
                </button>
                <button @click="activeTab = 'sholat'" :class="activeTab === 'sholat' ? 'active' : ''" class="tab-btn flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Jadwal Sholat
                </button>
            </div>

            {{-- Right: Clock + Back --}}
            <div class="flex items-center gap-3 shrink-0">
                <div class="hidden sm:block text-sm font-mono font-bold text-emerald-400 bg-emerald-900/30 px-3 py-1 rounded-lg border border-emerald-800/40" x-text="currentTime"></div>
                <a href="{{ url('/') }}" class="flex items-center gap-1.5 text-sm text-emerald-500 hover:text-white transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    <span class="hidden sm:inline">Beranda</span>
                </a>
            </div>
        </div>

        {{-- Mobile Tab Bar --}}
        <div class="sm:hidden flex items-center border-t border-emerald-900/30">
            <button @click="activeTab = 'quran'" :class="activeTab === 'quran' ? 'text-emerald-400 border-b-2 border-emerald-400' : 'text-emerald-700'" class="flex-1 py-2.5 text-xs font-bold flex flex-col items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                Al-Qur'an
            </button>
            <button @click="activeTab = 'sholat'" :class="activeTab === 'sholat' ? 'text-emerald-400 border-b-2 border-emerald-400' : 'text-emerald-700'" class="flex-1 py-2.5 text-xs font-bold flex flex-col items-center gap-1 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Jadwal Sholat
            </button>
        </div>
    </nav>

    {{-- ═══════════════════════════════════════
         PRAYER QUICK BAR (shown when on Quran tab + times loaded)
    ═══════════════════════════════════════ --}}
    <div x-show="activeTab === 'quran' && prayerTimes" x-cloak
         class="sticky z-40 border-b border-emerald-900/30 overflow-x-auto"
         style="top: 64px; background: rgba(11,26,20,0.92); backdrop-filter: blur(20px);">
        <div class="flex items-center gap-1 px-4 py-2 min-w-max mx-auto">
            <span class="text-[10px] text-emerald-700 font-bold uppercase tracking-wider mr-2">Waktu Sholat</span>
            <template x-for="p in prayerTimes" :key="p.key">
                <div class="quick-prayer-item flex items-center gap-2 px-3 py-1 rounded-lg text-xs font-bold transition-all"
                     :class="isNextPrayer(p.key)
                        ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 ring-1 ring-emerald-500/20'
                        : 'text-emerald-700'">
                    <span x-text="p.name"></span>
                    <span :class="isNextPrayer(p.key) ? 'text-white' : 'text-emerald-600'" class="font-mono" x-text="p.time"></span>
                    <span x-show="isNextPrayer(p.key)" class="text-[8px] bg-emerald-500 text-white px-1 py-0.5 rounded font-black">NEXT</span>
                </div>
            </template>
        </div>
    </div>

    {{-- ═══════════════════════════════════════
         HERO SECTION
    ═══════════════════════════════════════ --}}
    <section class="relative py-12 sm:py-16 px-4 overflow-hidden">
        {{-- Decorative orbs --}}
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-emerald-900/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute bottom-0 right-1/4 w-64 h-64 bg-amber-900/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center relative">
            {{-- Crescent moon icon --}}
            <div class="float-moon inline-block mb-6">
                <div class="w-20 h-20 mx-auto bg-emerald-900/50 rounded-full border border-emerald-700/40 flex items-center justify-center shadow-2xl shadow-emerald-900/50">
                    <svg class="w-10 h-10 text-amber-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </div>
            </div>

            {{-- Title --}}
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black mb-3 leading-tight">
                <span class="text-white">Digi</span><span class="green-text">Religi</span>
            </h1>
            <p class="text-base sm:text-lg text-emerald-400 font-medium mb-2">
                Platform Al-Qur'an Digital & Reminder Beribadah Otomatis
            </p>
            <p class="text-sm text-emerald-700 mb-8">SMK Telkom Lampung — Kabupaten Pringsewu, Lampung</p>

            {{-- Feature badges --}}
            <div class="flex flex-wrap justify-center gap-3 mb-8">
                <button @click="activeTab = 'quran'"
                    class="glass flex items-center gap-2.5 px-5 py-3 rounded-2xl border border-emerald-800/40 hover:border-emerald-600/50 transition-all group">
                    <svg class="w-5 h-5 text-emerald-400 group-hover:text-emerald-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-xs font-black text-white">Al-Qur'an Digital</div>
                        <div class="text-[10px] text-emerald-500">114 Surah + Terjemahan</div>
                    </div>
                </button>

                <button @click="activeTab = 'sholat'"
                    class="glass flex items-center gap-2.5 px-5 py-3 rounded-2xl border border-amber-900/30 hover:border-amber-700/50 transition-all group">
                    <svg class="w-5 h-5 text-amber-400 group-hover:text-amber-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-xs font-black text-white">Jadwal Sholat</div>
                        <div class="text-[10px] text-amber-600">Pringsewu, Lampung</div>
                    </div>
                </button>

                <div class="glass flex items-center gap-2.5 px-5 py-3 rounded-2xl border border-emerald-900/30">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <div class="text-left">
                        <div class="text-xs font-black text-white">Reminder Otomatis</div>
                        <div class="text-[10px] text-emerald-600">Pengingat Waktu Sholat</div>
                    </div>
                </div>
            </div>

            {{-- Divider --}}
            <div class="flex items-center gap-4 max-w-xs mx-auto">
                <div class="flex-1 h-px bg-gradient-to-r from-transparent to-emerald-900/50"></div>
                <span class="arabic text-amber-500/60 text-lg">﷽</span>
                <div class="flex-1 h-px bg-gradient-to-l from-transparent to-emerald-900/50"></div>
            </div>
        </div>
    </section>

    {{-- ═══════════════════════════════════════
         MAIN CONTENT
    ═══════════════════════════════════════ --}}
    <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 pb-16"
        :class="(isPlaying || playingAyah !== null) ? 'pb-32' : 'pb-16'">

        {{-- ─── AL-QUR'AN TAB ─── --}}
        <div x-show="activeTab === 'quran'" x-cloak>

            {{-- LIST VIEW --}}
            <div x-show="view === 'list'">

                {{-- Search Bar --}}
                <div class="mb-6 relative">
                    <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <input
                        x-model="searchQuery"
                        type="text"
                        placeholder="Cari surah (nama, terjemahan, atau nomor)..."
                        class="w-full bg-black/30 border border-emerald-900/50 rounded-2xl py-3.5 pl-12 pr-4 text-sm text-white placeholder-emerald-700 focus:outline-none focus:border-emerald-600/60 focus:ring-2 focus:ring-emerald-900/50 transition-all"
                    >
                    <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-4 top-1/2 -translate-y-1/2 text-emerald-600 hover:text-white transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Stats row --}}
                <div x-show="!loadingSurahs && surahs.length > 0" class="flex items-center justify-between mb-4">
                    <p class="text-sm text-emerald-600">
                        Menampilkan <span class="text-emerald-400 font-bold" x-text="filteredSurahs.length"></span>
                        dari <span class="text-emerald-400 font-bold">114</span> surah
                    </p>
                    <span class="text-xs text-emerald-700 bg-emerald-900/30 px-3 py-1 rounded-full border border-emerald-900/40">Al-Qur'an Al-Karim</span>
                </div>

                {{-- Loading Skeletons --}}
                <div x-show="loadingSurahs" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <template x-for="i in 12" :key="i">
                        <div class="skeleton h-28 rounded-2xl"></div>
                    </template>
                </div>

                {{-- Error State --}}
                <div x-show="!loadingSurahs && surahs.length === 0 && !searchQuery" class="text-center py-16">
                    <div class="text-4xl mb-3">📡</div>
                    <p class="text-emerald-600 font-medium">Gagal memuat daftar surah</p>
                    <button @click="loadSurahs()" class="mt-4 px-4 py-2 bg-emerald-800/50 text-emerald-300 rounded-xl text-sm font-bold hover:bg-emerald-700/50 transition-colors border border-emerald-700/40">
                        Coba Lagi
                    </button>
                </div>

                {{-- Surah Grid --}}
                <div x-show="!loadingSurahs && surahs.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
                    <template x-for="surah in filteredSurahs" :key="surah.nomor">
                        <button @click="selectSurah(surah)" class="surah-card glass rounded-2xl p-4 border border-emerald-900/30">
                            <div class="flex items-start gap-3">
                                {{-- Number badge --}}
                                <div class="w-10 h-10 shrink-0 rounded-xl bg-emerald-900/60 border border-emerald-800/50 flex items-center justify-center">
                                    <span class="text-xs font-black text-emerald-300" x-text="surah.nomor"></span>
                                </div>
                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <div class="arabic text-lg text-right text-amber-300 leading-tight mb-0.5" dir="rtl" x-text="surah.nama"></div>
                                    <div class="text-xs font-bold text-white truncate" x-text="surah.namaLatin"></div>
                                    <div class="text-xs text-emerald-600 truncate" x-text="surah.arti"></div>
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <span class="text-[10px] bg-emerald-900/50 text-emerald-500 px-1.5 py-0.5 rounded-md border border-emerald-900/50 font-bold" x-text="surah.jumlahAyat + ' Ayat'"></span>
                                        <span class="text-[10px] bg-emerald-900/50 text-emerald-500 px-1.5 py-0.5 rounded-md border border-emerald-900/50 font-bold" x-text="surah.tempatTurun"></span>
                                    </div>
                                </div>
                            </div>
                        </button>
                    </template>
                </div>

                {{-- No results --}}
                <div x-show="!loadingSurahs && filteredSurahs.length === 0 && searchQuery" class="text-center py-16">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-emerald-600 font-medium">Surah "<span x-text="searchQuery" class="text-emerald-400"></span>" tidak ditemukan</p>
                    <button @click="searchQuery = ''" class="mt-4 px-4 py-2 bg-emerald-800/50 text-emerald-300 rounded-xl text-sm font-bold hover:bg-emerald-700/50 transition-colors border border-emerald-700/40">
                        Tampilkan Semua
                    </button>
                </div>
            </div>

            {{-- READER VIEW --}}
            <div x-show="view === 'reader'" x-cloak id="reader-top">

                {{-- Reader Header --}}
                <div class="glass rounded-2xl p-5 mb-4 border border-emerald-900/30">
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <button @click="backToList()" class="w-10 h-10 rounded-xl bg-emerald-900/50 border border-emerald-800/50 flex items-center justify-center text-emerald-400 hover:text-white hover:bg-emerald-800/50 transition-all shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </button>
                            <div>
                                <div class="text-xs text-emerald-600 font-bold uppercase tracking-wider">Surah ke-<span x-text="selectedSurah?.nomor"></span></div>
                                <h2 class="text-lg font-black text-white leading-tight" x-text="selectedSurah?.namaLatin"></h2>
                                <p class="text-sm text-emerald-500" x-text="selectedSurah?.arti"></p>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <div class="arabic text-2xl text-amber-300 leading-tight mb-1" dir="rtl" x-text="selectedSurah?.nama"></div>
                            <div class="flex items-center justify-end gap-2">
                                <span class="text-[10px] bg-emerald-900/50 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-900/50 font-bold" x-text="(selectedSurah?.jumlahAyat ?? 0) + ' Ayat'"></span>
                                <span class="text-[10px] bg-emerald-900/50 text-emerald-400 px-2 py-0.5 rounded-full border border-emerald-900/50 font-bold" x-text="selectedSurah?.tempatTurun"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Prev / Next Surah nav --}}
                    <div class="flex items-center justify-between mt-4 pt-4 border-t border-emerald-900/30">
                        <button
                            @click="selectedSurah && selectedSurah.nomor > 1 && selectSurah(surahs.find(s => s.nomor === selectedSurah.nomor - 1))"
                            :disabled="!selectedSurah || selectedSurah.nomor <= 1"
                            class="flex items-center gap-2 text-xs font-bold text-emerald-500 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors px-3 py-1.5 rounded-lg hover:bg-emerald-900/30">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                            Surah Sebelumnya
                        </button>
                        <button @click="backToList()" class="text-xs text-emerald-600 hover:text-emerald-400 transition-colors font-bold">
                            ☰ Daftar Surah
                        </button>
                        <button
                            @click="selectedSurah && selectedSurah.nomor < 114 && selectSurah(surahs.find(s => s.nomor === selectedSurah.nomor + 1))"
                            :disabled="!selectedSurah || selectedSurah.nomor >= 114"
                            class="flex items-center gap-2 text-xs font-bold text-emerald-500 hover:text-white disabled:opacity-30 disabled:cursor-not-allowed transition-colors px-3 py-1.5 rounded-lg hover:bg-emerald-900/30">
                            Surah Berikutnya
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>

                    {{-- ── Audio Controls ── --}}
                    <div x-show="!loadingAyahs && ayahs.length > 0"
                         class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-emerald-900/30">

                        {{-- Mic icon --}}
                        <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 01-3-3V5a3 3 0 116 0v6a3 3 0 01-3 3z"/>
                        </svg>

                        {{-- Reciter dropdown --}}
                        <select @change="changeReciter($event.target.value)"
                            class="flex-1 min-w-0 bg-black/40 border border-emerald-900/50 rounded-xl px-3 py-1.5 text-xs text-emerald-300 font-bold focus:outline-none focus:border-emerald-600/50 cursor-pointer">
                            <template x-for="r in reciters" :key="r.id">
                                <option :value="r.id" :selected="r.id === selectedReciter" x-text="r.name"></option>
                            </template>
                        </select>

                        {{-- Auto-next toggle --}}
                        <label class="flex items-center gap-1.5 cursor-pointer shrink-0" @click.prevent="autoNext = !autoNext">
                            <div class="w-7 h-3.5 rounded-full transition-colors border relative"
                                :class="autoNext ? 'bg-emerald-600 border-emerald-500' : 'bg-black/40 border-emerald-900/50'">
                                <div class="absolute top-[1px] left-[1px] w-[10px] h-[10px] rounded-full bg-white shadow transition-transform"
                                    :class="autoNext ? 'translate-x-[14px]' : ''"></div>
                            </div>
                            <span class="text-[10px] text-emerald-600 font-bold uppercase tracking-wide">Auto-lanjut</span>
                        </label>

                        {{-- Play all button --}}
                        <button @click="playAyah(ayahs[0])"
                            x-show="!isPlaying"
                            class="shrink-0 flex items-center gap-1 px-3 py-1.5 bg-emerald-900/40 border border-emerald-800/40 rounded-xl text-xs font-bold text-emerald-400 hover:bg-emerald-800/50 hover:text-emerald-200 transition-all">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            Putar Semua
                        </button>
                        <button @click="stopAudio()"
                            x-show="isPlaying || playingAyah !== null"
                            class="shrink-0 flex items-center gap-1 px-3 py-1.5 bg-red-900/30 border border-red-900/50 rounded-xl text-xs font-bold text-red-400 hover:bg-red-900/50 transition-all">
                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M6 6h12v12H6z"/></svg>
                            Stop
                        </button>
                    </div>
                </div>

                {{-- Bismillah Header --}}
                <div x-show="selectedSurah && selectedSurah.nomor !== 1 && selectedSurah.nomor !== 9"
                     class="glass-gold rounded-2xl p-6 mb-4 text-center border border-amber-900/20">
                    <p class="bismillah-display">بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ</p>
                    <p class="text-xs text-amber-600/70 mt-2 font-medium">Dengan nama Allah Yang Maha Pengasih, Maha Penyayang</p>
                </div>

                {{-- Loading Ayahs --}}
                <div x-show="loadingAyahs" class="space-y-3">
                    <template x-for="i in 7" :key="i">
                        <div class="skeleton h-32 rounded-2xl"></div>
                    </template>
                </div>

                {{-- Ayah List --}}
                <div x-show="!loadingAyahs && ayahs.length > 0" class="space-y-0 glass rounded-2xl border border-emerald-900/30 overflow-hidden">
                    <template x-for="(ayah, idx) in ayahs" :key="ayah.nomorAyat">
                        <div class="ayah-item border-b border-emerald-900/20 last:border-b-0 p-5 transition-all duration-300"
                            :id="'ayah-' + ayah.nomorAyat"
                            :class="playingAyah === ayah.nomorAyat ? 'ayah-playing' : ''">

                            {{-- Ayah header row --}}
                            <div class="flex items-center justify-between mb-4">
                                {{-- Left: number badge (or soundwave when playing) --}}
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center transition-all duration-300 border"
                                        :class="playingAyah === ayah.nomorAyat
                                            ? 'bg-emerald-500/25 border-emerald-500/50'
                                            : 'bg-emerald-900/70 border-emerald-800/60'">
                                        <template x-if="playingAyah === ayah.nomorAyat && isPlaying">
                                            <div class="flex items-end gap-[2px] h-4 text-emerald-400">
                                                <span class="sw-bar" style="height:6px"></span>
                                                <span class="sw-bar" style="height:10px"></span>
                                                <span class="sw-bar" style="height:7px"></span>
                                                <span class="sw-bar" style="height:4px"></span>
                                            </div>
                                        </template>
                                        <template x-if="!(playingAyah === ayah.nomorAyat && isPlaying)">
                                            <span class="text-xs font-black"
                                                :class="playingAyah === ayah.nomorAyat ? 'text-emerald-400' : 'text-emerald-300'"
                                                x-text="ayah.nomorAyat"></span>
                                        </template>
                                    </div>
                                    <span x-show="playingAyah === ayah.nomorAyat"
                                        class="text-[10px] font-bold transition-colors"
                                        :class="isPlaying ? 'text-emerald-500' : 'text-yellow-500'"
                                        x-text="isPlaying ? 'Sedang diputar...' : 'Dijeda'"></span>
                                </div>

                                {{-- Right: ayat label + play/pause button --}}
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] text-emerald-800 font-mono">Ayat <span x-text="ayah.nomorAyat"></span></span>
                                    <button @click="toggleAyah(ayah)"
                                        class="w-7 h-7 rounded-full flex items-center justify-center transition-all duration-200 border"
                                        :class="playingAyah === ayah.nomorAyat
                                            ? (isPlaying
                                                ? 'bg-emerald-500/30 border-emerald-500/60 text-emerald-300 shadow-sm shadow-emerald-900/50'
                                                : 'bg-yellow-500/20 border-yellow-600/40 text-yellow-400')
                                            : 'bg-emerald-900/40 border-emerald-900/60 text-emerald-700 hover:text-emerald-300 hover:border-emerald-700/60 hover:bg-emerald-800/40'">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24">
                                            <path x-show="!(playingAyah === ayah.nomorAyat && isPlaying)" d="M8 5v14l11-7z"/>
                                            <path x-show="playingAyah === ayah.nomorAyat && isPlaying" d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            {{-- Arabic Text --}}
                            <p class="arabic text-2xl sm:text-3xl leading-loose text-right text-white mb-4 font-medium" dir="rtl" x-text="ayah.teksArab"></p>

                            {{-- Transliteration --}}
                            <p class="text-xs text-emerald-600 italic text-right mb-3 font-light" x-text="ayah.teksLatin"></p>

                            {{-- Indonesian Translation --}}
                            <p class="text-sm text-emerald-200 leading-relaxed border-t border-emerald-900/30 pt-3" x-text="ayah.teksIndonesia"></p>
                        </div>
                    </template>
                </div>

                {{-- Load error --}}
                <div x-show="!loadingAyahs && ayahs.length === 0 && selectedSurah" class="text-center py-12 glass rounded-2xl border border-emerald-900/30">
                    <div class="text-3xl mb-3">📡</div>
                    <p class="text-emerald-600 font-medium mb-4">Gagal memuat isi surah</p>
                    <button @click="selectSurah(selectedSurah)" class="px-4 py-2 bg-emerald-800/50 text-emerald-300 rounded-xl text-sm font-bold hover:bg-emerald-700/50 transition-colors border border-emerald-700/40">
                        Coba Lagi
                    </button>
                </div>
            </div>
        </div>

        {{-- ─── JADWAL SHOLAT TAB ─── --}}
        <div x-show="activeTab === 'sholat'" x-cloak>

            {{-- Header Info --}}
            <div class="glass rounded-2xl p-6 mb-6 border border-emerald-900/30">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-emerald-500 text-sm font-bold mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            Kab. Pringsewu, Lampung, Indonesia
                        </div>
                        <p class="text-xl font-black text-white" x-text="todayDate()"></p>
                        <p x-show="hijriDate" class="arabic text-base text-amber-400 mt-1" dir="rtl" x-text="hijriDate + ' هـ'"></p>
                    </div>
                    <div class="text-right">
                        <div class="text-4xl font-mono font-black text-emerald-400" x-text="currentTime"></div>
                        <div class="text-xs text-emerald-700 mt-1">Waktu Indonesia Barat (WIB)</div>
                    </div>
                </div>
            </div>

            {{-- Countdown Card --}}
            <div x-show="nextPrayerKey && !prayerLoading" class="glass-gold rounded-2xl p-6 mb-6 border border-amber-900/30 text-center">
                <p class="text-xs text-amber-600 font-bold uppercase tracking-widest mb-2">Menuju Waktu Sholat</p>
                <p class="text-2xl font-black text-white mb-1">
                    <span x-text="nextPrayerName"></span>
                </p>
                <div class="countdown-ring text-5xl sm:text-6xl font-black gold-text tracking-tight" x-text="countdown"></div>
                <p class="text-xs text-amber-700 mt-2">jam : menit : detik</p>
            </div>

            {{-- Prayer Times Loading --}}
            <div x-show="prayerLoading" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                <template x-for="i in 7" :key="i">
                    <div class="skeleton h-32 rounded-2xl"></div>
                </template>
            </div>

            {{-- Prayer Times Grid --}}
            <div x-show="!prayerLoading && prayerTimes" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 mb-6">
                <template x-for="prayer in prayerTimes" :key="prayer.key">
                    <div :class="{
                            'next-prayer': isNextPrayer(prayer.key),
                            'past-prayer': isPastPrayer(prayer),
                            'non-main': !prayer.main
                         }"
                         class="prayer-card">
                        <div class="text-2xl mb-2" x-text="prayer.icon"></div>
                        <div class="arabic text-sm text-emerald-400 mb-1 leading-none" dir="rtl" x-text="prayer.arabic"></div>
                        <div class="text-sm font-black text-white mb-2" x-text="prayer.name"></div>
                        <div class="text-2xl font-mono font-black leading-none"
                             :class="isNextPrayer(prayer.key) ? 'text-emerald-300' : 'text-white'"
                             x-text="prayer.time"></div>
                        <div x-show="isNextPrayer(prayer.key)" class="mt-2 inline-block text-[9px] bg-emerald-500 text-white px-2 py-0.5 rounded-full font-black uppercase tracking-wider">
                            Selanjutnya
                        </div>
                        <div x-show="isPastPrayer(prayer)" class="mt-2 inline-block text-[9px] bg-emerald-900/50 text-emerald-700 px-2 py-0.5 rounded-full font-bold">
                            Sudah Lewat
                        </div>
                    </div>
                </template>
            </div>

            {{-- Prayer Load Error --}}
            <div x-show="!prayerLoading && !prayerTimes" class="text-center py-12 glass rounded-2xl border border-emerald-900/30">
                <div class="text-3xl mb-3">📡</div>
                <p class="text-emerald-600 font-medium mb-4">Gagal memuat jadwal sholat. Cek koneksi internet.</p>
                <button @click="loadPrayerTimes()" class="px-4 py-2 bg-emerald-800/50 text-emerald-300 rounded-xl text-sm font-bold hover:bg-emerald-700/50 transition-colors border border-emerald-700/40">
                    Muat Ulang
                </button>
            </div>

            {{-- Info Note --}}
            <div class="glass rounded-2xl p-4 border border-emerald-900/20 flex items-start gap-3">
                <svg class="w-5 h-5 text-emerald-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-xs text-emerald-600 leading-relaxed">
                    Jadwal sholat dihitung berdasarkan koordinat Kabupaten Pringsewu (Lat: -5.3581°, Long: 104.9748°)
                    menggunakan metode perhitungan <strong class="text-emerald-500">Kementerian Agama RI (KEMENAG)</strong>.
                    Perbedaan beberapa menit mungkin terjadi — harap konfirmasi dengan pengumuman setempat.
                </p>
            </div>
        </div>

    </main>

    {{-- ═══════════════════════════════════════
         FOOTER
    ═══════════════════════════════════════ --}}
    <footer class="border-t border-emerald-900/30 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-lg bg-emerald-900/50 border border-emerald-800/40 flex items-center justify-center">
                    <svg class="w-4 h-4 text-emerald-400" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                    </svg>
                </div>
                <div>
                    <div class="text-sm font-black text-white">DigiReligi</div>
                    <div class="text-[10px] text-emerald-700">SMK Telkom Lampung — {{ date('Y') }}</div>
                </div>
            </div>
            <div class="flex items-center gap-4 text-xs text-emerald-700">
                <span>Sumber: equran.id &amp; aladhan.com</span>
                <span>·</span>
                <a href="{{ url('/') }}" class="hover:text-emerald-400 transition-colors">← Kembali ke Beranda</a>
            </div>
        </div>
    </footer>

    {{-- ══════════════════════════════════
         STICKY BOTTOM AUDIO PLAYER BAR
    ══════════════════════════════════ --}}
    <div x-show="isPlaying || playingAyah !== null"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-y-full opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="translate-y-full opacity-0"
        class="fixed bottom-0 left-0 right-0 z-[60] audio-bar px-4 py-3 safe-area-bottom">
        <div class="max-w-3xl mx-auto flex items-center gap-3">

            {{-- Info --}}
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <div x-show="isPlaying" class="flex items-end gap-[2px] h-3 text-emerald-400">
                        <span class="sw-bar" style="height:5px"></span>
                        <span class="sw-bar" style="height:9px"></span>
                        <span class="sw-bar" style="height:6px"></span>
                        <span class="sw-bar" style="height:4px"></span>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wide"
                        :class="isPlaying ? 'text-emerald-500' : 'text-yellow-500/80'"
                        x-text="isPlaying ? 'Sedang Diputar' : 'Dijeda'"></span>
                </div>
                <p class="text-sm font-black text-white truncate leading-tight" x-text="playingAyahLabel"></p>
                <p class="text-[10px] text-emerald-700 truncate" x-text="currentReciterName"></p>
            </div>

            {{-- Prev ayah --}}
            <button @click="playPrevAyah()"
                :disabled="playingAyah === null || ayahs.findIndex(a => a.nomorAyat === playingAyah) <= 0"
                class="w-9 h-9 rounded-full flex items-center justify-center text-emerald-600 hover:text-white hover:bg-emerald-900/60 transition-all disabled:opacity-25 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 6h2v12H6zm3.5 6 8.5 6V6z"/>
                </svg>
            </button>

            {{-- Play / Pause --}}
            <button @click="isPlaying ? pauseAudio() : resumeAudio()"
                class="w-12 h-12 rounded-full flex items-center justify-center text-white transition-all border shadow-lg"
                :class="isPlaying
                    ? 'bg-emerald-600 border-emerald-500 shadow-emerald-900/60'
                    : 'bg-emerald-900/70 border-emerald-700/60'">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path x-show="isPlaying"  d="M6 19h4V5H6v14zm8-14v14h4V5h-4z"/>
                    <path x-show="!isPlaying" d="M8 5v14l11-7z"/>
                </svg>
            </button>

            {{-- Next ayah --}}
            <button @click="playNextAyah()"
                :disabled="playingAyah === null || ayahs.findIndex(a => a.nomorAyat === playingAyah) >= ayahs.length - 1"
                class="w-9 h-9 rounded-full flex items-center justify-center text-emerald-600 hover:text-white hover:bg-emerald-900/60 transition-all disabled:opacity-25 disabled:cursor-not-allowed">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18l8.5-6L6 6v12zM16 6v12h2V6h-2z"/>
                </svg>
            </button>

            {{-- Stop / Close --}}
            <button @click="stopAudio()"
                class="w-9 h-9 rounded-full flex items-center justify-center text-emerald-700 hover:text-red-400 hover:bg-red-900/20 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

</div>{{-- end relative z-10 --}}

{{-- ═══════════════════════════════════════
     ALPINE.JS DATA FUNCTION
═══════════════════════════════════════ --}}
<script>
function digiReligi() {
    return {
        activeTab: 'quran',
        currentTime: '',

        // Quran
        surahs: [],
        selectedSurah: null,
        ayahs: [],
        loadingSurahs: false,
        loadingAyahs: false,
        searchQuery: '',
        view: 'list',

        // Audio
        reciters: [
            { id: '01', name: 'Abdullah Al-Juhany' },
            { id: '02', name: 'Abdul-Muhsin Al-Qasim' },
            { id: '03', name: 'Abdurrahman as-Sudais' },
            { id: '04', name: 'Ibrahim Al-Dossari' },
            { id: '05', name: 'Misyari Rasyid Al-Afasi' },
            { id: '06', name: 'Yasser Al-Dosari' },
        ],
        selectedReciter: '05',
        audioFull: {},
        currentAudio: null,
        playingAyah: null,
        isPlaying: false,
        autoNext: true,

        get currentReciterName() {
            return this.reciters.find(r => r.id === this.selectedReciter)?.name ?? '';
        },
        get playingAyahLabel() {
            if (this.playingAyah === null) return '';
            return `Ayat ${this.playingAyah}  —  ${this.selectedSurah?.namaLatin ?? ''}`;
        },

        // Prayer
        prayerTimes: null,
        hijriDate: '',
        nextPrayerKey: null,
        nextPrayerName: '',
        countdown: '00:00:00',
        prayerLoading: false,

        get filteredSurahs() {
            if (!this.searchQuery.trim()) return this.surahs;
            const q = this.searchQuery.toLowerCase().trim();
            return this.surahs.filter(s =>
                String(s.nomor).includes(q) ||
                s.namaLatin.toLowerCase().includes(q) ||
                s.arti.toLowerCase().includes(q) ||
                s.nama.includes(this.searchQuery)
            );
        },

        async init() {
            this.startClock();
            this.loadSurahs();
            this.loadPrayerTimes();
        },

        startClock() {
            const tick = () => {
                const now = new Date();
                this.currentTime = now.toLocaleTimeString('id-ID', { hour12: false, hour: '2-digit', minute: '2-digit', second: '2-digit' });
                this.updateCountdown();
            };
            tick();
            setInterval(tick, 1000);
        },

        async loadSurahs() {
            this.loadingSurahs = true;
            try {
                const res = await fetch('https://equran.id/api/v2/surat');
                if (!res.ok) throw new Error('Network error');
                const json = await res.json();
                this.surahs = json.data;
            } catch (e) {
                // Fallback to al-quran.cloud
                try {
                    const res = await fetch('https://api.alquran.cloud/v1/surah');
                    const json = await res.json();
                    const revMap = { Meccan: 'Mekah', Medinan: 'Madinah' };
                    this.surahs = json.data.map(s => ({
                        nomor: s.number,
                        nama: s.name,
                        namaLatin: s.englishName,
                        arti: s.englishNameTranslation,
                        jumlahAyat: s.numberOfAyahs,
                        tempatTurun: revMap[s.revelationType] ?? s.revelationType,
                    }));
                } catch (e2) {
                    console.error('All surah APIs failed', e2);
                }
            }
            this.loadingSurahs = false;
        },

        async selectSurah(surah) {
            if (!surah) return;
            this.stopAudio();
            this.selectedSurah = surah;
            this.view = 'reader';
            this.ayahs = [];
            this.audioFull = {};
            this.loadingAyahs = true;
            window.scrollTo({ top: 0, behavior: 'smooth' });

            try {
                const res = await fetch(`https://equran.id/api/v2/surat/${surah.nomor}`);
                if (!res.ok) throw new Error('Network error');
                const json = await res.json();
                this.ayahs    = json.data.ayat     ?? [];
                this.audioFull = json.data.audioFull ?? {};
            } catch (e) {
                // Fallback to al-quran.cloud (no audio in fallback)
                try {
                    const res = await fetch(`https://api.alquran.cloud/v1/surah/${surah.nomor}/editions/quran-uthmani,id.indonesian`);
                    const json = await res.json();
                    const arabic   = json.data[0].ayahs;
                    const terjemah = json.data[1].ayahs;
                    this.ayahs = arabic.map((a, i) => ({
                        nomorAyat:    a.numberInSurah,
                        teksArab:     a.text,
                        teksLatin:    '',
                        teksIndonesia: terjemah[i]?.text ?? '',
                        audio:        {},
                    }));
                } catch (e2) {
                    console.error('All ayah APIs failed', e2);
                }
            }
            this.loadingAyahs = false;
        },

        backToList() {
            this.stopAudio();
            this.view = 'list';
            this.selectedSurah = null;
            this.ayahs = [];
            this.audioFull = {};
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        // ── Audio methods ──
        playAyah(ayah) {
            if (!ayah) return;
            this.stopAudio();
            const url = ayah.audio?.[this.selectedReciter];
            if (!url) return;
            this.playingAyah  = ayah.nomorAyat;
            this.currentAudio = new Audio(url);
            this.isPlaying    = true;
            this.currentAudio.play().catch(() => { this.isPlaying = false; });
            this.currentAudio.onended = () => {
                if (this.autoNext) this.playNextAyah();
                else this.isPlaying = false;
            };
            // Scroll playing ayah into view
            setTimeout(() => {
                const el = document.getElementById('ayah-' + ayah.nomorAyat);
                if (el) el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 100);
        },

        toggleAyah(ayah) {
            if (this.playingAyah === ayah.nomorAyat) {
                this.isPlaying ? this.pauseAudio() : this.resumeAudio();
            } else {
                this.playAyah(ayah);
            }
        },

        pauseAudio() {
            this.currentAudio?.pause();
            this.isPlaying = false;
        },

        resumeAudio() {
            if (!this.currentAudio) return;
            this.currentAudio.play().catch(() => {});
            this.isPlaying = true;
        },

        stopAudio() {
            if (this.currentAudio) {
                this.currentAudio.onended = null;
                this.currentAudio.pause();
                this.currentAudio.src = '';
                this.currentAudio = null;
            }
            this.isPlaying   = false;
            this.playingAyah = null;
        },

        playNextAyah() {
            const idx = this.ayahs.findIndex(a => a.nomorAyat === this.playingAyah);
            if (idx !== -1 && idx < this.ayahs.length - 1) {
                this.playAyah(this.ayahs[idx + 1]);
            } else {
                this.isPlaying   = false;
                this.playingAyah = null;
            }
        },

        playPrevAyah() {
            const idx = this.ayahs.findIndex(a => a.nomorAyat === this.playingAyah);
            if (idx > 0) this.playAyah(this.ayahs[idx - 1]);
        },

        changeReciter(id) {
            const wasPlaying = this.isPlaying;
            const wasAyah    = this.playingAyah;
            this.stopAudio();
            this.selectedReciter = id;
            if (wasPlaying && wasAyah !== null) {
                const ayah = this.ayahs.find(a => a.nomorAyat === wasAyah);
                if (ayah) setTimeout(() => this.playAyah(ayah), 50);
            }
        },

        async loadPrayerTimes() {
            this.prayerLoading = true;
            try {
                const now = new Date();
                const dd = String(now.getDate()).padStart(2, '0');
                const mm = String(now.getMonth() + 1).padStart(2, '0');
                const yyyy = now.getFullYear();
                const dateStr = `${dd}-${mm}-${yyyy}`;

                // Pringsewu coordinates, method=20 (KEMENAG RI)
                const url = `https://api.aladhan.com/v1/timings/${dateStr}?latitude=-5.3581&longitude=104.9748&method=20&school=0`;
                const res = await fetch(url);
                const json = await res.json();

                if (json.code === 200) {
                    const t = json.data.timings;
                    const h = json.data.date.hijri;

                    // Strip parenthetical timezone notation e.g. "04:41 (WIB)" → "04:41"
                    const clean = (s) => s ? s.replace(/\s*\(.*\)/, '').trim() : '--:--';

                    this.prayerTimes = [
                        { key: 'imsak',   name: 'Imsak',   arabic: 'إمساك',   time: clean(t.Imsak),   icon: '🌙', main: false },
                        { key: 'subuh',   name: 'Subuh',   arabic: 'الفجر',   time: clean(t.Fajr),    icon: '🌤️', main: true  },
                        { key: 'syuruq',  name: 'Syuruq',  arabic: 'الشروق',  time: clean(t.Sunrise), icon: '🌅', main: false },
                        { key: 'dzuhur',  name: 'Dzuhur',  arabic: 'الظهر',   time: clean(t.Dhuhr),   icon: '☀️', main: true  },
                        { key: 'ashar',   name: 'Ashar',   arabic: 'العصر',   time: clean(t.Asr),     icon: '🌤️', main: true  },
                        { key: 'maghrib', name: 'Maghrib', arabic: 'المغرب',  time: clean(t.Maghrib), icon: '🌇', main: true  },
                        { key: 'isya',    name: 'Isya',    arabic: 'العشاء',  time: clean(t.Isha),    icon: '🌙', main: true  },
                    ];

                    this.hijriDate = `${h.day} ${h.month.en} ${h.year}`;
                    this.updateCountdown();
                }
            } catch (e) {
                console.error('Failed to load prayer times', e);
            }
            this.prayerLoading = false;
        },

        timeToMinutes(timeStr) {
            if (!timeStr || timeStr === '--:--') return -1;
            const parts = timeStr.split(':');
            return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        },

        updateCountdown() {
            if (!this.prayerTimes) return;
            const now = new Date();
            const nowMin = now.getHours() * 60 + now.getMinutes();

            const main = this.prayerTimes.filter(p => p.main && p.time !== '--:--');
            let next = main.find(p => this.timeToMinutes(p.time) > nowMin);
            if (!next) next = main[0]; // wrap to tomorrow's Subuh
            if (!next) return;

            this.nextPrayerKey = next.key;
            this.nextPrayerName = next.name;

            const nextMin = this.timeToMinutes(next.time);
            let diffSec = (nextMin - nowMin) * 60 - now.getSeconds();
            if (diffSec < 0) diffSec += 24 * 3600;

            const h = Math.floor(diffSec / 3600);
            const m = Math.floor((diffSec % 3600) / 60);
            const s = diffSec % 60;
            this.countdown = [h, m, s].map(v => String(v).padStart(2, '0')).join(':');
        },

        isNextPrayer(key) {
            return this.nextPrayerKey === key;
        },

        isPastPrayer(prayer) {
            if (!prayer.main || prayer.time === '--:--') return false;
            const now = new Date();
            const nowMin = now.getHours() * 60 + now.getMinutes();
            return this.timeToMinutes(prayer.time) < nowMin && !this.isNextPrayer(prayer.key);
        },

        todayDate() {
            return new Date().toLocaleDateString('id-ID', {
                weekday: 'long',
                year: 'numeric',
                month: 'long',
                day: 'numeric',
            });
        },
    };
}
</script>
</body>
</html>
