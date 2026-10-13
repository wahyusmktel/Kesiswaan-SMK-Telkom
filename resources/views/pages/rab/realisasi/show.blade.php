<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-red-600">
                    <a href="{{ route('rab.index') }}" class="hover:underline">Anggaran & Pengadaan</a>
                    <span>/</span>
                    <a href="{{ route('rab.show', $rab) }}" class="hover:underline">Dokumen RAB</a>
                    <span>/</span>
                    <span>Laporan Realisasi</span>
                </div>
                <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
                    Laporan Realisasi Penggunaan Anggaran
                </h1>
                <p class="text-sm text-slate-500">
                    {{ $rab->judul_pengajuan }} &bull; TA {{ $rab->tahun_anggaran }} &bull; Nomor: {{ $realisasi->nomor_realisasi ?: $rab->nomor_pengajuan }}
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('rab.show', $rab) }}"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali ke RAB
                </a>

                <a href="{{ route('rab.realisasi.edit', $rab) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-amber-500 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-amber-600">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Kelola Transaksi & Eviden
                </a>

                <a href="{{ route('rab.realisasi.print', $rab) }}" target="_blank"
                   class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2.5 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak
                </a>

                <a href="{{ route('rab.realisasi.pdf', $rab) }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    Unduh PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ previewImage: null, previewPdf: null, showUploadModal: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">✓</span>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Penerimaan</p>
                    <p class="mt-2 text-2xl font-black text-emerald-600">
                        Rp {{ number_format($realisasi->total_penerimaan, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Alokasi dana yang diterima</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pengeluaran</p>
                    <p class="mt-2 text-2xl font-black text-rose-600">
                        Rp {{ number_format($realisasi->total_pengeluaran, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Total belanja terlaksana</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Saldo Akhir (Sisa)</p>
                    <p class="mt-2 text-2xl font-black {{ $realisasi->saldo_akhir >= 0 ? 'text-blue-600' : 'text-red-600' }}">
                        Rp {{ number_format($realisasi->saldo_akhir, 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Sisa dana kas kegiatan</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Status & Bukti Eviden</p>
                    <div class="mt-2 flex items-center justify-between">
                        @php
                            $statusBadge = match($realisasi->status) {
                                'disetujui' => 'bg-emerald-100 text-emerald-700 border-emerald-300',
                                'diverifikasi_keuangan' => 'bg-indigo-100 text-indigo-700 border-indigo-300',
                                'diajukan' => 'bg-amber-100 text-amber-700 border-amber-300',
                                'ditolak' => 'bg-rose-100 text-rose-700 border-rose-300',
                                default => 'bg-slate-100 text-slate-700 border-slate-300',
                            };
                            $statusLabel = match($realisasi->status) {
                                'disetujui' => 'Selesai / Disetujui',
                                'diverifikasi_keuangan' => 'Verifikasi Keuangan',
                                'diajukan' => 'Diajukan',
                                'ditolak' => 'Ditolak',
                                default => 'Draft',
                            };
                        @endphp
                        <span class="rounded-full border px-3 py-1 text-xs font-black uppercase tracking-wider {{ $statusBadge }}">
                            {{ $statusLabel }}
                        </span>
                        <span class="text-xs font-bold text-slate-600">
                            {{ $realisasi->evidences->count() }} Berkas
                        </span>
                    </div>
                    <p class="mt-2 text-xs text-slate-500">Lampiran nota, kuitansi, bukti fisik</p>
                </div>
            </div>

            {{-- Dokumen Laporan Realisasi (Mirip format print / PDF) --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                {{-- Kop Surat UKK --}}
                @if(!empty($kopBase64))
                    <div class="w-full border-b border-slate-100">
                        <img src="{{ $kopBase64 }}" alt="Kop Surat" class="w-full block">
                    </div>
                @endif

                <div class="p-6 sm:p-8 space-y-6">
                    {{-- Header Dokumen --}}
                    <div class="text-center space-y-1">
                        <h2 class="text-lg font-black uppercase tracking-wider text-slate-900">
                            LAPORAN REALISASI PENGGUNAAN ANGGARAN
                        </h2>
                        <h3 class="text-base font-bold uppercase text-slate-800">
                            {{ $rab->judul_pengajuan }}
                        </h3>
                        <p class="text-sm font-semibold text-slate-700">
                            TAHUN ANGGARAN {{ $rab->tahun_anggaran }}
                        </p>
                    </div>

                    {{-- Metadata Pengajuan --}}
                    <div class="max-w-2xl text-xs space-y-1.5 font-medium text-slate-800 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        <div class="grid grid-cols-12 gap-2">
                            <span class="col-span-4 font-bold text-slate-600">NOMOR AKUN</span>
                            <span class="col-span-1 text-center font-bold">:</span>
                            <span class="col-span-7 font-mono">{{ $rab->nomor_akun }}</span>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <span class="col-span-4 font-bold text-slate-600">NAMA AKUN</span>
                            <span class="col-span-1 text-center font-bold">:</span>
                            <span class="col-span-7">{{ $rab->nama_akun }}</span>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <span class="col-span-4 font-bold text-slate-600">DRK</span>
                            <span class="col-span-1 text-center font-bold">:</span>
                            <span class="col-span-7">{{ $rab->drk }}</span>
                        </div>
                        <div class="grid grid-cols-12 gap-2">
                            <span class="col-span-4 font-bold text-slate-600">KEBUTUHAN WAKTU</span>
                            <span class="col-span-1 text-center font-bold">:</span>
                            <span class="col-span-7">{{ $rab->kebutuhan_waktu }}</span>
                        </div>
                    </div>

                    {{-- Tabel Transaksi Realisasi --}}
                    <div class="overflow-x-auto rounded-2xl border border-slate-900/20">
                        <table class="w-full text-left text-xs text-slate-900 border-collapse">
                            <thead>
                                <tr class="border-b border-slate-900 bg-slate-100 text-center font-black uppercase text-[11px]">
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 w-12">NO</th>
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 w-28">TGL</th>
                                    <th class="border-r border-slate-900/20 px-4 py-2.5 text-left">URAIAN</th>
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 text-right w-36">PENERIMAAN</th>
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 text-right w-36">PENGELUARAN</th>
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 text-right w-36">SALDO</th>
                                    <th class="px-3 py-2.5 text-center w-36">KETERANGAN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900/20">
                                @forelse($realisasi->items as $index => $item)
                                    <tr class="hover:bg-slate-50 transition">
                                        <td class="border-r border-slate-900/20 px-3 py-2.5 text-center font-semibold text-slate-600">
                                            {{ $index + 1 }}
                                        </td>
                                        <td class="border-r border-slate-900/20 px-3 py-2.5 text-center font-medium whitespace-nowrap">
                                            {{ \Carbon\Carbon::parse($item->tanggal)->translatedFormat('d-M-y') }}
                                        </td>
                                        <td class="border-r border-slate-900/20 px-4 py-2.5 font-medium leading-relaxed">
                                            {!! nl2br(e($item->uraian)) !!}
                                        </td>
                                        <td class="border-r border-slate-900/20 px-3 py-2.5 text-right font-semibold text-emerald-700 whitespace-nowrap">
                                            {{ $item->penerimaan > 0 ? 'Rp ' . number_format($item->penerimaan, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="border-r border-slate-900/20 px-3 py-2.5 text-right font-semibold text-rose-700 whitespace-nowrap">
                                            {{ $item->pengeluaran > 0 ? 'Rp ' . number_format($item->pengeluaran, 0, ',', '.') : '-' }}
                                        </td>
                                        <td class="border-r border-slate-900/20 px-3 py-2.5 text-right font-bold whitespace-nowrap">
                                            Rp {{ number_format($item->saldo, 0, ',', '.') }}
                                        </td>
                                        <td class="px-3 py-2.5 text-center text-slate-600">
                                            {{ !empty(trim((string) $item->keterangan)) ? $item->keterangan : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-8 text-center text-slate-500">
                                            Belum ada rincian transaksi realisasi.
                                            <a href="{{ route('rab.realisasi.edit', $rab) }}" class="text-red-600 underline font-bold ml-1">Susun sekarang</a>
                                        </td>
                                    </tr>
                                @endforelse

                                {{-- Baris Total / Jumlah --}}
                                <tr class="bg-slate-100 font-black border-t-2 border-slate-900 text-xs">
                                    <td colspan="3" class="border-r border-slate-900/20 px-4 py-3 text-center uppercase tracking-wider">
                                        JUMLAH
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-right text-emerald-700 whitespace-nowrap">
                                        Rp {{ number_format($realisasi->total_penerimaan, 0, ',', '.') }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-right text-rose-700 whitespace-nowrap">
                                        Rp {{ number_format($realisasi->total_pengeluaran, 0, ',', '.') }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-right text-slate-900 whitespace-nowrap">
                                        Rp {{ number_format($realisasi->saldo_akhir, 0, ',', '.') }}
                                    </td>
                                    <td class="px-3 py-3"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- Matriks Pengesahan 3 Pihak --}}
                    <div class="overflow-x-auto rounded-2xl border border-slate-900/20">
                        <table class="w-full text-left text-xs text-slate-900 border-collapse">
                            <thead>
                                <tr class="border-b border-slate-900 bg-slate-100 text-center font-black uppercase text-[11px]">
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 w-44"></th>
                                    <th class="border-r border-slate-900/20 px-4 py-2.5">NAMA / NIK</th>
                                    <th class="border-r border-slate-900/20 px-4 py-2.5">JABATAN</th>
                                    <th class="border-r border-slate-900/20 px-3 py-2.5 w-32">TANGGAL</th>
                                    <th class="px-3 py-2.5 w-36">TANDA TANGAN</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-900/20">
                                <tr>
                                    <td class="border-r border-slate-900/20 px-3 py-3 font-semibold text-slate-700">
                                        Dibuat oleh
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 font-bold text-center">
                                        {{ $realisasi->pembuat_nama }} / {{ $realisasi->pembuat_nik }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 text-center font-medium">
                                        {{ $realisasi->pembuat_jabatan }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-center whitespace-nowrap">
                                        {{ $realisasi->tanggal_dibuat ? \Carbon\Carbon::parse($realisasi->tanggal_dibuat)->translatedFormat('d-M-y') : '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-center text-slate-400 italic">
                                        (Tersimpan)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-r border-slate-900/20 px-3 py-3 font-semibold text-slate-700">
                                        Diperiksa & Disetujui oleh
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 font-bold text-center">
                                        {{ $realisasi->pemeriksa_nama }} / {{ $realisasi->pemeriksa_nik }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 text-center font-medium">
                                        {{ $realisasi->pemeriksa_jabatan }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-center whitespace-nowrap">
                                        {{ $realisasi->tanggal_diperiksa ? \Carbon\Carbon::parse($realisasi->tanggal_diperiksa)->translatedFormat('d-M-y') : '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-center text-slate-400 italic">
                                        (Kaur Keuangan)
                                    </td>
                                </tr>
                                <tr>
                                    <td class="border-r border-slate-900/20 px-3 py-3 font-semibold text-slate-700">
                                        Diperiksa & Disetujui Realisasi
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 font-bold text-center">
                                        {{ $realisasi->penyetuju_nama }} / {{ $realisasi->penyetuju_nik }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-4 py-3 text-center font-medium">
                                        {{ $realisasi->penyetuju_jabatan }}
                                    </td>
                                    <td class="border-r border-slate-900/20 px-3 py-3 text-center whitespace-nowrap">
                                        {{ $realisasi->tanggal_disetujui ? \Carbon\Carbon::parse($realisasi->tanggal_disetujui)->translatedFormat('d-M-y') : '-' }}
                                    </td>
                                    <td class="px-3 py-3 text-center text-slate-400 italic">
                                        (Kepala Sekolah)
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>

            {{-- Galeri Bukti Eviden (Nota, Kuitansi, Dokumen Fisik, PDF) --}}
            <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 sm:p-8 shadow-sm space-y-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-black text-slate-900 flex items-center gap-2">
                            <span>Bukti Eviden / Dokumen Pendukung</span>
                            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-bold text-slate-600">
                                {{ $realisasi->evidences->count() }} Berkas
                            </span>
                        </h3>
                        <p class="text-xs text-slate-500">
                            Nota belanja, bukti transfer kuitansi, atau dokumen pertanggungjawaban fisik (Mendukung Gambar & PDF).
                        </p>
                    </div>

                    <button type="button" @click="showUploadModal = true"
                            class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-slate-800">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Unggah Bukti Baru
                    </button>
                </div>

                @if($realisasi->evidences->isEmpty())
                    <div class="rounded-2xl border-2 border-dashed border-slate-200 p-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="mt-3 text-sm font-bold text-slate-700">Belum ada berkas bukti eviden yang diunggah</p>
                        <p class="mt-1 text-xs text-slate-400">Unggah foto nota, kuitansi, struk belanja, atau dokumen scan PDF sekarang.</p>
                        <button type="button" @click="showUploadModal = true"
                                class="mt-4 inline-flex items-center gap-2 rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                            + Unggah Berkas Eviden
                        </button>
                    </div>
                @else
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                        @foreach($realisasi->evidences as $evidence)
                            <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 transition hover:border-slate-300 hover:shadow-md">
                                <div class="p-3">
                                    @if($evidence->isImage())
                                        <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-200 cursor-pointer"
                                             @click="previewImage = '{{ asset('storage/' . $evidence->file_path) }}'">
                                            <img src="{{ asset('storage/' . $evidence->file_path) }}" alt="{{ $evidence->judul }}"
                                                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                            <span class="absolute bottom-2 right-2 rounded-lg bg-black/60 px-2 py-1 text-[10px] font-bold text-white backdrop-blur-sm">
                                                Foto / Gambar
                                            </span>
                                        </div>
                                    @else
                                        <div class="relative flex aspect-video w-full flex-col items-center justify-center rounded-xl bg-red-50 text-red-600 cursor-pointer"
                                             @click="previewPdf = '{{ asset('storage/' . $evidence->file_path) }}'">
                                            <svg class="h-10 w-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                            </svg>
                                            <span class="mt-2 text-xs font-black uppercase tracking-wider text-red-700">Dokumen PDF</span>
                                        </div>
                                    @endif

                                    <div class="mt-3 space-y-1">
                                        <p class="text-xs font-bold text-slate-800 line-clamp-1" title="{{ $evidence->judul }}">
                                            {{ $evidence->judul }}
                                        </p>
                                        <p class="text-[11px] text-slate-500 line-clamp-1">
                                            {{ $evidence->file_name }} &bull; {{ number_format($evidence->file_size / 1024, 0) }} KB
                                        </p>
                                        @if($evidence->keterangan)
                                            <p class="text-[11px] text-slate-600 italic line-clamp-2">
                                                "{{ $evidence->keterangan }}"
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center justify-between border-t border-slate-200/80 bg-white px-3 py-2 text-[11px]">
                                    <a href="{{ asset('storage/' . $evidence->file_path) }}" target="_blank"
                                       class="font-bold text-slate-600 hover:text-slate-900 flex items-center gap-1">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                        Buka
                                    </a>

                                    <form method="POST" action="{{ route('rab.realisasi.evidence.destroy', [$rab, $evidence]) }}"
                                          onsubmit="return confirm('Hapus bukti dokumen eviden ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-bold text-red-600 hover:text-red-800">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Kontrol Status / Verifikasi Keuangan --}}
            @if($isKeuanganUser)
                <div class="overflow-hidden rounded-3xl border border-indigo-200 bg-indigo-50/50 p-6 sm:p-8 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">
                            💼
                        </span>
                        <div>
                            <h3 class="text-base font-black text-slate-900">Panel Verifikasi Keuangan</h3>
                            <p class="text-xs text-slate-600">Perbarui status laporan realisasi dan sertakan catatan audit anggaran.</p>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('rab.realisasi.status', $rab) }}" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700">Status Laporan Realisasi</label>
                                <select name="status" class="mt-1 block w-full rounded-xl border-slate-300 bg-white text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="draft" {{ $realisasi->status === 'draft' ? 'selected' : '' }}>Draft (Penyusunan)</option>
                                    <option value="diajukan" {{ $realisasi->status === 'diajukan' ? 'selected' : '' }}>Diajukan ke Keuangan</option>
                                    <option value="diverifikasi_keuangan" {{ $realisasi->status === 'diverifikasi_keuangan' ? 'selected' : '' }}>Diverifikasi Keuangan (Valid)</option>
                                    <option value="disetujui" {{ $realisasi->status === 'disetujui' ? 'selected' : '' }}>Disetujui Selesai (Kepala Sekolah)</option>
                                    <option value="ditolak" {{ $realisasi->status === 'ditolak' ? 'selected' : '' }}>Ditolak / Perlu Revisi</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700">Catatan Keuangan / Pemeriksaan</label>
                                <input type="text" name="catatan_keuangan" value="{{ old('catatan_keuangan', $realisasi->catatan_keuangan) }}"
                                       placeholder="Misal: Nota belanja lengkap, saldo sisa sudah disetor kembali ke kas."
                                       class="mt-1 block w-full rounded-xl border-slate-300 bg-white text-xs focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-xs font-black text-white shadow-sm transition hover:bg-indigo-700">
                                Simpan Verifikasi Keuangan
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            {{-- Modal Upload Eviden Cepat --}}
            <div x-show="showUploadModal" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-2xl space-y-5" @click.outside="showUploadModal = false">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                        <h4 class="text-base font-black text-slate-900">Unggah Berkas Bukti Eviden</h4>
                        <button type="button" @click="showUploadModal = false" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form method="POST" action="{{ route('rab.realisasi.evidence.store', $rab) }}" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-slate-700">Pilih Berkas (Bisa Lebih Dari Satu)</label>
                            <input type="file" name="files[]" multiple required accept=".jpg,.jpeg,.png,.webp,.pdf"
                                   class="mt-1 block w-full rounded-xl border border-slate-200 p-2 text-xs text-slate-700 file:mr-3 file:rounded-lg file:border-0 file:bg-red-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-red-700 hover:file:bg-red-100">
                            <p class="mt-1 text-[11px] text-slate-400">Mendukung format JPG, PNG, WEBP, dan PDF (Maks. 10MB per file).</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Judul / Keterangan Berkas (Opsional)</label>
                            <input type="text" name="judul" placeholder="Contoh: Nota Toko Komputer, Bukti Transfer"
                                   class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-red-500 focus:ring-red-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Catatan Tambahan (Opsional)</label>
                            <textarea name="keterangan" rows="2" placeholder="Catatan opsional..."
                                      class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-red-500 focus:ring-red-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="showUploadModal = false"
                                    class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="rounded-xl bg-red-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-red-700">
                                Unggah Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Lightbox Gambar --}}
            <div x-show="previewImage" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md"
                 @click.self="previewImage = null">
                <div class="relative max-h-[90vh] max-w-4xl overflow-hidden rounded-2xl bg-white p-2 shadow-2xl">
                    <button type="button" @click="previewImage = null"
                            class="absolute top-4 right-4 z-10 flex h-8 w-8 items-center justify-center rounded-full bg-black/60 text-white hover:bg-black">✕</button>
                    <img :src="previewImage" alt="Preview Eviden" class="max-h-[85vh] w-auto rounded-xl object-contain">
                </div>
            </div>

            {{-- Modal Preview PDF --}}
            <div x-show="previewPdf" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 p-4 backdrop-blur-md"
                 @click.self="previewPdf = null">
                <div class="relative h-[90vh] w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl flex flex-col">
                    <div class="flex items-center justify-between border-b px-4 py-3">
                        <span class="text-xs font-black uppercase text-slate-700">Preview Dokumen PDF</span>
                        <button type="button" @click="previewPdf = null" class="text-slate-400 hover:text-slate-700">✕</button>
                    </div>
                    <iframe :src="previewPdf" class="h-full w-full border-0"></iframe>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
