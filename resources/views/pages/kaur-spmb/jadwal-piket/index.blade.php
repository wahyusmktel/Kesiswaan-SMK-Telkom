<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-2">
            <div>
                <nav class="flex text-xs text-slate-500 mb-1" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1">
                        <li><span>Beranda</span></li>
                        <li><span class="mx-1">/</span></li>
                        <li><span>Kaur SPMB</span></li>
                        <li><span class="mx-1">/</span></li>
                        <li class="font-semibold text-slate-800">Jadwal Piket</li>
                    </ol>
                </nav>
                <h2 class="text-xl font-bold text-slate-900 tracking-tight">Jadwal Piket SPMB Pegawai</h2>
                <p class="text-xs text-slate-500 mt-0.5">Penjadwalan piket SPMB hari Sabtu secara bergiliran bagi guru dan pegawai SMK Telkom Lampung</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-red-50 text-red-700 border border-red-200">
                    <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    T.P {{ $selectedYear }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="jadwalPiketPage()" x-init="init()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-5">

            {{-- FLASH NOTIFICATIONS --}}
            @if(session('success'))
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-emerald-800 bg-emerald-50 rounded-lg border border-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-600 hover:text-emerald-800">&times;</button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between p-3.5 text-xs font-medium text-red-800 bg-red-50 rounded-lg border border-red-200 shadow-sm">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-600 hover:text-red-800">&times;</button>
                </div>
            @endif

            {{-- STATS CARDS --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Kelompok</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalKelompok }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Kelompok piket bergiliran</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Petugas</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalPetugas }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Guru & pegawai terdaftar</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Hari Piket Sabtu</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalHariPiket }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Pertemuan terjadwal</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Piket Terdekat</p>
                        @if($piketTerdekat)
                            <h3 class="text-sm font-bold text-slate-800 mt-1 truncate max-w-[170px]" title="{{ $piketTerdekat->kelompok->nama_kelompok ?? '-' }}">
                                {{ $piketTerdekat->kelompok->nama_kelompok ?? '-' }}
                            </h3>
                            <p class="text-[11px] text-slate-500 mt-0.5 font-medium">
                                {{ \Carbon\Carbon::parse($piketTerdekat->tanggal)->locale('id')->translatedFormat('d M Y') }}
                            </p>
                        @else
                            <h3 class="text-sm font-bold text-slate-400 mt-1">Belum ada</h3>
                            <p class="text-[11px] text-slate-400 mt-0.5">Semua jadwal telah lewat</p>
                        @endif
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- CONTENT ACTION TOOLBAR --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    
                    {{-- Filter & Search Form --}}
                    <form method="GET" action="{{ route('kaur-spmb.jadwal-piket.index') }}" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <input type="hidden" name="view_mode" value="{{ $viewMode }}">

                        {{-- Academic Year Selector --}}
                        <div class="flex items-center gap-1.5">
                            <label class="text-xs font-medium text-slate-500">T.A:</label>
                            <select name="tahun_ajaran" onchange="this.form.submit()"
                                class="text-xs font-semibold border-slate-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm py-1.5 px-2.5 bg-slate-50">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear === $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Per Page (in table mode) --}}
                        @if($viewMode === 'tanggal')
                            <div class="flex items-center gap-1.5">
                                <label class="text-xs font-medium text-slate-500">Show:</label>
                                <select name="per_page" onchange="this.form.submit()"
                                    class="text-xs border-slate-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm py-1.5 px-2.5">
                                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                                </select>
                            </div>
                        @endif

                        {{-- Search Input --}}
                        <div class="relative flex-1 min-w-[200px]">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru, kelompok, tanggal..."
                                class="w-full text-xs pl-8 pr-3 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <button type="submit"
                            class="px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-md transition-colors">
                            Cari
                        </button>

                        @if($search !== '')
                            <a href="{{ route('kaur-spmb.jadwal-piket.index', ['tahun_ajaran' => $selectedYear, 'view_mode' => $viewMode]) }}"
                                class="px-2.5 py-1.5 text-xs text-red-600 hover:text-red-700 hover:underline">
                                Reset
                            </a>
                        @endif
                    </form>

                    {{-- Action Buttons (CRUD strictly in content area) --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- View Mode Switcher --}}
                        <div class="inline-flex rounded-md border border-slate-300 bg-slate-100 p-0.5">
                            <a href="{{ route('kaur-spmb.jadwal-piket.index', ['tahun_ajaran' => $selectedYear, 'view_mode' => 'kelompok', 'search' => $search]) }}"
                                class="px-2.5 py-1 text-xs font-semibold rounded {{ $viewMode === 'kelompok' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Format SK (Kelompok)
                            </a>
                            <a href="{{ route('kaur-spmb.jadwal-piket.index', ['tahun_ajaran' => $selectedYear, 'view_mode' => 'tanggal', 'search' => $search, 'per_page' => $perPage]) }}"
                                class="px-2.5 py-1 text-xs font-semibold rounded {{ $viewMode === 'tanggal' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Daftar Tanggal (Kalender)
                            </a>
                        </div>

                        {{-- Add Kelompok Button --}}
                        <button type="button" @click="openAddKelompokModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Kelompok
                        </button>

                        {{-- Auto-Generate Rotating Saturdays --}}
                        <button type="button" @click="openGenerateModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            Jadwalkan Bergilir
                        </button>

                        {{-- Print Official SK View --}}
                        <a href="{{ route('kaur-spmb.jadwal-piket.print', ['tahun_ajaran' => $selectedYear]) }}" target="_blank"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Surat Tugas
                        </a>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- VIEW TAB 1: FORMAT KELOMPOK (SK MATRIX) --}}
            {{-- ======================================================== --}}
            @if($viewMode === 'kelompok')
                <div class="space-y-4">
                    @forelse($kelompoks as $kelompok)
                        <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                            
                            {{-- Kelompok Header --}}
                            <div class="px-4 py-3 bg-slate-50 border-b border-slate-200 flex flex-wrap items-center justify-between gap-3">
                                <div class="flex items-center gap-3">
                                    <span class="w-7 h-7 rounded-md bg-slate-800 text-white font-bold text-xs flex items-center justify-center shadow-sm">
                                        {{ $kelompok->urutan }}
                                    </span>
                                    <div>
                                        <h3 class="text-sm font-bold text-slate-900">{{ $kelompok->nama_kelompok }}</h3>
                                        <div class="flex items-center gap-3 text-xs text-slate-500 mt-0.5">
                                            <span class="flex items-center gap-1">
                                                <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $kelompok->waktu ?: '07.30 - 12.00' }}
                                            </span>
                                            <span>•</span>
                                            <span>{{ $kelompok->petugas->count() }} Petugas</span>
                                            <span>•</span>
                                            <span>{{ $kelompok->tanggals->count() }} Hari Sabtu</span>
                                            @if($kelompok->keterangan)
                                                <span>•</span>
                                                <span class="italic text-slate-400">{{ $kelompok->keterangan }}</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Kelompok Actions --}}
                                <div class="flex items-center gap-1.5">
                                    <button type="button" @click="openAddPetugasModal({{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-emerald-700 bg-emerald-50 hover:bg-emerald-100 border border-emerald-200 rounded-md transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                        </svg>
                                        Petugas
                                    </button>

                                    <button type="button" @click="openAddTanggalModal({{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}', '{{ $kelompok->waktu }}')"
                                        class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 border border-blue-200 rounded-md transition-colors">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        Tanggal
                                    </button>

                                    <button type="button" @click="openEditKelompokModal({{ json_encode($kelompok) }})"
                                        class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Edit Kelompok">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>

                                    <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.kelompok.destroy', $kelompok) }}"
                                        onsubmit="return confirm('Hapus {{ $kelompok->nama_kelompok }} beserta seluruh petugas dan tanggal piketnya?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Hapus Kelompok">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Kelompok Details: Two columns (Petugas vs Tanggal) --}}
                            <div class="grid grid-cols-1 md:grid-cols-12 divide-y md:divide-y-0 md:divide-x divide-slate-200">
                                
                                {{-- Sub-column 1: Daftar Petugas (7 cols) --}}
                                <div class="md:col-span-7 p-4">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            Daftar Petugas Piket ({{ $kelompok->petugas->count() }})
                                        </h4>
                                        <button type="button" @click="openAddPetugasModal({{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}')"
                                            class="text-[11px] font-semibold text-red-600 hover:text-red-700 hover:underline">
                                            + Tambah Petugas
                                        </button>
                                    </div>

                                    @if($kelompok->petugas->isEmpty())
                                        <div class="p-3 text-center text-xs text-slate-400 bg-slate-50 rounded-md border border-dashed border-slate-200">
                                            Belum ada petugas di kelompok ini
                                        </div>
                                    @else
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                            @foreach($kelompok->petugas as $petugas)
                                                <div class="flex items-center justify-between p-2 rounded-md bg-slate-50 border border-slate-200 hover:border-slate-300 transition-colors">
                                                    <div class="flex items-center gap-2 min-w-0">
                                                        <span class="w-5 h-5 rounded bg-white text-slate-600 text-[10px] font-bold flex items-center justify-center border border-slate-200 flex-shrink-0">
                                                            {{ $petugas->urutan }}
                                                        </span>
                                                        <div class="truncate">
                                                            <p class="text-xs font-medium text-slate-800 truncate" title="{{ $petugas->nama_petugas }}">
                                                                {{ $petugas->nama_petugas }}
                                                            </p>
                                                            <p class="text-[10px] text-slate-400">
                                                                {{ $petugas->master_guru_id ? 'Guru / TPA' : ($petugas->user_id ? 'Pegawai' : 'Manual') }}
                                                            </p>
                                                        </div>
                                                    </div>

                                                    <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.petugas.destroy', $petugas) }}"
                                                        onsubmit="return confirm('Hapus petugas {{ $petugas->nama_petugas }}?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="p-1 text-slate-300 hover:text-red-600 rounded transition-colors" title="Hapus Petugas">
                                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                {{-- Sub-column 2: Jadwal Tanggal (5 cols) --}}
                                <div class="md:col-span-5 p-4 bg-slate-50/50">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-600 flex items-center gap-1.5">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            Jadwal Tanggal ({{ $kelompok->tanggals->count() }})
                                        </h4>
                                        <button type="button" @click="openAddTanggalModal({{ $kelompok->id }}, '{{ addslashes($kelompok->nama_kelompok) }}', '{{ $kelompok->waktu }}')"
                                            class="text-[11px] font-semibold text-blue-600 hover:text-blue-700 hover:underline">
                                            + Tambah Tanggal
                                        </button>
                                    </div>

                                    @if($kelompok->tanggals->isEmpty())
                                        <div class="p-3 text-center text-xs text-slate-400 bg-white rounded-md border border-dashed border-slate-200">
                                            Belum ada tanggal piket untuk kelompok ini
                                        </div>
                                    @else
                                        <div class="space-y-1.5 max-h-72 overflow-y-auto pr-1">
                                            @foreach($kelompok->tanggals as $tgl)
                                                <div class="flex items-center justify-between p-2 rounded-md bg-white border border-slate-200 hover:border-slate-300 transition-colors">
                                                    <div class="flex items-center gap-2">
                                                        <span class="w-2 h-2 rounded-full {{ $tgl->status === 'Terlaksana' ? 'bg-emerald-500' : ($tgl->status === 'Libur' ? 'bg-rose-500' : 'bg-blue-500') }}"></span>
                                                        <span class="text-xs font-medium text-slate-800">
                                                            {{ $tgl->hari }}, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                                        </span>
                                                        <span class="text-[10px] text-slate-400">({{ $tgl->waktu ?: '07.30 - 12.00' }})</span>
                                                    </div>

                                                    <div class="flex items-center gap-2">
                                                        {{-- Quick Status Form --}}
                                                        <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.tanggal.status', $tgl) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <select name="status" onchange="this.form.submit()"
                                                                class="text-[10px] font-semibold rounded py-0.5 px-1.5 border-slate-200 focus:ring-0 focus:border-slate-400 {{ $tgl->status === 'Terlaksana' ? 'bg-emerald-50 text-emerald-700' : ($tgl->status === 'Libur' ? 'bg-rose-50 text-rose-700' : 'bg-slate-50 text-slate-700') }}">
                                                                <option value="Terjadwal" {{ $tgl->status === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                                <option value="Terlaksana" {{ $tgl->status === 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                                                <option value="Libur" {{ $tgl->status === 'Libur' ? 'selected' : '' }}>Libur</option>
                                                                <option value="Diganti" {{ $tgl->status === 'Diganti' ? 'selected' : '' }}>Diganti</option>
                                                            </select>
                                                        </form>

                                                        <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.tanggal.destroy', $tgl) }}"
                                                            onsubmit="return confirm('Hapus tanggal ini?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="p-1 text-slate-300 hover:text-red-600 rounded transition-colors" title="Hapus Tanggal">
                                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                </svg>
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                            </div>
                        </div>
                    @empty
                        <div class="bg-white p-8 rounded-lg border border-slate-200 text-center">
                            <svg class="w-12 h-12 text-slate-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <h3 class="text-sm font-bold text-slate-800">Belum Ada Data Kelompok Piket</h3>
                            <p class="text-xs text-slate-500 mt-1 mb-4">Tambahkan kelompok baru atau lakukan penjadwalan bergilir untuk tahun ajaran {{ $selectedYear }}.</p>
                            <button type="button" @click="openAddKelompokModal()"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Tambah Kelompok Sekarang
                            </button>
                        </div>
                    @endforelse
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- VIEW TAB 2: DAFTAR TANGGAL (TABLE VIEW WITH PAGINATION) --}}
            {{-- ======================================================== --}}
            @if($viewMode === 'tanggal')
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-700">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-600 bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                                    <th scope="col" class="px-4 py-3">Hari & Tanggal</th>
                                    <th scope="col" class="px-4 py-3">Kelompok</th>
                                    <th scope="col" class="px-4 py-3">Waktu</th>
                                    <th scope="col" class="px-4 py-3">Petugas Bertugas</th>
                                    <th scope="col" class="px-4 py-3 text-center">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center w-24">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($paginatedTanggals as $index => $item)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-3 text-center font-medium text-slate-500">
                                            {{ $paginatedTanggals->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                            {{ $item->hari }}, {{ \Carbon\Carbon::parse($item->tanggal)->locale('id')->translatedFormat('d M Y') }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-slate-800 whitespace-nowrap">
                                            {{ $item->kelompok->nama_kelompok ?? '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 whitespace-nowrap">
                                            {{ $item->waktu ?: ($item->kelompok->waktu ?? '07.30 - 12.00') }}
                                        </td>
                                        <td class="px-4 py-3">
                                            @if($item->kelompok && $item->kelompok->petugas->isNotEmpty())
                                                <div class="flex flex-wrap gap-1 max-w-md">
                                                    @foreach($item->kelompok->petugas as $p)
                                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded bg-slate-100 text-slate-700 text-[10px] border border-slate-200 font-medium">
                                                            {{ $p->nama_petugas }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @else
                                                <span class="text-slate-400 italic">Belum ada petugas</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.tanggal.status', $item) }}" class="inline-block">
                                                @csrf
                                                @method('PATCH')
                                                <select name="status" onchange="this.form.submit()"
                                                    class="text-[11px] font-semibold rounded py-1 px-2 border-slate-200 focus:ring-0 focus:border-slate-400 {{ $item->status === 'Terlaksana' ? 'bg-emerald-50 text-emerald-700' : ($item->status === 'Libur' ? 'bg-rose-50 text-rose-700' : 'bg-blue-50 text-blue-700') }}">
                                                    <option value="Terjadwal" {{ $item->status === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                    <option value="Terlaksana" {{ $item->status === 'Terlaksana' ? 'selected' : '' }}>Terlaksana</option>
                                                    <option value="Libur" {{ $item->status === 'Libur' ? 'selected' : '' }}>Libur</option>
                                                    <option value="Diganti" {{ $item->status === 'Diganti' ? 'selected' : '' }}>Diganti</option>
                                                </select>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <form method="POST" action="{{ route('kaur-spmb.jadwal-piket.tanggal.destroy', $item) }}"
                                                onsubmit="return confirm('Hapus tanggal piket ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Hapus Tanggal">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-400">
                                            Tidak ditemukan data jadwal piket untuk filter ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Standard Pagination Footer --}}
                    @if($paginatedTanggals->hasPages() || $paginatedTanggals->total() > 0)
                        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500">
                            <div>
                                Menampilkan 
                                <strong class="text-slate-800">{{ $paginatedTanggals->firstItem() ?? 0 }}</strong> 
                                sampai 
                                <strong class="text-slate-800">{{ $paginatedTanggals->lastItem() ?? 0 }}</strong> 
                                dari 
                                <strong class="text-slate-800">{{ $paginatedTanggals->total() }}</strong> 
                                data jadwal
                            </div>
                            <div>
                                {{ $paginatedTanggals->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

        </div>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 1: KELOLA KELOMPOK (TAMBAH / EDIT)    --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalKelompok.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalKelompokBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalKelompok.maximized,
                        'w-full max-w-lg': !modalKelompok.maximized && modalKelompok.pos.x === null
                    }"
                    :style="!modalKelompok.maximized && modalKelompok.pos.x !== null ? 'position: fixed; left:' + modalKelompok.pos.x + 'px; top:' + modalKelompok.pos.y + 'px; width: 520px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between select-none"
                        :class="!modalKelompok.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalKelompok')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800"
                                x-text="modalKelompok.isEdit ? 'Edit Kelompok Piket' : 'Tambah Kelompok Piket Baru'">
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalKelompok')"
                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalKelompok.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalKelompok.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalKelompok')"
                                class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form :action="modalKelompok.actionUrl" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf
                        <template x-if="modalKelompok.isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Kelompok <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_kelompok" x-model="modalKelompok.form.nama_kelompok" required
                                placeholder="Contoh: Kelompok 1"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Piket <span class="text-red-500">*</span></label>
                            <input type="text" name="waktu" x-model="modalKelompok.form.waktu" required
                                placeholder="Contoh: 07.30 - 12.00"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan (Opsional)</label>
                            <input type="text" name="keterangan" x-model="modalKelompok.form.keterangan"
                                placeholder="Catatan tambahan..."
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalKelompok')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                Simpan Kelompok
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 2: TAMBAH PETUGAS                    --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalPetugas.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalPetugasBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalPetugas.maximized,
                        'w-full max-w-lg': !modalPetugas.maximized && modalPetugas.pos.x === null
                    }"
                    :style="!modalPetugas.maximized && modalPetugas.pos.x !== null ? 'position: fixed; left:' + modalPetugas.pos.x + 'px; top:' + modalPetugas.pos.y + 'px; width: 540px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-emerald-50 border-b border-emerald-200 flex items-center justify-between select-none"
                        :class="!modalPetugas.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalPetugas')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-emerald-900">
                                Tambah Petugas: <span x-text="modalPetugas.kelompokName"></span>
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalPetugas')"
                                class="p-1 text-emerald-600 hover:text-emerald-800 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalPetugas.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalPetugas.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalPetugas')"
                                class="p-1 text-emerald-600 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form :action="modalPetugas.actionUrl" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Sumber Data Petugas</label>
                            <div class="grid grid-cols-3 gap-2">
                                <label class="flex items-center gap-2 p-2 border rounded-md cursor-pointer text-xs"
                                    :class="modalPetugas.sourceType === 'guru' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700'">
                                    <input type="radio" name="source_type" value="guru" x-model="modalPetugas.sourceType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Master Guru</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 border rounded-md cursor-pointer text-xs"
                                    :class="modalPetugas.sourceType === 'user' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700'">
                                    <input type="radio" name="source_type" value="user" x-model="modalPetugas.sourceType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Pengguna</span>
                                </label>
                                <label class="flex items-center gap-2 p-2 border rounded-md cursor-pointer text-xs"
                                    :class="modalPetugas.sourceType === 'manual' ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'border-slate-200 text-slate-700'">
                                    <input type="radio" name="source_type" value="manual" x-model="modalPetugas.sourceType" class="text-emerald-600 focus:ring-emerald-500">
                                    <span>Manual</span>
                                </label>
                            </div>
                        </div>

                        {{-- Source: Master Guru --}}
                        <div x-show="modalPetugas.sourceType === 'guru'">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Guru / Pegawai</label>
                            <select name="master_guru_id" class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Pilih dari Daftar Master Guru --</option>
                                @foreach($masterGurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama_lengkap }} (NIK: {{ $guru->nik ?: '-' }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Source: User --}}
                        <div x-show="modalPetugas.sourceType === 'user'" style="display:none;">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Akun Pengguna</label>
                            <select name="user_id" class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-emerald-500 focus:ring-emerald-500">
                                <option value="">-- Pilih dari Akun Pengguna --</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Source: Manual --}}
                        <div x-show="modalPetugas.sourceType === 'manual'" style="display:none;">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Nama Petugas Lengkap & Gelar</label>
                            <input type="text" name="nama_petugas_manual" placeholder="Contoh: Irfan Novra Desando, S.Pd"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Jabatan / Keterangan</label>
                            <input type="text" name="jabatan" value="Petugas Piket SPMB"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-emerald-500 focus:ring-emerald-500">
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalPetugas')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-emerald-600 hover:bg-emerald-700 rounded-md shadow-sm transition-colors">
                                Tambahkan Petugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 3: TAMBAH TANGGAL                   --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalTanggal.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalTanggalBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalTanggal.maximized,
                        'w-full max-w-lg': !modalTanggal.maximized && modalTanggal.pos.x === null
                    }"
                    :style="!modalTanggal.maximized && modalTanggal.pos.x !== null ? 'position: fixed; left:' + modalTanggal.pos.x + 'px; top:' + modalTanggal.pos.y + 'px; width: 500px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-blue-50 border-b border-blue-200 flex items-center justify-between select-none"
                        :class="!modalTanggal.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalTanggal')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-blue-900">
                                Tambah Tanggal: <span x-text="modalTanggal.kelompokName"></span>
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalTanggal')"
                                class="p-1 text-blue-600 hover:text-blue-800 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalTanggal.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalTanggal.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalTanggal')"
                                class="p-1 text-blue-600 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form :action="modalTanggal.actionUrl" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tanggal Piket (Sabtu) <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal" required
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Waktu Piket</label>
                            <input type="text" name="waktu" x-model="modalTanggal.waktu" placeholder="07.30 - 12.00"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                            <select name="status" class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-blue-500 focus:ring-blue-500">
                                <option value="Terjadwal">Terjadwal</option>
                                <option value="Terlaksana">Terlaksana</option>
                                <option value="Libur">Libur</option>
                                <option value="Diganti">Diganti</option>
                            </select>
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalTanggal')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-md shadow-sm transition-colors">
                                Simpan Tanggal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 4: JADWALKAN BERGILIR OTOMATIS        --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalGenerate.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalGenerateBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalGenerate.maximized,
                        'w-full max-w-lg': !modalGenerate.maximized && modalGenerate.pos.x === null
                    }"
                    :style="!modalGenerate.maximized && modalGenerate.pos.x !== null ? 'position: fixed; left:' + modalGenerate.pos.x + 'px; top:' + modalGenerate.pos.y + 'px; width: 540px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between select-none"
                        :class="!modalGenerate.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalGenerate')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-800"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                Jadwalkan Hari Sabtu Bergilir Otomatis
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalGenerate')"
                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalGenerate.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalGenerate.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalGenerate')"
                                class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form action="{{ route('kaur-spmb.jadwal-piket.generate-dates') }}" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf
                        <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">

                        <div class="p-3 bg-slate-50 rounded-md border border-slate-200 text-xs text-slate-600 leading-relaxed">
                            Fitur ini akan secara otomatis mencari setiap hari <strong>Sabtu</strong> mulai dari tanggal awal hingga akhir, lalu membagikannya secara bergantian ke seluruh kelompok yang ada di Tahun Ajaran <strong>{{ $selectedYear }}</strong> (Kelompok 1, 2, 3, 4, dst).
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Mulai Dari Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="start_date" required value="2026-09-26"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Sampai Tanggal <span class="text-red-500">*</span></label>
                                <input type="date" name="end_date" required value="2027-07-10"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalGenerate')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-slate-900 hover:bg-black rounded-md shadow-sm transition-colors">
                                Mulai Jadwalkan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function jadwalPiketPage() {
            return {
                modalKelompok: {
                    open: false,
                    isEdit: false,
                    maximized: false,
                    actionUrl: '{{ route("kaur-spmb.jadwal-piket.kelompok.store") }}',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 },
                    form: { nama_kelompok: '', waktu: '07.30 - 12.00', keterangan: '' }
                },
                modalPetugas: {
                    open: false,
                    maximized: false,
                    kelompokId: null,
                    kelompokName: '',
                    sourceType: 'guru',
                    actionUrl: '',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 }
                },
                modalTanggal: {
                    open: false,
                    maximized: false,
                    kelompokId: null,
                    kelompokName: '',
                    waktu: '07.30 - 12.00',
                    actionUrl: '',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 }
                },
                modalGenerate: {
                    open: false,
                    maximized: false,
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 }
                },

                init() {
                    // Setup mouse listeners for drag
                    window.addEventListener('mousemove', (e) => this.onMouseMove(e));
                    window.addEventListener('mouseup', () => this.onMouseUp());
                },

                openAddKelompokModal() {
                    this.modalKelompok.isEdit = false;
                    this.modalKelompok.actionUrl = '{{ route("kaur-spmb.jadwal-piket.kelompok.store") }}';
                    this.modalKelompok.form = { nama_kelompok: '', waktu: '07.30 - 12.00', keterangan: '' };
                    this.modalKelompok.pos = { x: null, y: null };
                    this.modalKelompok.maximized = false;
                    this.modalKelompok.open = true;
                },

                openEditKelompokModal(kelompok) {
                    this.modalKelompok.isEdit = true;
                    this.modalKelompok.actionUrl = '{{ url("kaur-spmb/jadwal-piket/kelompok") }}/' + kelompok.id;
                    this.modalKelompok.form = {
                        nama_kelompok: kelompok.nama_kelompok,
                        waktu: kelompok.waktu || '07.30 - 12.00',
                        keterangan: kelompok.keterangan || ''
                    };
                    this.modalKelompok.pos = { x: null, y: null };
                    this.modalKelompok.maximized = false;
                    this.modalKelompok.open = true;
                },

                openAddPetugasModal(kelompokId, kelompokName) {
                    this.modalPetugas.kelompokId = kelompokId;
                    this.modalPetugas.kelompokName = kelompokName;
                    this.modalPetugas.sourceType = 'guru';
                    this.modalPetugas.actionUrl = '{{ url("kaur-spmb/jadwal-piket/kelompok") }}/' + kelompokId + '/petugas';
                    this.modalPetugas.pos = { x: null, y: null };
                    this.modalPetugas.maximized = false;
                    this.modalPetugas.open = true;
                },

                openAddTanggalModal(kelompokId, kelompokName, waktu) {
                    this.modalTanggal.kelompokId = kelompokId;
                    this.modalTanggal.kelompokName = kelompokName;
                    this.modalTanggal.waktu = waktu || '07.30 - 12.00';
                    this.modalTanggal.actionUrl = '{{ url("kaur-spmb/jadwal-piket/kelompok") }}/' + kelompokId + '/tanggal';
                    this.modalTanggal.pos = { x: null, y: null };
                    this.modalTanggal.maximized = false;
                    this.modalTanggal.open = true;
                },

                openGenerateModal() {
                    this.modalGenerate.pos = { x: null, y: null };
                    this.modalGenerate.maximized = false;
                    this.modalGenerate.open = true;
                },

                closeModal(key) {
                    this[key].open = false;
                },

                toggleMaximize(key) {
                    this[key].maximized = !this[key].maximized;
                },

                startDragModal(e, key) {
                    if (this[key].maximized) return;
                    if (e.target.closest('button') || e.target.closest('input')) return;

                    const refName = key + 'Box';
                    const el = this.$refs[refName];
                    if (!el) return;

                    const rect = el.getBoundingClientRect();
                    if (this[key].pos.x === null) {
                        this[key].pos.x = rect.left;
                        this[key].pos.y = rect.top;
                    }

                    this[key].drag.active = true;
                    this[key].drag.startX = e.clientX;
                    this[key].drag.startY = e.clientY;
                    this[key].drag.initialX = this[key].pos.x;
                    this[key].drag.initialY = this[key].pos.y;
                },

                onMouseMove(e) {
                    const keys = ['modalKelompok', 'modalPetugas', 'modalTanggal', 'modalGenerate'];
                    keys.forEach(key => {
                        if (this[key] && this[key].drag && this[key].drag.active) {
                            const dx = e.clientX - this[key].drag.startX;
                            const dy = e.clientY - this[key].drag.startY;
                            this[key].pos.x = Math.max(10, Math.min(window.innerWidth - 100, this[key].drag.initialX + dx));
                            this[key].pos.y = Math.max(10, Math.min(window.innerHeight - 100, this[key].drag.initialY + dy));
                        }
                    });
                },

                onMouseUp() {
                    const keys = ['modalKelompok', 'modalPetugas', 'modalTanggal', 'modalGenerate'];
                    keys.forEach(key => {
                        if (this[key] && this[key].drag) {
                            this[key].drag.active = false;
                        }
                    });
                }
            };
        }
    </script>
</x-app-layout>
