<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-base text-slate-800 leading-tight">Industri Kerjasama</h2>
            <p class="text-xs text-slate-500">Kemitraan Dunia Usaha & Dunia Industri (DUDI)</p>
        </div>
    </x-slot>

    <div class="py-6" x-data="{
        createModalOpen: false,
        editModalOpen: false,
        detailModalOpen: false,
        activeIndustri: null,
        openEdit(item) {
            this.activeIndustri = item;
            this.editModalOpen = true;
        },
        openDetail(item) {
            this.activeIndustri = item;
            this.detailModalOpen = true;
        }
    }" @open-create-modal.window="createModalOpen = true">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Page Title & Action Banner -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2 mb-1.5">
                        <span class="inline-flex items-center rounded-lg bg-red-50 border border-red-100 px-2.5 py-0.5 text-xs font-bold text-red-700">
                            HUBIN SINERGI UP & ALUMNI
                        </span>
                        <span class="inline-flex items-center rounded-lg bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">
                            Kemitraan DUDI
                        </span>
                    </div>
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">Data Industri Kerjasama</h1>
                    <p class="text-xs text-slate-500 mt-0.5">Kelola data kemitraan dunia usaha dan dunia industri (DUDI) yang bekerja sama dengan SMK Telkom Lampung.</p>
                </div>
                <div class="flex-shrink-0">
                    <button type="button" @click="createModalOpen = true"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm shadow-red-200 transition-all hover:bg-red-700 hover:shadow-md focus:outline-none focus:ring-2 focus:ring-red-500/20 active:scale-95">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Mitra Industri
                    </button>
                </div>
            </div>

            <!-- Flash Alert -->
            @if(session('success'))
                <div class="flex items-center justify-between rounded-xl bg-emerald-50 border border-emerald-200 p-4 text-emerald-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="flex items-center justify-between rounded-xl bg-red-50 border border-red-200 p-4 text-red-800 shadow-sm">
                    <div class="flex items-center gap-3">
                        <svg class="h-5 w-5 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm font-semibold">{{ session('error') }}</p>
                    </div>
                    <button type="button" @click="$el.parentElement.remove()" class="text-red-500 hover:text-red-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            @endif

            <!-- 1. KPI Metrik Cards -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Total Industri -->
                <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="rounded-xl bg-slate-100 p-2.5 text-slate-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total DUDI</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-slate-900">{{ $totalIndustri }}</div>
                        <p class="mt-1 text-xs text-slate-500">Mitra industri terdaftar</p>
                    </div>
                </div>

                <!-- MoU Aktif -->
                <div class="rounded-2xl border border-emerald-100 bg-gradient-to-br from-white to-emerald-50/40 p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="rounded-xl bg-emerald-100 p-2.5 text-emerald-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-700 uppercase tracking-wider">Aktif</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-emerald-700">{{ $totalAktif }}</div>
                        <p class="mt-1 text-xs text-emerald-600 font-medium">Kerjasama masih berlaku</p>
                    </div>
                </div>

                <!-- Segera Berakhir -->
                <div class="rounded-2xl border border-amber-100 bg-gradient-to-br from-white to-amber-50/40 p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="rounded-xl bg-amber-100 p-2.5 text-amber-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800 uppercase tracking-wider">≤ 60 Hari</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-amber-700">{{ $totalSegeraBerakhir }}</div>
                        <p class="mt-1 text-xs text-amber-600 font-medium">Perlu perpanjangan segera</p>
                    </div>
                </div>

                <!-- Berakhir / Nonaktif -->
                <div class="rounded-2xl border border-rose-100 bg-gradient-to-br from-white to-rose-50/40 p-5 shadow-sm transition hover:shadow-md">
                    <div class="flex items-center justify-between">
                        <span class="rounded-xl bg-rose-100 p-2.5 text-rose-700">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                            </svg>
                        </span>
                        <span class="rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800 uppercase tracking-wider">Kadaluarsa</span>
                    </div>
                    <div class="mt-4">
                        <div class="text-3xl font-black text-rose-700">{{ $totalBerakhir }}</div>
                        <p class="mt-1 text-xs text-rose-600 font-medium">MoU habis / nonaktif</p>
                    </div>
                </div>
            </div>

            <!-- 2. Filter & Pencarian Toolbar -->
            <div class="rounded-2xl border border-slate-200/80 bg-white p-5 shadow-sm">
                <form method="GET" action="{{ route('hubin.industri.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12 items-end">
                    <!-- Search Input -->
                    <div class="md:col-span-4">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Pencarian</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-5.2-5.2m2.2-4.8a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari nama, kota, PIC, nomor MoU..."
                                class="w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-9 pr-3 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100 transition">
                        </div>
                    </div>

                    <!-- Filter Status MoU -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Status MoU</label>
                        <select name="status_mou"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100 transition">
                            <option value="all" {{ request('status_mou') == 'all' ? 'selected' : '' }}>Semua Status</option>
                            <option value="aktif" {{ request('status_mou') == 'aktif' ? 'selected' : '' }}>MoU Aktif</option>
                            <option value="segera_berakhir" {{ request('status_mou') == 'segera_berakhir' ? 'selected' : '' }}>Segera Berakhir (≤ 60 Hari)</option>
                            <option value="berakhir" {{ request('status_mou') == 'berakhir' ? 'selected' : '' }}>Sudah Berakhir</option>
                            <option value="nonaktif" {{ request('status_mou') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                        </select>
                    </div>

                    <!-- Filter Bidang Usaha -->
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Bidang Usaha</label>
                        <select name="bidang_usaha"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100 transition">
                            <option value="all">Semua Bidang Usaha</option>
                            @foreach($bidangUsahaOptions as $opt)
                                <option value="{{ $opt }}" {{ request('bidang_usaha') == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter Bentuk Kerjasama -->
                    <div class="md:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-600 mb-1.5">Bentuk Kerjasama</label>
                        <select name="bentuk_kerjasama"
                            class="w-full rounded-xl border border-slate-200 bg-slate-50/50 px-3 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:bg-white focus:ring-2 focus:ring-red-100 transition">
                            <option value="all">Semua Bentuk</option>
                            @foreach($bentukKerjasamaOptions as $b)
                                <option value="{{ $b }}" {{ request('bentuk_kerjasama') == $b ? 'selected' : '' }}>{{ $b }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Actions -->
                    <div class="md:col-span-1 flex gap-1.5">
                        <button type="submit"
                            class="w-full inline-flex items-center justify-center rounded-xl bg-slate-900 py-2 text-xs font-bold text-white hover:bg-slate-800 transition">
                            Filter
                        </button>
                        @if(request()->anyFilled(['search', 'status_mou', 'bidang_usaha', 'bentuk_kerjasama']))
                            <a href="{{ route('hubin.industri.index') }}" title="Reset Filter"
                                class="inline-flex items-center justify-center rounded-xl border border-slate-200 bg-slate-100 p-2 text-xs font-bold text-slate-600 hover:bg-slate-200 transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 3. Tabel Data Industri Kerjasama -->
            <div class="rounded-2xl border border-slate-200/80 bg-white shadow-sm overflow-hidden">
                <div class="flex flex-col gap-3 border-b border-slate-100 px-6 py-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Daftar Mitra Industri Kerjasama</h3>
                        <p class="text-xs text-slate-500">Menampilkan {{ $industriList->firstItem() ?? 0 }} - {{ $industriList->lastItem() ?? 0 }} dari total {{ $industriList->total() }} industri mitra</p>
                    </div>
                    <button type="button" @click="createModalOpen = true"
                        class="inline-flex items-center gap-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-1.5 text-xs font-bold text-red-700 hover:bg-red-100 transition shadow-sm">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        + Tambah Mitra
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left">
                        <thead class="bg-slate-50/75">
                            <tr>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600 w-12 text-center">No</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600">Perusahaan & Bidang</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600">Kota / Alamat</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600">Kontak & PIC</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600">Status MoU</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600">Lingkup Kerjasama</th>
                                <th class="px-5 py-3.5 text-xs font-bold uppercase tracking-wider text-slate-600 text-center w-36">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($industriList as $index => $item)
                                @php
                                    $statusMou = $item->status_mou;
                                    $sisaHari = $item->sisa_hari_mou;
                                @endphp
                                <tr class="transition hover:bg-slate-50/70">
                                    <td class="px-5 py-4 text-xs font-semibold text-slate-400 text-center">
                                        {{ $industriList->firstItem() + $index }}
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-start gap-2.5">
                                            <div class="mt-0.5 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-red-50 text-red-600 font-bold text-xs uppercase border border-red-100">
                                                {{ substr($item->nama_industri, 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-sm text-slate-900">{{ $item->nama_industri }}</div>
                                                <div class="text-xs text-slate-500 font-medium">{{ $item->bidang_usaha ?: 'Bidang Umum' }}</div>
                                                @if($item->website)
                                                    <a href="{{ str_starts_with($item->website, 'http') ? $item->website : 'https://' . $item->website }}" target="_blank"
                                                        class="inline-flex items-center gap-1 text-[11px] text-blue-600 hover:underline mt-0.5">
                                                        <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                                        {{ parse_url($item->website, PHP_URL_HOST) ?: $item->website }}
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                        <div class="font-bold text-slate-800">{{ $item->kabupaten_name ?: $item->kota }}</div>
                                        <div class="text-slate-500 line-clamp-1 text-[11px]" title="{{ $item->alamat }}">{{ $item->alamat }}</div>
                                        @if($item->telepon)
                                            <div class="text-[11px] text-slate-400 mt-0.5">Telp: {{ $item->telepon }}</div>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                        @if($item->nama_pic)
                                            <div class="font-bold text-slate-800">{{ $item->nama_pic }}</div>
                                            @if($item->jabatan_pic)
                                                <div class="text-slate-500 text-[11px]">{{ $item->jabatan_pic }}</div>
                                            @endif
                                            @if($item->no_hp_pic)
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', str_starts_with($item->no_hp_pic, '0') ? '62' . substr($item->no_hp_pic, 1) : $item->no_hp_pic) }}" target="_blank"
                                                    class="inline-flex items-center gap-1 text-[11px] text-emerald-600 font-semibold hover:underline mt-0.5">
                                                    <span>WA: {{ $item->no_hp_pic }}</span>
                                                </a>
                                            @endif
                                        @else
                                            <span class="text-slate-400 italic text-[11px]">- Belum ada PIC -</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4 text-xs">
                                        @if($statusMou === 'aktif')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-0.5 text-[10px] font-black text-emerald-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                AKTIF
                                            </span>
                                        @elseif($statusMou === 'segera_berakhir')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-amber-100 px-2 py-0.5 text-[10px] font-black text-amber-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                                Sisa {{ $sisaHari }} Hari
                                            </span>
                                        @elseif($statusMou === 'berakhir')
                                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-100 px-2 py-0.5 text-[10px] font-black text-rose-800">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                BERAKHIR
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-bold text-slate-600">
                                                TANPA MOU
                                            </span>
                                        @endif

                                        @if($item->nomor_mou)
                                            <div class="mt-1 font-mono text-[10px] font-semibold text-slate-700">{{ $item->nomor_mou }}</div>
                                        @endif

                                        @if($item->tanggal_mou || $item->tanggal_akhir_mou)
                                            <div class="text-[10px] text-slate-400">
                                                {{ $item->tanggal_mou ? $item->tanggal_mou->format('d/m/Y') : '?' }} s/d {{ $item->tanggal_akhir_mou ? $item->tanggal_akhir_mou->format('d/m/Y') : 'Tanpa Batas' }}
                                            </div>
                                        @endif

                                        @if($item->file_mou)
                                            <a href="{{ route('hubin.industri.download-mou', $item->id) }}"
                                                class="mt-1 inline-flex items-center gap-1 text-[11px] font-bold text-red-600 hover:text-red-700 hover:underline">
                                                <svg class="h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                Berkas MoU
                                            </a>
                                        @endif
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-wrap gap-1 max-w-xs">
                                            @if(!empty($item->bentuk_kerjasama))
                                                @foreach($item->bentuk_kerjasama as $b)
                                                    <span class="inline-flex items-center rounded-md bg-slate-100 px-1.5 py-0.5 text-[10px] font-semibold text-slate-700">
                                                        {{ $b }}
                                                    </span>
                                                @endforeach
                                            @else
                                                <span class="text-slate-400 text-xs italic">Prakerin</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <div class="inline-flex items-center gap-1">
                                            <!-- Detail Button -->
                                            <button type="button" @click="openDetail({{ json_encode($item) }})"
                                                title="Lihat Detail Profil Industri"
                                                class="rounded-lg p-1.5 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            <!-- Edit Button -->
                                            <button type="button" @click="openEdit({{ json_encode($item) }})"
                                                title="Edit Data Industri"
                                                class="rounded-lg p-1.5 text-blue-600 hover:bg-blue-50 transition">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            <!-- Delete Button -->
                                            <form method="POST" action="{{ route('hubin.industri.destroy', $item->id) }}"
                                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus data industri {{ addslashes($item->nama_industri) }}? Data penempatan yang terkait mungkin ikut terdampak.');"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus Industri"
                                                    class="rounded-lg p-1.5 text-rose-500 hover:bg-rose-50 hover:text-rose-700 transition">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <h4 class="mt-3 text-sm font-bold text-slate-800">Tidak ada data industri mitra ditemukan</h4>
                                            <p class="mt-1 text-xs text-slate-500 max-w-sm">
                                                @if(request()->anyFilled(['search', 'status_mou', 'bidang_usaha', 'bentuk_kerjasama']))
                                                    Tidak ada hasil yang sesuai dengan kriteria filter pencarian Anda. Silakan reset filter.
                                                @else
                                                    Belum ada industri mitra yang ditambahkan. Klik tombol di bawah untuk menambah data baru.
                                                @endif
                                            </p>
                                            <button type="button" @click="createModalOpen = true"
                                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700 transition">
                                                + Tambah Industri Pertama
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($industriList->hasPages())
                    <div class="border-t border-slate-100 px-6 py-4">
                        {{ $industriList->links() }}
                    </div>
                @endif
            </div>

        </div>

        <!-- ======================================================== -->
        <!-- MODAL 1: TAMBAH INDUSTRI KERJASAMA -->
        <!-- ======================================================== -->
        <div x-show="createModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="createModalOpen = false"
                class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden my-8 max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-gradient-to-r from-red-600 to-rose-700 text-white">
                    <div>
                        <h3 class="text-lg font-black leading-tight">Tambah Mitra Industri Kerjasama</h3>
                        <p class="text-xs text-red-100 mt-0.5">Isi data profil perusahaan, kontak PIC, dan rincian perjanjian MoU/PKS.</p>
                    </div>
                    <button type="button" @click="createModalOpen = false" class="rounded-lg p-1.5 text-white/80 hover:bg-white/10 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form method="POST" action="{{ route('hubin.industri.store') }}" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-6 flex-1">
                    @csrf

                    <!-- Seksi 1: Profil Industri -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-700 text-xs font-black">1</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Profil Perusahaan / Industri</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan / Industri <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_industri" required placeholder="Contoh: PT Telkom Indonesia (Persero) Tbk"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bidang Usaha / Sektor</label>
                                <select name="bidang_usaha"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                                    <option value="">Pilih Bidang Usaha...</option>
                                    @foreach($bidangUsahaOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                                <input type="text" name="kota" required placeholder="Contoh: Bandar Lampung"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Website Perusahaan</label>
                                <input type="text" name="website" placeholder="https://www.telkom.co.id"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Telepon Kantor</label>
                                <input type="text" name="telepon" placeholder="0721-123456"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="alamat" rows="2" required placeholder="Jalan, nomor gedung, kelurahan/desa, kecamatan..."
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 2: PIC & Kontak -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-700 text-xs font-black">2</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Person in Charge (PIC) & Narahubung</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Lengkap PIC</label>
                                <input type="text" name="nama_pic" placeholder="Contoh: Budi Santoso, S.Kom"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan PIC</label>
                                <input type="text" name="jabatan_pic" placeholder="Contoh: HRD Manager / Lead Engineer"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="no_hp_pic" placeholder="081234567890"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email PIC / Resmi</label>
                                <input type="email" name="email_pic" placeholder="pic@perusahaan.com"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 3: Dokumen MoU & Kerjasama -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-red-100 text-red-700 text-xs font-black">3</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Perjanjian Kerjasama (MoU / PKS)</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Dokumen MoU</label>
                                <input type="text" name="nomor_mou" placeholder="001/MOU/SMK-TELKOM/2026"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div class="flex items-center pt-5">
                                <label class="relative flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="is_mou_active" value="1" checked
                                        class="h-4 w-4 rounded border-slate-300 text-red-600 focus:ring-red-500">
                                    <span class="text-xs font-bold text-slate-800">Status Kerjasama Aktif</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai MoU</label>
                                <input type="date" name="tanggal_mou"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berakhir MoU</label>
                                <input type="date" name="tanggal_akhir_mou"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition">
                                <span class="text-[10px] text-slate-400">Kosongkan jika tidak ada batas waktu.</span>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Bentuk / Ruang Lingkup Kerjasama</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                                    @foreach($bentukKerjasamaOptions as $bOption)
                                        <label class="flex items-center gap-2 text-xs text-slate-700 font-medium cursor-pointer hover:text-slate-900">
                                            <input type="checkbox" name="bentuk_kerjasama[]" value="{{ $bOption }}"
                                                class="rounded border-slate-300 text-red-600 focus:ring-red-500 h-3.5 w-3.5">
                                            <span>{{ $bOption }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Upload Berkas Dokumen MoU (PDF / Dokumen)</label>
                                <input type="file" name="file_mou" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 transition">
                                <p class="text-[10px] text-slate-400 mt-1">Maksimal 10 MB (Format disarankan: PDF).</p>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan Kerjasama</label>
                                <textarea name="catatan_mou" rows="2" placeholder="Catatan khusus, ruang lingkup per jurusan, dll..."
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-red-500 focus:ring-2 focus:ring-red-100 transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2">
                        <button type="button" @click="createModalOpen = false"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm shadow-red-200 hover:bg-red-700 transition">
                            Simpan Data Industri
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 2: EDIT INDUSTRI KERJASAMA -->
        <!-- ======================================================== -->
        <div x-show="editModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="editModalOpen = false"
                class="w-full max-w-3xl rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden my-8 max-h-[90vh] flex flex-col">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-gradient-to-r from-blue-600 to-indigo-700 text-white">
                    <div>
                        <h3 class="text-lg font-black leading-tight">Edit Data Mitra Industri</h3>
                        <p class="text-xs text-blue-100 mt-0.5" x-text="activeIndustri ? activeIndustri.nama_industri : ''"></p>
                    </div>
                    <button type="button" @click="editModalOpen = false" class="rounded-lg p-1.5 text-white/80 hover:bg-white/10 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Form Edit -->
                <form :action="'{{ url('hubin/industri') }}/' + (activeIndustri ? activeIndustri.id : '')"
                    method="POST" enctype="multipart/form-data" class="overflow-y-auto p-6 space-y-6 flex-1">
                    @csrf
                    @method('PUT')

                    <!-- Seksi 1: Profil -->
                    <div class="space-y-4">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-black">1</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Profil Perusahaan</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama Perusahaan <span class="text-red-500">*</span></label>
                                <input type="text" name="nama_industri" required :value="activeIndustri?.nama_industri"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Bidang Usaha</label>
                                <select name="bidang_usaha" :value="activeIndustri?.bidang_usaha"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                                    <option value="">Pilih Bidang Usaha...</option>
                                    @foreach($bidangUsahaOptions as $opt)
                                        <option value="{{ $opt }}">{{ $opt }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kabupaten <span class="text-red-500">*</span></label>
                                <input type="text" name="kota" required :value="activeIndustri?.kota"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Website Perusahaan</label>
                                <input type="text" name="website" :value="activeIndustri?.website"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Telepon Kantor</label>
                                <input type="text" name="telepon" :value="activeIndustri?.telepon"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                                <textarea name="alamat" rows="2" required x-text="activeIndustri?.alamat"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 2: PIC -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-black">2</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Kontak Person (PIC)</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nama PIC</label>
                                <input type="text" name="nama_pic" :value="activeIndustri?.nama_pic"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jabatan PIC</label>
                                <input type="text" name="jabatan_pic" :value="activeIndustri?.jabatan_pic"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                                <input type="text" name="no_hp_pic" :value="activeIndustri?.no_hp_pic"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Email PIC</label>
                                <input type="email" name="email_pic" :value="activeIndustri?.email_pic"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>
                        </div>
                    </div>

                    <!-- Seksi 3: MoU -->
                    <div class="space-y-4 pt-2">
                        <div class="flex items-center gap-2 border-b border-slate-100 pb-2">
                            <span class="flex h-6 w-6 items-center justify-center rounded-full bg-blue-100 text-blue-700 text-xs font-black">3</span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">MoU & Lingkup Kerjasama</h4>
                        </div>

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Nomor Dokumen MoU</label>
                                <input type="text" name="nomor_mou" :value="activeIndustri?.nomor_mou"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div class="flex items-center pt-5">
                                <label class="relative flex items-center gap-2.5 cursor-pointer">
                                    <input type="checkbox" name="is_mou_active" value="1" :checked="activeIndustri?.is_mou_active"
                                        class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-xs font-bold text-slate-800">Status Kerjasama Aktif</span>
                                </label>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Mulai MoU</label>
                                <input type="date" name="tanggal_mou" :value="activeIndustri?.tanggal_mou ? activeIndustri.tanggal_mou.substring(0, 10) : ''"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Berakhir MoU</label>
                                <input type="date" name="tanggal_akhir_mou" :value="activeIndustri?.tanggal_akhir_mou ? activeIndustri.tanggal_akhir_mou.substring(0, 10) : ''"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition">
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1.5">Bentuk / Ruang Lingkup Kerjasama</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 p-3 rounded-2xl border border-slate-200/70">
                                    @foreach($bentukKerjasamaOptions as $bOption)
                                        <label class="flex items-center gap-2 text-xs text-slate-700 font-medium cursor-pointer hover:text-slate-900">
                                            <input type="checkbox" name="bentuk_kerjasama[]" value="{{ $bOption }}"
                                                :checked="activeIndustri?.bentuk_kerjasama && activeIndustri.bentuk_kerjasama.includes('{{ $bOption }}')"
                                                class="rounded border-slate-300 text-blue-600 focus:ring-blue-500 h-3.5 w-3.5">
                                            <span>{{ $bOption }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Ganti Berkas MoU (Opsional)</label>
                                <input type="file" name="file_mou" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                    class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition">
                                <template x-if="activeIndustri?.file_mou">
                                    <p class="text-[10px] text-emerald-600 mt-1 font-semibold">
                                        ✓ Dokumen MoU sudah diunggah sebelumnya. Biarkan kosong jika tidak ingin mengganti file.
                                    </p>
                                </template>
                            </div>

                            <div class="sm:col-span-2">
                                <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                                <textarea name="catatan_mou" rows="2" x-text="activeIndustri?.catatan_mou"
                                    class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 transition"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Footer Buttons -->
                    <div class="border-t border-slate-100 pt-4 flex items-center justify-end gap-2">
                        <button type="button" @click="editModalOpen = false"
                            class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="rounded-xl bg-blue-600 px-5 py-2 text-xs font-bold text-white shadow-sm shadow-blue-200 hover:bg-blue-700 transition">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- MODAL 3: DETAIL PREVIEW INDUSTRI -->
        <!-- ======================================================== -->
        <div x-show="detailModalOpen" x-cloak
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            
            <div @click.away="detailModalOpen = false"
                class="w-full max-w-2xl rounded-3xl bg-white shadow-2xl border border-slate-100 overflow-hidden my-8 max-h-[90vh] flex flex-col">
                
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-5 bg-slate-900 text-white">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-600 text-white font-bold text-sm">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-base font-black leading-tight" x-text="activeIndustri?.nama_industri"></h3>
                            <p class="text-xs text-slate-400 mt-0.5" x-text="activeIndustri?.bidang_usaha || 'Mitra Industri'"></p>
                        </div>
                    </div>
                    <button type="button" @click="detailModalOpen = false" class="rounded-lg p-1.5 text-slate-400 hover:bg-slate-800 hover:text-white transition">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <div class="overflow-y-auto p-6 space-y-6 flex-1 text-xs">
                    <!-- Ringkasan Status MoU -->
                    <div class="rounded-2xl bg-slate-50 p-4 border border-slate-100 flex items-center justify-between">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Status Kerjasama</div>
                            <div class="text-sm font-bold text-slate-900 mt-0.5" x-text="activeIndustri?.is_mou_active ? 'Kerjasama Aktif' : 'Tidak Aktif / Nonaktif'"></div>
                            <div class="text-[11px] text-slate-500 font-mono mt-0.5" x-text="'No: ' + (activeIndustri?.nomor_mou || '-')"></div>
                        </div>
                        <template x-if="activeIndustri?.file_mou">
                            <a :href="'{{ url('hubin/industri') }}/' + activeIndustri?.id + '/mou-download'"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2 text-xs font-bold text-white hover:bg-red-700 shadow-sm transition">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                Unduh Berkas MoU
                            </a>
                        </template>
                    </div>

                    <!-- Informasi Detail Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Lokasi / Kota</span>
                            <p class="font-bold text-slate-800" x-text="activeIndustri?.kota"></p>
                            <p class="text-slate-600" x-text="activeIndustri?.alamat"></p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Kontak Kantor</span>
                            <p class="font-semibold text-slate-800" x-text="'Telp: ' + (activeIndustri?.telepon || '-')"></p>
                            <p class="text-slate-600" x-text="'Web: ' + (activeIndustri?.website || '-')"></p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Kontak PIC</span>
                            <p class="font-bold text-slate-800" x-text="activeIndustri?.nama_pic || '-'"></p>
                            <p class="text-slate-600" x-text="(activeIndustri?.jabatan_pic ? activeIndustri.jabatan_pic + ' | ' : '') + (activeIndustri?.no_hp_pic || '')"></p>
                            <p class="text-slate-500" x-text="activeIndustri?.email_pic || ''"></p>
                        </div>

                        <div class="space-y-1">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider">Periode Masa Berlaku</span>
                            <p class="font-bold text-slate-800"
                                x-text="(activeIndustri?.tanggal_mou ? activeIndustri.tanggal_mou.substring(0, 10) : '?') + ' s/d ' + (activeIndustri?.tanggal_akhir_mou ? activeIndustri.tanggal_akhir_mou.substring(0, 10) : 'Tanpa Batas')"></p>
                        </div>
                    </div>

                    <!-- Bentuk Kerjasama -->
                    <div>
                        <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block mb-2">Bentuk / Ruang Lingkup Kerjasama</span>
                        <div class="flex flex-wrap gap-1.5">
                            <template x-if="activeIndustri?.bentuk_kerjasama && activeIndustri.bentuk_kerjasama.length > 0">
                                <template x-for="b in activeIndustri.bentuk_kerjasama" :key="b">
                                    <span class="inline-flex items-center rounded-lg bg-red-50 border border-red-100 px-2.5 py-1 text-xs font-bold text-red-700" x-text="b"></span>
                                </template>
                            </template>
                            <template x-if="!activeIndustri?.bentuk_kerjasama || activeIndustri.bentuk_kerjasama.length === 0">
                                <span class="text-slate-400 italic">Prakerin / PKL Siswa</span>
                            </template>
                        </div>
                    </div>

                    <!-- Catatan MoU -->
                    <template x-if="activeIndustri?.catatan_mou">
                        <div class="rounded-xl bg-slate-50 p-3.5 border border-slate-100">
                            <span class="text-slate-400 font-bold uppercase text-[10px] tracking-wider block mb-1">Catatan Tambahan</span>
                            <p class="text-slate-700 leading-relaxed" x-text="activeIndustri?.catatan_mou"></p>
                        </div>
                    </template>
                </div>

                <div class="border-t border-slate-100 px-6 py-4 bg-slate-50 flex items-center justify-between">
                    <button type="button" @click="detailModalOpen = false"
                        class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-100 transition">
                        Tutup
                    </button>
                    <button type="button" @click="detailModalOpen = false; openEdit(activeIndustri)"
                        class="rounded-xl bg-blue-600 px-4 py-2 text-xs font-bold text-white hover:bg-blue-700 transition">
                        Edit Data Industri
                    </button>
                </div>
            </div>
        </div>

    </div>
</x-app-layout>
