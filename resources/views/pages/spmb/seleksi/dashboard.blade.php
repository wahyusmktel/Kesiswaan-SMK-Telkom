<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Beranda Ujian Seleksi | SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full font-sans antialiased text-slate-800 bg-slate-100/70 min-h-screen flex flex-col justify-between">

    {{-- TOP NAVBAR --}}
    <header class="bg-white border-b border-slate-200 shadow-sm sticky top-0 z-30">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-lg bg-red-600 flex items-center justify-center text-white font-black text-lg shadow-sm">
                    TL
                </div>
                <div>
                    <h1 class="text-xs sm:text-sm font-extrabold text-slate-900 uppercase tracking-tight">
                        SMK Telkom Lampung
                    </h1>
                    <p class="text-[10px] sm:text-xs font-semibold text-red-600 uppercase">
                        Portal Seleksi Calon Siswa Baru
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                {{-- Logout Form --}}
                <form method="POST" action="{{ route('seleksi.logout') }}" onsubmit="return confirm('Apakah Anda yakin ingin keluar dari sistem ujian?')">
                    @csrf
                    <button type="submit" 
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold text-red-600 hover:text-red-700 bg-red-50 hover:bg-red-100 rounded-lg border border-red-200 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        <span>Keluar</span>
                    </button>
                </form>
            </div>
        </div>
    </header>

    {{-- MAIN CONTAINER --}}
    <main class="flex-1 py-5 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto w-full space-y-5">

        {{-- FLASH MESSAGES --}}
        @if(session('success'))
            <div class="p-4 rounded-xl text-xs sm:text-sm font-medium bg-emerald-50 text-emerald-800 border border-emerald-200 flex items-center justify-between gap-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800 font-bold">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-xl text-xs sm:text-sm font-medium bg-rose-50 text-rose-800 border border-rose-200 flex items-center justify-between gap-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-rose-600 hover:text-rose-800 font-bold">&times;</button>
            </div>
        @endif

        @if(session('info'))
            <div class="p-4 rounded-xl text-xs sm:text-sm font-medium bg-blue-50 text-blue-800 border border-blue-200 flex items-center justify-between gap-2 shadow-sm">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('info') }}</span>
                </div>
                <button type="button" @click="$el.parentElement.remove()" class="text-blue-600 hover:text-blue-800 font-bold">&times;</button>
            </div>
        @endif

        {{-- CALON SISWA IDENTITY CARD --}}
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3.5">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-red-600 to-rose-500 text-white flex items-center justify-center font-extrabold text-lg flex-shrink-0 shadow-md shadow-red-500/20">
                        {{ strtoupper(substr($peserta->nama_lengkap, 0, 2)) }}
                    </div>
                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-base sm:text-lg font-black text-slate-900 leading-snug">
                                {{ $peserta->nama_lengkap }}
                            </h2>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-red-700">
                                Calon Siswa
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">
                            No. Pendaftaran: <strong class="text-slate-800 font-mono">{{ $peserta->nomor_pendaftaran }}</strong> &bull; Akun ID: <span class="font-mono text-slate-700 font-semibold">{{ $peserta->raw_password }}</span>
                        </p>
                        @if($peserta->asal_sekolah)
                            <p class="text-[11px] text-slate-400 mt-0.5">Asal Sekolah: {{ $peserta->asal_sekolah }}</p>
                        @endif
                    </div>
                </div>

                {{-- Status Progress --}}
                @php
                    $stepDone = 0;
                    if($bwSelesai) $stepDone++;
                    if($mtkSelesai) $stepDone++;
                    if($bingSelesai) $stepDone++;
                    if($bindoSelesai) $stepDone++;
                    if($tikSelesai) $stepDone++;
                    $percent = ($stepDone / 5) * 100;
                @endphp
                <div class="sm:text-right bg-slate-50 p-3 sm:p-0 rounded-xl sm:bg-transparent">
                    <span class="text-xs font-semibold text-slate-500">Progres Seleksi:</span>
                    <div class="text-sm font-black text-slate-900 mt-0.5">
                        {{ $stepDone }} dari 5 Tahap Selesai
                    </div>
                    <div class="w-full sm:w-40 bg-slate-200 rounded-full h-2 mt-1.5 overflow-hidden">
                        <div class="bg-red-600 h-2 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ALL FINISHED CONFIRMATION BOX --}}
        @if($allFinished)
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-2xl p-5 text-white shadow-lg shadow-emerald-500/10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold">Seluruh Rangkaian Ujian Telah Selesai!</h3>
                        <p class="text-xs text-emerald-100 mt-0.5">Terima kasih telah mengikuti Tes Buta Warna dan Tes Potensi Akademik (TPA). Hasil ujian Anda telah tersimpan secara permanen.</p>
                    </div>
                </div>
                <a href="{{ route('seleksi.selesai') }}"
                    class="inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-white text-emerald-800 hover:bg-emerald-50 text-xs font-bold shadow-sm transition-all flex-shrink-0">
                    Lihat Bukti Selesai
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </div>
        @endif

        {{-- DAFTAR UJIAN (URUTAN WAJIB) --}}
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                    </svg>
                    Alur & Daftar Tes Seleksi Wajib
                </h3>
                <span class="text-[11px] text-slate-500">Wajib dikerjakan secara berurutan</span>
            </div>

            {{-- 1. TES BUTA WARNA --}}
            <div class="bg-white rounded-2xl border transition-all p-4 sm:p-5 {{ $bwSelesai ? 'border-emerald-200 bg-emerald-50/20' : 'border-slate-200 shadow-sm' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $bwSelesai ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                            1
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">Tes Buta Warna (Ishihara)</h4>
                                @if($bwSelesai)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        <svg class="w-3 h-3 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                        Wajib Pertama
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Uji persepsi warna melalui {{ $totalSoalBw }} lempeng Ishihara plate berangka.
                            </p>
                        </div>
                    </div>

                    <div>
                        @if($bwSelesai)
                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-emerald-100 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Tuntas Dikerjakan
                            </span>
                        @else
                            <a href="{{ route('seleksi.buta-warna') }}"
                                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-md shadow-red-600/20 transition-all">
                                <span>Mulai Tes Buta Warna</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- TAHAP 2: TES POTENSI AKADEMIK (TPA) CONTAINER --}}
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 sm:p-5 space-y-4">
                <div class="border-b border-slate-100 pb-3">
                    <div class="flex items-center justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider text-red-600">Tahap 2</span>
                            <h4 class="text-sm font-bold text-slate-900">Tes Potensi Akademik (TPA)</h4>
                        </div>
                        @if(!$bwSelesai)
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Terkunci
                            </span>
                        @endif
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Terdiri dari 4 mata pelajaran yang wajib dikerjakan berurutan: Matematika, Bahasa Inggris, Bahasa Indonesia, dan Dasar TIK.
                    </p>
                </div>

                {{-- Alert jika Buta Warna Belum Selesai --}}
                @if(!$bwSelesai)
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 flex items-start gap-2.5">
                        <svg class="w-4 h-4 text-amber-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>Akses Ujian TPA masih terkunci. Silakan selesaikan <strong>Tahap 1 (Tes Buta Warna)</strong> terlebih dahulu untuk membuka akses ujian ini.</span>
                    </div>
                @endif

                {{-- LIST 4 MAPEL TPA --}}
                <div class="grid grid-cols-1 gap-3">

                    {{-- 2.1 MATEMATIKA (MTK) --}}
                    @php
                        $mtkActive = $bwSelesai && !$mtkSelesai;
                        $katMtk = $kategoris->get('MTK');
                    @endphp
                    <div class="p-3.5 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all
                        {{ $mtkSelesai ? 'border-emerald-200 bg-emerald-50/20' : ($mtkActive ? 'border-blue-300 bg-blue-50/30 ring-1 ring-blue-400/20' : 'border-slate-200 bg-slate-50 opacity-60') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $mtkSelesai ? 'bg-emerald-100 text-emerald-800' : ($mtkActive ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-200 text-slate-600') }}">
                                A
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900">1. Matematika (MTK)</h5>
                                    <span class="text-[11px] text-slate-500">({{ $katMtk ? $katMtk->active_soals_count : 10 }} Soal &bull; {{ $katMtk ? $katMtk->durasi_menit : 25 }} Menit)</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Logika pola deret, aritmetika sosial, dan aljabar dasar.</p>
                            </div>
                        </div>

                        <div>
                            @if($mtkSelesai)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </span>
                            @elseif($mtkActive)
                                <a href="{{ route('seleksi.tpa', 'mtk') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 shadow-sm transition-all">
                                    <span>Mulai Ujian Matematika</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2.2 BAHASA INGGRIS (BING) --}}
                    @php
                        $bingActive = $mtkSelesai && !$bingSelesai;
                        $katBing = $kategoris->get('BING');
                    @endphp
                    <div class="p-3.5 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all
                        {{ $bingSelesai ? 'border-emerald-200 bg-emerald-50/20' : ($bingActive ? 'border-purple-300 bg-purple-50/30 ring-1 ring-purple-400/20' : 'border-slate-200 bg-slate-50 opacity-60') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $bingSelesai ? 'bg-emerald-100 text-emerald-800' : ($bingActive ? 'bg-purple-600 text-white shadow-sm' : 'bg-slate-200 text-slate-600') }}">
                                B
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900">2. Bahasa Inggris</h5>
                                    <span class="text-[11px] text-slate-500">({{ $katBing ? $katBing->active_soals_count : 10 }} Soal &bull; {{ $katBing ? $katBing->durasi_menit : 20 }} Menit)</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Vocabulary, contextual conversations, and reading comprehension.</p>
                            </div>
                        </div>

                        <div>
                            @if($bingSelesai)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </span>
                            @elseif($bingActive)
                                <a href="{{ route('seleksi.tpa', 'bing') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-purple-600 hover:bg-purple-700 shadow-sm transition-all">
                                    <span>Mulai Ujian B. Inggris</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2.3 BAHASA INDONESIA (BINDO) --}}
                    @php
                        $bindoActive = $bingSelesai && !$bindoSelesai;
                        $katBindo = $kategoris->get('BINDO');
                    @endphp
                    <div class="p-3.5 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all
                        {{ $bindoSelesai ? 'border-emerald-200 bg-emerald-50/20' : ($bindoActive ? 'border-red-300 bg-red-50/30 ring-1 ring-red-400/20' : 'border-slate-200 bg-slate-50 opacity-60') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $bindoSelesai ? 'bg-emerald-100 text-emerald-800' : ($bindoActive ? 'bg-red-600 text-white shadow-sm' : 'bg-slate-200 text-slate-600') }}">
                                C
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900">3. Bahasa Indonesia</h5>
                                    <span class="text-[11px] text-slate-500">({{ $katBindo ? $katBindo->active_soals_count : 10 }} Soal &bull; {{ $katBindo ? $katBindo->durasi_menit : 20 }} Menit)</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Pemahaman wacana, penalaran verbal, dan kaidah PUEBI.</p>
                            </div>
                        </div>

                        <div>
                            @if($bindoSelesai)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </span>
                            @elseif($bindoActive)
                                <a href="{{ route('seleksi.tpa', 'bindo') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-all">
                                    <span>Mulai Ujian B. Indonesia</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- 2.4 DASAR TIK (TIK) --}}
                    @php
                        $tikActive = $bindoSelesai && !$tikSelesai;
                        $katTik = $kategoris->get('TIK');
                    @endphp
                    <div class="p-3.5 rounded-xl border flex flex-col sm:flex-row sm:items-center justify-between gap-3 transition-all
                        {{ $tikSelesai ? 'border-emerald-200 bg-emerald-50/20' : ($tikActive ? 'border-emerald-300 bg-emerald-50/30 ring-1 ring-emerald-400/20' : 'border-slate-200 bg-slate-50 opacity-60') }}">
                        <div class="flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-xs flex-shrink-0
                                {{ $tikSelesai ? 'bg-emerald-100 text-emerald-800' : ($tikActive ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-200 text-slate-600') }}">
                                D
                            </span>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h5 class="text-xs sm:text-sm font-bold text-slate-900">4. Dasar-Dasar TIK</h5>
                                    <span class="text-[11px] text-slate-500">({{ $katTik ? $katTik->active_soals_count : 20 }} Soal &bull; {{ $katTik ? $katTik->durasi_menit : 20 }} Menit)</span>
                                </div>
                                <p class="text-[11px] text-slate-500">Komponen perangkat keras, jaringan internet, dan literasi digital.</p>
                            </div>
                        </div>

                        <div>
                            @if($tikSelesai)
                                <span class="inline-flex items-center gap-1 text-xs font-bold text-emerald-700 bg-emerald-100 px-3 py-1.5 rounded-lg">
                                    <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Selesai
                                </span>
                            @elseif($tikActive)
                                <a href="{{ route('seleksi.tpa', 'tik') }}"
                                    class="w-full sm:w-auto inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all">
                                    <span>Mulai Ujian Dasar TIK</span>
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    Terkunci
                                </span>
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            {{-- 3. TAHAP PENYELESAIAN & PENGUMUMAN --}}
            <div class="bg-white rounded-2xl border transition-all p-4 sm:p-5 {{ $allFinished ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200 bg-slate-50 opacity-60' }}">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3.5">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-sm flex-shrink-0 {{ $allFinished ? 'bg-emerald-600 text-white shadow-md' : 'bg-slate-200 text-slate-600' }}">
                            3
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900">Pengumuman Kelulusan Seleksi</h4>
                                @if($allFinished)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        Siap
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-500 mt-0.5">
                                Pengumuman hasil seleksi kelulusan akan diinformasikan sesuai jadwal resmi yang telah ditetapkan panitia.
                            </p>
                        </div>
                    </div>

                    <div>
                        @if($allFinished)
                            <a href="{{ route('seleksi.selesai') }}"
                                class="inline-flex items-center justify-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-sm transition-all">
                                <span>Lihat Informasi</span>
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </a>
                        @else
                            <span class="text-[11px] font-semibold text-slate-400 flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Menunggu Tes Tuntas
                            </span>
                        @endif
                    </div>
                </div>
            </div>

        </div>

    </main>

    {{-- FOOTER --}}
    <footer class="py-4 text-center text-xs text-slate-500 border-t border-slate-200 bg-white">
        <p>&copy; {{ date('Y') }} Panitia SPMB SMK Telkom Lampung &bull; Computer Based Test (CBT)</p>
    </footer>

</body>
</html>
