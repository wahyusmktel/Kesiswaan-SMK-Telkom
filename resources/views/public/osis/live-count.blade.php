<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Live Count Pemilihan Ketua OSIS - SMK Telkom Lampung</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@600;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #070913; color: #f8fafc; }
        .font-outfit { font-family: 'Outfit', sans-serif; }
        .font-mono-num { font-family: 'JetBrains Mono', monospace; }
        .glass-dark {
            background: rgba(15, 23, 42, 0.7);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #991b1b 100%);
        }
        .glow-cyan {
            box-shadow: 0 0 30px rgba(6, 182, 212, 0.25);
        }
        .glow-red {
            box-shadow: 0 0 30px rgba(239, 68, 68, 0.25);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col justify-between selection:bg-cyan-500 selection:text-white"
      x-data="liveCountApp()"
      x-init="startPolling()">

    <!-- Header Live Count -->
    <header class="glass-dark border-b border-white/10 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('public.osis.index') }}" class="flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center p-2.5 shadow-lg shadow-cyan-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-outfit font-black text-xl text-white tracking-tight">LIVE COUNT</span>
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-rose-500 text-white uppercase tracking-wider flex items-center gap-1 animate-pulse">
                                <span class="w-1.5 h-1.5 rounded-full bg-white"></span> REALTIME
                            </span>
                        </div>
                        <span class="text-xs text-slate-400 block font-medium">Pilketos SMK Telkom Lampung</span>
                    </div>
                </a>
            </div>

            <!-- Polling Indicator & Booth Link -->
            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 glass-card px-3 py-1.5 rounded-xl">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Update: <span class="text-slate-200 font-mono" x-text="lastUpdated">Menghubungkan...</span></span>
                </div>

                <a href="{{ route('pilketos.booth') }}" target="_blank" class="px-4 py-2 rounded-xl text-xs font-bold gradient-red text-white hover:brightness-110 shadow-lg shadow-red-500/25 flex items-center gap-1.5 transition-all">
                    <span>Buka Bilik Suara</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
            </div>
        </div>
    </header>

    <!-- Main Live Count Display -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 w-full flex-1 space-y-10">

        <!-- Title & Period Info -->
        <div class="text-center space-y-3">
            <span class="px-4 py-1.5 rounded-full bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 text-xs font-bold tracking-widest uppercase">
                Rekapitulasi Suara Resmi
            </span>
            <h1 class="font-outfit font-black text-3xl sm:text-5xl text-white tracking-tight" x-text="namaPeriode">
                {{ $pilketos?->nama_periode ?? 'Pemilihan Ketua OSIS' }}
            </h1>
            <p class="text-slate-400 text-sm max-w-2xl mx-auto">
                Hasil perhitungan suara langsung dari seluruh bilik suara E-Voting SMK Telkom Lampung. Diperbarui secara otomatis tanpa perlu refresh halaman.
            </p>
        </div>

        <!-- 3 Primary Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Total DPT -->
            <div class="glass-card p-6 rounded-3xl border-slate-700/50 relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total DPT Terdaftar</span>
                    <div class="w-9 h-9 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>
                <div class="font-outfit font-black text-4xl text-white font-mono-num" x-text="totalDpt">
                    {{ $totalDpt }}
                </div>
                <div class="text-xs text-slate-400 mt-2">Seluruh siswa aktif berhak memilih</div>
            </div>

            <!-- Card 2: Suara Masuk -->
            <div class="glass-card p-6 rounded-3xl border-emerald-500/30 relative overflow-hidden bg-emerald-950/10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider">Total Suara Masuk</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
                <div class="font-outfit font-black text-4xl text-emerald-400 font-mono-num" x-text="totalVotes">
                    {{ $totalVotes }}
                </div>
                <div class="text-xs text-emerald-400/80 mt-2">Suara sah masuk ke kotak suara digital</div>
            </div>

            <!-- Card 3: Persentase Partisipasi -->
            <div class="glass-card p-6 rounded-3xl border-cyan-500/30 relative overflow-hidden bg-cyan-950/10">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-cyan-400 uppercase tracking-wider">Tingkat Partisipasi</span>
                    <div class="w-9 h-9 rounded-xl bg-cyan-500/20 text-cyan-400 flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
                <div class="font-outfit font-black text-4xl text-cyan-300 font-mono-num">
                    <span x-text="turnoutPercentage">{{ $turnout }}</span>%
                </div>
                <!-- Mini Progress Bar -->
                <div class="w-full bg-slate-800 rounded-full h-2 mt-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-cyan-500 to-blue-500 h-2 rounded-full transition-all duration-700"
                         :style="'width: ' + turnoutPercentage + '%'"></div>
                </div>
            </div>
        </div>

        <!-- Paslon Live Count Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <template x-for="k in kandidats" :key="k.id">
                <div class="glass-card rounded-[32px] overflow-hidden p-6 space-y-6 flex flex-col justify-between border-white/10 hover:border-cyan-500/40 transition-all"
                     :class="k.percentage > 40 ? 'glow-cyan border-cyan-500/30' : ''">
                    
                    <!-- Paslon Header & Nomor Urut -->
                    <div class="flex items-start justify-between">
                        <div class="w-14 h-14 rounded-2xl flex items-center justify-center text-white font-outfit font-black text-3xl shadow-xl"
                             :class="k.nomor_urut == 1 ? 'bg-gradient-to-br from-red-600 to-rose-700' : (k.nomor_urut == 2 ? 'bg-gradient-to-br from-blue-600 to-indigo-700' : 'bg-gradient-to-br from-amber-600 to-orange-700')">
                            0<span x-text="k.nomor_urut"></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Perolehan</span>
                            <div class="font-outfit font-black text-3xl text-white font-mono-num">
                                <span x-text="k.percentage">0</span><span class="text-base text-cyan-400 font-sans">%</span>
                            </div>
                            <span class="text-xs text-slate-400 font-mono-num font-semibold">
                                <span x-text="k.votes_count">0</span> suara
                            </span>
                        </div>
                    </div>

                    <!-- Foto Paslon -->
                    <div class="flex justify-center">
                        <div class="w-36 h-36 rounded-3xl overflow-hidden border-2 border-white/10 shadow-2xl relative">
                            <img :src="k.foto_paslon" :alt="k.nama_ketua" class="w-full h-full object-cover">
                        </div>
                    </div>

                    <!-- Identitas Paslon -->
                    <div class="text-center space-y-1">
                        <h3 class="font-outfit font-black text-xl text-white" x-text="k.nama_ketua"></h3>
                        <div class="text-sm text-slate-300 font-semibold" x-show="k.nama_wakil">
                            &amp; <span x-text="k.nama_wakil"></span>
                        </div>
                        <div class="text-xs text-slate-400 font-mono" x-text="k.kelas_ketua + (k.kelas_wakil ? ' • ' + k.kelas_wakil : '')"></div>
                        <p class="text-[11px] text-slate-400 italic pt-2 line-clamp-2" x-text="k.tagline"></p>
                    </div>

                    <!-- Progress Bar Suara -->
                    <div class="space-y-1.5 pt-4 border-t border-white/5">
                        <div class="flex items-center justify-between text-xs font-bold text-slate-300">
                            <span>Grafik Suara</span>
                            <span x-text="k.percentage + '%'"></span>
                        </div>
                        <div class="w-full bg-slate-900 rounded-full h-3.5 overflow-hidden p-0.5 border border-white/10">
                            <div class="h-full rounded-full transition-all duration-700"
                                 :class="k.nomor_urut == 1 ? 'bg-gradient-to-r from-red-500 to-rose-500' : (k.nomor_urut == 2 ? 'bg-gradient-to-r from-blue-500 to-cyan-500' : 'bg-gradient-to-r from-amber-500 to-orange-500')"
                                 :style="'width: ' + k.percentage + '%'"></div>
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <!-- Bilik Suara Stats Footer Breakdown -->
        <div class="glass-card rounded-3xl p-6 sm:p-8 space-y-4">
            <h3 class="font-outfit font-bold text-lg text-white flex items-center gap-2">
                <svg class="w-5 h-5 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                Aktivitas Bilik Pemilihan
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <template x-for="b in bilikStats" :key="b.bilik">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/5">
                        <span class="text-xs text-slate-400 uppercase font-semibold" x-text="b.bilik"></span>
                        <div class="font-outfit font-black text-2xl text-cyan-300 font-mono-num mt-1" x-text="b.count + ' suara'"></div>
                    </div>
                </template>
            </div>
        </div>

    </main>

    <!-- Footer -->
    <footer class="glass-dark border-t border-white/10 py-6 text-center text-xs text-slate-500">
        Live Count Resmi Pilketos SMK Telkom Lampung &bull; Sistem Pemilu Digital Anti-Kecurangan SISFO
    </footer>

    <!-- Alpine.js Live Count Controller -->
    <script>
        function liveCountApp() {
            return {
                namaPeriode: '{{ $pilketos?->nama_periode ?? "Pemilihan Ketua OSIS" }}',
                totalDpt: {{ $totalDpt }},
                totalVotes: {{ $totalVotes }},
                turnoutPercentage: {{ $turnout }},
                lastUpdated: 'Baru saja',
                kandidats: {!! json_encode($kandidatsJson ?? []) !!},
                bilikStats: [
                    { bilik: 'Bilik 1', count: {{ $totalVotes }} }
                ],
                pollingInterval: null,

                startPolling() {
                    this.fetchData();
                    this.pollingInterval = setInterval(() => {
                        this.fetchData();
                    }, 3500); // Poll every 3.5 seconds
                },

                async fetchData() {
                    try {
                        const res = await fetch('{{ route("api.osis.live-count-data") }}');
                        const data = await res.json();
                        if (data.status === 'success') {
                            this.namaPeriode = data.nama_periode;
                            this.totalDpt = data.total_dpt;
                            this.totalVotes = data.total_votes;
                            this.turnoutPercentage = data.turnout_percentage;
                            this.kandidats = data.kandidats;
                            this.lastUpdated = data.last_updated;
                            if (data.bilik_stats && data.bilik_stats.length > 0) {
                                this.bilikStats = data.bilik_stats;
                            }
                        }
                    } catch (e) {
                        console.warn('Live count polling failed:', e);
                    }
                }
            };
        }
    </script>
</body>
</html>
