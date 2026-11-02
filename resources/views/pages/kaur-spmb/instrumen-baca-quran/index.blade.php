<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Instrumen Tes Baca Al-Qur'an</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="instrumenBacaQuranPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ACTION TOOLBAR (OUTSIDE HEADER) --}}
            <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h3 class="text-sm font-bold text-slate-900">FORM TEST BACA AL-QUR'AN CASIS SMK TELKOM LAMPUNG</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Pengelolaan Rubrik, Kriteria Penilaian, Rekapitulasi & Rekaman Suara Baca Al-Qur'an Calon Siswa Baru</p>
                </div>
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" @click="showSimulasiModal = true"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-800 border border-emerald-200 text-xs font-bold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Preview Formulir Penguji
                    </button>
                    <button type="button" @click="openAddModal()" 
                        class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        + Tambah Kriteria Baru
                    </button>
                </div>
            </div>

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
                {{-- Total Butir --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Rubrik</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalRubrik) }} <span class="text-xs font-normal text-slate-500">aspek</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Instrumen Google Form</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                </div>

                {{-- Aspek Penilaian --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Aspek Utama</p>
                        <h3 class="text-2xl font-bold text-teal-600 mt-1">{{ number_format($totalAspekPenilaian) }} <span class="text-xs font-normal text-slate-500">komponen</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Tajwid, Makhraj, Kelancaran</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-teal-50 text-teal-600 flex items-center justify-center border border-teal-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z" />
                        </svg>
                    </div>
                </div>

                {{-- Media Rekaman --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Eviden Rekaman</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-1">Audio / Video</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">MP3, M4A, WAV, MP4</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                        </svg>
                    </div>
                </div>

                {{-- Status Aktif --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Status Instrumen</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalAktif) }} / {{ number_format($totalRubrik) }}</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Aktif di formulir penguji</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- INFO CALLOUT BOX --}}
            <div class="bg-gradient-to-r from-emerald-700 to-teal-800 text-white p-4 rounded-lg shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5 border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white">Form Test Baca Al-Qur'an Calon Siswa Baru SMK Telkom Lampung</h4>
                        <p class="text-xs text-emerald-100 mt-0.5 leading-relaxed">
                            Instrumen resmi yang diadopsi dari Google Form seleksi penerimaan murid baru untuk menguji kemampuan membaca Al-Qur'an. Mencakup rubrik <strong>Tajwid</strong>, <strong>Makharijul Huruf</strong>, <strong>Kelancaran</strong>, <strong>Rekapitulasi Hasil</strong>, <strong>Unggah Rekaman Suara</strong>, dan <strong>Catatan Penguji</strong>.
                        </p>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-white text-emerald-800 text-xs font-bold shadow-sm">
                        Standardisasi Standar YPT
                    </span>
                </div>
            </div>

            {{-- RUBRIK CARDS LIST --}}
            <div class="space-y-4">
                @forelse($rubriks as $rubrik)
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm p-5 hover:shadow-md transition-shadow">
                        <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                            <div class="flex items-start gap-3.5">
                                <span class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-800 font-bold text-sm flex items-center justify-center flex-shrink-0 mt-0.5">
                                    {{ $rubrik->nomor_urut }}
                                </span>
                                <div class="space-y-1.5">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="text-base font-bold text-slate-900 tracking-wide uppercase">{{ $rubrik->aspek }}</h3>
                                        @if($rubrik->is_required)
                                            <span class="text-red-500 font-black text-sm" title="Wajib Diisi">*</span>
                                        @endif
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider
                                            {{ $rubrik->kategori === 'penilaian' ? 'bg-teal-50 text-teal-700 border border-teal-200' :
                                               ($rubrik->kategori === 'rekapitulasi' ? 'bg-blue-50 text-blue-700 border border-blue-200' :
                                               ($rubrik->kategori === 'media' ? 'bg-purple-50 text-purple-700 border border-purple-200' : 'bg-slate-100 text-slate-700 border border-slate-300')) }}">
                                            {{ $rubrik->kategori }}
                                        </span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $rubrik->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                            {{ $rubrik->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                    </div>

                                    @if($rubrik->petunjuk)
                                        <p class="text-xs text-slate-500 italic">{{ $rubrik->petunjuk }}</p>
                                    @endif

                                    {{-- DETAIL PILIHAN OPSI ATAU TIPE INPUT --}}
                                    <div class="pt-2">
                                        @if(in_array($rubrik->tipe_input, ['radio', 'select']) && !empty($rubrik->pilihan_opsi))
                                            <div class="flex flex-wrap items-center gap-2">
                                                @foreach($rubrik->pilihan_opsi as $opt)
                                                    @php
                                                        $bobotNilai = $rubrik->skor_bobot[$opt] ?? null;
                                                    @endphp
                                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-xs font-semibold bg-slate-50 border border-slate-300 text-slate-800">
                                                        <span class="w-1.5 h-1.5 rounded-full {{ $opt === 'SANGAT BAIK' ? 'bg-emerald-500' : ($opt === 'BAIK' ? 'bg-blue-500' : ($opt === 'CUKUP' ? 'bg-amber-500' : 'bg-red-500')) }}"></span>
                                                        {{ $opt }}
                                                        @if($bobotNilai !== null)
                                                            <span class="text-[10px] text-slate-500 font-normal">({{ $bobotNilai }} pt)</span>
                                                        @endif
                                                    </span>
                                                @endforeach
                                            </div>
                                        @elseif($rubrik->tipe_input === 'file_upload')
                                            <div class="flex items-center gap-2 p-2 rounded-lg bg-purple-50/60 border border-purple-200 text-xs text-purple-900 max-w-md">
                                                <svg class="w-4 h-4 text-purple-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11a7 7 0 01-7 7m0 0a7 7 0 01-7-7m7 7v4m0 0H8m4 0h4m-4-8a3 3 0 100-6 3 3 0 000 6z" />
                                                </svg>
                                                <span>Input Pengunggahan Berkas Audio / Suara Tes Baca Al-Qur'an (MP3 / WAV / M4A / MP4)</span>
                                            </div>
                                        @elseif($rubrik->tipe_input === 'textarea')
                                            <div class="flex items-center gap-2 p-2 rounded-lg bg-slate-50 border border-slate-200 text-xs text-slate-600 max-w-md">
                                                <svg class="w-4 h-4 text-slate-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7" />
                                                </svg>
                                                <span>Area Teks Bebas untuk Catatan Evaluasi & Temuan Khusus Penguji</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- ACTIONS --}}
                            <div class="flex items-center gap-1.5 self-end sm:self-start">
                                {{-- Toggle Active --}}
                                <form action="{{ route('kaur-spmb.administrasi.instrumen-baca-quran.toggle', $rubrik) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="p-1.5 rounded border transition-colors {{ $rubrik->is_active ? 'bg-white hover:bg-slate-100 text-slate-600 border-slate-300' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }}"
                                            title="{{ $rubrik->is_active ? 'Nonaktifkan Aspek' : 'Aktifkan Aspek' }}">
                                        @if($rubrik->is_active)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        @endif
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <button type="button" @click="openEditModal({{ json_encode($rubrik) }})"
                                    class="p-1.5 rounded bg-white hover:bg-slate-100 text-blue-600 border border-slate-300 transition-colors"
                                    title="Edit Kriteria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Delete Button --}}
                                <button type="button" @click="confirmDelete('{{ route('kaur-spmb.administrasi.instrumen-baca-quran.destroy', $rubrik) }}', '{{ $rubrik->aspek }}')"
                                    class="p-1.5 rounded bg-white hover:bg-red-50 text-red-600 border border-slate-300 hover:border-red-300 transition-colors"
                                    title="Hapus Kriteria">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="bg-white rounded-lg border border-slate-200 p-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                        <p class="font-semibold text-slate-600">Belum ada rubrik instrumen tes baca Al-Qur'an.</p>
                        <p class="text-xs text-slate-400 mt-1">Jalankan seeder bawaan atau klik "+ Tambah Kriteria Baru".</p>
                    </div>
                @endforelse
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL TAMBAH KRITERIA                                     --}}
            {{-- ======================================================== --}}
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <h3 class="text-base font-bold text-slate-900">Tambah Aspek Penilaian Baca Al-Qur'an</h3>
                            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <form action="{{ route('kaur-spmb.administrasi.instrumen-baca-quran.store') }}" method="POST" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Aspek / Kriteria</label>
                                <input type="text" name="aspek" placeholder="Contoh: KELANCARAN / TAJWID" required
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                                    <select name="kategori" required class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="penilaian">Penilaian Aspek</option>
                                        <option value="rekapitulasi">Rekapitulasi Hasil</option>
                                        <option value="media">Unggah Media / Rekaman</option>
                                        <option value="catatan">Catatan / Uraian</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Input</label>
                                    <select name="tipe_input" x-model="addTipeInput" required class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="radio">Pilihan Radio</option>
                                        <option value="select">Dropdown Select</option>
                                        <option value="file_upload">Upload Berkas (Audio)</option>
                                        <option value="textarea">Uraian Teks</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petunjuk Pengisian</label>
                                <textarea name="petunjuk" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" placeholder="Silahkan pilih sesuai dengan hasil tes baca Al-Qur'an..."></textarea>
                            </div>

                            {{-- Pilihan Opsi Jika Tipe Radio / Select --}}
                            <div x-show="['radio', 'select'].includes(addTipeInput)" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Pilihan Opsi & Bobot Nilai</label>
                                    <button type="button" @click="addOptionRow()" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800">+ Tambah Opsi</button>
                                </div>

                                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                    <template x-for="(opt, idx) in addOptions" :key="idx">
                                        <div class="flex items-center gap-2">
                                            <input type="text" :name="'opsi[' + idx + ']'" x-model="opt.label" placeholder="Contoh: SANGAT BAIK" required
                                                   class="flex-1 text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                            <input type="number" :name="'skor[' + idx + ']'" x-model="opt.skor" placeholder="Skor" min="0" max="100"
                                                   class="w-20 text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500" title="Skor Nilai">
                                            <button type="button" @click="removeOptionRow(idx)" class="text-red-500 hover:text-red-700 p-1">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_required" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>Wajib Diisi (Required)</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" checked class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>Aktifkan Aspek Ini</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 mt-6">
                                <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow transition-colors">
                                    Simpan Aspek
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL EDIT KRITERIA                                       --}}
            {{-- ======================================================== --}}
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <h3 class="text-base font-bold text-slate-900">Edit Aspek Penilaian Baca Al-Qur'an</h3>
                            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <form :action="editAction" method="POST" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Nama Aspek / Kriteria</label>
                                <input type="text" name="aspek" x-model="editItem.aspek" required
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                            </div>

                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Kategori</label>
                                    <select name="kategori" x-model="editItem.kategori" required class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="penilaian">Penilaian Aspek</option>
                                        <option value="rekapitulasi">Rekapitulasi Hasil</option>
                                        <option value="media">Unggah Media / Rekaman</option>
                                        <option value="catatan">Catatan / Uraian</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Tipe Input</label>
                                    <select name="tipe_input" x-model="editItem.tipe_input" required class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                        <option value="radio">Pilihan Radio</option>
                                        <option value="select">Dropdown Select</option>
                                        <option value="file_upload">Upload Berkas (Audio)</option>
                                        <option value="textarea">Uraian Teks</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petunjuk Pengisian</label>
                                <textarea name="petunjuk" rows="2" x-model="editItem.petunjuk" class="w-full text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500"></textarea>
                            </div>

                            {{-- Pilihan Opsi Edit --}}
                            <div x-show="['radio', 'select'].includes(editItem.tipe_input)" class="space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider">Daftar Pilihan Opsi & Bobot Nilai</label>
                                    <button type="button" @click="addEditOptionRow()" class="text-[11px] font-bold text-emerald-600 hover:text-emerald-800">+ Tambah Opsi</button>
                                </div>

                                <div class="space-y-1.5 max-h-48 overflow-y-auto">
                                    <template x-for="(opt, idx) in editOptions" :key="idx">
                                        <div class="flex items-center gap-2">
                                            <input type="text" :name="'opsi[' + idx + ']'" x-model="opt.label" required
                                                   class="flex-1 text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                            <input type="number" :name="'skor[' + idx + ']'" x-model="opt.skor" placeholder="Skor" min="0" max="100"
                                                   class="w-20 text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500">
                                            <button type="button" @click="removeEditOptionRow(idx)" class="text-red-500 hover:text-red-700 p-1">&times;</button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-1">
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_required" value="1" :checked="editItem.is_required" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>Wajib Diisi (Required)</span>
                                </label>
                                <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" name="is_active" value="1" :checked="editItem.is_active" class="rounded text-emerald-600 focus:ring-emerald-500">
                                    <span>Aktifkan Aspek Ini</span>
                                </label>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 mt-6">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg shadow transition-colors">
                                    Perbarui Aspek
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL PREVIEW / SIMULASI FORMULIR PENGUJI                 --}}
            {{-- ======================================================== --}}
            <div x-show="showSimulasiModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showSimulasiModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div class="inline-block w-full max-w-2xl p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">FORM TEST BACA AL-QUR'AN CASIS SMK TELKOM LAMPUNG</h3>
                                <p class="text-xs text-slate-500">Simulasi Tampilan Formulir bagi Guru Petugas Piket SPMB</p>
                            </div>
                            <button type="button" @click="showSimulasiModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <div class="mt-4 space-y-5 max-h-[70vh] overflow-y-auto pr-1">
                            @foreach($rubriks as $r)
                                <div class="p-4 rounded-lg border border-slate-200 bg-slate-50/50 space-y-2">
                                    <div class="flex items-center justify-between">
                                        <label class="font-bold text-xs text-slate-900 uppercase">
                                            {{ $r->aspek }}
                                            @if($r->is_required)
                                                <span class="text-red-500">*</span>
                                            @endif
                                        </label>
                                        <span class="text-[10px] font-bold text-slate-400">#{{ $r->nomor_urut }}</span>
                                    </div>

                                    @if($r->petunjuk)
                                        <p class="text-[11px] text-slate-500 italic">{{ $r->petunjuk }}</p>
                                    @endif

                                    <div class="pt-1">
                                        @if($r->tipe_input === 'radio' && !empty($r->pilihan_opsi))
                                            <div class="space-y-1.5">
                                                @foreach($r->pilihan_opsi as $opt)
                                                    <label class="flex items-center gap-2 p-2 rounded bg-white border border-slate-200 text-xs text-slate-800 hover:bg-emerald-50/50 cursor-pointer transition-colors">
                                                        <input type="radio" name="preview_{{ $r->id }}" class="text-emerald-600 focus:ring-emerald-500">
                                                        <span class="font-medium">{{ $opt }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                        @elseif($r->tipe_input === 'file_upload')
                                            <input type="file" disabled class="w-full text-xs text-slate-400 file:mr-2 file:py-1.5 file:px-2.5 file:rounded file:border-0 file:text-xs file:bg-slate-200 file:text-slate-700 border border-slate-300 rounded bg-white">
                                        @elseif($r->tipe_input === 'textarea')
                                            <textarea rows="3" disabled placeholder="Tuliskan catatan hasil pengujian..." class="w-full text-xs rounded border-slate-300 bg-white"></textarea>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="border-t border-slate-200 pt-3 mt-4 text-right">
                            <button type="button" @click="showSimulasiModal = false" class="px-4 py-1.5 text-xs font-bold text-white bg-slate-800 rounded-lg">
                                Tutup Preview
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DELETE CONFIRMATION FORM --}}
            <form id="delete-rubrik-form" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function instrumenBacaQuranPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showSimulasiModal: false,

                // Add state
                addTipeInput: 'radio',
                addOptions: [
                    { label: 'SANGAT BAIK', skor: 100 },
                    { label: 'BAIK', skor: 80 },
                    { label: 'CUKUP', skor: 65 },
                    { label: 'KURANG', skor: 50 },
                ],

                // Edit state
                editItem: {},
                editAction: '',
                editOptions: [],

                openAddModal() {
                    this.addTipeInput = 'radio';
                    this.addOptions = [
                        { label: 'SANGAT BAIK', skor: 100 },
                        { label: 'BAIK', skor: 80 },
                        { label: 'CUKUP', skor: 65 },
                        { label: 'KURANG', skor: 50 },
                    ];
                    this.showAddModal = true;
                },

                addOptionRow() {
                    this.addOptions.push({ label: '', skor: 70 });
                },

                removeOptionRow(idx) {
                    this.addOptions.splice(idx, 1);
                },

                openEditModal(item) {
                    this.editItem = item;
                    this.editAction = '{{ url("/kaur-spmb/administrasi/instrumen-baca-quran") }}/' + item.id;
                    
                    const opsi = Array.isArray(item.pilihan_opsi) ? item.pilihan_opsi : [];
                    const skorMap = item.skor_bobot || {};
                    
                    this.editOptions = opsi.map(opt => ({
                        label: opt,
                        skor: skorMap[opt] !== undefined ? skorMap[opt] : 75
                    }));

                    if (this.editOptions.length === 0 && ['radio', 'select'].includes(item.tipe_input)) {
                        this.editOptions = [{ label: 'BAIK', skor: 100 }, { label: 'CUKUP', skor: 75 }];
                    }

                    this.showEditModal = true;
                },

                addEditOptionRow() {
                    this.editOptions.push({ label: '', skor: 70 });
                },

                removeEditOptionRow(idx) {
                    this.editOptions.splice(idx, 1);
                },

                confirmDelete(url, aspek) {
                    if (confirm('Apakah Anda yakin ingin menghapus aspek penilaian "' + aspek + '" dari instrumen?')) {
                        const form = document.getElementById('delete-rubrik-form');
                        form.action = url;
                        form.submit();
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
