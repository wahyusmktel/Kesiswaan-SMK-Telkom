<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Kelola Absensi Piket SPMB</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="absensiPiketPage()" x-init="init()">
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

            {{-- KPI STATS CARDS --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Total Jadwal</p>
                    <h3 class="text-xl font-bold text-slate-800 mt-0.5">{{ $stats['total_jadwal'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Pegawai bertugas</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-emerald-600 uppercase tracking-wider">Hadir</p>
                    <h3 class="text-xl font-bold text-emerald-700 mt-0.5">{{ $stats['hadir'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Tepat waktu & absen</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-amber-600 uppercase tracking-wider">Izin</p>
                    <h3 class="text-xl font-bold text-amber-700 mt-0.5">{{ $stats['izin'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Izin terkonfirmasi</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-rose-600 uppercase tracking-wider">Sakit</p>
                    <h3 class="text-xl font-bold text-rose-700 mt-0.5">{{ $stats['sakit'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Keterangan sakit</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-slate-500 uppercase tracking-wider">Alpa / Belum</p>
                    <h3 class="text-xl font-bold text-slate-700 mt-0.5">{{ $stats['alpa'] }}</h3>
                    <p class="text-[10px] text-slate-400 mt-0.5">Belum ada catatan</p>
                </div>

                <div class="bg-white p-3.5 rounded-lg border border-slate-200 shadow-sm">
                    <p class="text-[11px] font-medium text-blue-600 uppercase tracking-wider">% Kehadiran</p>
                    <h3 class="text-xl font-bold text-blue-700 mt-0.5">{{ $stats['persentase'] }}%</h3>
                    <div class="w-full bg-slate-100 rounded-full h-1.5 mt-1.5 overflow-hidden">
                        <div class="bg-blue-600 h-1.5 rounded-full" style="width: {{ min(100, $stats['persentase']) }}%"></div>
                    </div>
                </div>
            </div>

            {{-- CONTENT ACTION TOOLBAR --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3">
                    
                    {{-- Filter & Search Form --}}
                    <form method="GET" action="{{ route('kaur-spmb.absensi.index') }}" class="flex flex-wrap items-center gap-2.5 w-full lg:w-auto">
                        <input type="hidden" name="tab" value="{{ $activeTab }}">

                        {{-- Academic Year --}}
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

                        {{-- Filter Tanggal Piket --}}
                        <div class="flex items-center gap-1.5">
                            <label class="text-xs font-medium text-slate-500">Tanggal:</label>
                            <select name="tanggal_id" onchange="this.form.submit()"
                                class="text-xs border-slate-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm py-1.5 px-2.5 max-w-[180px]">
                                <option value="">-- Semua Tanggal Sabtu --</option>
                                @foreach($allTanggals as $tgl)
                                    <option value="{{ $tgl->id }}" {{ $selectedTanggalId == $tgl->id ? 'selected' : '' }}>
                                        {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }} ({{ $tgl->kelompok->nama_kelompok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Filter Status --}}
                        @if($activeTab === 'daftar')
                            <div class="flex items-center gap-1.5">
                                <label class="text-xs font-medium text-slate-500">Status:</label>
                                <select name="status" onchange="this.form.submit()"
                                    class="text-xs border-slate-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm py-1.5 px-2.5">
                                    <option value="">Semua Status</option>
                                    <option value="Hadir" {{ $selectedStatus === 'Hadir' ? 'selected' : '' }}>Hadir</option>
                                    <option value="Izin" {{ $selectedStatus === 'Izin' ? 'selected' : '' }}>Izin</option>
                                    <option value="Sakit" {{ $selectedStatus === 'Sakit' ? 'selected' : '' }}>Sakit</option>
                                    <option value="Alpa" {{ $selectedStatus === 'Alpa' ? 'selected' : '' }}>Alpa</option>
                                </select>
                            </div>

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
                        <div class="relative flex-1 min-w-[190px]">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama guru / catatan..."
                                class="w-full text-xs pl-8 pr-3 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                        </div>

                        <button type="submit"
                            class="px-3 py-1.5 text-xs font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 border border-slate-300 rounded-md transition-colors">
                            Filter
                        </button>

                        @if($search !== '' || $selectedTanggalId || $selectedStatus)
                            <a href="{{ route('kaur-spmb.absensi.index', ['tahun_ajaran' => $selectedYear, 'tab' => $activeTab]) }}"
                                class="px-2.5 py-1.5 text-xs text-red-600 hover:text-red-700 hover:underline">
                                Reset
                            </a>
                        @endif
                    </form>

                    {{-- Action Buttons (CRUD strictly in content area) --}}
                    <div class="flex flex-wrap items-center gap-2">
                        {{-- Tab Switcher --}}
                        <div class="inline-flex rounded-md border border-slate-300 bg-slate-100 p-0.5">
                            <a href="{{ route('kaur-spmb.absensi.index', ['tahun_ajaran' => $selectedYear, 'tab' => 'daftar', 'tanggal_id' => $selectedTanggalId, 'status' => $selectedStatus, 'search' => $search]) }}"
                                class="px-2.5 py-1 text-xs font-semibold rounded {{ $activeTab === 'daftar' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Daftar Absensi
                            </a>
                            <a href="{{ route('kaur-spmb.absensi.index', ['tahun_ajaran' => $selectedYear, 'tab' => 'rekap', 'tanggal_id' => $selectedTanggalId, 'search' => $search]) }}"
                                class="px-2.5 py-1 text-xs font-semibold rounded {{ $activeTab === 'rekap' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                                Rekapitulasi Pegawai
                            </a>
                        </div>

                        {{-- Input Manual Attendance Button --}}
                        <button type="button" @click="openManualInputModal()"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Input Absensi Manual
                        </button>

                        {{-- Export Excel Button --}}
                        <a href="{{ route('kaur-spmb.absensi.export', ['tahun_ajaran' => $selectedYear, 'tanggal_id' => $selectedTanggalId, 'status' => $selectedStatus]) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-emerald-800 bg-emerald-50 hover:bg-emerald-100 border border-emerald-300 rounded-md shadow-sm transition-colors"
                            title="Download format tabel excel rapi dan lembut untuk dianalisa">
                            <svg class="w-3.5 h-3.5 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Export Excel
                        </a>
                    </div>

                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- TAB 1: DAFTAR ABSENSI DETAIL                             --}}
            {{-- ======================================================== --}}
            @if($activeTab === 'daftar')
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-700">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-600 bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                                    <th scope="col" class="px-4 py-3">Hari & Tanggal</th>
                                    <th scope="col" class="px-4 py-3">Kelompok</th>
                                    <th scope="col" class="px-4 py-3">Nama Petugas Piket</th>
                                    <th scope="col" class="px-4 py-3 text-center">Status</th>
                                    <th scope="col" class="px-4 py-3 text-center">Waktu Masuk</th>
                                    <th scope="col" class="px-4 py-3 text-center">Bukti Eviden</th>
                                    <th scope="col" class="px-4 py-3">Metode Input</th>
                                    <th scope="col" class="px-4 py-3">Keterangan</th>
                                    <th scope="col" class="px-4 py-3 text-center w-20">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($absensis as $index => $item)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-3 text-center font-medium text-slate-500">
                                            {{ $absensis->firstItem() + $index }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900 whitespace-nowrap">
                                            {{ $item->tanggal->hari ?? 'Sabtu' }}, 
                                            {{ $item->tanggal ? \Carbon\Carbon::parse($item->tanggal->tanggal)->locale('id')->translatedFormat('d M Y') : '-' }}
                                        </td>
                                        <td class="px-4 py-3 font-bold text-slate-800 whitespace-nowrap">
                                            {{ $item->petugas?->kelompok?->nama_kelompok ?? ($item->tanggal?->kelompok?->nama_kelompok ?? '-') }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-slate-900">
                                            {{ $item->petugas->nama_petugas ?? '-' }}
                                            <div class="text-[10px] text-slate-400 font-normal">
                                                {{ $item->petugas?->jabatan ?? 'Petugas Piket SPMB' }}
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($item->status === 'Hadir')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    Hadir
                                                </span>
                                            @elseif($item->status === 'Izin')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    Izin
                                                </span>
                                            @elseif($item->status === 'Sakit')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    Sakit
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                                                    Alpa
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center text-slate-600 font-medium whitespace-nowrap">
                                            {{ $item->formatted_jam_absen }} WIB
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            @if($item->foto_eviden)
                                                <button type="button" @click="openImageModal('{{ $item->foto_eviden_url }}', '{{ addslashes($item->petugas->nama_petugas ?? '') }}')"
                                                    class="inline-flex items-center gap-1 px-2 py-1 text-[11px] font-semibold text-blue-700 bg-blue-50 hover:bg-blue-100 rounded border border-blue-200 transition-colors">
                                                    <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Lihat Foto
                                                </button>
                                            @else
                                                <span class="text-slate-400 italic text-[11px]">Tanpa Foto</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            @if($item->metode_input === 'manual_kaur')
                                                <span class="inline-flex items-center gap-1 text-[11px] text-amber-700 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                                    Manual Kaur
                                                </span>
                                            @else
                                                <span class="inline-flex items-center gap-1 text-[11px] text-emerald-700 font-medium">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    Mandiri (Guru)
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-slate-600 max-w-xs truncate" title="{{ $item->keterangan }}">
                                            {{ $item->keterangan ?: '-' }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <form method="POST" action="{{ route('kaur-spmb.absensi.destroy', $item) }}"
                                                onsubmit="return confirm('Hapus data absensi ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Hapus Data Absensi">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="px-4 py-8 text-center text-slate-400">
                                            Belum ada data absensi untuk filter yang dipilih.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Pagination Controls --}}
                    @if($absensis->hasPages() || $absensis->total() > 0)
                        <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500">
                            <div>
                                Menampilkan 
                                <strong class="text-slate-800">{{ $absensis->firstItem() ?? 0 }}</strong> 
                                sampai 
                                <strong class="text-slate-800">{{ $absensis->lastItem() ?? 0 }}</strong> 
                                dari 
                                <strong class="text-slate-800">{{ $absensis->total() }}</strong> 
                                data absensi
                            </div>
                            <div>
                                {{ $absensis->links() }}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- TAB 2: REKAPITULASI PER PEGAWAI                          --}}
            {{-- ======================================================== --}}
            @if($activeTab === 'rekap')
                <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs text-left text-slate-700">
                            <thead class="text-[11px] font-bold uppercase tracking-wider text-slate-600 bg-slate-50 border-b border-slate-200">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-center w-12">No</th>
                                    <th scope="col" class="px-4 py-3">Nama Guru / Pegawai</th>
                                    <th scope="col" class="px-4 py-3">Kelompok</th>
                                    <th scope="col" class="px-4 py-3 text-center">Total Jadwal</th>
                                    <th scope="col" class="px-4 py-3 text-center text-emerald-700">Hadir</th>
                                    <th scope="col" class="px-4 py-3 text-center text-amber-700">Izin</th>
                                    <th scope="col" class="px-4 py-3 text-center text-rose-700">Sakit</th>
                                    <th scope="col" class="px-4 py-3 text-center text-slate-500">Alpa</th>
                                    <th scope="col" class="px-4 py-3 text-center w-40">% Kehadiran</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200">
                                @forelse($rekapPerPegawai as $idx => $r)
                                    <tr class="hover:bg-slate-50/80 transition-colors">
                                        <td class="px-4 py-3 text-center font-medium text-slate-500">
                                            {{ $idx + 1 }}
                                        </td>
                                        <td class="px-4 py-3 font-semibold text-slate-900">
                                            {{ $r['nama_petugas'] }}
                                        </td>
                                        <td class="px-4 py-3 font-medium text-slate-700 whitespace-nowrap">
                                            {{ $r['nama_kelompok'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-slate-800">
                                            {{ $r['total_jadwal'] }}x
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-emerald-700">
                                            {{ $r['hadir'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-amber-700">
                                            {{ $r['izin'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-bold text-rose-700">
                                            {{ $r['sakit'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center font-medium text-slate-500">
                                            {{ $r['alpa'] }}
                                        </td>
                                        <td class="px-4 py-3 text-center whitespace-nowrap">
                                            <div class="flex items-center justify-center gap-2">
                                                <span class="font-bold text-xs {{ $r['persentase'] >= 80 ? 'text-emerald-700' : ($r['persentase'] >= 50 ? 'text-amber-700' : 'text-slate-600') }}">
                                                    {{ $r['persentase'] }}%
                                                </span>
                                                <div class="w-16 bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                                    <div class="h-1.5 rounded-full {{ $r['persentase'] >= 80 ? 'bg-emerald-500' : ($r['persentase'] >= 50 ? 'bg-amber-500' : 'bg-slate-400') }}"
                                                        style="width: {{ min(100, $r['persentase']) }}%"></div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="px-4 py-8 text-center text-slate-400">
                                            Belum ada data pegawai untuk tahun ajaran ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL: INPUT ABSENSI MANUAL                  --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalManual.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalManualBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalManual.maximized,
                        'w-full max-w-lg': !modalManual.maximized && modalManual.pos.x === null
                    }"
                    :style="!modalManual.maximized && modalManual.pos.x !== null ? 'position: fixed; left:' + modalManual.pos.x + 'px; top:' + modalManual.pos.y + 'px; width: 540px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between select-none"
                        :class="!modalManual.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event)">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800">
                                Input Absensi Kehadiran Manual (Kaur SPMB)
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize()"
                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalManual.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalManual.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="modalManual.open = false"
                                class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form action="{{ route('kaur-spmb.absensi.store-manual') }}" method="POST" enctype="multipart/form-data" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf

                        {{-- Tanggal Piket --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Jadwal Tanggal Piket <span class="text-red-500">*</span></label>
                            <select name="spmb_piket_tanggal_id" required x-model="modalManual.selectedTanggalId" @change="onTanggalChange()"
                                class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                <option value="">-- Pilih Hari Sabtu Terjadwal --</option>
                                @foreach($allTanggals as $tgl)
                                    <option value="{{ $tgl->id }}">
                                        {{ $tgl->hari }}, {{ \Carbon\Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d M Y') }} ({{ $tgl->kelompok->nama_kelompok }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Petugas --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Pilih Guru / Pegawai <span class="text-red-500">*</span></label>
                            <select name="spmb_piket_petugas_id" required
                                class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach($kelompoks as $kel)
                                    <optgroup label="{{ $kel->nama_kelompok }}">
                                        @foreach($kel->petugas as $p)
                                            <option value="{{ $p->id }}">{{ $p->nama_petugas }} ({{ $kel->nama_kelompok }})</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>

                        {{-- Status & Jam --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Status Kehadiran <span class="text-red-500">*</span></label>
                                <select name="status" required class="w-full text-xs border border-slate-300 rounded-md py-2 px-3 focus:border-red-500 focus:ring-red-500">
                                    <option value="Hadir">Hadir</option>
                                    <option value="Izin">Izin</option>
                                    <option value="Sakit">Sakit</option>
                                    <option value="Alpa">Alpa</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jam Absen / Masuk</label>
                                <input type="time" name="jam_absen" value="{{ now()->format('H:i') }}"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        {{-- Foto Eviden --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Foto Bukti Eviden (Opsional jika manual)</label>
                            <input type="file" name="foto_eviden" accept="image/*"
                                class="w-full text-xs file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200 border border-slate-300 rounded-md">
                            <p class="text-[10px] text-slate-400 mt-1">Maksimal ukuran file: 5 MB (JPG, PNG).</p>
                        </div>

                        {{-- Keterangan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Keterangan / Alasan Manual</label>
                            <textarea name="keterangan" rows="2" placeholder="Contoh: Petugas hadir di lokasi namun terkendala login / HP lowbat..."
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                        </div>

                        <div class="pt-2 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="modalManual.open = false"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                Simpan Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

        {{-- ======================================================== --}}
        {{-- LIGHTBOX MODAL: PREVIEW FOTO EVIDEN                      --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalImage.open"
                @click="modalImage.open = false"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div class="relative bg-white rounded-lg p-2 max-w-xl w-full shadow-2xl" @click.stop>
                    <div class="flex items-center justify-between pb-2 border-b border-slate-200 px-2">
                        <span class="text-xs font-bold text-slate-800" x-text="'Eviden Kehadiran: ' + modalImage.title"></span>
                        <button type="button" @click="modalImage.open = false" class="text-slate-400 hover:text-red-600 text-lg">&times;</button>
                    </div>
                    <div class="p-2 flex items-center justify-center bg-slate-100 rounded mt-2">
                        <img :src="modalImage.url" alt="Eviden Piket SPMB" class="max-h-[75vh] w-auto rounded object-contain shadow-sm">
                    </div>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function absensiPiketPage() {
            return {
                modalManual: {
                    open: false,
                    maximized: false,
                    selectedTanggalId: '{{ $selectedTanggalId ?? "" }}',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 }
                },
                modalImage: {
                    open: false,
                    url: '',
                    title: ''
                },

                init() {
                    window.addEventListener('mousemove', (e) => this.onMouseMove(e));
                    window.addEventListener('mouseup', () => this.onMouseUp());
                },

                openManualInputModal() {
                    this.modalManual.pos = { x: null, y: null };
                    this.modalManual.maximized = false;
                    this.modalManual.open = true;
                },

                openImageModal(url, title) {
                    this.modalImage.url = url;
                    this.modalImage.title = title;
                    this.modalImage.open = true;
                },

                toggleMaximize() {
                    this.modalManual.maximized = !this.modalManual.maximized;
                },

                startDragModal(e) {
                    if (this.modalManual.maximized) return;
                    if (e.target.closest('button') || e.target.closest('input') || e.target.closest('select')) return;

                    const el = this.$refs.modalManualBox;
                    if (!el) return;

                    const rect = el.getBoundingClientRect();
                    if (this.modalManual.pos.x === null) {
                        this.modalManual.pos.x = rect.left;
                        this.modalManual.pos.y = rect.top;
                    }

                    this.modalManual.drag.active = true;
                    this.modalManual.drag.startX = e.clientX;
                    this.modalManual.drag.startY = e.clientY;
                    this.modalManual.drag.initialX = this.modalManual.pos.x;
                    this.modalManual.drag.initialY = this.modalManual.pos.y;
                },

                onMouseMove(e) {
                    if (this.modalManual.drag && this.modalManual.drag.active) {
                        const dx = e.clientX - this.modalManual.drag.startX;
                        const dy = e.clientY - this.modalManual.drag.startY;
                        this.modalManual.pos.x = Math.max(10, Math.min(window.innerWidth - 100, this.modalManual.drag.initialX + dx));
                        this.modalManual.pos.y = Math.max(10, Math.min(window.innerHeight - 100, this.modalManual.drag.initialY + dy));
                    }
                },

                onMouseUp() {
                    if (this.modalManual.drag) {
                        this.modalManual.drag.active = false;
                    }
                },

                onTanggalChange() {
                    // Placeholder for future dynamic officer filtering if desired
                }
            };
        }
    </script>
</x-app-layout>
