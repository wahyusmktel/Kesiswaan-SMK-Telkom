<x-app-layout>
    @php
        $penempatan = $penempatan ?? $laporan?->penempatan;
    @endphp
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.index') }}"
                    class="p-2 rounded-xl bg-gray-100 hover:bg-gray-200 text-gray-700 transition" title="Kembali">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Lembar Bimbingan: {{ $penempatan->siswa?->nama_lengkap }}</h2>
                    <p class="text-xs text-gray-500">{{ $penempatan->industri?->nama_industri }} • Rombel: {{ $penempatan->rombelPkl?->nama_rombel }}</p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                @if ($laporan->judul_status === 'disetujui')
                    <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.riwayat-pdf', $laporan) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh Rekap Riwayat (PDF)
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{
        showTolakJudulModal: false,
        showSetujuiJudulModal: false,
        showTambahTahap: false,
        showAccFinalModal: false
    }">
        {{-- Flash Messages --}}
        @if (session('success'))
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-emerald-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-800 flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5 text-rose-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        {{-- Mandatory Warning if Pembimbing has not set up Digital Signature --}}
        @if (!($hasTtdDigital ?? false))
            <div class="rounded-2xl border-2 border-amber-300 bg-amber-50 p-5 shadow-sm flex items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-amber-200 rounded-xl text-amber-800">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-amber-900 text-sm">Tanda Tangan Digital Belum Disetup</h4>
                        <p class="text-xs text-amber-800">Anda wajib mengaktifkan Tanda Tangan Digital untuk dapat menyetujui tahapan dan menerbitkan Berita Acara resmi.</p>
                    </div>
                </div>
                <a href="{{ route('tanda-tangan.index') }}"
                    class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-bold text-xs rounded-xl shadow-sm transition whitespace-nowrap">
                    Setup TTD Digital
                </a>
            </div>
        @endif

        {{-- Siswa Header Info Card --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-rose-200">
                        {{ strtoupper(substr($penempatan->siswa?->nama_lengkap ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Siswa Bimbingan</div>
                        <div class="font-bold text-gray-900 text-base leading-tight">{{ $penempatan->siswa?->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">NIS: {{ $penempatan->siswa?->nis ?? '-' }} • {{ $penempatan->siswa?->kelas_saat_ini ?? '-' }}</div>
                    </div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Industri Mitra PKL</div>
                    <div class="font-bold text-gray-900 text-sm mt-0.5">{{ $penempatan->industri?->nama_industri ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">{{ $penempatan->rombelPkl?->nama_rombel }}</div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Periode Penempatan</div>
                    <div class="font-bold text-gray-900 text-sm mt-0.5">
                        {{ $penempatan->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }} s/d {{ $penempatan->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}
                    </div>
                    <div class="text-xs text-emerald-600 font-semibold mt-0.5 uppercase tracking-wider">{{ $penempatan->status }}</div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Laporan</div>
                    <div class="mt-1">
                        @if ($laporan->status === 'selesai')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-black bg-emerald-100 text-emerald-800">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                ACC Final Selesai
                            </span>
                        @elseif ($laporan->judul_status === 'disetujui')
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">
                                Bimbingan Bab Aktif
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                Tahap Review Judul
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TAHAP 1: REVIEW JUDUL LAPORAN --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl {{ $laporan->judul_status === 'disetujui' ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center font-bold text-sm">
                        1
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Tahap 1: Pengajuan Judul Laporan Siswa</h3>
                        <p class="text-xs text-gray-500">Periksa kesesuaian judul dengan kompetensi dan aktivitas siswa di industri mitra</p>
                    </div>
                </div>

                <div>
                    @if ($laporan->judul_status === 'disetujui')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Judul Telah Disetujui
                        </span>
                    @elseif ($laporan->judul_status === 'ditolak')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                            <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            Judul Ditolak (Menunggu Revisi Siswa)
                        </span>
                    @elseif ($laporan->judul_status === 'diajukan')
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 animate-pulse">
                            Menunggu Persetujuan Anda
                        </span>
                    @else
                        <span class="px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                            Siswa Belum Mengajukan Judul
                        </span>
                    @endif
                </div>
            </div>

            <div class="p-6">
                @if ($laporan->judul)
                    <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200/80 space-y-3">
                        <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Judul yang Diajukan Siswa:</div>
                        <h4 class="font-extrabold text-gray-900 text-lg leading-snug">"{{ $laporan->judul }}"</h4>
                        @if ($laporan->abstrak_rencana)
                            <div class="text-xs text-gray-600 bg-white p-3 rounded-xl border border-gray-100">
                                <span class="font-bold text-gray-700">Ringkasan Rencana:</span> {{ $laporan->abstrak_rencana }}
                            </div>
                        @endif

                        @if ($laporan->catatan_pembimbing)
                            <div class="text-xs text-gray-700 bg-amber-50/70 border border-amber-200 p-3 rounded-xl">
                                <span class="font-bold text-amber-900">Catatan Review Anda Sebelumnya:</span> {{ $laporan->catatan_pembimbing }}
                            </div>
                        @endif
                    </div>

                    {{-- Review Action Buttons --}}
                    @if ($laporan->judul_status === 'diajukan' || $laporan->judul_status === 'ditolak')
                        <div class="flex items-center justify-end gap-3 mt-4 pt-4 border-t border-gray-100">
                            <button type="button" @click="showTolakJudulModal = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2.5 text-xs font-bold text-rose-700 bg-rose-50 border border-rose-200 hover:bg-rose-100 rounded-xl transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                Tolak dengan Catatan Alasan
                            </button>
                            <button type="button" @click="showSetujuiJudulModal = true"
                                class="inline-flex items-center gap-1.5 px-5 py-2.5 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                Setujui Judul Laporan
                            </button>
                        </div>
                    @endif
                @else
                    <div class="py-8 text-center text-gray-400 text-xs">
                        Siswa belum mengajukan judul laporan prakerin. Tahapan bimbingan bab akan otomatis terbuka setelah siswa mengajukan judul dan Anda menyetujuinya.
                    </div>
                @endif
            </div>
        </div>

        {{-- TAHAP 2: BIMBINGAN TAHAP BAB & INTERACTIVE ANNOTATION STUDIO --}}
        @if ($laporan->judul_status === 'disetujui')
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Tahap 2: Bimbingan Bab Dokumen Laporan</h3>
                                <p class="text-xs text-gray-500">Periksa file PDF laporan, berikan coretan/kotak revisi interaktif, dan setujui tahapan bab</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" @click="showTambahTahap = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Tambah Bab / Tahap
                            </button>

                            @if ($laporan->status !== 'selesai' && $laporan->semua_bab_disetujui)
                                <button type="button" @click="showAccFinalModal = true"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-extrabold text-white bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-700 hover:to-teal-700 rounded-xl shadow-md shadow-emerald-200 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    ACC Laporan Prakerin Final
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-gray-700">Progres Kelengkapan Bimbingan</span>
                            <span class="font-extrabold text-rose-600">{{ $laporan->persentase_selesai }}% Selesai ({{ $laporan->tahaps->where('status', 'disetujui')->count() }} dari {{ $laporan->tahaps->count() }} Tahap Disetujui)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden p-0.5">
                            <div class="bg-gradient-to-r from-rose-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                                style="width: {{ $laporan->persentase_selesai }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Daftar Tahap --}}
                <div class="divide-y divide-gray-100">
                    @forelse ($laporan->tahaps as $tahap)
                        <div class="p-6 hover:bg-gray-50/50 transition flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5
                                    {{ $tahap->status === 'disetujui' ? 'bg-emerald-100 text-emerald-700' : ($tahap->status === 'revisi' ? 'bg-rose-100 text-rose-700' : ($tahap->status === 'diajukan' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-600')) }}">
                                    #{{ $tahap->urutan }}
                                </div>
                                <div>
                                    <div class="flex items-center gap-2.5 flex-wrap">
                                        <h4 class="font-bold text-gray-900 text-sm">{{ $tahap->nama_tahap }}</h4>
                                        @if ($tahap->status === 'belum_upload')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">Siswa Belum Mengunggah Dokumen</span>
                                        @elseif ($tahap->status === 'diajukan')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                                Perlu Diperiksa & Direview
                                            </span>
                                        @elseif ($tahap->status === 'revisi')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Dalam Proses Revisi Siswa
                                            </span>
                                        @elseif ($tahap->status === 'disetujui')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Telah Disetujui
                                            </span>
                                        @endif

                                        @if ($tahap->anotasis_count > 0)
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800">
                                                {{ $tahap->anotasis_count }} Coretan / Catatan Aktif
                                            </span>
                                        @endif
                                    </div>

                                    @if ($tahap->deskripsi)
                                        <p class="text-xs text-gray-500 mt-1">{{ $tahap->deskripsi }}</p>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mt-2">
                                        @if ($tahap->file_path)
                                            <span class="flex items-center gap-1 text-gray-600">
                                                <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"/></svg>
                                                {{ $tahap->file_nama_asli ?? 'Dokumen PDF' }}
                                            </span>
                                            <span>Ukuran: {{ $tahap->file_size_formatted }}</span>
                                            <span>Unggah: {{ $tahap->diunggah_at?->translatedFormat('d M Y H:i') }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Siswa belum mengunggah dokumen pada bab ini</span>
                                        @endif
                                    </div>

                                    @if ($tahap->catatan_siswa)
                                        <div class="mt-2.5 p-2.5 rounded-xl bg-gray-50 border border-gray-100 text-xs text-gray-600">
                                            <span class="font-bold text-gray-700">Catatan Siswa:</span> "{{ $tahap->catatan_siswa }}"
                                        </div>
                                    @endif

                                    @if ($tahap->catatan_pembimbing)
                                        <div class="mt-2.5 p-2.5 rounded-xl bg-rose-50/70 border border-rose-100 text-xs text-rose-900">
                                            <span class="font-bold text-rose-800">Catatan Review Anda:</span> {{ $tahap->catatan_pembimbing }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex flex-wrap items-center gap-2 lg:flex-nowrap">
                                @if ($tahap->file_path)
                                    <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.annotator', $tahap) }}"
                                        class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold rounded-xl {{ $tahap->status === 'disetujui' ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-rose-600 text-white hover:bg-rose-700 shadow-md shadow-rose-200' }} transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                        {{ $tahap->status === 'disetujui' ? 'Lihat Coretan Dokumen' : 'Periksa & Buat Coretan Revisi' }}
                                    </a>

                                    @if ($tahap->status === 'disetujui' || $tahap->nomor_berita_acara)
                                        <a href="{{ route('pembimbing-prakerin.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                            Berita Acara (PDF)
                                        </a>
                                    @endif
                                @endif

                                @if ($tahap->status !== 'disetujui' && $laporan->tahaps->count() > 1)
                                    <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.hapus-tahap', $tahap) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahap bimbingan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-gray-400 hover:text-rose-600 rounded-xl hover:bg-rose-50 transition" title="Hapus Tahap">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    @empty
                        <div class="p-12 text-center text-gray-400 text-sm">
                            Belum ada tahapan bab bimbingan.
                        </div>
                    @endforelse
                </div>
            </div>
        @endif

        {{-- RIWAYAT AKTIVITAS LOG --}}
        @if ($laporan->aktivitasLogs->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-gray-50 text-gray-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Riwayat Aktivitas & Audit Trail</h3>
                        <p class="text-xs text-gray-500">Log kronologis kegiatan pengajuan, review, dan koreksi bimbingan</p>
                    </div>
                </div>

                <div class="relative pl-6 space-y-6 before:absolute before:left-2.5 before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                    @foreach ($laporan->aktivitasLogs as $log)
                        <div class="relative flex items-start gap-3">
                            <div class="absolute -left-6 top-1 w-3.5 h-3.5 rounded-full border-2 border-white 
                                {{ $log->role === 'Pembimbing' ? 'bg-rose-500' : 'bg-blue-500' }} shadow-sm"></div>
                            <div class="bg-gray-50 rounded-xl p-3 text-xs w-full border border-gray-100">
                                <div class="flex items-center justify-between gap-2 flex-wrap">
                                    <span class="font-bold text-gray-900">{{ $log->deskripsi }}</span>
                                    <span class="text-gray-400 text-[11px]">{{ $log->created_at->translatedFormat('d M Y, H:i') }}</span>
                                </div>
                                <div class="text-gray-500 mt-1 flex items-center gap-2">
                                    <span class="font-semibold text-gray-700">{{ $log->user?->name ?? 'Pengguna' }}</span>
                                    <span>•</span>
                                    <span class="px-1.5 py-0.2 rounded text-[10px] font-bold {{ $log->role === 'Pembimbing' ? 'bg-rose-100 text-rose-700' : 'bg-blue-100 text-blue-700' }}">
                                        {{ $log->role }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- MODAL SETUJUI JUDUL --}}
        <div x-show="showSetujuiJudulModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showSetujuiJudulModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h4 class="font-bold text-gray-900 text-base">Setujui Judul Laporan</h4>
                    <button type="button" @click="showSetujuiJudulModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.review-judul', $laporan) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="action" value="setuju">

                    <p class="text-xs text-gray-600 leading-relaxed">
                        Anda akan menyetujui judul laporan <strong class="text-gray-900">"{{ $laporan->judul }}"</strong>. Setelah disetujui, siswa dapat mulai menyusun dan mengunggah dokumen bab laporan prakerin.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Catatan atau Arahan Pembimbing (Opsional)
                        </label>
                        <textarea name="catatan" rows="3" maxlength="1000"
                            placeholder="Tuliskan arahan materi atau penekanan yang perlu diperhatikan siswa dalam penyusunan..."
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showSetujuiJudulModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-200 transition">
                            Konfirmasi Persetujuan Judul
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TOLAK JUDUL --}}
        <div x-show="showTolakJudulModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showTolakJudulModal = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h4 class="font-bold text-gray-900 text-base">Tolak Pengajuan Judul</h4>
                    <button type="button" @click="showTolakJudulModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.review-judul', $laporan) }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="action" value="tolak">

                    <p class="text-xs text-rose-800 bg-rose-50 p-3 rounded-xl border border-rose-200">
                        <span class="font-bold">Wajib mencantumkan alasan / catatan penolakan:</span>
                        Catatan ini akan dikirimkan langsung ke siswa sebagai panduan perbaikan judul laporan.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Alasan Penolakan & Catatan Revisi <span class="text-rose-500">*</span>
                        </label>
                        <textarea name="catatan" rows="4" required maxlength="1000"
                            placeholder="Contoh: Judul terlalu umum. Silakan dipersempit menjadi konfigurasi router Mikrotik pada divisi NOC sesuai kegiatan PKL Anda."
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showTolakJudulModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-200 transition">
                            Kirim Penolakan & Catatan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL TAMBAH TAHAP --}}
        <div x-show="showTambahTahap" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showTambahTahap = false" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <h4 class="font-bold text-gray-900 text-base">Tambah Bab / Tahap Bimbingan</h4>
                    <button type="button" @click="showTambahTahap = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.tambah-tahap', $laporan) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nama Tahap / Bab <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_tahap" required maxlength="100"
                            placeholder="Contoh: Bab 4 - Hasil Pengujian"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Deskripsi Singkat (Opsional)
                        </label>
                        <textarea name="deskripsi" rows="2" maxlength="255"
                            placeholder="Keterangan materi pokok yang harus disusun siswa..."
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showTambahTahap = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-200 transition">
                            Simpan Tahap
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- MODAL ACC FINAL LAPORAN --}}
        <div x-show="showAccFinalModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showAccFinalModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-emerald-100 text-emerald-700 rounded-xl">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h4 class="font-bold text-gray-900 text-base">ACC Laporan Prakerin Final</h4>
                    </div>
                    <button type="button" @click="showAccFinalModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('pembimbing-prakerin.bimbingan-laporan.acc-final', $laporan) }}" class="space-y-4">
                    @csrf
                    <p class="text-xs text-gray-600 leading-relaxed">
                        Seluruh tahapan bab laporan prakerin siswa telah disetujui. Tindakan ini akan <strong>mengesahkan laporan prakerin secara final</strong> dan menandai proses bimbingan telah selesai.
                    </p>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Catatan Rekomendasi / Kesimpulan Akhir (Opsional)
                        </label>
                        <textarea name="catatan_final" rows="3" maxlength="1000"
                            placeholder="Tuliskan catatan apresiasi atau evaluasi umum untuk bekal siswa..."
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            PIN Tanda Tangan Digital <span class="text-rose-500">*</span>
                        </label>
                        <input type="password" name="pin" required maxlength="10" placeholder="Masukkan 6 digit PIN TTD Digital Anda"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                        <p class="text-[11px] text-gray-400 mt-1">Dibutuhkan untuk validasi tanda tangan digital resmi pada rekap riwayat bimbingan.</p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showAccFinalModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-xl shadow-md shadow-emerald-200 transition">
                            Sahkan ACC Final
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
