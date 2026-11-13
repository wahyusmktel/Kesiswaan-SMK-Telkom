<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OSIS SMK Telkom Lampung - Organisasi Siswa Intra Sekolah & Pemilu Raya</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if($appSetting?->favicon)
        <link rel="icon" type="image/x-icon" href="{{ Storage::url($appSetting->favicon) }}">
    @endif

    <style>
        [x-cloak] { display: none !important; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #0b0f19; color: #f8fafc; }
        h1, h2, h3, h4, .font-outfit { font-family: 'Outfit', sans-serif; }
        .glass-dark {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.02) 100%);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            transform: translateY(-4px);
            border-color: rgba(239, 68, 68, 0.3);
            box-shadow: 0 20px 40px -15px rgba(239, 68, 68, 0.15);
        }
        .gradient-red {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 50%, #991b1b 100%);
        }
        .text-gradient {
            background: linear-gradient(135deg, #ffffff 0%, #cbd5e1 50%, #94a3b8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .text-gradient-red {
            background: linear-gradient(135deg, #f87171 0%, #ef4444 50%, #dc2626 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
    </style>
</head>
<body class="antialiased selection:bg-red-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-dark border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-rose-700 flex items-center justify-center p-2 shadow-lg shadow-red-500/20 group-hover:scale-105 transition-transform">
                        <svg class="w-full h-full text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <div>
                        <span class="font-outfit font-black text-xl text-white tracking-tight flex items-center gap-1.5">
                            OSIS <span class="text-rose-500">TELKOM</span>
                        </span>
                        <span class="text-[10px] text-slate-400 block tracking-widest font-semibold uppercase">SMK Telkom Lampung</span>
                    </div>
                </a>
            </div>

            <div class="hidden md:flex items-center gap-8 text-sm font-semibold">
                <a href="{{ url('/') }}" class="text-slate-400 hover:text-white transition-colors">Beranda SISFO</a>
                <a href="#tentang" class="text-slate-400 hover:text-white transition-colors">Tentang OSIS</a>
                <a href="#visi-misi" class="text-slate-400 hover:text-white transition-colors">Visi Misi</a>
                <a href="#pengurus" class="text-slate-400 hover:text-white transition-colors">Pengurus</a>
                <a href="#agenda" class="text-slate-400 hover:text-white transition-colors">Agenda</a>
                <a href="#pilketos" class="text-rose-400 hover:text-rose-300 font-bold flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-500 animate-ping"></span>
                    Pemilu Pilketos
                </a>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('public.osis.live-count') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 transition-all flex items-center gap-1.5 shadow-lg shadow-cyan-500/10">
                    <svg class="w-4 h-4 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Live Count
                </a>
                <a href="{{ route('pilketos.booth') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold gradient-red text-white hover:brightness-110 shadow-lg shadow-red-500/30 transition-all flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    Bilik Suara
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-36 pb-24 px-4 sm:px-6 lg:px-8 overflow-hidden min-h-[90vh] flex items-center">
        <!-- Glow Orbs -->
        <div class="absolute top-1/4 -left-40 w-96 h-96 bg-red-600/20 rounded-full blur-[128px] pointer-events-none"></div>
        <div class="absolute bottom-10 right-0 w-96 h-96 bg-rose-600/15 rounded-full blur-[140px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto w-full relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-7 space-y-6 text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-rose-500/10 border border-rose-500/20 text-rose-400 text-xs font-bold tracking-wide uppercase">
                        <span class="w-2 h-2 rounded-full bg-rose-500 animate-pulse"></span>
                        Periode Kepengurusan {{ $setting->periode_aktif }}
                    </div>

                    <h1 class="font-outfit font-black text-4xl sm:text-6xl lg:text-7xl leading-tight tracking-tight text-white">
                        Wadah Pemimpin <br/>
                        <span class="text-gradient-red">Generasi Digital</span> Telkom
                    </h1>

                    <p class="text-slate-300 text-lg leading-relaxed max-w-2xl font-normal">
                        {{ $setting->deskripsi ?: 'Organisasi Siswa Intra Sekolah (OSIS) SMK Telkom Lampung menginspirasi, berkarya nyata, dan membimbing seluruh siswa berkarakter unggul, religius, serta berdaya saing global.' }}
                    </p>

                    <div class="pt-4 flex flex-wrap gap-4 justify-center lg:justify-start">
                        <a href="#pilketos" class="px-7 py-3.5 rounded-2xl font-bold text-sm gradient-red text-white shadow-xl shadow-red-500/25 hover:brightness-110 transition-all flex items-center gap-2">
                            <span>Ikuti Pemilu Pilketos</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </a>
                        <a href="#pengurus" class="px-7 py-3.5 rounded-2xl font-bold text-sm glass-card text-white hover:bg-white/10 transition-all">
                            Kenali Pengurus
                        </a>
                    </div>

                    <!-- Quick Stats -->
                    <div class="pt-8 grid grid-cols-3 gap-4 border-t border-white/10 max-w-lg mx-auto lg:mx-0">
                        <div>
                            <div class="font-outfit font-black text-2xl sm:text-3xl text-white">{{ $penguruses->count() }}</div>
                            <div class="text-xs text-slate-400 font-medium">Pengurus Aktif</div>
                        </div>
                        <div>
                            <div class="font-outfit font-black text-2xl sm:text-3xl text-rose-400">{{ $agendas->count() }}</div>
                            <div class="text-xs text-slate-400 font-medium">Program Kerja</div>
                        </div>
                        <div>
                            <div class="font-outfit font-black text-2xl sm:text-3xl text-cyan-400">{{ $kandidats->count() }}</div>
                            <div class="text-xs text-slate-400 font-medium">Kandidat Paslon</div>
                        </div>
                    </div>
                </div>

                <!-- Existing Leaders Showcase Card -->
                <div class="lg:col-span-5">
                    <div class="glass-card rounded-[32px] p-8 relative overflow-hidden border-rose-500/20">
                        <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-rose-500/10 rounded-full blur-2xl"></div>
                        
                        <div class="flex items-center justify-between pb-6 border-b border-white/10">
                            <div>
                                <span class="text-xs font-bold text-rose-400 uppercase tracking-widest block">Pimpinan OSIS</span>
                                <h3 class="font-outfit font-bold text-xl text-white">Masa Bakti {{ $setting->periode_aktif }}</h3>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/10 flex items-center justify-center p-2.5">
                                <img src="https://upload.wikimedia.org/wikipedia/id/d/dc/Logo_SMK_Telkom_Malang.png" alt="Logo" class="w-full h-full object-contain">
                            </div>
                        </div>

                        <div class="py-6 space-y-6">
                            <!-- Ketua Existing -->
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                                <img src="{{ $setting->foto_ketua_url }}" alt="{{ $setting->nama_ketua_existing }}" class="w-16 h-16 rounded-2xl object-cover border border-rose-500/30">
                                <div>
                                    <div class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Ketua Umum OSIS</div>
                                    <h4 class="font-outfit font-bold text-lg text-white">{{ $setting->nama_ketua_existing ?? 'M. Fathan Pratama' }}</h4>
                                    <p class="text-xs text-slate-400">SMK Telkom Lampung</p>
                                </div>
                            </div>

                            <!-- Wakil Ketua Existing -->
                            <div class="flex items-center gap-4 p-4 rounded-2xl bg-white/5 border border-white/5">
                                <img src="{{ $setting->foto_wakil_url }}" alt="{{ $setting->nama_wakil_existing }}" class="w-16 h-16 rounded-2xl object-cover border border-blue-500/30">
                                <div>
                                    <div class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Wakil Ketua OSIS</div>
                                    <h4 class="font-outfit font-bold text-lg text-white">{{ $setting->nama_wakil_existing ?? 'Nabila Putri Zahra' }}</h4>
                                    <p class="text-xs text-slate-400">SMK Telkom Lampung</p>
                                </div>
                            </div>

                            <!-- Pesan Ketua -->
                            @if($setting->pesan_ketua_existing)
                                <div class="p-4 rounded-2xl bg-rose-500/5 border border-rose-500/10 text-xs text-slate-300 italic leading-relaxed">
                                    "{{ $setting->pesan_ketua_existing }}"
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-white/10 flex items-center justify-between text-xs text-slate-400">
                            <span>Semboyan:</span>
                            <span class="font-bold text-rose-300">{{ $setting->semboyan }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION UNGGULAN: PEMILIHAN KETUA OSIS (PILKETOS) -->
    <section id="pilketos" class="py-24 px-4 sm:px-6 lg:px-8 relative bg-gradient-to-b from-slate-950 via-slate-900 to-slate-950 border-y border-white/10">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section Pilketos -->
            <div class="text-center max-w-3xl mx-auto space-y-4 mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase tracking-widest">
                    <span>⚡</span> Fitur Unggulan E-Voting Terpercaya
                </div>
                <h2 class="font-outfit font-black text-3xl sm:text-5xl text-white tracking-tight">
                    Pemilihan Ketua &amp; Wakil Ketua OSIS <br/>
                    <span class="text-gradient-red">{{ $pilketos?->tahun_pelajaran ?? '2026/2027' }}</span>
                </h2>
                <p class="text-slate-400 text-sm sm:text-base leading-relaxed">
                    Sistem pemungutan suara elektronik anti-kecurangan berbasis verifikasi Barcode fisik 1D sekali pakai (single-use token) di bilik suara tertutup.
                </p>

                <!-- Status Banner -->
                @if($pilketos)
                    <div class="pt-4 flex flex-wrap items-center justify-center gap-4">
                        <span class="px-5 py-2 rounded-xl text-xs font-black uppercase tracking-wider
                            {{ $pilketos->status === 'voting_berlangsung' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 animate-pulse' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' }}">
                            Status: {{ strtoupper(str_replace('_', ' ', $pilketos->status)) }}
                        </span>
                        <a href="{{ route('public.osis.live-count') }}" class="px-5 py-2 rounded-xl text-xs font-bold bg-cyan-500/10 border border-cyan-500/30 text-cyan-400 hover:bg-cyan-500/20 flex items-center gap-1.5 transition-all">
                            <span class="w-2 h-2 rounded-full bg-cyan-400 animate-ping"></span>
                            Buka Layar Live Count Realtime ↗
                        </a>
                        <a href="{{ route('pilketos.booth') }}" class="px-5 py-2 rounded-xl text-xs font-bold gradient-red text-white hover:brightness-110 flex items-center gap-1.5 transition-all shadow-lg shadow-red-500/20">
                            Akses Bilik Pemilihan ↗
                        </a>
                    </div>
                @endif
            </div>

            <!-- Timeline Tahapan Pemilu -->
            @if($timelines->isNotEmpty())
                <div class="mb-20">
                    <h3 class="font-outfit font-bold text-xl text-white mb-8 text-center flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        Timeline &amp; Agenda Tahapan Pemilu
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($timelines as $tl)
                            <div class="glass-card p-6 rounded-2xl relative overflow-hidden {{ $tl->status_tahap === 'berlangsung' ? 'border-rose-500/50 bg-rose-500/5' : '' }}">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center font-outfit font-black text-sm text-rose-400">
                                        {{ $loop->iteration }}
                                    </span>
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold uppercase
                                        {{ $tl->status_tahap === 'selesai' ? 'bg-slate-800 text-slate-400' : ($tl->status_tahap === 'berlangsung' ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30 animate-pulse' : 'bg-blue-500/20 text-blue-400') }}">
                                        {{ $tl->status_tahap }}
                                    </span>
                                </div>
                                <h4 class="font-outfit font-bold text-lg text-white mb-2">{{ $tl->judul_tahap }}</h4>
                                <div class="text-xs text-rose-400 font-semibold mb-2">
                                    {{ $tl->tanggal_mulai->format('d M Y') }}
                                    @if($tl->tanggal_selesai && $tl->tanggal_selesai != $tl->tanggal_mulai)
                                        - {{ $tl->tanggal_selesai->format('d M Y') }}
                                    @endif
                                </div>
                                @if($tl->deskripsi)
                                    <p class="text-xs text-slate-400 leading-relaxed">{{ $tl->deskripsi }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Daftar Paslon Kandidat Ketua OSIS -->
            <div class="space-y-10">
                <div class="text-center">
                    <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">Kandidat Pasangan Calon</span>
                    <h3 class="font-outfit font-black text-3xl text-white mt-1">Pilihan Terbaik untuk Masa Depan Sekolah</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @forelse($kandidats as $kandidat)
                        <div class="glass-card rounded-[28px] overflow-hidden flex flex-col justify-between border-white/10 hover:border-rose-500/40 group">
                            <!-- Nomor Urut Badge & Foto -->
                            <div class="relative bg-slate-900/60 p-6 flex flex-col items-center border-b border-white/5">
                                <div class="absolute top-4 left-4 w-12 h-12 rounded-2xl gradient-red flex items-center justify-center text-white font-outfit font-black text-2xl shadow-lg shadow-red-500/30">
                                    0{{ $kandidat->nomor_urut }}
                                </div>

                                <div class="w-40 h-40 rounded-3xl overflow-hidden mt-4 border-2 border-white/10 shadow-2xl group-hover:scale-105 transition-transform">
                                    <img src="{{ $kandidat->foto_paslon_url }}" alt="Paslon {{ $kandidat->nomor_urut }}" class="w-full h-full object-cover">
                                </div>

                                <div class="mt-4 text-center">
                                    <span class="text-[10px] font-bold text-rose-400 uppercase tracking-widest block">Calon Ketua &amp; Wakil</span>
                                    <h4 class="font-outfit font-black text-xl text-white mt-1">{{ $kandidat->nama_ketua }}</h4>
                                    @if($kandidat->nama_wakil)
                                        <div class="text-xs text-slate-300 font-semibold">&amp; {{ $kandidat->nama_wakil }}</div>
                                    @endif
                                    <div class="text-[11px] text-slate-400 mt-1 font-mono">
                                        {{ $kandidat->kelas_ketua }} @if($kandidat->kelas_wakil) • {{ $kandidat->kelas_wakil }} @endif
                                    </div>
                                </div>

                                @if($kandidat->tagline)
                                    <div class="mt-3 px-3 py-1.5 rounded-xl bg-white/5 text-[11px] text-slate-300 italic text-center">
                                        "{{ $kandidat->tagline }}"
                                    </div>
                                @endif
                            </div>

                            <!-- Visi, Misi & Program -->
                            <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider block">Visi Utama</span>
                                        <p class="text-xs text-slate-300 leading-relaxed font-medium line-clamp-4 mt-1">
                                            {{ $kandidat->visi }}
                                        </p>
                                    </div>

                                    <div>
                                        <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider block">Misi</span>
                                        <div class="text-xs text-slate-400 leading-relaxed mt-1 whitespace-pre-line line-clamp-4">
                                            {{ $kandidat->misi }}
                                        </div>
                                    </div>

                                    @if($kandidat->program_unggulan)
                                        <div>
                                            <span class="text-[10px] font-bold text-cyan-400 uppercase tracking-wider block">Program Kerja Unggulan</span>
                                            <div class="text-xs text-slate-400 leading-relaxed mt-1 whitespace-pre-line line-clamp-3">
                                                {{ $kandidat->program_unggulan }}
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="pt-4 border-t border-white/5">
                                    <a href="{{ route('pilketos.booth') }}" class="w-full py-3 rounded-xl text-xs font-bold bg-white/5 hover:bg-rose-600 text-white border border-white/10 hover:border-rose-500 transition-all flex items-center justify-center gap-2 group-hover:bg-rose-600">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        Pilih di Bilik Suara
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-12 glass-card rounded-2xl">
                            <p class="text-slate-400 text-sm">Belum ada kandidat paslon yang terdaftar pada periode ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION VISI & MISI ORGANISASI OSIS -->
    <section id="visi-misi" class="py-24 px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <div class="lg:col-span-5 space-y-6">
                    <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">Fondasi Kepemimpinan</span>
                    <h2 class="font-outfit font-black text-3xl sm:text-5xl text-white tracking-tight">
                        Visi &amp; Misi <br/>
                        <span class="text-gradient">OSIS Telkom Lampung</span>
                    </h2>
                    <p class="text-slate-400 text-sm leading-relaxed">
                        Menjadi teladan integritas, inovasi, dan kepedulian yang mempersiapkan siswa SMK Telkom Lampung menjadi pionir industri teknologi masa depan.
                    </p>
                    <div class="p-6 rounded-2xl bg-rose-500/10 border border-rose-500/20 text-rose-300 text-sm font-semibold flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl gradient-red flex items-center justify-center shrink-0 text-white font-bold text-xl">
                            ★
                        </div>
                        <div>
                            "{{ $setting->semboyan ?? 'Kreatif, Inovatif, Berkarakter & Berintegritas' }}"
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-7 space-y-6">
                    <!-- Visi Card -->
                    <div class="glass-card p-8 rounded-3xl border-rose-500/20">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold text-sm">V</span>
                            <h3 class="font-outfit font-bold text-xl text-white">Visi Organisasi</h3>
                        </div>
                        <p class="text-slate-300 text-sm sm:text-base leading-relaxed">
                            {{ $setting->visi }}
                        </p>
                    </div>

                    <!-- Misi Card -->
                    <div class="glass-card p-8 rounded-3xl border-white/10">
                        <div class="flex items-center gap-3 mb-4">
                            <span class="w-8 h-8 rounded-lg bg-blue-500/20 text-blue-400 flex items-center justify-center font-bold text-sm">M</span>
                            <h3 class="font-outfit font-bold text-xl text-white">Misi Organisasi</h3>
                        </div>
                        <div class="text-slate-300 text-sm sm:text-base leading-relaxed whitespace-pre-line space-y-2">
                            {{ $setting->misi }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION STRUKTUR PENGURUS OSIS -->
    <section id="pengurus" class="py-24 px-4 sm:px-6 lg:px-8 bg-slate-950/70 border-t border-white/10">
        <div class="max-w-7xl mx-auto space-y-16">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">Struktur Kepengurusan</span>
                <h2 class="font-outfit font-black text-3xl sm:text-4xl text-white">Pengurus OSIS Masa Bakti {{ $setting->periode_aktif }}</h2>
                <p class="text-slate-400 text-sm">Para penggerak roda organisasi dan pelayan aspirasi siswa SMK Telkom Lampung.</p>
            </div>

            <!-- BPH Section -->
            @if($bph->isNotEmpty())
                <div>
                    <h3 class="font-outfit font-bold text-xl text-rose-400 mb-8 text-center uppercase tracking-wider">
                        Badan Pengurus Harian (BPH)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                        @foreach($bph as $p)
                            <div class="glass-card p-6 rounded-2xl text-center space-y-4 border-rose-500/20">
                                <div class="w-24 h-24 mx-auto rounded-2xl overflow-hidden border-2 border-rose-500/30 shadow-lg shadow-rose-500/10">
                                    <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="w-full h-full object-cover">
                                </div>
                                <div>
                                    <h4 class="font-outfit font-bold text-white text-base">{{ $p->nama }}</h4>
                                    <div class="text-xs font-bold text-rose-400 mt-0.5">{{ $p->jabatan }}</div>
                                    <div class="text-[11px] text-slate-400 mt-1 font-mono">{{ $p->kelas ?: 'SMK Telkom' }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Sekbid Grouping -->
            @if($sekbids->isNotEmpty())
                <div class="space-y-12">
                    <h3 class="font-outfit font-bold text-xl text-slate-300 text-center uppercase tracking-wider">
                        Koordinator Seksi Bidang (Sekbid)
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($penguruses->diff($bph) as $p)
                            <div class="glass-card p-5 rounded-2xl flex items-center gap-4">
                                <img src="{{ $p->foto_url }}" alt="{{ $p->nama }}" class="w-16 h-16 rounded-xl object-cover border border-white/10 shrink-0">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ $p->sekbid ?: 'Sekbid' }}</span>
                                    <h4 class="font-outfit font-bold text-white text-sm">{{ $p->nama }}</h4>
                                    <div class="text-xs text-rose-400 font-semibold">{{ $p->jabatan }}</div>
                                    <div class="text-[10px] text-slate-400 font-mono">{{ $p->kelas }}</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- SECTION AGENDA & PROGRAM KERJA -->
    <section id="agenda" class="py-24 px-4 sm:px-6 lg:px-8 border-t border-white/10">
        <div class="max-w-7xl mx-auto space-y-12">
            <div class="text-center max-w-2xl mx-auto space-y-3">
                <span class="text-xs font-bold text-rose-400 uppercase tracking-widest">Kalender Kegiatan</span>
                <h2 class="font-outfit font-black text-3xl sm:text-4xl text-white">Agenda &amp; Program Kerja OSIS</h2>
                <p class="text-slate-400 text-sm">Kegiatan berkala, program pengembangan kompetensi, dan perayaan akbar sekolah.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse($agendas as $agenda)
                    <div class="glass-card rounded-2xl overflow-hidden flex flex-col justify-between">
                        @if($agenda->foto_url)
                            <div class="h-48 overflow-hidden">
                                <img src="{{ $agenda->foto_url }}" alt="{{ $agenda->judul }}" class="w-full h-full object-cover">
                            </div>
                        @endif
                        <div class="p-6 space-y-4 flex-1 flex flex-col justify-between">
                            <div class="space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        {{ $agenda->kategori }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium">
                                        {{ $agenda->tanggal_mulai->format('d M Y') }}
                                    </span>
                                </div>
                                <h3 class="font-outfit font-bold text-xl text-white leading-snug">{{ $agenda->judul }}</h3>
                                @if($agenda->deskripsi)
                                    <p class="text-xs text-slate-400 leading-relaxed">{{ $agenda->deskripsi }}</p>
                                @endif
                            </div>

                            @if($agenda->lokasi)
                                <div class="pt-4 border-t border-white/5 flex items-center gap-2 text-xs text-slate-400">
                                    <svg class="w-4 h-4 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    <span>{{ $agenda->lokasi }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-12 glass-card rounded-2xl">
                        <p class="text-slate-400 text-sm">Belum ada agenda OSIS yang dipublikasikan saat ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-12 px-4 sm:px-6 lg:px-8 border-t border-white/10 glass-dark">
        <div class="max-w-7xl mx-auto flex flex-col md:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-lg gradient-red flex items-center justify-center text-white font-bold text-sm">
                    OT
                </div>
                <div>
                    <div class="font-outfit font-bold text-white text-sm">OSIS SMK Telkom Lampung</div>
                    <div class="text-xs text-slate-400">Pusat Kepemimpinan &amp; E-Voting Digital Terintegrasi SISFO</div>
                </div>
            </div>

            <div class="flex items-center gap-6 text-xs text-slate-400">
                <a href="{{ route('public.osis.live-count') }}" class="hover:text-cyan-400 transition-colors">Live Count</a>
                <a href="{{ route('pilketos.booth') }}" class="hover:text-rose-400 transition-colors">Bilik Suara</a>
                <a href="{{ route('login') }}" class="hover:text-white transition-colors">Login Admin OSIS</a>
            </div>

            <div class="text-xs text-slate-500">
                &copy; {{ date('Y') }} SISFO SMK Telkom Lampung. All rights reserved.
            </div>
        </div>
    </footer>

</body>
</html>
