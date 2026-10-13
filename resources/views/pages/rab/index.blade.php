@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-slate-50/70 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Top Header --}}
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white rounded-2xl p-6 shadow-sm border border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">
                        Keuangan & Anggaran
                    </span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-100 text-purple-700 gap-1">
                        ✨ Terintegrasi Stella AI
                    </span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight mt-1">
                    Rencana Anggaran Biaya (RAB)
                </h1>
                <p class="text-sm text-slate-500 mt-0.5">
                    Kelola pengadaan, rincian biaya anggaran, integrasi AI, dan cetak dokumen resmi Telkom Schools.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('rab.create') }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-xl bg-gradient-to-r from-red-600 to-rose-600 text-white font-bold text-sm shadow-lg shadow-red-500/25 hover:shadow-red-500/40 hover:from-red-700 hover:to-rose-700 transition-all duration-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    <span>Buat Pengajuan RAB</span>
                </a>
            </div>
        </div>

        {{-- Flash Notification --}}
        @if(session('success'))
            <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-red-50 text-red-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Dokumen</p>
                    <h3 class="text-2xl font-black text-slate-900 mt-0.5">{{ number_format($stats['total_dokumen']) }}</h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Anggaran</p>
                    <h3 class="text-xl sm:text-2xl font-black text-emerald-600 mt-0.5">
                        Rp {{ number_format($stats['total_nominal'], 0, ',', '.') }}
                    </h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Draft / Diajukan</p>
                    <h3 class="text-2xl font-black text-amber-600 mt-0.5">
                        {{ number_format($stats['draft'] + $stats['diajukan']) }}
                    </h3>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Disetujui</p>
                    <h3 class="text-2xl font-black text-blue-600 mt-0.5">{{ number_format($stats['disetujui']) }}</h3>
                </div>
            </div>
        </div>

        {{-- Filters & Search --}}
        <div class="bg-white rounded-2xl p-5 border border-slate-100 shadow-sm">
            <form method="GET" action="{{ route('rab.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3">
                <div class="lg:col-span-5 relative">
                    <input type="text" name="search" value="{{ $search }}"
                        placeholder="Cari judul pengajuan, nomor akun, DRK, pembuat..."
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm focus:border-red-500 focus:ring-red-500/20">
                    <svg class="w-5 h-5 text-slate-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>

                <div class="lg:col-span-3">
                    <select name="status" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-sm focus:border-red-500 focus:ring-red-500/20">
                        <option value="">Semua Status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="diajukan" {{ request('status') === 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                        <option value="disetujui" {{ request('status') === 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                        <option value="ditolak" {{ request('status') === 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <select name="tahun" class="w-full py-2.5 px-3 rounded-xl border border-slate-200 text-sm focus:border-red-500 focus:ring-red-500/20">
                        <option value="">Semua Tahun</option>
                        @foreach($daftarTahun as $thn)
                            <option value="{{ $thn }}" {{ request('tahun') == $thn ? 'selected' : '' }}>{{ $thn }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="lg:col-span-2 flex items-center gap-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white text-sm font-bold rounded-xl transition-colors">
                        Filter
                    </button>
                    @if(request()->hasAny(['search', 'status', 'tahun']))
                        <a href="{{ route('rab.index') }}"
                            class="p-2.5 text-slate-500 hover:text-slate-800 hover:bg-slate-100 rounded-xl transition-colors" title="Reset Filter">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Table Content --}}
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/80 text-xs uppercase tracking-wider text-slate-500 border-b border-slate-100">
                        <tr>
                            <th class="py-4 px-5 font-bold">Judul & Nomor Pengajuan</th>
                            <th class="py-4 px-5 font-bold">Akun & DRK</th>
                            <th class="py-4 px-5 font-bold">Waktu & Pembuat</th>
                            <th class="py-4 px-5 font-bold text-right">Total Anggaran</th>
                            <th class="py-4 px-5 font-bold text-center">Status</th>
                            <th class="py-4 px-5 font-bold text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rabs as $rab)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-slate-900 text-base leading-tight">
                                        <a href="{{ route('rab.show', $rab) }}" class="hover:text-red-600 transition-colors">
                                            {{ $rab->judul_pengajuan }}
                                        </a>
                                    </div>
                                    <div class="text-xs font-semibold text-slate-400 mt-1 flex items-center gap-2">
                                        <span>{{ $rab->nomor_pengajuan ?: '-' }}</span>
                                        <span>•</span>
                                        <span class="text-red-600 font-bold">TA {{ $rab->tahun_anggaran }}</span>
                                    </div>
                                </td>

                                <td class="py-4 px-5">
                                    <div class="font-semibold text-slate-800 text-xs">
                                        {{ $rab->nomor_akun }} - {{ $rab->nama_akun }}
                                    </div>
                                    <div class="text-xs text-slate-500 line-clamp-1 mt-0.5" title="{{ $rab->drk }}">
                                        {{ $rab->drk }}
                                    </div>
                                </td>

                                <td class="py-4 px-5">
                                    <div class="font-semibold text-slate-800 text-xs">
                                        {{ $rab->kebutuhan_waktu ?: '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 mt-0.5">
                                        Oleh: {{ $rab->pembuat_nama ?: ($rab->user?->name ?: '-') }}
                                    </div>
                                </td>

                                <td class="py-4 px-5 text-right font-black text-slate-900 text-base whitespace-nowrap">
                                    Rp {{ number_format($rab->total_anggaran, 0, ',', '.') }}
                                    <div class="text-[11px] font-normal text-slate-400">
                                        {{ $rab->items->count() }} item
                                    </div>
                                </td>

                                <td class="py-4 px-5 text-center">
                                    @php
                                        $badgeClass = match($rab->status) {
                                            'disetujui' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
                                            'diajukan' => 'bg-blue-100 text-blue-800 border-blue-200',
                                            'ditolak' => 'bg-rose-100 text-rose-800 border-rose-200',
                                            default => 'bg-slate-100 text-slate-700 border-slate-200',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $badgeClass }} uppercase">
                                        {{ $rab->status }}
                                    </span>
                                </td>

                                <td class="py-4 px-5">
                                    <div class="flex items-center justify-center gap-1.5">
                                        {{-- Detail --}}
                                        <a href="{{ route('rab.show', $rab) }}"
                                            class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg transition-colors"
                                            title="Lihat Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                        </a>

                                        {{-- Print Browser --}}
                                        <a href="{{ route('rab.print', $rab) }}" target="_blank"
                                            class="p-2 text-indigo-600 hover:text-indigo-900 hover:bg-indigo-50 rounded-lg transition-colors"
                                            title="Cetak Langsung">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                                            </svg>
                                        </a>

                                        {{-- Download PDF --}}
                                        <a href="{{ route('rab.pdf', $rab) }}"
                                            class="p-2 text-red-600 hover:text-red-900 hover:bg-red-50 rounded-lg transition-colors"
                                            title="Unduh PDF Resmi">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                            </svg>
                                        </a>

                                        {{-- Edit --}}
                                        <a href="{{ route('rab.edit', $rab) }}"
                                            class="p-2 text-amber-600 hover:text-amber-900 hover:bg-amber-50 rounded-lg transition-colors"
                                            title="Edit RAB">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                        </a>

                                        {{-- Hapus --}}
                                        <form action="{{ route('rab.destroy', $rab) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus dokumen RAB ini? Data rincian akan terhapus.');"
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-2 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors"
                                                title="Hapus RAB">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-500">
                                    <div class="w-16 h-16 mx-auto mb-3 text-slate-300">
                                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </div>
                                    <p class="font-bold text-slate-700 text-base">Belum ada dokumen RAB</p>
                                    <p class="text-xs text-slate-400 mt-1">Klik tombol "Buat Pengajuan RAB" di atas untuk mulai membuat rencana pengadaan anggaran.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rabs->hasPages())
                <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                    {{ $rabs->links() }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
