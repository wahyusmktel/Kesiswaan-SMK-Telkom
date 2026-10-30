<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Administrasi SPMB</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="administrasiSpmbPage()" x-init="init()">
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
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Berita Acara</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $totalBeritaAcara }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Dokumen laporan piket dibuat</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Pekan Piket Terlaksana</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ $pekanTerlaksana }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Hari Sabtu kegiatan selesai</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Calon Siswa</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalCasis) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Casis hadir selama piket</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center border border-indigo-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>

                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Total Tamu / Wali</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalTamu) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Orang tua & wali terlayani</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- FILTER & ACTION TOOLBAR --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    
                    {{-- Left Filters --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Academic Year Selector --}}
                        <form method="GET" action="{{ route('kaur-spmb.administrasi.index') }}" class="flex items-center gap-2">
                            <input type="hidden" name="search" value="{{ $search }}">
                            <label class="text-xs font-semibold text-slate-600">T.P:</label>
                            <select name="tahun_ajaran" onchange="this.form.submit()"
                                class="text-xs font-semibold rounded-md border-slate-300 focus:border-red-500 focus:ring-red-500 py-1.5 pl-3 pr-8 bg-slate-50">
                                @foreach($availableYears as $year)
                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                        {{ $year }}
                                    </option>
                                @endforeach
                            </select>
                        </form>

                        {{-- Search Input --}}
                        <form method="GET" action="{{ route('kaur-spmb.administrasi.index') }}" class="relative">
                            <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nomor / ringkasan..."
                                class="text-xs pl-8 pr-3 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 w-48 sm:w-64 bg-slate-50">
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </form>
                    </div>

                    {{-- Right Action Buttons --}}
                    <div class="flex items-center gap-2">
                        <button type="button" @click="openAddBeritaAcaraModal()"
                            class="inline-flex items-center gap-1.5 px-3.5 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Buat Berita Acara Baru
                        </button>
                    </div>

                </div>
            </div>

            {{-- MAIN TABLE: DAFTAR BERITA ACARA --}}
            <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left divide-y divide-slate-200">
                        <thead class="bg-slate-50 text-slate-700 font-semibold uppercase tracking-wider text-[11px]">
                            <tr>
                                <th class="px-4 py-3 text-center w-12">NO</th>
                                <th class="px-4 py-3">NOMOR & JUDUL BERITA ACARA</th>
                                <th class="px-4 py-3">HARI & TANGGAL PIKET</th>
                                <th class="px-4 py-3 text-center">CASIS / TAMU</th>
                                <th class="px-4 py-3">PENANDATANGAN (KAUR)</th>
                                <th class="px-4 py-3 text-center">STATUS</th>
                                <th class="px-4 py-3 text-center w-32">AKSI</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            @forelse($beritaAcaras as $idx => $ba)
                                <tr class="hover:bg-slate-50/70 transition-colors">
                                    <td class="px-4 py-3 text-center font-medium text-slate-500">
                                        {{ $beritaAcaras->firstItem() + $idx }}
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-bold text-slate-900 font-mono text-[11.5px]">
                                            {{ $ba->nomor_surat }}
                                        </div>
                                        <div class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">
                                            {{ $ba->judul }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-800">
                                            {{ $ba->formatted_tanggal_kegiatan }}
                                        </div>
                                        <div class="text-[10.5px] text-slate-400 mt-0.5 flex items-center gap-1.5">
                                            <span>{{ $ba->waktu_mulai }} - {{ $ba->waktu_selesai }} WIB</span>
                                            <span>•</span>
                                            <span class="text-slate-600 font-medium">{{ $ba->tanggal?->kelompok?->nama_kelompok ?? 'Kelompok Piket' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 font-semibold text-[10.5px] border border-blue-200" title="Calon Siswa Hadir">
                                                {{ $ba->jumlah_casis_hadir }} Casis
                                            </span>
                                            <span class="px-2 py-0.5 rounded bg-purple-50 text-purple-700 font-semibold text-[10.5px] border border-purple-200" title="Tamu / Orang Tua Hadir">
                                                {{ $ba->jumlah_tamu }} Tamu
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-800">
                                            {{ $ba->nama_penandatangan }}
                                        </div>
                                        <div class="text-[10px] text-slate-400">
                                            {{ $ba->nip_penandatangan ? 'NIP. ' . $ba->nip_penandatangan : $ba->jabatan_penandatangan }}
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded text-[10.5px] font-semibold {{ $ba->status === 'Final' ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-amber-50 text-amber-700 border border-amber-200' }}">
                                            {{ $ba->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex items-center justify-center gap-1">
                                            {{-- Cetak Berita Acara & Lampiran --}}
                                            <a href="{{ route('kaur-spmb.administrasi.print', $ba) }}" target="_blank"
                                                class="p-1.5 text-slate-600 hover:text-red-600 hover:bg-slate-100 rounded transition-colors"
                                                title="Cetak Berita Acara & Lampiran Absensi">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                </svg>
                                            </a>

                                            {{-- Edit --}}
                                            <button type="button" @click="openEditBeritaAcaraModal({{ json_encode($ba) }})"
                                                class="p-1.5 text-slate-600 hover:text-blue-600 hover:bg-slate-100 rounded transition-colors"
                                                title="Edit Berita Acara">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>

                                            {{-- Hapus --}}
                                            <form method="POST" action="{{ route('kaur-spmb.administrasi.destroy', $ba) }}"
                                                onsubmit="return confirm('Hapus Berita Acara {{ $ba->nomor_surat }}?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors" title="Hapus Berita Acara">
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
                                    <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                                        <div class="max-w-sm mx-auto space-y-2">
                                            <svg class="w-8 h-8 mx-auto text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="font-semibold text-slate-600">Belum ada Berita Acara SPMB</p>
                                            <p class="text-[11px] text-slate-400">Klik tombol "Buat Berita Acara Baru" untuk membuat laporan kegiatan piket pekanan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- PAGINATION FOOTER --}}
                @if($beritaAcaras->hasPages() || $beritaAcaras->total() > 0)
                    <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-500">
                        <div>
                            Menampilkan 
                            <strong class="text-slate-800">{{ $beritaAcaras->firstItem() ?? 0 }}</strong> 
                            sampai 
                            <strong class="text-slate-800">{{ $beritaAcaras->lastItem() ?? 0 }}</strong> 
                            dari 
                            <strong class="text-slate-800">{{ $beritaAcaras->total() }}</strong> 
                            data Berita Acara
                        </div>
                        <div>
                            {{ $beritaAcaras->links() }}
                        </div>
                    </div>
                @endif
            </div>

        </div>

        {{-- ======================================================== --}}
        {{-- INTERACTIVE MODAL: KELOLA BERITA ACARA (TAMBAH / EDIT)   --}}
        {{-- ======================================================== --}}
        <template x-teleport="body">
            <div x-show="modalBeritaAcara.open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-[99999]"
                style="display: none;">

                <div x-ref="modalBeritaAcaraBox"
                    class="bg-white rounded-lg shadow-2xl border border-slate-300 overflow-hidden flex flex-col transition-all duration-150 max-h-[92vh]"
                    :class="{
                        'fixed inset-4 w-auto h-auto': modalBeritaAcara.maximized,
                        'w-full max-w-3xl': !modalBeritaAcara.maximized && modalBeritaAcara.pos.x === null
                    }"
                    :style="!modalBeritaAcara.maximized && modalBeritaAcara.pos.x !== null ? 'position: fixed; left:' + modalBeritaAcara.pos.x + 'px; top:' + modalBeritaAcara.pos.y + 'px; width: 740px;' : ''">

                    {{-- Draggable Header --}}
                    <div class="px-4 py-3 bg-slate-100 border-b border-slate-200 flex items-center justify-between select-none flex-shrink-0"
                        :class="!modalBeritaAcara.maximized ? 'cursor-grab active:cursor-grabbing' : ''"
                        @mousedown="startDragModal($event, 'modalBeritaAcara')">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-red-600"></span>
                            <h3 class="text-xs font-bold uppercase tracking-wider text-slate-800"
                                x-text="modalBeritaAcara.isEdit ? 'Edit Berita Acara Piket SPMB' : 'Buat Berita Acara Piket SPMB Baru'">
                            </h3>
                        </div>
                        <div class="flex items-center gap-1">
                            <button type="button" @click="toggleMaximize('modalBeritaAcara')"
                                class="p-1 text-slate-400 hover:text-slate-700 rounded transition-colors" title="Maximize/Restore">
                                <svg x-show="!modalBeritaAcara.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                                <svg x-show="modalBeritaAcara.maximized" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="display:none;">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9L4 4m0 0v4m0-4h4m6 6l5-5m-5 5v-4m0 4h4M9 15l-5 5m0 0v-4m0 4h4m11-5l-5 5m5-5v4m0-4h-4" />
                                </svg>
                            </button>
                            <button type="button" @click="closeModal('modalBeritaAcara')"
                                class="p-1 text-slate-400 hover:text-red-600 rounded transition-colors" title="Tutup">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Form Body --}}
                    <form :action="modalBeritaAcara.actionUrl" method="POST" class="flex-1 overflow-y-auto p-5 space-y-4">
                        @csrf
                        <template x-if="modalBeritaAcara.isEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <input type="hidden" name="tahun_ajaran" value="{{ $selectedYear }}">

                        {{-- 1. Pilih Pekan Piket (hanya saat create) --}}
                        <div x-show="!modalBeritaAcara.isEdit">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Pilih Hari / Pekan Piket SPMB <span class="text-red-500">*</span>
                            </label>
                            <select name="spmb_piket_tanggal_id" x-model="modalBeritaAcara.form.spmb_piket_tanggal_id" @change="onTanggalPiketChanged()"
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-medium bg-white">
                                <option value="">-- Pilih Pekan Piket Selesai --</option>
                                <template x-for="opt in tanggalOptions" :key="opt.id">
                                    <option :value="opt.id" x-text="opt.label"></option>
                                </template>
                            </select>
                            <p class="text-[11px] text-slate-400 mt-1">
                                Pilih hari Sabtu tugas yang telah selesai untuk membuat berita acara dan merekap kehadirannya.
                            </p>
                        </div>

                        {{-- 2. Metadata Surat --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 p-3.5 bg-slate-50 rounded-md border border-slate-200">
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nomor Berita Acara <span class="text-red-500">*</span></label>
                                <input type="text" name="nomor_surat" x-model="modalBeritaAcara.form.nomor_surat" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 font-mono">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Judul Dokumen <span class="text-red-500">*</span></label>
                                <input type="text" name="judul" x-model="modalBeritaAcara.form.judul" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tanggal Pelaksanaan Piket <span class="text-red-500">*</span></label>
                                <input type="date" name="tanggal_kegiatan" x-model="modalBeritaAcara.form.tanggal_kegiatan" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tempat Pelaksanaan <span class="text-red-500">*</span></label>
                                <input type="text" name="tempat" x-model="modalBeritaAcara.form.tempat" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Waktu Mulai</label>
                                <input type="text" name="waktu_mulai" x-model="modalBeritaAcara.form.waktu_mulai" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-[11px] font-semibold text-slate-700 mb-1">Waktu Selesai</label>
                                <input type="text" name="waktu_selesai" x-model="modalBeritaAcara.form.waktu_selesai" required
                                    class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        {{-- 3. Uraian & Ringkasan Kegiatan --}}
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Uraian & Ringkasan Kegiatan Piket SPMB <span class="text-red-500">*</span>
                            </label>
                            <textarea name="ringkasan_kegiatan" x-model="modalBeritaAcara.form.ringkasan_kegiatan" rows="4" required
                                class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500 leading-relaxed"></textarea>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Jelaskan proses pelayanan piket SPMB (penyambutan, tes buta warna, wawancara, dsb).
                            </p>
                        </div>

                        {{-- 4. Statistik Casis & Tamu --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Calon Siswa Hadir</label>
                                <input type="number" min="0" name="jumlah_casis_hadir" x-model="modalBeritaAcara.form.jumlah_casis_hadir"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Jumlah Tamu / Orang Tua Hadir</label>
                                <input type="number" min="0" name="jumlah_tamu" x-model="modalBeritaAcara.form.jumlah_tamu"
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                            </div>
                        </div>

                        {{-- 5. Catatan Kendala & Kesimpulan --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Catatan / Kendala Selama Piket (Opsional)</label>
                                <textarea name="catatan_kendala" x-model="modalBeritaAcara.form.catatan_kendala" rows="2"
                                    placeholder="Tuliskan jika ada kendala sistem, sarana, atau teknis..."
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Kesimpulan / Tindak Lanjut (Opsional)</label>
                                <textarea name="kesimpulan" x-model="modalBeritaAcara.form.kesimpulan" rows="2"
                                    placeholder="Rencana evaluasi atau tindak lanjut pekan berikutnya..."
                                    class="w-full text-xs px-3 py-2 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>
                        </div>

                        {{-- 6. Pengaturan Penandatangan (Kaur SPMB) --}}
                        <div class="p-3.5 bg-slate-50 rounded-md border border-slate-200 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    Pengaturan Penandatangan (Kaur SPMB)
                                </h4>
                                <span class="text-[10.5px] text-slate-400">Dapat disetting manual sebelum pencetakan</span>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Nama Penandatangan <span class="text-red-500">*</span></label>
                                    <input type="text" name="nama_penandatangan" x-model="modalBeritaAcara.form.nama_penandatangan" required
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">NIP Penandatangan (Opsional)</label>
                                    <input type="text" name="nip_penandatangan" x-model="modalBeritaAcara.form.nip_penandatangan"
                                        placeholder="Kosongkan jika tidak ada"
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Jabatan Penandatangan <span class="text-red-500">*</span></label>
                                    <input type="text" name="jabatan_penandatangan" x-model="modalBeritaAcara.form.jabatan_penandatangan" required
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-semibold text-slate-700 mb-1">Tempat TTD <span class="text-red-500">*</span></label>
                                    <input type="text" name="tempat_ttd" x-model="modalBeritaAcara.form.tempat_ttd" required
                                        class="w-full text-xs px-2.5 py-1.5 border border-slate-300 rounded-md focus:border-red-500 focus:ring-red-500">
                                </div>
                            </div>
                        </div>

                        {{-- 7. Status Dokumen --}}
                        <div class="flex items-center gap-4">
                            <label class="text-xs font-semibold text-slate-700">Status Dokumen:</label>
                            <label class="inline-flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                                <input type="radio" name="status" value="Final" x-model="modalBeritaAcara.form.status" class="text-red-600 focus:ring-red-500">
                                <span class="font-bold text-emerald-700">Final (Sah & Siap Cetak)</span>
                            </label>
                            <label class="inline-flex items-center gap-1.5 text-xs text-slate-700 cursor-pointer">
                                <input type="radio" name="status" value="Draft" x-model="modalBeritaAcara.form.status" class="text-red-600 focus:ring-red-500">
                                <span class="text-slate-500">Draft (Konsep Sementara)</span>
                            </label>
                        </div>

                        {{-- Footer Action --}}
                        <div class="pt-3 flex justify-end gap-2 border-t border-slate-200">
                            <button type="button" @click="closeModal('modalBeritaAcara')"
                                class="px-3.5 py-2 text-xs font-semibold text-slate-600 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-md transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-4 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-md shadow-sm transition-colors">
                                Simpan Berita Acara
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </template>

    </div>

    {{-- JAVASCRIPT / ALPINE LOGIC --}}
    <script>
        function administrasiSpmbPage() {
            return {
                tanggalOptions: @json($tanggalOptions),
                defaultNamaSigner: @json($defaultNamaSigner),
                defaultNipSigner: @json($defaultNipSigner),
                defaultJabatanSigner: @json($defaultJabatanSigner),

                modalBeritaAcara: {
                    open: false,
                    isEdit: false,
                    maximized: false,
                    actionUrl: '{{ route("kaur-spmb.administrasi.store") }}',
                    pos: { x: null, y: null },
                    drag: { active: false, startX: 0, startY: 0, initialX: 0, initialY: 0 },
                    form: {
                        spmb_piket_tanggal_id: '',
                        nomor_surat: '',
                        judul: 'Berita Acara Pelaksanaan Piket SPMB',
                        tanggal_kegiatan: '',
                        waktu_mulai: '07:30',
                        waktu_selesai: '12:00',
                        tempat: 'SMK Telkom Lampung',
                        ringkasan_kegiatan: '',
                        jumlah_casis_hadir: 0,
                        jumlah_tamu: 0,
                        catatan_kendala: '',
                        kesimpulan: '',
                        nama_penandatangan: '',
                        nip_penandatangan: '',
                        jabatan_penandatangan: '',
                        tempat_ttd: 'Pringsewu',
                        status: 'Final'
                    }
                },

                init() {
                    window.addEventListener('mousemove', (e) => this.onMouseMove(e));
                    window.addEventListener('mouseup', () => this.onMouseUp());
                },

                openAddBeritaAcaraModal() {
                    this.modalBeritaAcara.isEdit = false;
                    this.modalBeritaAcara.actionUrl = '{{ route("kaur-spmb.administrasi.store") }}';
                    
                    let firstTgl = this.tanggalOptions.length > 0 ? this.tanggalOptions[0] : null;

                    this.modalBeritaAcara.form = {
                        spmb_piket_tanggal_id: firstTgl ? firstTgl.id : '',
                        nomor_surat: firstTgl ? firstTgl.nomor_surat : '01/BA-SPMB/SMKTEL-LPG/X/2026',
                        judul: 'Berita Acara Pelaksanaan Piket SPMB',
                        tanggal_kegiatan: firstTgl ? firstTgl.tanggal : '',
                        waktu_mulai: '07:30',
                        waktu_selesai: '12:00',
                        tempat: 'SMK Telkom Lampung',
                        ringkasan_kegiatan: firstTgl ? firstTgl.ringkasan_default : '',
                        jumlah_casis_hadir: 0,
                        jumlah_tamu: 0,
                        catatan_kendala: '',
                        kesimpulan: '',
                        nama_penandatangan: this.defaultNamaSigner,
                        nip_penandatangan: this.defaultNipSigner,
                        jabatan_penandatangan: this.defaultJabatanSigner,
                        tempat_ttd: 'Pringsewu',
                        status: 'Final'
                    };

                    this.modalBeritaAcara.pos = { x: null, y: null };
                    this.modalBeritaAcara.maximized = false;
                    this.modalBeritaAcara.open = true;
                },

                openEditBeritaAcaraModal(ba) {
                    this.modalBeritaAcara.isEdit = true;
                    this.modalBeritaAcara.actionUrl = '{{ url("kaur-spmb/administrasi/berita-acara") }}/' + ba.id;
                    
                    this.modalBeritaAcara.form = {
                        spmb_piket_tanggal_id: ba.spmb_piket_tanggal_id,
                        nomor_surat: ba.nomor_surat,
                        judul: ba.judul || 'Berita Acara Pelaksanaan Piket SPMB',
                        tanggal_kegiatan: ba.tanggal_kegiatan ? ba.tanggal_kegiatan.substring(0, 10) : '',
                        waktu_mulai: ba.waktu_mulai || '07:30',
                        waktu_selesai: ba.waktu_selesai || '12:00',
                        tempat: ba.tempat || 'SMK Telkom Lampung',
                        ringkasan_kegiatan: ba.ringkasan_kegiatan || '',
                        jumlah_casis_hadir: ba.jumlah_casis_hadir || 0,
                        jumlah_tamu: ba.jumlah_tamu || 0,
                        catatan_kendala: ba.catatan_kendala || '',
                        kesimpulan: ba.kesimpulan || '',
                        nama_penandatangan: ba.nama_penandatangan || this.defaultNamaSigner,
                        nip_penandatangan: ba.nip_penandatangan || '',
                        jabatan_penandatangan: ba.jabatan_penandatangan || this.defaultJabatanSigner,
                        tempat_ttd: ba.tempat_ttd || 'Pringsewu',
                        status: ba.status || 'Final'
                    };

                    this.modalBeritaAcara.pos = { x: null, y: null };
                    this.modalBeritaAcara.maximized = false;
                    this.modalBeritaAcara.open = true;
                },

                onTanggalPiketChanged() {
                    const selId = this.modalBeritaAcara.form.spmb_piket_tanggal_id;
                    const found = this.tanggalOptions.find(t => t.id == selId);
                    if (found) {
                        this.modalBeritaAcara.form.nomor_surat = found.nomor_surat;
                        this.modalBeritaAcara.form.tanggal_kegiatan = found.tanggal;
                        this.modalBeritaAcara.form.ringkasan_kegiatan = found.ringkasan_default;
                    }
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
                    if (this.modalBeritaAcara.drag && this.modalBeritaAcara.drag.active) {
                        const dx = e.clientX - this.modalBeritaAcara.drag.startX;
                        const dy = e.clientY - this.modalBeritaAcara.drag.startY;
                        this.modalBeritaAcara.pos.x = Math.max(10, Math.min(window.innerWidth - 100, this.modalBeritaAcara.drag.initialX + dx));
                        this.modalBeritaAcara.pos.y = Math.max(10, Math.min(window.innerHeight - 100, this.modalBeritaAcara.drag.initialY + dy));
                    }
                },

                onMouseUp() {
                    if (this.modalBeritaAcara.drag) {
                        this.modalBeritaAcara.drag.active = false;
                    }
                }
            };
        }
    </script>
</x-app-layout>
