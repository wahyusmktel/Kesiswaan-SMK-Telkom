<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Daftar Seleksi Peserta SPMB</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="pesertaSeleksiPage()" x-init="init()">
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
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Calon Siswa</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalPeserta) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Terdaftar pada periode ini</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kehadiran Siswa</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalHadirSiswa) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Siswa telah hadir di lokasi tes</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Kehadiran Orang Tua</p>
                        <h3 class="text-2xl font-bold text-purple-600 mt-1">{{ number_format($totalHadirOrtu) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Wali pendamping hadir</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Menunggu / Terjadwal</p>
                        <h3 class="text-2xl font-bold text-amber-600 mt-1">{{ number_format($totalTerjadwal) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Belum melaksanakan tes</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center border border-amber-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- FILTER & ACTION TOOLBAR --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3">
                    
                    {{-- Left Filters --}}
                    <form method="GET" action="{{ route('kaur-spmb.administrasi.peserta-seleksi.index') }}" class="flex flex-wrap items-center gap-2">
                        {{-- Academic Year --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">T.P:</span>
                            <select name="tahun_ajaran" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Tanggal Jadwal Piket SPMB --}}
                        <div class="flex items-center gap-1.5">
                            <span class="text-xs font-semibold text-slate-600">Jadwal:</span>
                            <select name="tanggal_id" onchange="this.form.submit()"
                                class="text-xs font-medium rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50 max-w-[200px] truncate">
                                <option value="">-- Semua Jadwal Piket --</option>
                                @foreach($tanggalOptions as $opt)
                                    <option value="{{ $opt['id'] }}" {{ $tanggalId == $opt['id'] ? 'selected' : '' }}>
                                        {{ $opt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Jurusan --}}
                        <select name="jurusan" onchange="this.form.submit()"
                            class="text-xs font-medium rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-2.5 pr-7 bg-slate-50 max-w-[150px]">
                            <option value="">-- Semua Jurusan --</option>
                            @foreach($jurusanList as $jur)
                                <option value="{{ $jur }}" {{ $jurusan == $jur ? 'selected' : '' }}>{{ $jur }}</option>
                            @endforeach
                        </select>

                        {{-- Search Input --}}
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama / no pendaftaran..."
                                class="text-xs pl-8 pr-3 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 w-48 sm:w-56 bg-slate-50">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </form>

                    {{-- Right Actions --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Cetak Presensi Siswa --}}
                        <button type="button" @click="openModalCetakPresensi('siswa')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Hadir Siswa
                        </button>

                        {{-- Cetak Presensi Orang Tua --}}
                        <button type="button" @click="openModalCetakPresensi('ortu')"
                            class="inline-flex items-center gap-1 px-3 py-1.5 text-xs font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-300 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                            </svg>
                            Cetak Hadir Ortu
                        </button>

                        {{-- Tambah Calon Siswa --}}
                        <button type="button" @click="openAddPesertaModal()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            + Tambah Calon Peserta
                        </button>
                    </div>

                </div>
            </div>

            {{-- MAIN TABLE: DAFTAR CALON PESERTA SELEKSI --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-3 py-3 text-center w-10">NO</th>
                                <th class="px-3 py-3">NO. PENDAFTARAN & AKUN</th>
                                <th class="px-3 py-3">NAMA SISWA & ASAL SEKOLAH</th>
                                <th class="px-3 py-3">PILIHAN JURUSAN</th>
                                <th class="px-3 py-3">ORANG TUA / WALI</th>
                                <th class="px-3 py-3">JADWAL TES</th>
                                <th class="px-3 py-3 text-center">PRESENSI SISWA</th>
                                <th class="px-3 py-3 text-center">PRESENSI ORTU</th>
                                <th class="px-3 py-3 text-center w-20">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($pesertas as $idx => $p)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-3 py-2.5 text-center font-medium text-slate-500">
                                        {{ $pesertas->firstItem() + $idx }}
                                    </td>
                                    
                                    {{-- No Pendaftaran & Akun Login --}}
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-slate-900 font-mono text-[11px] text-red-600">
                                            {{ $p->nomor_pendaftaran }}
                                        </div>
                                        <div class="text-[10px] text-slate-500 mt-0.5 flex items-center gap-1" title="Akun Login Calon Siswa">
                                            <span class="font-mono bg-slate-100 px-1 py-0.5 rounded border border-slate-200">{{ $p->user?->username ?? '-' }}</span>
                                            <span class="text-slate-400">/</span>
                                            <span class="font-mono text-slate-600 bg-slate-100 px-1 py-0.5 rounded border border-slate-200">{{ $p->raw_password ?: 'spmb2027' }}</span>
                                        </div>
                                    </td>

                                    {{-- Nama Siswa & Asal Sekolah --}}
                                    <td class="px-3 py-2.5">
                                        <div class="font-bold text-slate-900 flex items-center gap-1.5">
                                            <span>{{ $p->nama_lengkap }}</span>
                                            <span class="text-[10px] px-1 rounded font-normal {{ $p->jenis_kelamin === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600' }}">
                                                {{ $p->jenis_kelamin }}
                                            </span>
                                        </div>
                                        <div class="text-[10.5px] text-slate-500 mt-0.5">
                                            {{ $p->asal_sekolah ?: 'Asal sekolah belum diisi' }}
                                        </div>
                                    </td>

                                    {{-- Jurusan --}}
                                    <td class="px-3 py-2.5">
                                        <span class="px-2 py-0.5 rounded font-semibold text-[10.5px] bg-slate-100 text-slate-800 border border-slate-200">
                                            {{ $p->jurusan_pilihan }}
                                        </span>
                                        @if($p->jurusan_pilihan_2)
                                            <div class="text-[10px] text-slate-400 mt-0.5">P2: {{ $p->jurusan_pilihan_2 }}</div>
                                        @endif
                                    </td>

                                    {{-- Orang Tua & No WA --}}
                                    <td class="px-3 py-2.5">
                                        <div class="font-semibold text-slate-800">
                                            {{ $p->nama_orang_tua ?: '-' }}
                                            <span class="text-[10px] text-slate-400 font-normal">({{ $p->hubungan_wali }})</span>
                                        </div>
                                        @if($p->no_wa_orang_tua)
                                            <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $p->no_wa_orang_tua)) }}" target="_blank"
                                                class="inline-flex items-center gap-1 text-[10px] text-emerald-600 hover:text-emerald-700 font-medium mt-0.5">
                                                <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.77-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.299.045-.677.063-1.092-.069-.252-.08-.575-.187-.988-.365-1.739-.751-2.874-2.502-2.961-2.617-.087-.116-.708-.94-.708-1.793s.448-1.273.607-1.446c.159-.173.346-.217.462-.217l.332.006c.106.005.249-.04.39.298.144.347.491 1.2.534 1.287.043.087.072.188.014.304-.058.116-.087.188-.173.289l-.26.304c-.087.086-.177.18-.076.354.101.174.449.741.964 1.201.662.591 1.221.774 1.394.86.173.086.275.073.376-.043.101-.116.433-.506.549-.68.116-.173.231-.145.39-.087s1.011.477 1.184.564.289.13.332.202c.043.072.043.419-.101.824z"/>
                                                </svg>
                                                {{ $p->no_wa_orang_tua }}
                                            </a>
                                        @endif
                                    </td>

                                    {{-- Jadwal Tes --}}
                                    <td class="px-3 py-2.5">
                                        <div class="font-medium text-slate-800">
                                            {{ $p->formatted_tanggal }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $p->tanggal?->kelompok?->nama_kelompok ?? '-' }}
                                        </div>
                                    </td>

                                    {{-- Quick Kehadiran Siswa --}}
                                    <td class="px-3 py-2.5 text-center">
                                        <form method="POST" action="{{ route('kaur-spmb.administrasi.peserta-seleksi.kehadiran', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="target" value="siswa">
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-[10.5px] font-semibold rounded py-0.5 px-1.5 border-slate-200 focus:ring-0 focus:border-slate-400 {{ $p->status_kehadiran_siswa === 'Hadir' ? 'bg-emerald-50 text-emerald-700' : ($p->status_kehadiran_siswa === 'Tidak Hadir' ? 'bg-rose-50 text-rose-700' : 'bg-slate-50 text-slate-700') }}">
                                                <option value="Terjadwal" {{ $p->status_kehadiran_siswa === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                <option value="Hadir" {{ $p->status_kehadiran_siswa === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="Tidak Hadir" {{ $p->status_kehadiran_siswa === 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Quick Kehadiran Ortu --}}
                                    <td class="px-3 py-2.5 text-center">
                                        <form method="POST" action="{{ route('kaur-spmb.administrasi.peserta-seleksi.kehadiran', $p) }}">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="target" value="ortu">
                                            <select name="status" onchange="this.form.submit()"
                                                class="text-[10.5px] font-semibold rounded py-0.5 px-1.5 border-slate-200 focus:ring-0 focus:border-slate-400 {{ $p->status_kehadiran_ortu === 'Hadir' ? 'bg-purple-50 text-purple-700' : ($p->status_kehadiran_ortu === 'Tidak Hadir' ? 'bg-rose-50 text-rose-700' : 'bg-slate-50 text-slate-700') }}">
                                                <option value="Terjadwal" {{ $p->status_kehadiran_ortu === 'Terjadwal' ? 'selected' : '' }}>Terjadwal</option>
                                                <option value="Hadir" {{ $p->status_kehadiran_ortu === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                                <option value="Tidak Hadir" {{ $p->status_kehadiran_ortu === 'Tidak Hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                                            </select>
                                        </form>
                                    </td>

                                    {{-- Actions --}}
                                    <td class="px-3 py-2.5 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Edit --}}
                                            <button type="button" @click="openEditPesertaModal({{ json_encode($p) }})"
                                                class="p-1 text-slate-500 hover:text-blue-600 rounded hover:bg-slate-100 transition-colors" title="Edit Peserta">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Delete --}}
                                            <form method="POST" action="{{ route('kaur-spmb.administrasi.peserta-seleksi.destroy', $p) }}"
                                                onsubmit="return confirm('Hapus calon siswa {{ $p->nama_lengkap }} beserta akun loginnya?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded hover:bg-red-50 transition-colors" title="Hapus Peserta">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-10 text-center text-slate-400">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <svg class="w-8 h-8 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                            </svg>
                                            <p class="font-semibold text-slate-600">Belum ada Calon Peserta Seleksi</p>
                                            <p class="text-[11px] text-slate-400">Gunakan tombol "+ Tambah Calon Peserta" untuk menambahkan data siswa pendaftar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION --}}
                @if($pesertas->hasPages() || $pesertas->total() > 0)
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500">
                        <div>
                            Menampilkan 
                            <strong class="text-slate-800">{{ $pesertas->firstItem() ?? 0 }}</strong> 
                            sampai 
                            <strong class="text-slate-800">{{ $pesertas->lastItem() ?? 0 }}</strong> 
                            dari 
                            <strong class="text-slate-800">{{ $pesertas->total() }}</strong> 
                            calon peserta seleksi
                        </div>
                        <div>
                            {{ $pesertas->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 1: TAMBAH / EDIT CALON PESERTA         --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalPeserta.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalPesertaBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150 max-h-[92vh]"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalPeserta.maximized,
                        'w-full max-w-2xl': !modalPeserta.maximized && modalPeserta.pos.x === null
                    }"
                    :style="!modalPeserta.maximized && modalPeserta.pos.x !== null ? 'position: fixed; left:' + modalPeserta.pos.x + 'px; top:' + modalPeserta.pos.y + 'px; width: 680px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between select-none flex-shrink-0"
                        :class="!modalPeserta.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalPeserta')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800"
                                x-text="modalPeserta.isEdit ? 'Edit Data Calon Peserta Seleksi' : 'Tambah Calon Peserta Seleksi Baru'">
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalPeserta')"
                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalPeserta.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalPeserta.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalPeserta')"
                                class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form :action="modalPeserta.actionUrl" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf
                        <template x-if="modalPeserta.isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">

                        {{-- Section 1: Tanggal & No Pendaftaran --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3 bg-slate-50 rounded-md border border-slate-200">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Jadwal Piket / Tanggal Tes <span class="text-red-500">*</span>
                                </label>
                                <select name="spmb_piket_tanggal_id" x-model="modalPeserta.form.spmb_piket_tanggal_id" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                    <option value="">-- Pilih Hari Sabtu Piket --</option>
                                    @foreach($tanggalOptions as $opt)
                                        <option value="{{ $opt['id'] }}">{{ $opt['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">
                                    Nomor Pendaftaran / No. Tes <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="nomor_pendaftaran" x-model="modalPeserta.form.nomor_pendaftaran" required
                                    :readonly="modalPeserta.isEdit"
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono font-bold text-red-600 bg-white"
                                    :class="modalPeserta.isEdit ? 'bg-slate-100 cursor-not-allowed' : ''">
                            </div>
                        </div>

                        {{-- Section 2: Data Calon Siswa --}}
                        <div class="space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                                Identitas Calon Siswa
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div class="sm:col-span-2">
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Lengkap Siswa <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_lengkap" x-model="modalPeserta.form.nama_lengkap" required placeholder="Sesuai Ijazah/Akte"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jenis Kelamin <span class="text-red-500">*</span></label>
                                    <select name="jenis_kelamin" x-model="modalPeserta.form.jenis_kelamin" required
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="L">Laki-laki (L)</option>
                                        <option value="P">Perempuan (P)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">NISN (Opsional)</label>
                                    <input type="text" name="nisn" x-model="modalPeserta.form.nisn" placeholder="10 digit NISN"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">NIK (Opsional)</label>
                                    <input type="text" name="nik" x-model="modalPeserta.form.nik" placeholder="16 digit NIK"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">No. WhatsApp Siswa</label>
                                    <input type="text" name="no_wa_siswa" x-model="modalPeserta.form.no_wa_siswa" placeholder="08xxxxxxxxxx"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Asal Sekolah SMP/MTs</label>
                                    <input type="text" name="asal_sekolah" x-model="modalPeserta.form.asal_sekolah" placeholder="Contoh: SMPN 1 Pringsewu"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pilihan Jurusan 1 <span class="text-red-500">*</span></label>
                                    <select name="jurusan_pilihan" x-model="modalPeserta.form.jurusan_pilihan" required
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="">-- Pilih Jurusan Utama --</option>
                                        @foreach($jurusanList as $jur)
                                            <option value="{{ $jur }}">{{ $jur }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Pilihan Jurusan 2 (Opsional)</label>
                                    <select name="jurusan_pilihan_2" x-model="modalPeserta.form.jurusan_pilihan_2"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="">-- Tidak Memilih --</option>
                                        @foreach($jurusanList as $jur)
                                            <option value="{{ $jur }}">{{ $jur }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Data Orang Tua / Wali --}}
                        <div class="space-y-3 pt-2 border-t border-slate-200">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-purple-600"></span>
                                Data Orang Tua / Wali Pendamping
                            </h4>

                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Orang Tua / Wali</label>
                                    <input type="text" name="nama_orang_tua" x-model="modalPeserta.form.nama_orang_tua" placeholder="Nama ayah / ibu / wali"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Hubungan Keluarga</label>
                                    <select name="hubungan_wali" x-model="modalPeserta.form.hubungan_wali"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="Orang Tua">Orang Tua (Ayah/Ibu)</option>
                                        <option value="Ayah">Ayah Kandung</option>
                                        <option value="Ibu">Ibu Kandung</option>
                                        <option value="Wali">Wali</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">No. WhatsApp Orang Tua</label>
                                    <input type="text" name="no_wa_orang_tua" x-model="modalPeserta.form.no_wa_orang_tua" placeholder="08xxxxxxxxxx"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono">
                                </div>
                            </div>
                        </div>

                        {{-- Section 4: Akun Login Calon Siswa --}}
                        <div class="p-3 bg-amber-50/60 rounded-md border border-amber-200 text-xs text-amber-900 space-y-2">
                            <div class="font-bold flex items-center gap-1.5 text-amber-950">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                </svg>
                                Akun Login Siswa Otomatis Dibuat
                            </div>
                            <p class="text-[11px] text-amber-800 leading-relaxed">
                                Sistem secara otomatis membuatkan akun login user (Role: <strong>Calon Siswa</strong>) dengan Username berupa Nomor Pendaftaran. Akun ini disiapkan untuk calon siswa mengakses portal seleksi/ujian online mendatang.
                            </p>
                            <div>
                                <label class="block text-[11px] font-semibold text-amber-950 mb-1">
                                    Password Login Siswa (Opsional)
                                </label>
                                <input type="text" name="custom_password" x-model="modalPeserta.form.custom_password"
                                    :placeholder="modalPeserta.isEdit ? 'Kosongkan jika tidak ingin mengubah password' : 'Default: sama dengan nomor pendaftaran'"
                                    class="w-full text-xs px-2.5 py-1.5 border border-amber-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono bg-white">
                            </div>
                        </div>

                        {{-- Status Kehadiran (khusus saat edit) --}}
                        <template x-if="modalPeserta.isEdit">
                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status Kehadiran Siswa</label>
                                    <select name="status_kehadiran_siswa" x-model="modalPeserta.form.status_kehadiran_siswa"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="Terjadwal">Terjadwal</option>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Tidak Hadir">Tidak Hadir</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Status Kehadiran Orang Tua</label>
                                    <select name="status_kehadiran_ortu" x-model="modalPeserta.form.status_kehadiran_ortu"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 bg-white">
                                        <option value="Terjadwal">Terjadwal</option>
                                        <option value="Hadir">Hadir</option>
                                        <option value="Tidak Hadir">Tidak Hadir</option>
                                    </select>
                                </div>
                            </div>
                        </template>

                        {{-- Catatan --}}
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                            <textarea name="catatan" x-model="modalPeserta.form.catatan" rows="2"
                                placeholder="Misal: konfirmasi kehadiran, berkas khusus, dll"
                                class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                        </div>

                        {{-- Footer Action --}}
                        <div class="pt-3 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalPeserta')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                Simpan Calon Siswa
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL 2: PILIH TANGGAL CETAK PRESENSI        --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalCetak.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden w-full max-w-md">
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800"
                            x-text="modalCetak.target === 'siswa' ? 'Cetak Daftar Hadir Calon Siswa' : 'Cetak Daftar Hadir Orang Tua Pendamping'">
                        </h3>
                        <button type="button" @click="modalCetak.open = false" class="text-slate-400 hover:text-red-600">&times;</button>
                    </div>

                    <form :action="modalCetak.target === 'siswa' ? '{{ route('kaur-spmb.administrasi.peserta-seleksi.cetak-hadir-siswa') }}' : '{{ route('kaur-spmb.administrasi.peserta-seleksi.cetak-hadir-ortu') }}'"
                        method="GET" target="_blank" class="p-5 space-y-4">
                        <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Tanggal Jadwal Piket SPMB <span class="text-red-500">*</span>
                            </label>
                            <select name="tanggal_id" required
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-medium bg-white">
                                @foreach($tanggalOptions as $opt)
                                    <option value="{{ $opt['id'] }}" {{ $tanggalId == $opt['id'] ? 'selected' : '' }}>
                                        {{ $opt['label'] }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Lembar absensi akan dicetak dalam format A4 Portrait lengkap dengan Kop Surat resmi.
                            </p>
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="modalCetak.open = false"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 rounded-md">
                                Batal
                            </button>
                            <button type="submit" @click="modalCetak.open = false"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm">
                                Buka Dokumen Cetak
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function pesertaSeleksiPage() {
            return {
                suggestedNoPendaftaran: @json($suggestedNoPendaftaran),
                tanggalOptions: @json($tanggalOptions),
                defaultTanggalId: @json($tanggalId) || (@json($tanggalOptions)[0]?.id || ''),

                modalPeserta: {
                    open: false,
                    isEdit: false,
                    maximized: false,
                    actionUrl: '{{ route("kaur-spmb.administrasi.peserta-seleksi.store") }}',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 },
                    form: {
                        spmb_piket_tanggal_id: '',
                        nomor_pendaftaran: '',
                        nama_lengkap: '',
                        nisn: '',
                        nik: '',
                        jenis_kelamin: 'L',
                        asal_sekolah: '',
                        jurusan_pilihan: 'Teknik Komputer dan Jaringan',
                        jurusan_pilihan_2: '',
                        no_wa_siswa: '',
                        nama_orang_tua: '',
                        hubungan_wali: 'Orang Tua',
                        no_wa_orang_tua: '',
                        custom_password: '',
                        status_kehadiran_siswa: 'Terjadwal',
                        status_kehadiran_ortu: 'Terjadwal',
                        catatan: ''
                    }
                },

                modalCetak: {
                    open: false,
                    target: 'siswa'
                },

                init() {
                    window.addEventListener('mousemove', (e) => this.onMouseMove(e));
                    window.addEventListener('mouseup', () => this.onMouseUp());
                },

                openAddPesertaModal() {
                    this.modalPeserta.isEdit = false;
                    this.modalPeserta.actionUrl = '{{ route("kaur-spmb.administrasi.peserta-seleksi.store") }}';
                    
                    this.modalPeserta.form = {
                        spmb_piket_tanggal_id: this.defaultTanggalId,
                        nomor_pendaftaran: this.suggestedNoPendaftaran,
                        nama_lengkap: '',
                        nisn: '',
                        nik: '',
                        jenis_kelamin: 'L',
                        asal_sekolah: '',
                        jurusan_pilihan: 'Teknik Komputer dan Jaringan',
                        jurusan_pilihan_2: '',
                        no_wa_siswa: '',
                        nama_orang_tua: '',
                        hubungan_wali: 'Orang Tua',
                        no_wa_orang_tua: '',
                        custom_password: '',
                        status_kehadiran_siswa: 'Terjadwal',
                        status_kehadiran_ortu: 'Terjadwal',
                        catatan: ''
                    };

                    this.modalPeserta.pos = { x: null, y: null };
                    this.modalPeserta.maximized = false;
                    this.modalPeserta.open = true;
                },

                openEditPesertaModal(p) {
                    this.modalPeserta.isEdit = true;
                    this.modalPeserta.actionUrl = '{{ url("kaur-spmb/administrasi/peserta-seleksi") }}/' + p.id;
                    
                    this.modalPeserta.form = {
                        spmb_piket_tanggal_id: p.spmb_piket_tanggal_id,
                        nomor_pendaftaran: p.nomor_pendaftaran,
                        nama_lengkap: p.nama_lengkap,
                        nisn: p.nisn || '',
                        nik: p.nik || '',
                        jenis_kelamin: p.jenis_kelamin || 'L',
                        asal_sekolah: p.asal_sekolah || '',
                        jurusan_pilihan: p.jurusan_pilihan,
                        jurusan_pilihan_2: p.jurusan_pilihan_2 || '',
                        no_wa_siswa: p.no_wa_siswa || '',
                        nama_orang_tua: p.nama_orang_tua || '',
                        hubungan_wali: p.hubungan_wali || 'Orang Tua',
                        no_wa_orang_tua: p.no_wa_orang_tua || '',
                        custom_password: '',
                        status_kehadiran_siswa: p.status_kehadiran_siswa || 'Terjadwal',
                        status_kehadiran_ortu: p.status_kehadiran_ortu || 'Terjadwal',
                        catatan: p.catatan || ''
                    };

                    this.modalPeserta.pos = { x: null, y: null };
                    this.modalPeserta.maximized = false;
                    this.modalPeserta.open = true;
                },

                openModalCetakPresensi(target) {
                    this.modalCetak.target = target;
                    this.modalCetak.open = true;
                },

                closeModal(key) {
                    this[key].open = false;
                },

                toggleMaximize(key) {
                    this[key].maximized = !this[key].maximized;
                },

                startDragModal(e, key) {
                    if (this[key].maximized) return;
                    if (e.target.closest('button') || e.target.closest('input') || e.target.closest('select') || e.target.closest('textarea')) return;

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
                    if (this.modalPeserta.drag && this.modalPeserta.drag.active) {
                        const dx = e.clientX - this.modalPeserta.drag.startX;
                        const dy = e.clientY - this.modalPeserta.drag.startY;
                        this.modalPeserta.pos.x = Math.max(10, Math.min(window.innerWidth - 100, this.modalPeserta.drag.initialX + dx));
                        this.modalPeserta.pos.y = Math.max(10, Math.min(window.innerHeight - 100, this.modalPeserta.drag.initialY + dy));
                    }
                },

                onMouseUp() {
                    if (this.modalPeserta.drag) {
                        this.modalPeserta.drag.active = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
