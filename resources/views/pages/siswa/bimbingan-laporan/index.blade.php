<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-rose-50 text-rose-600 rounded-2xl shadow-sm border border-rose-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Bimbingan Laporan Prakerin</h2>
                    <p class="text-xs text-gray-500">Alur pengajuan judul, penyusunan bab, koreksi interaktif dokumen, dan berita acara digital</p>
                </div>
            </div>

            @if ($laporan && $laporan->judul_status === 'disetujui')
                <div class="flex items-center gap-2">
                    <a href="{{ route('siswa.bimbingan-laporan.riwayat-pdf') }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 shadow-sm transition">
                        <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Unduh Lembar Riwayat (PDF)
                    </a>
                </div>
            @endif
        </div>
    </x-slot>

    <div class="py-6 space-y-6" x-data="{
        showTambahTahap: false,
        showUploadModal: false,
        selectedTahap: null,
        openUpload(tahap) {
            this.selectedTahap = tahap;
            this.showUploadModal = true;
        }
    }">
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
        @if ($errors->any())
            <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 shadow-sm">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Terjadi kesalahan input:
                </div>
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Info Card Siswa & Penempatan --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 items-center">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-rose-500 to-red-600 text-white flex items-center justify-center font-bold text-lg shadow-md shadow-rose-200">
                        {{ strtoupper(substr($penempatan->siswa?->nama_lengkap ?? 'S', 0, 1)) }}
                    </div>
                    <div>
                        <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Siswa Prakerin</div>
                        <div class="font-bold text-gray-900 text-base leading-tight">{{ $penempatan->siswa?->nama_lengkap }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">NIS: {{ $penempatan->siswa?->nis ?? '-' }} • Kelas: {{ $penempatan->siswa?->kelas_saat_ini ?? '-' }}</div>
                    </div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Industri Mitra PKL</div>
                    <div class="font-bold text-gray-900 text-sm mt-0.5">{{ $penempatan->industri?->nama_industri ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Rombel: {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Pembimbing Internal</div>
                    <div class="font-bold text-gray-900 text-sm mt-0.5">{{ ($pembimbing ?? null)?->nama ?? $penempatan->guruPembimbing?->nama_lengkap ?? '-' }}</div>
                    <div class="text-xs text-gray-500 mt-0.5">Guru Pembimbing Sekolah</div>
                </div>

                <div class="border-t md:border-t-0 md:border-l border-gray-100 md:pl-6">
                    <div class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status Bimbingan</div>
                    <div class="mt-1">
                        @if (!$laporan || $laporan->judul_status === 'draft')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-700">Belum Mengajukan Judul</span>
                        @elseif ($laporan->judul_status === 'diajukan')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                                <span class="relative flex h-2 w-2">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                                </span>
                                Proses Persetujuan Judul
                            </span>
                        @elseif ($laporan->judul_status === 'ditolak')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-rose-100 text-rose-800">Judul Perlu Revisi</span>
                        @elseif ($laporan->status === 'selesai')
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-800">Laporan Telah di-ACC (Selesai)</span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">Proses Bimbingan Bab</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- TAHAP 1: PENGAJUAN / REVIEW JUDUL --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl {{ ($laporan && $laporan->judul_status === 'disetujui') ? 'bg-emerald-50 text-emerald-600' : 'bg-rose-50 text-rose-600' }} flex items-center justify-center font-bold text-sm">
                        1
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-base">Tahap 1: Pengajuan Judul Laporan Prakerin</h3>
                        <p class="text-xs text-gray-500">Siswa wajib mengajukan judul kepada guru pembimbing internal dan menunggu persetujuan.</p>
                    </div>
                </div>

                @if ($laporan && $laporan->judul_status === 'disetujui')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        Judul Disetujui
                    </span>
                @elseif ($laporan && $laporan->judul_status === 'diajukan')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-amber-100 text-amber-800">
                        <span class="relative flex h-2 w-2">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                            <span class="relative inline-flex rounded-full h-2 w-2 bg-amber-500"></span>
                        </span>
                        Proses Persetujuan Pembimbing
                    </span>
                @elseif ($laporan && $laporan->judul_status === 'ditolak')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                        <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        Judul Ditolak (Perlu Perbaikan)
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                        Belum Diajukan
                    </span>
                @endif
            </div>

            <div class="p-6">
                @if ($laporan && $laporan->judul_status === 'disetujui')
                    {{-- Judul Disetujui View --}}
                    <div class="bg-gradient-to-r from-emerald-50/70 to-teal-50/50 border border-emerald-200 rounded-2xl p-6">
                        <div class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Judul Laporan yang Telah Disetujui</div>
                        <h4 class="font-extrabold text-gray-900 text-lg mt-1">"{{ $laporan->judul }}"</h4>
                        @if ($laporan->abstrak_rencana)
                            <p class="text-xs text-gray-600 mt-2 leading-relaxed bg-white/70 p-3 rounded-xl border border-emerald-100">
                                <span class="font-semibold text-gray-700">Ringkasan/Rencana:</span> {{ $laporan->abstrak_rencana }}
                            </p>
                        @endif
                        <div class="flex flex-wrap items-center gap-4 mt-4 text-xs text-gray-500">
                            <span class="flex items-center gap-1">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Disetujui pada: {{ $laporan->judul_disetujui_at ? \Carbon\Carbon::parse($laporan->judul_disetujui_at)->translatedFormat('d F Y H:i') : '-' }}
                            </span>
                            @if ($laporan->catatan_pembimbing)
                                <span class="flex items-center gap-1 text-emerald-700 font-medium">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/></svg>
                                    Catatan Pembimbing: {{ $laporan->catatan_pembimbing }}
                                </span>
                            @endif
                        </div>
                    </div>
                @elseif ($laporan && $laporan->judul_status === 'diajukan')
                    {{-- Tampilan Saat Judul Sedang Dalam Proses Persetujuan Pembimbing --}}
                    <div class="bg-gradient-to-r from-amber-50/80 via-orange-50/40 to-yellow-50/30 border border-amber-200 rounded-2xl p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                            <span class="text-xs font-black text-amber-800 uppercase tracking-wider flex items-center gap-2">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                </span>
                                Judul Sedang Dalam Proses Persetujuan
                            </span>
                            <span class="text-[11px] font-semibold text-amber-700 bg-amber-100/90 px-3 py-1 rounded-full w-fit">
                                Diajukan pada: {{ $laporan->judul_diajukan_at ? \Carbon\Carbon::parse($laporan->judul_diajukan_at)->translatedFormat('d F Y H:i') : '-' }}
                            </span>
                        </div>

                        <div class="bg-white/90 p-4 rounded-xl border border-amber-200/70 shadow-xs">
                            <div class="text-[11px] font-bold text-gray-500 uppercase tracking-wider mb-1">Judul Laporan yang Diajukan:</div>
                            <h4 class="font-black text-gray-900 text-lg leading-snug">"{{ $laporan->judul }}"</h4>
                            @if ($laporan->abstrak_rencana)
                                <div class="mt-2.5 pt-2.5 border-t border-gray-100 text-xs text-gray-600 leading-relaxed">
                                    <span class="font-bold text-gray-700">Ringkasan / Rencana Penulisan:</span>
                                    <p class="mt-0.5">{{ $laporan->abstrak_rencana }}</p>
                                </div>
                            @endif
                        </div>

                        <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs">
                            <div class="flex items-center gap-2 text-gray-600">
                                <svg class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                <span>Guru Pembimbing Internal: <strong class="text-gray-900">{{ $penempatan->guruPembimbing?->nama ?? 'Guru Pembimbing' }}</strong></span>
                            </div>

                            <button type="button" disabled
                                class="cursor-not-allowed inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gray-100 border border-gray-200 text-gray-400 font-bold text-xs shadow-none">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                Judul Sedang Ditinjau Pembimbing
                            </button>
                        </div>

                        <div class="mt-3.5 p-3 rounded-xl bg-amber-100/60 border border-amber-200/50 flex items-start gap-2.5 text-xs text-amber-900">
                            <svg class="w-4 h-4 text-amber-700 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Judul Anda telah masuk ke sistem antrean guru pembimbing. Form penyusunan dan pengunggahan bab laporan akan terbuka otomatis setelah judul disetujui.</span>
                        </div>
                    </div>
                @else
                    {{-- Form Ajukan / Perbaiki Judul (Saat Draft atau Ditolak) --}}
                    @if ($laporan && $laporan->judul_status === 'ditolak')
                        <div class="mb-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800">
                            <div class="flex items-start gap-3">
                                <div class="p-1 bg-rose-100 rounded-lg text-rose-600 mt-0.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                </div>
                                <div class="text-xs">
                                    <span class="font-bold text-rose-900 block text-sm">Alasan / Catatan Penolakan dari Pembimbing:</span>
                                    <p class="mt-1 font-medium leading-relaxed">{{ $laporan->catatan_pembimbing ?: 'Silakan revisi judul laporan agar lebih spesifik sesuai pekerjaan teknis di industri.' }}</p>
                                    <p class="mt-2 text-rose-700">Silakan perbaiki judul dan kirimkan kembali melalui formulir di bawah ini.</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('siswa.bimbingan-laporan.ajukan-judul') }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Judul Laporan Prakerin <span class="text-rose-500">*</span>
                            </label>
                            <input type="text" name="judul" required maxlength="255"
                                value="{{ old('judul', $laporan?->judul) }}"
                                placeholder="Contoh: Implementasi Sistem Manajemen Jaringan Fiber Optik pada PT Telkom Akses Lampung"
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                            <p class="text-[11px] text-gray-400 mt-1">Tulis judul secara spesifik mencerminkan kegiatan dan kompetensi yang dipelajari selama prakerin.</p>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                                Ringkasan / Rencana Penulisan Laporan (Opsional)
                            </label>
                            <textarea name="abstrak_rencana" rows="3" maxlength="1500"
                                placeholder="Jelaskan secara singkat topik bahasan utama, studi kasus, atau modul teknologi yang diangkat dalam laporan..."
                                class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">{{ old('abstrak_rencana', $laporan?->abstrak_rencana) }}</textarea>
                        </div>

                        <div class="flex items-center justify-end pt-2">
                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 shadow-md shadow-rose-200 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                {{ ($laporan && $laporan->judul_status === 'ditolak') ? 'Kirim Ulang Revisi Judul' : 'Ajukan Judul ke Pembimbing' }}
                            </button>
                        </div>
                    </form>
                @endif
            </div>
        </div>

        {{-- TAHAP 2: PROGRES & PENYUSUNAN BAB LAPORAN (Hanya Aktif Setelah Judul Disetujui) --}}
        @if ($laporan && $laporan->judul_status === 'disetujui')
            {{-- Banner ACC Final jika sudah selesai --}}
            @if ($laporan->status === 'selesai')
                <div class="rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 text-white p-6 shadow-lg shadow-emerald-200/50">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center text-white">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-black bg-white/20 text-white uppercase tracking-wider mb-1">
                                    Laporan Prakerin Selesai
                                </span>
                                <h3 class="font-extrabold text-xl leading-tight">Selamat! Laporan Prakerin Anda Telah di-ACC Final</h3>
                                <p class="text-xs text-white/80 mt-1">Semua tahapan bimbingan telah dinyatakan lengkap dan disetujui oleh guru pembimbing internal.</p>
                            </div>
                        </div>
                        <a href="{{ route('siswa.bimbingan-laporan.riwayat-pdf') }}" target="_blank"
                            class="px-5 py-2.5 rounded-xl bg-white text-emerald-800 font-bold text-xs hover:bg-emerald-50 shadow-md transition flex items-center justify-center gap-2">
                            <svg class="w-4 h-4 text-emerald-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Cetak Rekap Riwayat Bimbingan (PDF)
                        </a>
                    </div>
                </div>
            @endif

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                {{-- Header Progres Bimbingan --}}
                <div class="p-6 border-b border-gray-100">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold text-sm">
                                2
                            </div>
                            <div>
                                <h3 class="font-bold text-gray-900 text-base">Tahap 2: Bimbingan Bab Dokumen Laporan</h3>
                                <p class="text-xs text-gray-500">Unggah dokumen laporan dalam format PDF. Guru pembimbing akan memeriksa dan mencoret dokumen interaktif.</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <button type="button" @click="showTambahTahap = true"
                                class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-bold text-rose-600 bg-rose-50 border border-rose-200 rounded-xl hover:bg-rose-100 transition shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                Tambah Bab / Tahap
                            </button>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="font-bold text-gray-700">Progres Kelengkapan Bimbingan</span>
                            <span class="font-extrabold text-rose-600">{{ $laporan->persentase_selesai }}% Selesai ({{ $laporan->tahaps->where('status', 'disetujui')->count() }} dari {{ $laporan->tahaps->count() }} Tahap)</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-3 overflow-hidden p-0.5">
                            <div class="bg-gradient-to-r from-rose-500 to-emerald-500 h-2 rounded-full transition-all duration-500"
                                style="width: {{ $laporan->persentase_selesai }}%"></div>
                        </div>
                    </div>
                </div>

                {{-- Daftar Tahap Table / Cards --}}
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
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-gray-100 text-gray-600">Belum Unggah Dokumen</span>
                                        @elseif ($tahap->status === 'diajukan')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-blue-100 text-blue-700 flex items-center gap-1">
                                                <span class="w-1.5 h-1.5 rounded-full bg-blue-600 animate-pulse"></span>
                                                Menunggu Review
                                            </span>
                                        @elseif ($tahap->status === 'revisi')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-rose-100 text-rose-700 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                                Perlu Revisi
                                            </span>
                                        @elseif ($tahap->status === 'disetujui')
                                            <span class="px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-100 text-emerald-700 flex items-center gap-1">
                                                <svg class="w-3 h-3 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                Disetujui
                                            </span>
                                        @endif

                                        @if ($tahap->anotasis_count > 0)
                                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold bg-amber-100 text-amber-800">
                                                {{ $tahap->anotasis_count }} Catatan / Coretan Revisi
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
                                            <span>Unggah: {{ $tahap->diunggah_at?->diffForHumans() }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Belum ada file PDF yang diunggah</span>
                                        @endif
                                    </div>

                                    {{-- Catatan Review Terakhir --}}
                                    @if ($tahap->catatan_pembimbing)
                                        <div class="mt-3 p-3 rounded-xl bg-rose-50/70 border border-rose-100 text-xs text-rose-900 leading-relaxed">
                                            <span class="font-bold text-rose-800 block mb-0.5">Catatan Review Pembimbing:</span>
                                            {{ $tahap->catatan_pembimbing }}
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Action Buttons --}}
                            <div class="flex flex-wrap items-center gap-2 lg:flex-nowrap">
                                {{-- Tombol Upload / Perbarui Dokumen --}}
                                @if ($tahap->status !== 'disetujui')
                                    <button type="button" @click="openUpload({{ json_encode($tahap) }})"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl {{ $tahap->file_path ? 'bg-gray-100 text-gray-700 hover:bg-gray-200' : 'bg-rose-600 text-white hover:bg-rose-700 shadow-sm' }} transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                        {{ $tahap->file_path ? 'Unggah Versi Baru' : 'Unggah PDF' }}
                                    </button>
                                @endif

                                {{-- Tombol Viewer Review & Coretan --}}
                                @if ($tahap->file_path)
                                    <a href="{{ route('siswa.bimbingan-laporan.viewer', $tahap) }}"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                        Lihat Review & Coretan
                                    </a>
                                @endif

                                {{-- Tombol Berita Acara (jika disetujui / ada review) --}}
                                @if ($tahap->status === 'disetujui' || $tahap->nomor_berita_acara)
                                    <a href="{{ route('siswa.bimbingan-laporan.berita-acara', $tahap) }}" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-bold rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition">
                                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                        Berita Acara (PDF)
                                    </a>
                                @endif

                                {{-- Tombol Hapus Tahap Kustom (hanya jika belum disetujui) --}}
                                @if ($tahap->status !== 'disetujui' && $laporan->tahaps->count() > 1)
                                    <form method="POST" action="{{ route('siswa.bimbingan-laporan.hapus-tahap', $tahap) }}"
                                        onsubmit="return confirm('Apakah Anda yakin ingin menghapus tahapan bimbingan ini?')">
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
                            Belum ada tahapan bimbingan yang terdaftar. Silakan tambahkan tahapan bimbingan baru.
                        </div>
                    @endforelse
                </div>
            </div>
        @else
            {{-- Lock notice jika judul belum disetujui --}}
            <div class="bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl p-10 text-center">
                <div class="w-12 h-12 rounded-2xl bg-gray-200 text-gray-400 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                </div>
                <h4 class="font-bold text-gray-700 text-base">Tahap 2: Bimbingan Bab Dokumen Terkunci</h4>
                <p class="text-xs text-gray-500 max-w-md mx-auto mt-1">
                    Anda belum dapat mengunggah bab laporan prakerin. Tunggu hingga guru pembimbing internal menyetujui pengajuan judul laporan Anda pada Tahap 1 di atas.
                </p>
            </div>
        @endif

        {{-- RIWAYAT AKTIVITAS BIMBINGAN (AUDIT TRAIL) --}}
        @if ($laporan && $laporan->aktivitasLogs->isNotEmpty())
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <div class="flex items-center gap-3 mb-6">
                    <div class="p-2 bg-gray-50 text-gray-600 rounded-xl">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900 text-sm">Riwayat Aktivitas Bimbingan</h3>
                        <p class="text-xs text-gray-500">Catatan audit resmi setiap perubahan status dan aktivitas bimbingan</p>
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

                <form method="POST" action="{{ route('siswa.bimbingan-laporan.tambah-tahap') }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Nama Tahap / Bab <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_tahap" required maxlength="100"
                            placeholder="Contoh: Bab 4 - Pengujian Sistem"
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Deskripsi Singkat (Opsional)
                        </label>
                        <textarea name="deskripsi" rows="2" maxlength="255"
                            placeholder="Keterangan materi isi bab..."
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

        {{-- MODAL UPLOAD DOKUMEN PDF --}}
        <div x-show="showUploadModal" style="display: none;"
            class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
            <div @click.away="showUploadModal = false" class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-100">
                    <div>
                        <h4 class="font-bold text-gray-900 text-base">Unggah Dokumen Laporan (PDF)</h4>
                        <p class="text-xs text-gray-500" x-text="selectedTahap ? selectedTahap.nama_tahap : ''"></p>
                    </div>
                    <button type="button" @click="showUploadModal = false" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="selectedTahap ? '{{ url('siswa/bimbingan-laporan/tahap') }}/' + selectedTahap.id + '/upload' : '#'"
                    method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Pilih Dokumen PDF <span class="text-rose-500">* (Wajib Format .PDF, Maks. 20MB)</span>
                        </label>
                        <input type="file" name="file_dokumen" accept="application/pdf" required
                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100 border border-gray-300 rounded-xl cursor-pointer shadow-sm">
                        <p class="text-[11px] text-gray-400 mt-1">Pastikan file dalam format PDF asli untuk kemudahan coretan review guru.</p>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">
                            Catatan untuk Pembimbing (Opsional)
                        </label>
                        <textarea name="catatan_siswa" rows="2" maxlength="500"
                            placeholder="Tuliskan catatan perbaikan atau bagian penting yang ingin dikonsultasikan..."
                            class="w-full text-sm rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition"></textarea>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                        <button type="button" @click="showUploadModal = false"
                            class="px-4 py-2 text-xs font-semibold text-gray-600 hover:bg-gray-100 rounded-xl transition">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2 text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-md shadow-rose-200 transition">
                            Ajukan Dokumen
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
