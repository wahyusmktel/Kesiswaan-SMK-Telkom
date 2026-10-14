<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-indigo-600">
            <a href="{{ route('rab.index') }}" class="hover:underline">Anggaran & Pengadaan</a>
            <span>/</span>
            <span>Portal Keuangan</span>
        </div>
        <h1 class="mt-1 text-2xl font-black tracking-tight text-slate-900">
            Verifikasi Anggaran & Realisasi
        </h1>
        <p class="mt-0.5 text-xs text-slate-500">
            Verifikasi pengajuan RAB, reimburse, dan audit laporan realisasi belanja.
        </p>
    </x-slot>

    <div class="py-8" x-data="{
        activeTab: '{{ $activeTab }}',
        previewImage: null,
        previewPdf: null,
        verifyModalRab: null,
        verifyModalRealisasi: null
    }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="flex h-8 w-8 items-center justify-center rounded-xl bg-emerald-600 text-white font-bold">✓</span>
                        <p class="text-sm font-semibold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            {{-- Metrik Ringkasan Keuangan --}}
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Menunggu Verifikasi</p>
                    <p class="mt-2 text-3xl font-black text-amber-600">
                        {{ $stats['menunggu_verifikasi'] }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Usulan butuh persetujuan / review</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Anggaran Disetujui</p>
                    <p class="mt-2 text-2xl font-black text-emerald-600">
                        Rp {{ number_format($stats['total_anggaran_disetujui'], 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Akumulasi usulan berstatus disetujui</p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Realisasi Belanja & Sisa</p>
                    <p class="mt-2 text-lg font-black text-rose-600">
                        Rp {{ number_format($stats['total_realisasi_pengeluaran'], 0, ',', '.') }}
                    </p>
                    <p class="mt-1 text-xs font-semibold text-blue-600">
                        Sisa Kas: Rp {{ number_format($stats['total_realisasi_saldo'], 0, ',', '.') }}
                    </p>
                </div>

                <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Bukti Eviden Terkumpul</p>
                    <p class="mt-2 text-3xl font-black text-indigo-600">
                        {{ $stats['total_evidences'] }}
                    </p>
                    <p class="mt-1 text-xs text-slate-500">Nota, kuitansi, dan berkas PDF fisik</p>
                </div>
            </div>

            {{-- Navigasi Tab & Aksi Cepat --}}
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-3 border-b border-slate-200 pb-2 lg:pb-0">
                <div class="flex items-center gap-2 overflow-x-auto">
                    <a href="{{ route('keuangan.rab.index', array_merge(request()->query(), ['tab' => 'usulan'])) }}"
                       class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap {{ $activeTab === 'usulan' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Usulan Anggaran (RAB & Reimburse)</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $usulans->total() }}</span>
                    </a>

                    <a href="{{ route('keuangan.rab.index', array_merge(request()->query(), ['tab' => 'realisasi'])) }}"
                       class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap {{ $activeTab === 'realisasi' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>Laporan Realisasi Anggaran</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $realisasis->total() }}</span>
                    </a>

                    <a href="{{ route('keuangan.rab.index', array_merge(request()->query(), ['tab' => 'eviden'])) }}"
                       class="inline-flex items-center gap-2 border-b-2 px-4 py-3 text-sm font-bold transition whitespace-nowrap {{ $activeTab === 'eviden' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-800' }}">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <span>Arsip Bukti Eviden Transaksi</span>
                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $evidences->total() }}</span>
                    </a>
                </div>

                {{-- Tombol Aksi (Dipindahkan dari Header) --}}
                <div class="flex items-center gap-2 shrink-0 py-1">
                    <a href="{{ route('rab.index') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-sm transition hover:bg-slate-50 hover:border-slate-300">
                        <svg class="h-4 w-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        <span>Halaman Utama RAB</span>
                    </a>
                    <a href="{{ route('rab.create') }}"
                       class="inline-flex items-center gap-1.5 rounded-xl bg-red-600 px-3.5 py-2 text-xs font-bold text-white shadow-sm transition hover:bg-red-700">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>Buat Pengajuan Baru</span>
                    </a>
                </div>
            </div>

            {{-- Filter & Pencarian Bar --}}
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <form method="GET" action="{{ route('keuangan.rab.index') }}" class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-5 items-end">
                    <input type="hidden" name="tab" value="{{ $activeTab }}">

                    <div class="lg:col-span-2">
                        <label class="block text-xs font-bold text-slate-600">Cari Dokumen / Nama</label>
                        <input type="text" name="q" value="{{ $search }}"
                               placeholder="Cari judul kegiatan, nomor akun, pembuat..."
                               class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600">Tahun Anggaran</label>
                        <select name="tahun" class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="semua" {{ $tahun === 'semua' ? 'selected' : '' }}>Semua Tahun</option>
                            @foreach($daftarTahun as $th)
                                <option value="{{ $th }}" {{ (string)$tahun === (string)$th ? 'selected' : '' }}>{{ $th }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-600">Status</label>
                        <select name="status" class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status</option>
                            <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="diajukan" {{ $status === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                            <option value="diverifikasi_keuangan" {{ $status === 'diverifikasi_keuangan' ? 'selected' : '' }}>Diverifikasi Keuangan</option>
                            <option value="disetujui" {{ $status === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                            <option value="ditolak" {{ $status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-bold text-white hover:bg-slate-800 transition">
                            Terapkan
                        </button>
                        <a href="{{ route('keuangan.rab.index', ['tab' => $activeTab]) }}" class="rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-bold text-slate-600 hover:bg-slate-50">
                            Reset
                        </a>
                    </div>
                </form>
            </div>

            {{-- ======================================================== --}}
            {{-- TAB 1: USULAN ANGGARAN (RAB & REIMBURSE) --}}
            {{-- ======================================================== --}}
            @if($activeTab === 'usulan')
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-slate-50 font-bold uppercase text-[11px] text-slate-600">
                                <tr>
                                    <th class="px-4 py-3.5">Dokumen & Judul</th>
                                    <th class="px-4 py-3.5">Pembuat / Jabatan</th>
                                    <th class="px-4 py-3.5">Jenis</th>
                                    <th class="px-4 py-3.5 text-right">Total Anggaran</th>
                                    <th class="px-4 py-3.5 text-center">Status</th>
                                    <th class="px-4 py-3.5 text-center">Realisasi</th>
                                    <th class="px-4 py-3.5 text-center">Aksi Verifikasi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($usulans as $item)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5">
                                            <a href="{{ route('rab.show', $item) }}" class="font-bold text-slate-900 hover:text-red-600 line-clamp-1">
                                                {{ $item->judul_pengajuan }}
                                            </a>
                                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                {{ $item->nomor_pengajuan }} &bull; TA {{ $item->tahun_anggaran }} &bull; Akun {{ $item->nomor_akun }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <p class="font-bold text-slate-800">{{ $item->pembuat_nama }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $item->pembuat_jabatan }} ({{ $item->pembuat_nik }})</p>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            @if($item->jenis_pengajuan === 'reimburse')
                                                <span class="rounded-full bg-amber-100 px-2.5 py-1 text-[10px] font-black uppercase text-amber-800">
                                                    Reimburse
                                                </span>
                                            @else
                                                <span class="rounded-full bg-blue-100 px-2.5 py-1 text-[10px] font-black uppercase text-blue-800">
                                                    RAB
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-slate-900">
                                            Rp {{ number_format($item->total_anggaran, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            @php
                                                $badgeClass = match($item->status) {
                                                    'disetujui' => 'bg-emerald-100 text-emerald-800',
                                                    'diajukan' => 'bg-amber-100 text-amber-800',
                                                    'ditolak' => 'bg-rose-100 text-rose-800',
                                                    default => 'bg-slate-100 text-slate-700',
                                                };
                                            @endphp
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $badgeClass }}">
                                                {{ ucfirst($item->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            @if($item->jenis_pengajuan === 'rab')
                                                @if($item->realisasi)
                                                    <a href="{{ route('rab.realisasi.show', $item) }}"
                                                       class="inline-flex items-center gap-1 text-[11px] font-bold text-indigo-600 hover:underline">
                                                        <span>Ada ({{ $item->realisasi->evidences->count() }} Eviden)</span>
                                                    </a>
                                                @else
                                                    <a href="{{ route('rab.realisasi.show', $item) }}"
                                                       class="text-[11px] font-semibold text-slate-400 hover:text-indigo-600">
                                                        + Buat Realisasi
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-[11px] text-slate-400">-</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button"
                                                        @click="verifyModalRab = {{ Js::from(['id' => $item->id, 'nomor' => $item->nomor_pengajuan, 'judul' => $item->judul_pengajuan, 'status' => $item->status, 'catatan' => $item->catatan]) }}"
                                                        class="rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">
                                                    Verifikasi
                                                </button>
                                                <a href="{{ route('rab.show', $item) }}"
                                                   class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                                    Detail
                                                </a>
                                                <a href="{{ route('rab.pdf', $item) }}" target="_blank"
                                                   class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">
                                            Tidak ditemukan data usulan anggaran sesuai kriteria filter.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($usulans->hasPages())
                        <div class="border-t border-slate-100 p-4">
                            {{ $usulans->links() }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- TAB 2: LAPORAN REALISASI ANGGARAN --}}
            {{-- ======================================================== --}}
            @if($activeTab === 'realisasi')
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-700">
                            <thead class="border-b border-slate-200 bg-slate-50 font-bold uppercase text-[11px] text-slate-600">
                                <tr>
                                    <th class="px-4 py-3.5">Nomor & Kegiatan</th>
                                    <th class="px-4 py-3.5">Pelaksana / Pembuat</th>
                                    <th class="px-4 py-3.5 text-right">Penerimaan</th>
                                    <th class="px-4 py-3.5 text-right">Pengeluaran</th>
                                    <th class="px-4 py-3.5 text-right">Sisa Saldo</th>
                                    <th class="px-4 py-3.5 text-center">Bukti Eviden</th>
                                    <th class="px-4 py-3.5 text-center">Status</th>
                                    <th class="px-4 py-3.5 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse($realisasis as $r)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="px-4 py-3.5">
                                            <a href="{{ route('rab.realisasi.show', $r->rabSubmission) }}"
                                               class="font-bold text-slate-900 hover:text-red-600 line-clamp-1">
                                                {{ $r->rabSubmission?->judul_pengajuan }}
                                            </a>
                                            <p class="text-[11px] text-slate-400 font-mono mt-0.5">
                                                {{ $r->nomor_realisasi }} &bull; TA {{ $r->rabSubmission?->tahun_anggaran }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <p class="font-bold text-slate-800">{{ $r->pembuat_nama }}</p>
                                            <p class="text-[11px] text-slate-400">{{ $r->pembuat_jabatan }}</p>
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-emerald-700">
                                            Rp {{ number_format($r->total_penerimaan, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-mono font-bold text-rose-700">
                                            Rp {{ number_format($r->total_pengeluaran, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3.5 text-right font-mono font-bold {{ $r->saldo_akhir >= 0 ? 'text-blue-700' : 'text-red-700' }}">
                                            Rp {{ number_format($r->saldo_akhir, 0, ',', '.') }}
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-bold text-slate-700">
                                                {{ $r->evidences->count() }} Dokumen
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            @php
                                                $rBadge = match($r->status) {
                                                    'disetujui' => 'bg-emerald-100 text-emerald-800',
                                                    'diverifikasi_keuangan' => 'bg-indigo-100 text-indigo-800',
                                                    'diajukan' => 'bg-amber-100 text-amber-800',
                                                    'ditolak' => 'bg-rose-100 text-rose-800',
                                                    default => 'bg-slate-100 text-slate-700',
                                                };
                                            @endphp
                                            <span class="rounded-full px-2.5 py-1 text-[10px] font-black uppercase {{ $rBadge }}">
                                                {{ str_replace('_', ' ', $r->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3.5 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button"
                                                        @click="verifyModalRealisasi = {{ Js::from(['id' => $r->id, 'nomor' => $r->nomor_realisasi, 'status' => $r->status, 'catatan' => $r->catatan_keuangan]) }}"
                                                        class="rounded-lg bg-indigo-50 px-2.5 py-1.5 text-xs font-bold text-indigo-700 hover:bg-indigo-100">
                                                    Audit Status
                                                </button>
                                                <a href="{{ route('rab.realisasi.show', $r->rabSubmission) }}"
                                                   class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold text-slate-600 hover:bg-slate-50">
                                                    Buka
                                                </a>
                                                <a href="{{ route('rab.realisasi.pdf', $r->rabSubmission) }}" target="_blank"
                                                   class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50">
                                                    PDF
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-12 text-center text-slate-400">
                                            Belum ada laporan realisasi yang tercatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($realisasis->hasPages())
                        <div class="border-t border-slate-100 p-4">
                            {{ $realisasis->links() }}
                        </div>
                    @endif
                </div>
            @endif

            {{-- ======================================================== --}}
            {{-- TAB 3: ARSIP BUKTI EVIDEN TRANSAKSI --}}
            {{-- ======================================================== --}}
            @if($activeTab === 'eviden')
                <div class="overflow-hidden rounded-3xl border border-slate-200 bg-white p-6 shadow-sm space-y-6">
                    <div>
                        <h3 class="text-base font-black text-slate-900">Arsip Seluruh Bukti Eviden Transaksi</h3>
                        <p class="text-xs text-slate-500">Kumpulan nota, kuitansi, struk belanja, dan bukti scan dokumen fisik.</p>
                    </div>

                    @if($evidences->isEmpty())
                        <div class="py-12 text-center text-slate-400">
                            Belum ada bukti dokumen eviden yang diunggah.
                        </div>
                    @else
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
                            @foreach($evidences as $ev)
                                <div class="group relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200 bg-slate-50 transition hover:shadow-md">
                                    <div class="p-3">
                                        @if($ev->isImage())
                                            <div class="relative aspect-video w-full overflow-hidden rounded-xl bg-slate-200 cursor-pointer"
                                                 @click="previewImage = '{{ asset('storage/' . $ev->file_path) }}'">
                                                <img src="{{ asset('storage/' . $ev->file_path) }}" alt="{{ $ev->judul }}"
                                                     class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
                                                <span class="absolute bottom-2 right-2 rounded-lg bg-black/60 px-2 py-0.5 text-[10px] font-bold text-white">
                                                    Foto / Nota
                                                </span>
                                            </div>
                                        @else
                                            <div class="relative flex aspect-video w-full flex-col items-center justify-center rounded-xl bg-red-50 text-red-600 cursor-pointer"
                                                 @click="previewPdf = '{{ asset('storage/' . $ev->file_path) }}'">
                                                <svg class="h-10 w-10 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd" />
                                                </svg>
                                                <span class="mt-2 text-xs font-black uppercase text-red-700">Dokumen PDF</span>
                                            </div>
                                        @endif

                                        <div class="mt-3 space-y-1">
                                            <p class="text-xs font-bold text-slate-800 truncate" title="{{ $ev->judul }}">
                                                {{ $ev->judul }}
                                            </p>
                                            <p class="text-[11px] text-slate-500 truncate">
                                                {{ $ev->realisasi?->rabSubmission?->judul_pengajuan ?? 'RAB' }}
                                            </p>
                                            <p class="text-[10px] text-slate-400">
                                                {{ $ev->file_name }} &bull; {{ number_format($ev->file_size / 1024, 0) }} KB
                                            </p>
                                        </div>
                                    </div>

                                    <div class="flex items-center justify-between border-t border-slate-200/80 bg-white px-3 py-2 text-[11px]">
                                        <a href="{{ asset('storage/' . $ev->file_path) }}" target="_blank"
                                           class="font-bold text-slate-700 hover:text-indigo-600 flex items-center gap-1">
                                            Unduh
                                        </a>
                                        @if($ev->realisasi?->rabSubmission)
                                            <a href="{{ route('rab.realisasi.show', $ev->realisasi->rabSubmission) }}"
                                               class="font-semibold text-indigo-600 hover:underline">
                                                Ke Laporan
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        @if($evidences->hasPages())
                            <div class="border-t border-slate-100 pt-4">
                                {{ $evidences->links() }}
                            </div>
                        @endif
                    @endif
                </div>
            @endif

            {{-- Modal Verifikasi Cepat RAB --}}
            <div x-show="verifyModalRab" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl space-y-4" @click.outside="verifyModalRab = null">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h4 class="text-sm font-black text-slate-900">Verifikasi Pengajuan Anggaran</h4>
                        <button type="button" @click="verifyModalRab = null" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form :action="`/keuangan/rab/${verifyModalRab?.id}/status`" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <p class="text-xs font-bold text-slate-800" x-text="verifyModalRab?.judul"></p>
                            <p class="text-[11px] text-slate-400 font-mono mt-0.5" x-text="verifyModalRab?.nomor"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Status Persetujuan Keuangan</label>
                            <select name="status" x-model="verifyModalRab.status" class="mt-1 block w-full rounded-xl border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft">Draft</option>
                                <option value="diajukan">Diajukan</option>
                                <option value="disetujui">Disetujui</option>
                                <option value="ditolak">Ditolak</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Catatan Pemeriksaan</label>
                            <textarea name="catatan" x-model="verifyModalRab.catatan" rows="3"
                                      placeholder="Catatan persetujuan anggaran..."
                                      class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="verifyModalRab = null"
                                    class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">
                                Simpan Verifikasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Modal Verifikasi Cepat Realisasi --}}
            <div x-show="verifyModalRealisasi" x-cloak
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4 backdrop-blur-sm">
                <div class="w-full max-w-md rounded-3xl bg-white p-6 shadow-2xl space-y-4" @click.outside="verifyModalRealisasi = null">
                    <div class="flex items-center justify-between border-b pb-3">
                        <h4 class="text-sm font-black text-slate-900">Audit Status Laporan Realisasi</h4>
                        <button type="button" @click="verifyModalRealisasi = null" class="text-slate-400 hover:text-slate-600">✕</button>
                    </div>

                    <form :action="`/keuangan/realisasi/${verifyModalRealisasi?.id}/status`" method="POST" class="space-y-4">
                        @csrf
                        @method('PATCH')

                        <div>
                            <p class="text-xs font-bold text-slate-800" x-text="verifyModalRealisasi?.nomor"></p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Status Audit Realisasi</label>
                            <select name="status" x-model="verifyModalRealisasi.status" class="mt-1 block w-full rounded-xl border-slate-200 text-xs font-semibold focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="draft">Draft</option>
                                <option value="diajukan">Diajukan</option>
                                <option value="diverifikasi_keuangan">Diverifikasi Keuangan (Lengkap)</option>
                                <option value="disetujui">Disetujui Selesai</option>
                                <option value="ditolak">Ditolak / Revisi</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-slate-700">Catatan Keuangan / Temuan Audit</label>
                            <textarea name="catatan_keuangan" x-model="verifyModalRealisasi.catatan" rows="3"
                                      placeholder="Catatan kelengkapan nota / bukti eviden..."
                                      class="mt-1 block w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" @click="verifyModalRealisasi = null"
                                    class="rounded-xl border border-slate-200 px-4 py-2 text-xs font-bold text-slate-600 hover:bg-slate-50">
                                Batal
                            </button>
                            <button type="submit"
                                    class="rounded-xl bg-indigo-600 px-5 py-2 text-xs font-bold text-white shadow-sm hover:bg-indigo-700">
                                Simpan Audit
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
