<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Ujian Seleksi Selesai | SPMB SMK Telkom Lampung</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet" />

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased text-slate-800 bg-gradient-to-br from-slate-100 via-slate-50 to-red-50/30 flex flex-col justify-between py-6 px-4 sm:px-6">

    {{-- TOP NAVBAR --}}
    <header class="max-w-xl mx-auto w-full flex items-center justify-between pb-6">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-red-600 text-white flex items-center justify-center font-black shadow-md shadow-red-600/20">
                <span class="text-xs tracking-tighter">SMK</span>
            </div>
            <div>
                <h1 class="text-xs sm:text-sm font-extrabold text-slate-900 tracking-tight leading-none uppercase">
                    SMK Telkom Lampung
                </h1>
                <p class="text-[10px] text-slate-500 font-semibold uppercase tracking-wider mt-0.5">
                    Portal Ujian CBT Seleksi Casis
                </p>
            </div>
        </div>

        {{-- Form Logout Quick --}}
        <form method="POST" action="{{ route('seleksi.logout') }}">
            @csrf
            <button type="submit" 
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-slate-600 hover:text-red-700 bg-white hover:bg-red-50 border border-slate-200 shadow-xs transition-colors">
                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                <span>Keluar</span>
            </button>
        </form>
    </header>

    {{-- MAIN CONTENT CARD --}}
    <main class="max-w-xl mx-auto w-full my-auto">
        <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 sm:p-8 text-center space-y-6 relative overflow-hidden">
            
            {{-- Accent Top Bar --}}
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-500 to-emerald-600"></div>

            {{-- Big Celebration Icon --}}
            <div class="pt-2">
                <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-emerald-50 border-2 border-emerald-100 flex items-center justify-center text-emerald-600 shadow-lg shadow-emerald-500/10">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>

            {{-- Main Congratulations & Information Notice --}}
            <div class="space-y-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[11px] font-black uppercase tracking-wider bg-emerald-100 text-emerald-800">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    Seluruh Rangkaian Ujian Selesai
                </span>
                
                <h2 class="text-xl sm:text-2xl font-black text-slate-900 tracking-tight">
                    Terima Kasih, {{ $peserta->nama_lengkap }}!
                </h2>

                <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-normal pt-1">
                    Terima kasih sudah melakukan <strong class="text-slate-800 font-semibold">Tes Buta Warna</strong> dan <strong class="text-slate-800 font-semibold">Tes Potensi Akademik (TPA)</strong>. Seluruh jawaban Anda telah berhasil kami rekam di sistem seleksi SPMB SMK Telkom Lampung.
                </p>
            </div>

            {{-- Announcement Notice Box --}}
            <div class="p-4 sm:p-5 rounded-2xl bg-amber-50/80 border border-amber-200/90 text-left space-y-2">
                <div class="flex items-center gap-2 text-amber-900 font-bold text-xs sm:text-sm">
                    <svg class="w-4 h-4 text-amber-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                    </svg>
                    <span>Informasi Pengumuman Kelulusan</span>
                </div>
                <p class="text-xs text-amber-800 leading-relaxed">
                    Tunggu hasilnya pada saat pengumuman resmi. <strong>Pengumuman kelulusan akan diinformasikan sesuai dengan jadwal yang telah ditetapkan oleh panitia SPMB</strong> melalui portal resmi dan grup WhatsApp peserta. Hasil tes tidak ditampilkan langsung untuk menjaga kerahasiaan proses evaluasi panitia.
                </p>
            </div>

            {{-- Candidate Identity Summary Card --}}
            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200/80 text-left space-y-2.5 text-xs">
                <div class="text-[11px] font-black text-slate-400 uppercase tracking-wider pb-1 border-b border-slate-200/80">
                    Rincian Peserta Ujian
                </div>

                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-500">Nomor Pendaftaran</span>
                    <span class="font-mono font-bold text-slate-900">{{ $peserta->nomor_pendaftaran }}</span>
                </div>

                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-500">Nama Calon Siswa</span>
                    <span class="font-bold text-slate-900">{{ $peserta->nama_lengkap }}</span>
                </div>

                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-500">Pilihan Jurusan</span>
                    <span class="font-semibold text-slate-900">{{ $peserta->jurusan->nama ?? $peserta->jurusan_pilihan ?? 'SMK Telkom' }}</span>
                </div>

                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-500">Status Tes Buta Warna</span>
                    <span class="font-bold text-emerald-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Selesai Dikerjakan
                    </span>
                </div>

                <div class="flex items-center justify-between py-0.5">
                    <span class="text-slate-500">Status Tes TPA (4 Mapel)</span>
                    <span class="font-bold text-emerald-600 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                        Lengkap (MTK, BING, BINDO, TIK)
                    </span>
                </div>
            </div>

            {{-- ACTION BUTTONS: LOGOUT & HELP --}}
            <div class="pt-2 space-y-3">
                {{-- Tombol Logout Utama (Sangat Jelas & Lega) --}}
                <form method="POST" action="{{ route('seleksi.logout') }}">
                    @csrf
                    <button type="submit"
                        class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-2xl text-sm font-extrabold text-white bg-red-600 hover:bg-red-700 active:scale-[0.99] shadow-lg shadow-red-600/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar Dari Sistem Ujian</span>
                    </button>
                </form>

                <p class="text-[11px] text-slate-400">
                    Pastikan Anda telah logout sebelum meninggalkan perangkat Anda.
                </p>
            </div>

        </div>
    </main>

    {{-- FOOTER --}}
    <footer class="max-w-xl mx-auto w-full text-center pt-6 text-[11px] text-slate-400 font-medium">
        &copy; {{ date('Y') }} Panitia SPMB SMK Telkom Lampung &bull; Sistem Informasi Akademik
    </footer>

</body>
</html>
