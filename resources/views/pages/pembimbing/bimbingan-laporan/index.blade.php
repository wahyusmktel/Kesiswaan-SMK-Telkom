<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-rose-50 text-rose-600 rounded-2xl shadow-sm border border-rose-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Bimbingan Laporan Prakerin Siswa</h2>
                    <p class="text-xs text-gray-500">Pemeriksaan judul, koreksi coretan dokumen interaktif, dan penerbitan Berita Acara Digital</p>
                </div>
            </div>

            @if (!($hasTtdDigital ?? false))
                <a href="{{ route('tanda-tangan.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2 text-xs font-bold text-white bg-amber-600 hover:bg-amber-700 rounded-xl shadow-md shadow-amber-200 transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    Aktivasi TTD Digital Sekarang
                </a>
            @else
                <div class="inline-flex items-center gap-2 px-3 py-1.5 text-xs font-bold text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-xl">
                    <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    TTD Digital Siap & Aktif
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Mandatory Warning Banner: TTD Digital Required --}}
        @if (!($hasTtdDigital ?? false))
            <div class="rounded-2xl border-2 border-amber-300 bg-gradient-to-r from-amber-50 to-orange-50 p-6 shadow-md shadow-amber-100/50">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <div class="p-3 bg-amber-500 text-white rounded-2xl shadow-sm flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black bg-amber-200 text-amber-900 uppercase tracking-wider mb-1">
                                Persyaratan Wajib Pembimbing
                            </span>
                            <h3 class="font-extrabold text-gray-900 text-base">Setup Tanda Tangan Digital Diperlukan</h3>
                            <p class="text-xs text-gray-600 mt-1 max-w-2xl leading-relaxed">
                                Sesuai prosedur operasional standar bimbingan prakerin SMK Telkom Lampung, guru pembimbing internal
                                <strong>wajib melakukan aktivasi Tanda Tangan Digital</strong> terlebih dahulu sebelum dapat menyetujui judul, memberikan koreksi bimbingan bab, atau menerbitkan Berita Acara resmi.
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('tanda-tangan.index') }}"
                        class="px-5 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs shadow-md shadow-amber-200 transition flex items-center justify-center gap-2 whitespace-nowrap">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Buka Menu Tanda Tangan Digital
                    </a>
                </div>
            </div>
        @endif

        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Total Siswa Bimbingan</div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ $penempatans->total() }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Menunggu Review Judul</div>
                    <div class="text-2xl font-extrabold text-amber-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->judul_status === 'diajukan')->count() }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Sedang Proses Bimbingan</div>
                    <div class="text-2xl font-extrabold text-rose-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->status === 'proses')->count() }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Laporan Selesai (ACC)</div>
                    <div class="text-2xl font-extrabold text-emerald-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->status === 'selesai')->count() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <form method="GET" action="{{ route('pembimbing-prakerin.bimbingan-laporan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cari Siswa / Judul</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ketik nama siswa, NIS, atau kata kunci judul..."
                            class="w-full text-xs rounded-xl border-gray-300 pl-10 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Rombel PKL</label>
                    <select name="rombel_id" class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                        <option value="">Semua Rombel PKL Bimbingan</option>
                        @foreach (($rombels ?? []) as $r)
                            <option value="{{ $r->id }}" {{ request('rombel_id') == $r->id ? 'selected' : '' }}>
                                {{ $r->nama_rombel }} ({{ $r->industri?->nama_industri }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-3 flex items-center gap-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter Data
                    </button>
                    @if (request()->hasAny(['search', 'rombel_id']))
                        <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.index') }}"
                            class="p-2.5 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Siswa Bimbingan Grid / Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            @forelse ($penempatans as $penempatan)
                @php
                    $lap = $penempatan->bimbinganLaporan;
                    $persen = $lap?->persentase_selesai ?? 0;
                    $tahapDisetujui = $lap ? $lap->tahaps->where('status', 'disetujui')->count() : 0;
                    $totalTahap = $lap ? $lap->tahaps->count() : 0;
                @endphp
                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-md transition flex flex-col justify-between overflow-hidden">
                    <div class="p-6 space-y-4">
                        {{-- Siswa Header --}}
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center font-bold text-sm shadow-sm">
                                    {{ strtoupper(substr($penempatan->siswa?->nama_lengkap ?? 'S', 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-900 text-sm leading-tight">{{ $penempatan->siswa?->nama_lengkap }}</h4>
                                    <span class="text-[11px] text-gray-400">NIS: {{ $penempatan->siswa?->nis ?? '-' }} • {{ $penempatan->siswa?->kelas_saat_ini ?? '-' }}</span>
                                </div>
                            </div>

                            @if (!$lap || $lap->judul_status === 'draft')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">Belum Ada Judul</span>
                            @elseif ($lap->judul_status === 'diajukan')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 animate-pulse">Review Judul</span>
                            @elseif ($lap->status === 'selesai')
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">ACC Selesai</span>
                            @else
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">Proses Bab</span>
                            @endif
                        </div>

                        {{-- Industri & Rombel --}}
                        <div class="text-xs text-gray-500 space-y-1 bg-gray-50/70 p-3 rounded-xl border border-gray-100">
                            <div class="flex items-center gap-1 font-semibold text-gray-700">
                                <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                {{ $penempatan->industri?->nama_industri ?? '-' }}
                            </div>
                            <div class="text-[11px] text-gray-400">Rombel: {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</div>
                        </div>

                        {{-- Judul Laporan --}}
                        <div>
                            <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Judul Laporan</div>
                            @if ($lap && $lap->judul)
                                <p class="text-xs font-bold text-gray-800 line-clamp-2" title="{{ $lap->judul }}">
                                    "{{ $lap->judul }}"
                                </p>
                            @else
                                <p class="text-xs text-gray-400 italic">Siswa belum mengajukan judul laporan</p>
                            @endif
                        </div>

                        {{-- Progress Bar Bab --}}
                        @if ($lap && $lap->judul_status === 'disetujui')
                            <div class="space-y-1.5 pt-2 border-t border-gray-100">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-gray-600">Progres Bab</span>
                                    <span class="font-extrabold text-rose-600">{{ $tahapDisetujui }}/{{ $totalTahap }} Tahap ({{ $persen }}%)</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-2 overflow-hidden">
                                    <div class="bg-gradient-to-r from-rose-500 to-emerald-500 h-2 rounded-full"
                                        style="width: {{ $persen }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Card Footer Action --}}
                    <div class="p-4 bg-gray-50 border-t border-gray-100 flex items-center justify-between gap-3">
                        <span class="text-[11px] text-gray-400">
                            @if ($lap && $lap->updated_at)
                                Diperbarui: {{ $lap->updated_at->diffForHumans() }}
                            @else
                                Belum ada aktivitas
                            @endif
                        </span>

                        @if ($lap)
                            <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.detail', $lap) }}"
                                class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-sm transition">
                                <span>Buka Bimbingan</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        @else
                            <button type="button" disabled
                                class="px-3.5 py-1.5 rounded-xl bg-gray-200 text-gray-400 font-semibold text-xs cursor-not-allowed">
                                Menunggu Siswa
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full bg-white rounded-2xl border border-gray-100 p-12 text-center text-gray-400 space-y-2">
                    <div class="w-12 h-12 rounded-2xl bg-gray-100 text-gray-400 flex items-center justify-center mx-auto mb-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    </div>
                    <p class="font-bold text-gray-700 text-base">Tidak ada siswa bimbingan ditemukan</p>
                    <p class="text-xs text-gray-500">Pastikan Anda telah ditugaskan sebagai pembimbing internal pada rombel PKL terkait.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="pt-4">
            {{ $penempatans->links() }}
        </div>
    </div>
</x-app-layout>
