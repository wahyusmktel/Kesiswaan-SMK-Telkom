<x-app-layout>
    {{-- CSS Gradient Animation --}}
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Piket</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            @include('shared.fingerprint-today-card')

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div
                        class="relative rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 shadow-lg overflow-hidden p-8 animate-gradient h-full">
                        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                        <div class="relative z-10 text-white">
                            <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👮‍♂️</h3>
                            <p class="mt-2 text-amber-100 font-medium text-lg">Siap memantau aktivitas siswa hari ini? Tetap
                                semangat!</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    @if ($kegiatanSaatIni)
                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group h-full">
                            <div class="absolute -right-4 -bottom-4 opacity-20 transform group-hover:scale-110 transition-transform">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                            </div>
                            <div class="relative z-10">
                                <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block border border-white/30">Kegiatan Saat Ini</span>
                                <h4 class="text-xl font-black leading-tight mb-1">{{ str_replace('_', ' ', strtoupper($kegiatanSaatIni->tipe_kegiatan)) }}</h4>
                                <p class="text-amber-50 text-xs font-bold font-mono">
                                    {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_selesai)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6 flex flex-col items-center justify-center text-center h-full group hover:border-amber-300 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3 group-hover:bg-amber-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-300 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Tidak Ada Kegiatan Spesial</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ══ RUNNING TEXT MANAGER ══ --}}
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-violet-50 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center shadow-sm shadow-indigo-200">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="font-black text-gray-800 text-sm">Running Text Smart TV</h3>
                            <p class="text-xs text-gray-500 font-medium">Informasi yang tampil di layar TV jadwal pelajaran</p>
                        </div>
                    </div>
                    <a href="{{ route('tv.jadwal') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 text-white text-xs font-bold rounded-lg hover:bg-indigo-700 transition-all shadow-sm">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        Lihat TV
                    </a>
                </div>

                <div class="p-6">
                    {{-- Form tambah --}}
                    <form action="{{ route('piket.info-ticker.store') }}" method="POST" class="flex gap-3 mb-5">
                        @csrf
                        <input type="text" name="konten" required maxlength="500"
                               placeholder="Ketik informasi untuk running text... (maks 500 karakter)"
                               class="flex-1 rounded-xl border border-gray-300 px-4 py-2.5 text-sm font-medium focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100 outline-none transition-all bg-gray-50 focus:bg-white">
                        <button type="submit"
                                class="flex-shrink-0 inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl transition-all shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah
                        </button>
                    </form>

                    {{-- Daftar ticker --}}
                    @if($infoTickers->isEmpty())
                        <div class="flex flex-col items-center justify-center py-8 text-center border-2 border-dashed border-gray-100 rounded-xl">
                            <svg class="w-10 h-10 text-gray-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Belum ada running text</p>
                        </div>
                    @else
                        <div class="space-y-2 max-h-52 overflow-y-auto pr-1">
                            @foreach($infoTickers as $ticker)
                                <div class="flex items-center gap-3 px-4 py-3 rounded-xl border transition-all
                                    {{ $ticker->is_active ? 'bg-indigo-50/50 border-indigo-100' : 'bg-gray-50 border-gray-100 opacity-60' }}">
                                    {{-- Toggle aktif --}}
                                    <form action="{{ route('piket.info-ticker.toggle', $ticker) }}" method="POST">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="{{ $ticker->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                                class="w-8 h-8 rounded-lg flex items-center justify-center transition-all
                                                {{ $ticker->is_active ? 'bg-indigo-100 text-indigo-600 hover:bg-indigo-200' : 'bg-gray-200 text-gray-400 hover:bg-gray-300' }}">
                                            @if($ticker->is_active)
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            @else
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>
                                                </svg>
                                            @endif
                                        </button>
                                    </form>

                                    <p class="flex-1 text-sm font-medium text-gray-700 truncate">{{ $ticker->konten }}</p>

                                    <span class="text-[10px] font-bold text-gray-400 flex-shrink-0 hidden sm:block">
                                        {{ $ticker->creator->name ?? '-' }}
                                    </span>

                                    {{-- Badge status --}}
                                    <span class="flex-shrink-0 text-[10px] font-black px-2 py-0.5 rounded-full
                                        {{ $ticker->is_active ? 'bg-indigo-100 text-indigo-700' : 'bg-gray-200 text-gray-500' }}">
                                        {{ $ticker->is_active ? 'AKTIF' : 'NONAKTIF' }}
                                    </span>

                                    {{-- Hapus --}}
                                    <form action="{{ route('piket.info-ticker.destroy', $ticker) }}" method="POST"
                                          onsubmit="return confirm('Hapus info ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="w-7 h-7 rounded-lg flex items-center justify-center text-gray-300 hover:text-red-500 hover:bg-red-50 transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            {{-- ══ END RUNNING TEXT ══ --}}

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-2xl p-6 border border-indigo-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-500 uppercase tracking-wider">Anda Proses</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $totalIzinDiprosesPiket }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Total Izin Keluar</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-amber-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-500 uppercase tracking-wider">Izin Hari Ini</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $izinHariIni->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Tidak Masuk Sekolah</p>
                    </div>
                </div>

                <!-- NEW: Keterlambatan Hari Ini -->
                <div
                    class="bg-white rounded-2xl p-6 border border-red-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-500 uppercase tracking-wider">Terlambat Hari Ini</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $keterlambatanHariIni }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Siswa Terlambat</p>
                    </div>
                </div>

                <!-- NEW: Total Keterlambatan -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Terlambat</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $totalKeterlambatan }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Akumulasi Data</p>
                    </div>
                </div>
            </div>

            {{-- Quick Actions Section --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden p-6">
                <h3 class="text-sm font-bold text-gray-500 uppercase tracking-widest mb-6 flex items-center gap-2">
                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                    Aksi Cepat
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <a href="{{ route('piket.persetujuan-izin-keluar.create') }}" 
                        class="group relative flex flex-col p-5 bg-gradient-to-br from-red-500 to-red-600 rounded-xl text-white shadow-lg shaow-red-200/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-white/20 rounded-lg group-hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-lg">Tambah Izin Keluar</h4>
                        <p class="text-red-100 text-xs mt-1">Input izin meninggalkan kelas untuk siswa.</p>
                    </a>

                    <a href="{{ route('piket.penanganan-terlambat.index') }}" 
                        class="group relative flex flex-col p-5 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl text-white shadow-lg shadow-orange-200/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-white/20 rounded-lg group-hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-lg">Catat Keterlambatan</h4>
                        <p class="text-orange-100 text-xs mt-1">Input data siswa yang datang terlambat.</p>
                    </a>

                    <a href="{{ route('piket.monitoring.index') }}" 
                        class="group relative flex flex-col p-5 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl text-white shadow-lg shadow-indigo-200/50 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="p-2 bg-white/20 rounded-lg group-hover:bg-white/30 transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                            </div>
                            <svg class="w-5 h-5 opacity-50 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </div>
                        <h4 class="font-bold text-lg">Monitoring Perizinan</h4>
                        <p class="text-indigo-100 text-xs mt-1">Cek riwayat dan statistik perizinan.</p>
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                                Izin Tidak Masuk (Hari Ini)
                            </h3>
                            <span
                                class="text-xs font-medium text-gray-500 bg-white border px-2 py-1 rounded">{{ date('d M Y') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($izinHariIni as $izin)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $izin->user->name }}</td>
                                            <td class="px-6 py-3 text-gray-500">
                                                {{ $izin->user->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                @php
                                                    $statusClass = match ($izin->status) {
                                                        'diajukan' => 'bg-yellow-100 text-yellow-800',
                                                        'disetujui' => 'bg-green-100 text-green-800',
                                                        'ditolak' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                                    {{ $izin->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>Semua siswa hadir (atau belum ada data).</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                Siswa Terlambat (Hari Ini)
                            </h3>
                            <span class="text-xs font-medium text-gray-500 bg-white border px-2 py-1 rounded">Security Log</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Waktu</th>
                                        <th class="px-6 py-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($detailKeterlambatan as $late)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3">
                                                <div class="font-medium text-gray-900">{{ $late->siswa->user->name }}</div>
                                                <div class="text-[10px] text-gray-400">Security: {{ $late->security->name }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-gray-500">
                                                {{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-500 font-mono text-xs">
                                                {{ $late->waktu_dicatat_security->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                @php
                                                    $lateStatusClass = match ($late->status) {
                                                        'diajukan', 'menunggu_verifikasi' => 'bg-yellow-101 text-yellow-801',
                                                        'diverifikasi', 'masuk_kelas' => 'bg-green-101 text-green-801',
                                                        'ditolak' => 'bg-red-101 text-red-801',
                                                        default => 'bg-gray-101 text-gray-801',
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $lateStatusClass }}">
                                                    {{ str_replace('_', ' ', $late->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Tidak ada siswa terlambat hari ini.</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6">Tren Izin Keluar (30 Hari Terakhir)</h4>
                        <div class="h-64 w-full">
                            <canvas id="trenIzinPribadiChart"></canvas>
                        </div>
                    </div>

                    <!-- NEW: Analisa Keterlambatan -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                             <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                             Analisa Keterlambatan (30 Hari Terakhir)
                        </h4>
                        <div class="h-64 w-full">
                            <canvas id="analisaKeterlambatanChart"></canvas>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800 text-sm uppercase flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                                Aktivitas Terbaru
                            </h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse ($recentActivity as $activity)
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            @if($activity['type'] == 'Keterlambatan')
                                                <div class="p-2 bg-red-50 rounded-lg">
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @else
                                                <div class="p-2 bg-amber-50 rounded-lg">
                                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ $activity['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity['type'] }} • {{ $activity['time']->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $activity['color'] == 'red' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-400 text-sm italic">Belum ada aktivitas terekam hari ini.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-4 text-center">Proporsi Status Izin</h4>
                        <div class="h-48 flex justify-center">
                            <canvas id="statusIzinChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800 text-sm uppercase">Top Siswa Sering Keluar</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto custom-scrollbar">
                            @forelse ($topSiswaIzinKeluarGlobal as $siswa)
                                <div
                                    class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-xs font-bold text-gray-600">{{ $loop->iteration }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 truncate w-32">
                                                {{ $siswa->name }}</p>
                                            <p class="text-[10px] text-gray-500">Global Stats</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold px-2 py-1 bg-red-50 text-red-600 rounded-full">{{ $siswa->izin_meninggalkan_kelas_count }}x</span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-400">Belum ada data.</div>
                            @endforelse
                        </div>
                        </div>

                    <!-- NEW: Top Kelas Terlambat -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800 text-sm uppercase">Kelas Paling Sering Terlambat</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto custom-scrollbar">
                            @forelse ($topKelasTerlambat as $kelas)
                                <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-xs font-bold text-gray-600">{{ $loop->iteration }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $kelas->nama_kelas }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $kelas->nama_wali_kelas ?? 'Belum ada Wali Kelas' }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 bg-red-100 text-red-700 rounded-full">{{ $kelas->total }}x</span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-400">Belum ada data.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Config Defaults
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                // Data
                const statusData = @json($statusChartData);
                const dailyDataPiket = @json($dailyChartDataPiket);

                // 1. Doughnut Chart: Status Izin
                if (document.getElementById('statusIzinChart')) {
                    new Chart(document.getElementById('statusIzinChart'), {
                        type: 'doughnut',
                        data: {
                            labels: statusData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                            datasets: [{
                                data: statusData.data,
                                backgroundColor: ['#f59e0b', '#10b981', '#ef4444'], // Amber, Green, Red
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: 15,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Line Chart: Tren Izin Keluar (Smooth)
                if (document.getElementById('trenIzinPribadiChart')) {
                    const ctx = document.getElementById('trenIzinPribadiChart').getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)'); // Indigo pudar
                    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dailyDataPiket.labels,
                            datasets: [{
                                label: 'Diproses',
                                data: dailyDataPiket.data,
                                borderColor: '#6366f1',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    },
                                    grid: {
                                        borderDash: [2, 4]
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }

                // 3. New: Analisa Keterlambatan Chart
                if (document.getElementById('analisaKeterlambatanChart')) {
                    const ctx = document.getElementById('analisaKeterlambatanChart').getContext('2d');
                    const dailyDataKeterlambatan = @json($analisaKeterlambatanChart);
                    
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // Red pudar
                    gradient.addColorStop(1, 'rgba(239, 68, 68, 0)');

                    new Chart(ctx, {
                        type: 'bar', // Using bar for contrast with line chart
                        data: {
                            labels: dailyDataKeterlambatan.labels,
                            datasets: [{
                                label: 'Terlambat',
                                data: dailyDataKeterlambatan.data,
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                                barPercentage: 0.6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 },
                                    grid: { borderDash: [2, 4] }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
