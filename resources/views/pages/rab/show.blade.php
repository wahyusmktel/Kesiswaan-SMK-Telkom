@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/70 p-4 sm:p-6 lg:p-8">
    <div class="max-w-5xl mx-auto space-y-6">

        {{-- Top Bar & Actions --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div>
                <a href="{{ route('rab.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-red-600 mb-2 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    <span>Kembali ke Daftar RAB</span>
                </a>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-black text-slate-900 tracking-tight">
                        Dokumen RAB
                    </h1>
                    @php
                        $badgeClass = match($rab->status) {
                            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                            'diajukan' => 'bg-blue-100 text-blue-800 border-blue-200',
                            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $badgeClass }} uppercase">
                        {{ $rab->status }}
                    </span>
                </div>
                <p class="text-xs text-slate-400 mt-0.5 font-semibold">
                    {{ $rab->nomor_pengajuan ?: '-' }} • Dibuat oleh {{ $rab->pembuat_nama ?: ($rab->user?->name ?: '-') }}
                </p>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap items-center gap-2">
                {{-- Print View --}}
                <a href="{{ route('rab.print', $rab) }}" target="_blank"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-sm transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    <span>Cetak Dokumen</span>
                </a>

                {{-- Download PDF --}}
                <a href="{{ route('rab.pdf', $rab) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl bg-red-600 hover:bg-red-700 text-white text-xs font-bold shadow-sm shadow-red-500/20 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    <span>Unduh PDF</span>
                </a>

                {{-- Edit --}}
                <a href="{{ route('rab.edit', $rab) }}"
                    class="inline-flex items-center gap-1.5 px-4 py-2.5 rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition-all">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    <span>Edit</span>
                </a>
            </div>
        </div>

        {{-- Status Change Form (Quick update) --}}
        <div class="bg-white rounded-2xl p-4 border border-slate-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-3 text-xs">
            <span class="font-bold text-slate-700 flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-red-500 animate-pulse"></span>
                Ubah Alur Status Dokumen RAB:
            </span>
            <form action="{{ route('rab.status', $rab) }}" method="POST" class="flex items-center gap-2 w-full sm:w-auto">
                @csrf
                @method('PATCH')
                <select name="status" class="py-1.5 px-3 rounded-xl border border-slate-200 text-xs font-bold focus:border-red-500">
                    <option value="draft" {{ $rab->status === 'draft' ? 'selected' : '' }}>Draft (Konsep)</option>
                    <option value="diajukan" {{ $rab->status === 'diajukan' ? 'selected' : '' }}>Diajukan (Menunggu Persetujuan)</option>
                    <option value="disetujui" {{ $rab->status === 'disetujui' ? 'selected' : '' }}>Disetujui (Disahkan)</option>
                    <option value="ditolak" {{ $rab->status === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                </select>
                <button type="submit" class="px-3.5 py-1.5 bg-slate-800 hover:bg-slate-900 text-white rounded-xl font-bold transition-colors">
                    Perbarui Status
                </button>
            </form>
        </div>

        {{-- Preview Document Card (Styled identical to PDF print layout) --}}
        <div class="bg-white rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-xl overflow-hidden relative font-sans">
            {{-- Top Kop Surat --}}
            <div class="flex items-center gap-6 border-b-4 border-red-700 pb-4 mb-6">
                <div class="w-24 shrink-0 flex flex-col items-center justify-center text-center">
                    <img src="{{ asset('images/teaching-module/smk-telkom-lampung.png') }}"
                        alt="Logo SMK Telkom"
                        class="w-20 h-auto object-contain"
                        onerror="this.style.display='none'">
                </div>
                <div class="flex-1 text-center text-slate-900">
                    <h3 class="font-bold text-sm tracking-wider uppercase text-slate-800">YAYASAN PENDIDIKAN TELKOM</h3>
                    <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-950 my-0.5">SMK Telkom Lampung</h2>
                    <p class="text-[11px] italic text-slate-600">Kompetensi Keahlian :</p>
                    <p class="text-[10px] text-slate-600">(1) Teknik Jaringan Akses Telekomunikasi (2) Rekayasa Perangkat Lunak (3) Teknik Komputer Dan Jaringan (4) Animasi</p>
                    <p class="text-[10px] text-slate-600">Alamat: Jl. Raya Gadingrejo, Kec. Gading Rejo, Kabupaten Pringsewu, Lampung 35372 - Telp/Faks : 0721 5621 547</p>
                    <p class="text-[10px] font-bold text-slate-800">NPSN : 69944770</p>
                    <p class="text-[10px] text-slate-600">w: www.smktelkom-lpg.sch.id e: smktelkom9914@gmail.com / telkomschool@smktelkom-lpg.sch.id</p>
                </div>
            </div>

            {{-- Document Title --}}
            <div class="text-center my-6 space-y-1">
                <h2 class="text-base sm:text-lg font-black text-slate-900 tracking-wide uppercase">RENCANA ANGGARAN BIAYA (RAB)</h2>
                <h3 class="text-sm sm:text-base font-bold text-slate-900 uppercase">{{ $rab->judul_pengajuan }}</h3>
                <h4 class="text-sm sm:text-base font-bold text-slate-900 uppercase">TAHUN ANGGARAN {{ $rab->tahun_anggaran }}</h4>
            </div>

            {{-- Metadata Table --}}
            <div class="my-6 text-xs text-slate-900 space-y-1 max-w-xl">
                <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 font-bold uppercase">NOMOR AKUN</span>
                    <span class="col-span-1 text-center font-bold">:</span>
                    <span class="col-span-7 font-medium">{{ $rab->nomor_akun }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 font-bold uppercase">NAMA AKUN</span>
                    <span class="col-span-1 text-center font-bold">:</span>
                    <span class="col-span-7 font-medium">{{ $rab->nama_akun }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 font-bold uppercase">DRK</span>
                    <span class="col-span-1 text-center font-bold">:</span>
                    <span class="col-span-7 font-medium">{{ $rab->drk }}</span>
                </div>
                <div class="grid grid-cols-12 gap-1">
                    <span class="col-span-4 font-bold uppercase">KEBUTUHAN WAKTU</span>
                    <span class="col-span-1 text-center font-bold">:</span>
                    <span class="col-span-7 font-medium">{{ $rab->kebutuhan_waktu }}</span>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="overflow-x-auto my-6 border border-black">
                <table class="w-full text-left text-xs border-collapse">
                    <thead class="bg-white text-slate-900 uppercase font-bold border-b border-black text-center">
                        <tr>
                            <th class="border-r border-black p-2 w-10">NO</th>
                            <th class="border-r border-black p-2">URAIAN KEGIATAN</th>
                            <th class="border-r border-black p-2">SPESIFIKASI</th>
                            <th class="border-r border-black p-2 w-20">SATUAN</th>
                            <th class="border-r border-black p-2 w-14">VOL</th>
                            <th class="border-r border-black p-2 w-28 text-right">HARGA</th>
                            <th class="p-2 w-32 text-right">JUMLAH</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black">
                        @foreach($rab->items as $index => $item)
                            <tr>
                                <td class="border-r border-black p-2 text-center">{{ $index + 1 }}</td>
                                <td class="border-r border-black p-2 font-medium">{{ $item->uraian_kegiatan }}</td>
                                <td class="border-r border-black p-2 text-center">{{ $item->spesifikasi ?: '-' }}</td>
                                <td class="border-r border-black p-2 text-center">{{ $item->satuan }}</td>
                                <td class="border-r border-black p-2 text-center">{{ $item->volume == intval($item->volume) ? intval($item->volume) : number_format($item->volume, 1) }}</td>
                                <td class="border-r border-black p-2 text-right whitespace-nowrap">Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</td>
                                <td class="p-2 text-right font-semibold whitespace-nowrap">Rp {{ number_format($item->jumlah, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-bold border-t-2 border-black bg-slate-50">
                            <td colspan="6" class="border-r border-black p-2.5 text-right uppercase tracking-wider font-bold">
                                TOTAL ANGGARAN
                            </td>
                            <td class="p-2.5 text-right font-black text-sm whitespace-nowrap">
                                Rp {{ number_format($rab->total_anggaran, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Approval Table --}}
            <div class="my-6 border border-black overflow-x-auto text-xs">
                <table class="w-full border-collapse">
                    <thead class="bg-white uppercase font-bold border-b border-black text-center">
                        <tr>
                            <th class="border-r border-black p-2 w-1/4"></th>
                            <th class="border-r border-black p-2 w-1/3">NAMA / NIK</th>
                            <th class="border-r border-black p-2 w-1/5">JABATAN</th>
                            <th class="border-r border-black p-2 w-24">TANGGAL</th>
                            <th class="p-2 w-28">TANDA TANGAN</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-black">
                        {{-- Dibuat oleh --}}
                        <tr>
                            <td class="border-r border-black p-2 font-medium">Dibuat oleh</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->pembuat_nama }}{{ $rab->pembuat_nik ? ' / ' . $rab->pembuat_nik : '' }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->pembuat_jabatan ?: '-' }}</td>
                            <td class="border-r border-black p-2 text-center whitespace-nowrap">{{ $rab->tanggal_dibuat ? $rab->tanggal_dibuat->format('d-M-y') : '-' }}</td>
                            <td class="p-2 h-14"></td>
                        </tr>

                        {{-- Catatan Anggaran --}}
                        <tr>
                            <td colspan="5" class="p-2.5 text-left font-normal bg-slate-50/50">
                                <span class="font-bold uppercase text-[11px] block">CATATAN ANGGARAN:</span>
                                <span>{{ $rab->catatan_anggaran ?: '-' }}</span>
                            </td>
                        </tr>

                        {{-- Diperiksa & Disetujui oleh --}}
                        <tr>
                            <td class="border-r border-black p-2 font-medium">Diperiksa & Disetujui oleh</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->pemeriksa_nama }}{{ $rab->pemeriksa_nik ? ' / ' . $rab->pemeriksa_nik : '' }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->pemeriksa_jabatan ?: 'Kaur Keuangan' }}</td>
                            <td class="border-r border-black p-2 text-center whitespace-nowrap">{{ $rab->tanggal_diperiksa ? $rab->tanggal_diperiksa->format('d-M-y') : '-' }}</td>
                            <td class="p-2 h-14"></td>
                        </tr>

                        {{-- Diperiksa & Disetujui Realisasi --}}
                        <tr>
                            <td class="border-r border-black p-2 font-medium">Diperiksa & Disetujui Realisasi</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->penyetuju_nama }}{{ $rab->penyetuju_nik ? ' / ' . $rab->penyetuju_nik : '' }}</td>
                            <td class="border-r border-black p-2 text-center">{{ $rab->penyetuju_jabatan ?: 'Kepala Sekolah' }}</td>
                            <td class="border-r border-black p-2 text-center whitespace-nowrap">{{ $rab->tanggal_disetujui ? $rab->tanggal_disetujui->format('d-M-y') : '-' }}</td>
                            <td class="p-2 h-14"></td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Catatan Penutup Box --}}
            <div class="border border-dotted border-slate-600 p-3 rounded-lg text-xs space-y-1">
                <span class="font-bold uppercase tracking-wider block">CATATAN:</span>
                <p class="text-slate-800">{{ $rab->catatan ?: '-' }}</p>
            </div>
        </div>

    </div>
</div>
@endsection
