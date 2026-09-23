<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <div class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Rombel PKL & Penempatan Siswa</h2>
                <p class="text-xs text-gray-500">Kelola rombongan belajar PKL, pembimbing industri & guru, serta mapping siswa kelas XII</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="hubinRombelManager()">
        {{-- Flash / Error Messages --}}
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Terjadi kesalahan validasi:
                </div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Page Title Card with Action Button --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-6 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">
                        Hubin & Alumni
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        Tingkat Kelas XII
                    </span>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Kelola Rombel PKL (Praktik Kerja Lapangan)</h1>
                <p class="text-sm text-gray-500 max-w-2xl">
                    Buat rombongan belajar PKL dengan industri mitra yang telah terdaftar, tentukan guru pembimbing sekolah & pembimbing industri, serta petakan siswa kelas XII.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    type="button"
                    @click="openCreateModal()"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold text-sm shadow-md shadow-red-600/20 transition-all hover:shadow-lg hover:shadow-red-600/30">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>+ Buat Rombel PKL</span>
                </button>
            </div>
        </div>

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Total Rombel --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Rombel PKL</p>
                        <h3 class="text-2xl font-extrabold text-gray-900 mt-1">{{ number_format($totalRombel) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Kelompok belajar industri</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-rose-50 text-rose-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Siswa Sudah Ditempatkan --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Siswa Ditempatkan</p>
                        <h3 class="text-2xl font-extrabold text-emerald-600 mt-1">{{ number_format($totalSiswaDitempatkan) }}</h3>
                        <p class="text-xs text-emerald-600 font-medium mt-1">Sudah masuk rombel PKL</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Siswa Kelas XII Belum Ditempatkan --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Kelas XII Belum Dimapping</p>
                        <h3 class="text-2xl font-extrabold text-amber-600 mt-1">{{ number_format($totalSiswaXiiBelumDitempatkan) }}</h3>
                        <p class="text-xs text-amber-600 font-medium mt-1">Siap untuk dimapping</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- Industri Mitra Digunakan --}}
            <div class="relative overflow-hidden rounded-2xl bg-white border border-gray-100 p-5 shadow-sm transition hover:shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Industri Aktif PKL</p>
                        <h3 class="text-2xl font-extrabold text-blue-600 mt-1">{{ number_format($totalIndustriDigunakan) }}</h3>
                        <p class="text-xs text-gray-400 mt-1">Dari {{ $industriList->count() }} mitra kerjasama</p>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Toolbar --}}
        <div class="rounded-2xl bg-white border border-gray-100 p-5 shadow-sm">
            <form method="GET" action="{{ route('hubin.rombel-pkl.index') }}" class="grid grid-cols-1 gap-4 md:grid-cols-12 items-end">
                {{-- Search --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Pencarian</label>
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Cari rombel, nama industri, atau guru..."
                            class="w-full rounded-xl border-gray-200 bg-gray-50/50 pl-10 pr-4 text-sm focus:border-red-500 focus:bg-white focus:ring-red-500"
                        >
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                {{-- Filter Industri Mitra --}}
                <div class="md:col-span-4">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Industri Mitra</label>
                    <select name="prakerin_industri_id" class="js-tomselect w-full rounded-xl border-gray-200 text-sm" data-placeholder="Semua Industri Mitra...">
                        <option value="">Semua Industri Mitra</option>
                        @foreach($industriList as $ind)
                            <option value="{{ $ind->id }}" @selected(request('prakerin_industri_id') == $ind->id)>
                                {{ $ind->nama_industri }} ({{ $ind->kota ?? 'Mitra' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Filter Status --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Status Rombel</label>
                    <select name="status" class="w-full rounded-xl border-gray-200 bg-gray-50/50 text-sm focus:border-red-500 focus:bg-white focus:ring-red-500">
                        <option value="all" @selected(request('status') === 'all' || !request()->has('status'))>Semua Status</option>
                        <option value="aktif" @selected(request('status') === 'aktif')>Aktif</option>
                        <option value="draft" @selected(request('status') === 'draft')>Draft</option>
                        <option value="selesai" @selected(request('status') === 'selesai')>Selesai</option>
                    </select>
                </div>

                {{-- Actions --}}
                <div class="md:col-span-2 flex items-center gap-2">
                    <button
                        type="submit"
                        class="w-full inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-xl bg-gray-900 hover:bg-gray-800 text-white font-semibold text-xs shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                        </svg>
                        <span>Filter</span>
                    </button>
                    @if(request()->anyFilled(['search', 'prakerin_industri_id', 'status']))
                        <a href="{{ route('hubin.rombel-pkl.index') }}" title="Reset Filter"
                            class="inline-flex items-center justify-center p-2.5 rounded-xl border border-gray-200 bg-white hover:bg-gray-100 text-gray-500 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Card --}}
        <div class="rounded-2xl bg-white border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="font-bold text-gray-900 text-base">Daftar Rombongan Belajar PKL</h3>
                    <p class="text-xs text-gray-500">Menampilkan {{ $rombels->total() }} rombel PKL yang terdaftar</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="openCreateModal()"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-700 font-semibold text-xs transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>+ Tambah Rombel</span>
                    </button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-gray-600">
                    <thead class="bg-gray-50/80 text-xs uppercase font-semibold text-gray-500 border-b border-gray-100">
                        <tr>
                            <th class="px-5 py-4">Rombel PKL & Status</th>
                            <th class="px-5 py-4">Industri Mitra</th>
                            <th class="px-5 py-4">Pembimbing Internal (Guru)</th>
                            <th class="px-5 py-4">Pembimbing Industri</th>
                            <th class="px-5 py-4 text-center">Anggota Siswa</th>
                            <th class="px-5 py-4 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 font-normal">
                        @forelse($rombels as $item)
                            <tr class="hover:bg-gray-50/60 transition">
                                {{-- Rombel & Status --}}
                                <td class="px-5 py-4">
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="font-bold text-gray-900 text-base">{{ $item->nama_rombel }}</span>
                                            @if($item->status === 'aktif')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                                    AKTIF
                                                </span>
                                            @elseif($item->status === 'draft')
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 text-amber-800">
                                                    DRAFT
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-700">
                                                    SELESAI
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-400">
                                            @if($item->gunakan_periode_kustom && $item->tanggal_mulai)
                                                Periode: {{ $item->tanggal_mulai->format('d M Y') }} s/d {{ $item->tanggal_selesai?->format('d M Y') ?? '-' }}
                                            @else
                                                <span class="text-gray-400 italic">Mengikuti periode global sekolah</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                {{-- Industri Mitra --}}
                                <td class="px-5 py-4">
                                    <div class="space-y-0.5">
                                        <p class="font-semibold text-gray-900">{{ $item->industri?->nama_industri ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $item->industri?->kota ?? '-' }}
                                            @if($item->industri?->bidang_usaha)
                                                &bull; <span class="text-blue-600">{{ $item->industri->bidang_usaha }}</span>
                                            @endif
                                        </p>
                                    </div>
                                </td>

                                {{-- Pembimbing Internal (Guru) --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-rose-100 text-rose-700 font-bold text-xs flex-shrink-0">
                                            {{ substr($item->pembimbingInternal?->nama ?? 'G', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-xs">
                                                {{ $item->pembimbingInternal?->nama ?? '-' }}
                                            </p>
                                            <p class="text-[11px] text-gray-400">
                                                {{ $item->pembimbingInternal?->guru?->kode_guru ? 'Kode: ' . $item->pembimbingInternal->guru->kode_guru : ($item->pembimbingInternal?->telepon ?? 'Internal Guru') }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Pembimbing Eksternal (Industri) --}}
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2.5">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-blue-700 font-bold text-xs flex-shrink-0">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-medium text-gray-900 text-xs">
                                                {{ $item->pembimbingExternal?->nama ?? ($item->industri?->nama_pic ?? 'Belum ditentukan') }}
                                            </p>
                                            <p class="text-[11px] text-gray-400">
                                                {{ $item->pembimbingExternal?->jabatan ?? ($item->industri?->jabatan_pic ?? 'Pembimbing Lapangan') }}
                                                @if($item->pembimbingExternal?->telepon)
                                                    &bull; {{ $item->pembimbingExternal->telepon }}
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Anggota Siswa --}}
                                <td class="px-5 py-4 text-center">
                                    <div class="inline-flex flex-col items-center">
                                        <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold {{ $item->penempatans_count > 0 ? 'bg-indigo-50 text-indigo-700' : 'bg-gray-100 text-gray-500' }}">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            {{ $item->penempatans_count }} Siswa
                                        </span>
                                    </div>
                                </td>

                                {{-- Aksi --}}
                                <td class="px-5 py-4 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center gap-2">
                                        {{-- Mapping Siswa --}}
                                        <a
                                            href="{{ route('hubin.rombel-pkl.mapping', $item->id) }}"
                                            title="Kelola Mapping Siswa Kelas XII"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs shadow-sm transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                            </svg>
                                            <span>Mapping Siswa</span>
                                        </a>

                                        {{-- Edit --}}
                                        <button
                                            type="button"
                                            @click="openEditModal(@js($item))"
                                            title="Edit Rombel PKL"
                                            class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </button>

                                        {{-- Hapus --}}
                                        <form method="POST" action="{{ route('hubin.rombel-pkl.destroy', $item->id) }}" class="inline delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="button"
                                                @click="confirmDelete($event, '{{ $item->nama_rombel }}')"
                                                title="Hapus Rombel PKL"
                                                class="p-1.5 rounded-lg bg-red-50 hover:bg-red-100 text-red-600 transition">
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
                                <td colspan="6" class="p-8 text-center text-gray-400">
                                    <div class="flex flex-col items-center justify-center space-y-2">
                                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                                        </svg>
                                        <p class="text-base font-semibold text-gray-600">Belum ada rombel PKL</p>
                                        <p class="text-xs text-gray-400">Klik tombol "+ Buat Rombel PKL" di atas untuk menambahkan data baru.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rombels->hasPages())
                <div class="p-5 border-t border-gray-100">
                    {{ $rombels->links() }}
                </div>
            @endif
        </div>

        {{-- Modal Create / Edit Rombel PKL --}}
        <div
            x-cloak
            x-show="showModal"
            class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title"
            role="dialog"
            aria-modal="true">
            <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
                {{-- Backdrop --}}
                <div
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0"
                    @click="closeModal()"
                    class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity"></div>

                {{-- Modal Panel --}}
                <div
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">

                    {{-- Modal Header --}}
                    <div class="border-b border-gray-100 bg-gray-50/50 px-6 py-4 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 bg-rose-50 text-rose-600 rounded-xl">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-gray-900" x-text="isEdit ? 'Edit Data Rombel PKL' : 'Buat Rombel PKL Baru'"></h3>
                                <p class="text-xs text-gray-500">Tentukan nama rombel, industri mitra, dan pembimbing</p>
                            </div>
                        </div>
                        <button type="button" @click="closeModal()" class="text-gray-400 hover:text-gray-500 p-1 rounded-lg hover:bg-gray-100 transition">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form :action="formAction" method="POST" class="p-6 space-y-4">
                        @csrf
                        <template x-if="isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        {{-- Nama Rombel PKL --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Nama Rombel PKL <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                name="nama_rombel"
                                x-model="formData.nama_rombel"
                                placeholder="Contoh: PKL PT Telkom - XII RPL 1"
                                required
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                            >
                            <p class="text-[11px] text-gray-400 mt-1">Dapat dinamai sesuai industri dan kelompok/kelas peminatan siswa.</p>
                        </div>

                        {{-- Industri Mitra (Searchable Select) --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Industri Mitra PKL <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="modal-industry-select"
                                name="prakerin_industri_id"
                                required
                                class="w-full rounded-xl border-gray-200 text-sm"
                                data-placeholder="Pilih atau cari industri mitra...">
                                <option value="">Pilih industri mitra...</option>
                                @foreach($industriList as $ind)
                                    <option value="{{ $ind->id }}" data-pic="{{ $ind->nama_pic }}" data-jabatan="{{ $ind->jabatan_pic }}" data-phone="{{ $ind->no_hp_pic }}">
                                        {{ $ind->nama_industri }} ({{ $ind->kota ?? 'Mitra' }}) - PIC: {{ $ind->nama_pic ?? '-' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pembimbing Internal (Guru) (Searchable Select) --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">
                                Pembimbing Internal (Guru Sekolah) <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="modal-guru-select"
                                name="master_guru_id"
                                required
                                class="w-full rounded-xl border-gray-200 text-sm"
                                data-placeholder="Pilih atau cari nama guru pembimbing...">
                                <option value="">Pilih guru pembimbing...</option>
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->id }}">
                                        {{ $guru->nama_lengkap }} {{ $guru->kode_guru ? "({$guru->kode_guru})" : '' }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="text-[11px] text-gray-400 mt-1">Pilih guru internal yang bertugas memonitoring kegiatan PKL siswa.</p>
                        </div>

                        {{-- Pembimbing Eksternal (Industri) --}}
                        <div class="rounded-xl border border-gray-100 bg-gray-50/50 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <label class="block text-xs font-bold text-gray-800">
                                    Pembimbing Eksternal (Dari Industri)
                                </label>
                                <span class="text-[11px] text-gray-500">Narahubung / Instruktur Lapangan</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nama Pembimbing Industri</label>
                                    <input
                                        type="text"
                                        name="pembimbing_external_nama"
                                        x-model="formData.pembimbing_external_nama"
                                        placeholder="Contoh: Bpk. Hendra Gunawan"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Jabatan / Posisi</label>
                                    <input
                                        type="text"
                                        name="pembimbing_external_jabatan"
                                        x-model="formData.pembimbing_external_jabatan"
                                        placeholder="Contoh: Senior Tech Lead / HR"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-gray-600 mb-1">Nomor WhatsApp / Kontak</label>
                                <input
                                    type="text"
                                    name="pembimbing_external_telepon"
                                    x-model="formData.pembimbing_external_telepon"
                                    placeholder="Contoh: 081234567890"
                                    class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                >
                            </div>
                        </div>

                        {{-- Periode Pelaksanaan Kustom --}}
                        <div class="rounded-xl border border-gray-100 p-4 space-y-3">
                            <label class="flex items-start gap-2.5 cursor-pointer">
                                <input
                                    type="checkbox"
                                    name="gunakan_periode_kustom"
                                    value="1"
                                    x-model="formData.gunakan_periode_kustom"
                                    class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <div>
                                    <span class="text-xs font-bold text-gray-800 block">Atur Tanggal Periode PKL Khusus</span>
                                    <span class="text-[11px] text-gray-500 block">Jika tidak dicentang, rombel akan mengikuti jadwal PKL global sekolah.</span>
                                </div>
                            </label>

                            <div x-show="formData.gunakan_periode_kustom" x-collapse class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-2">
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tanggal Mulai PKL</label>
                                    <input
                                        type="date"
                                        name="tanggal_mulai"
                                        x-model="formData.tanggal_mulai"
                                        :required="formData.gunakan_periode_kustom"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-gray-600 mb-1">Tanggal Selesai PKL</label>
                                    <input
                                        type="date"
                                        name="tanggal_selesai"
                                        x-model="formData.tanggal_selesai"
                                        :required="formData.gunakan_periode_kustom"
                                        class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500"
                                    >
                                </div>
                            </div>
                        </div>

                        {{-- Status Rombel --}}
                        <div>
                            <label class="block text-xs font-semibold text-gray-700 mb-1">Status Rombel PKL</label>
                            <select
                                name="status"
                                x-model="formData.status"
                                class="w-full rounded-xl border-gray-200 text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="aktif">Aktif (Sedang Berjalan / Siap Mapping)</option>
                                <option value="draft">Draft (Perencanaan)</option>
                                <option value="selesai">Selesai (Pelaksanaan PKL Telah Berakhir)</option>
                            </select>
                        </div>

                        {{-- Modal Footer --}}
                        <div class="border-t border-gray-100 pt-4 flex items-center justify-end gap-3">
                            <button
                                type="button"
                                @click="closeModal()"
                                class="px-4 py-2.5 rounded-xl border border-gray-200 bg-white text-gray-700 font-semibold text-xs hover:bg-gray-50 transition">
                                Batal
                            </button>
                            <button
                                type="submit"
                                class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 hover:from-red-700 hover:to-rose-700 text-white font-semibold text-xs shadow-md transition">
                                <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Rombel PKL'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control {
                border-radius: 0.75rem !important;
                border-color: #e5e7eb !important;
                min-height: 42px;
                padding: 0.5rem 0.75rem !important;
                font-size: 0.875rem !important;
            }
            .ts-control:focus {
                border-color: #ef4444 !important;
                box-shadow: 0 0 0 1px #ef4444 !important;
            }
            .ts-dropdown {
                border-radius: 0.75rem !important;
                box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1) !important;
                border-color: #f3f4f6 !important;
                z-index: 60 !important;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            let tomSelectToolbar = null;
            let tomSelectModalIndustry = null;
            let tomSelectModalGuru = null;

            document.addEventListener('DOMContentLoaded', function() {
                // Inisialisasi TomSelect pada filter toolbar
                document.querySelectorAll('.js-tomselect').forEach(function(el) {
                    if (!el.tomselect) {
                        tomSelectToolbar = new TomSelect(el, {
                            create: false,
                            allowEmptyOption: true,
                            maxOptions: 50,
                            placeholder: el.getAttribute('data-placeholder') || 'Pilih...',
                        });
                    }
                });

                // Inisialisasi TomSelect pada modal
                const modalIndEl = document.getElementById('modal-industry-select');
                if (modalIndEl && !modalIndEl.tomselect) {
                    tomSelectModalIndustry = new TomSelect(modalIndEl, {
                        create: false,
                        maxOptions: 100,
                        placeholder: 'Pilih atau cari industri mitra...',
                    });
                }

                const modalGuruEl = document.getElementById('modal-guru-select');
                if (modalGuruEl && !modalGuruEl.tomselect) {
                    tomSelectModalGuru = new TomSelect(modalGuruEl, {
                        create: false,
                        maxOptions: 150,
                        placeholder: 'Pilih atau cari guru pembimbing...',
                    });
                }
            });

            function hubinRombelManager() {
                return {
                    showModal: false,
                    isEdit: false,
                    formAction: '{{ route('hubin.rombel-pkl.store') }}',
                    formData: {
                        id: null,
                        nama_rombel: '',
                        prakerin_industri_id: '',
                        master_guru_id: '',
                        pembimbing_external_nama: '',
                        pembimbing_external_jabatan: '',
                        pembimbing_external_telepon: '',
                        gunakan_periode_kustom: false,
                        tanggal_mulai: '',
                        tanggal_selesai: '',
                        status: 'aktif',
                    },

                    openCreateModal() {
                        this.isEdit = false;
                        this.formAction = '{{ route('hubin.rombel-pkl.store') }}';
                        this.formData = {
                            id: null,
                            nama_rombel: '',
                            prakerin_industri_id: '',
                            master_guru_id: '',
                            pembimbing_external_nama: '',
                            pembimbing_external_jabatan: '',
                            pembimbing_external_telepon: '',
                            gunakan_periode_kustom: false,
                            tanggal_mulai: '',
                            tanggal_selesai: '',
                            status: 'aktif',
                        };

                        if (tomSelectModalIndustry) tomSelectModalIndustry.clear();
                        if (tomSelectModalGuru) tomSelectModalGuru.clear();

                        this.showModal = true;
                    },

                    openEditModal(item) {
                        this.isEdit = true;
                        this.formAction = '{{ url('hubin/rombel-pkl') }}/' + item.id;
                        this.formData = {
                            id: item.id,
                            nama_rombel: item.nama_rombel || '',
                            prakerin_industri_id: item.prakerin_industri_id ? String(item.prakerin_industri_id) : '',
                            master_guru_id: item.pembimbing_internal?.master_guru_id ? String(item.pembimbing_internal.master_guru_id) : '',
                            pembimbing_external_nama: item.pembimbing_external?.nama || item.industri?.nama_pic || '',
                            pembimbing_external_jabatan: item.pembimbing_external?.jabatan || item.industri?.jabatan_pic || '',
                            pembimbing_external_telepon: item.pembimbing_external?.telepon || item.industri?.no_hp_pic || '',
                            gunakan_periode_kustom: !!item.gunakan_periode_kustom,
                            tanggal_mulai: item.tanggal_mulai ? item.tanggal_mulai.substring(0, 10) : '',
                            tanggal_selesai: item.tanggal_selesai ? item.tanggal_selesai.substring(0, 10) : '',
                            status: item.status || 'aktif',
                        };

                        if (tomSelectModalIndustry) {
                            tomSelectModalIndustry.setValue(this.formData.prakerin_industri_id);
                        }
                        if (tomSelectModalGuru) {
                            tomSelectModalGuru.setValue(this.formData.master_guru_id);
                        }

                        this.showModal = true;
                    },

                    closeModal() {
                        this.showModal = false;
                    },

                    confirmDelete(event, name) {
                        const form = event.target.closest('form');
                        Swal.fire({
                            title: 'Hapus Rombel PKL?',
                            text: `Rombel "${name}" dan data penempatan siswanya akan dihapus.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc2626',
                            cancelButtonColor: '#6b7280',
                            confirmButtonText: 'Ya, Hapus Rombel',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }
                };
            }
        </script>
    @endpush
</x-app-layout>
