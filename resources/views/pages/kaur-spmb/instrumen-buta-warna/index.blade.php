<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Instrumen Tes Buta Warna</h2>
                <p class="text-xs text-slate-500 mt-0.5">Pengelolaan Soal & Pelat Ishihara Calon Siswa Baru SMK Telkom Lampung</p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="openAddModal()" 
                    class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-lg shadow-sm transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    + Tambah Soal Baru
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full" x-data="instrumenButaWarnaPage()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

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
                {{-- Total Soal --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Total Soal</p>
                        <h3 class="text-2xl font-bold text-slate-800 mt-1">{{ number_format($totalSoal) }} <span class="text-xs font-normal text-slate-500">butir</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Instrumen Tes Buta Warna</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center border border-red-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>

                {{-- Soal Aktif --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Soal Aktif</p>
                        <h3 class="text-2xl font-bold text-emerald-600 mt-1">{{ number_format($totalAktif) }} <span class="text-xs font-normal text-slate-500">butir</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Digunakan saat ujian peserta</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center border border-emerald-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>

                {{-- Tipe Pertanyaan --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Tipe Pertanyaan</p>
                        <h3 class="text-xl font-bold text-slate-800 mt-1">3 Pilihan Ganda</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Standar Pelat Ishihara</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center border border-purple-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </div>
                </div>

                {{-- Total Bobot Nilai --}}
                <div class="bg-white p-4 rounded-lg border border-slate-200 shadow-sm flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider">Akumulasi Bobot</p>
                        <h3 class="text-2xl font-bold text-blue-600 mt-1">{{ number_format($totalBobot) }} <span class="text-xs font-normal text-slate-500">poin</span></h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Skor maksimal ujian</p>
                    </div>
                    <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center border border-blue-100">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                        </svg>
                    </div>
                </div>
            </div>

            {{-- INFO CALLOUT BOX --}}
            <div class="bg-gradient-to-r from-red-600 to-rose-700 text-white p-4 rounded-lg shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center flex-shrink-0 mt-0.5 border border-white/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-sm text-white">Instrumen Tes Buta Warna - Calon Siswa Baru SMK Telkom Lampung</h4>
                        <p class="text-xs text-red-100 mt-0.5 leading-relaxed">
                            Bank soal ini digunakan untuk menguji persepsi visual warna calon siswa. Setiap butir menampilkan pelat angka Ishihara dengan 3 opsi jawaban. Sistem secara otomatis mencatat dan mengevaluasi status penglihatan (*Normal*, *Buta Warna Parsial*, atau *Buta Warna Total*).
                        </p>
                    </div>
                </div>

                <div class="flex-shrink-0">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-md bg-white text-red-700 text-xs font-bold shadow-sm">
                        Total {{ $soals->count() }} Soal Terkonfigurasi
                    </span>
                </div>
            </div>

            {{-- SOAL GRID CARDS --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                @forelse($soals as $soal)
                    <div class="bg-white rounded-lg border border-slate-200 shadow-sm overflow-hidden flex flex-col justify-between hover:shadow-md transition-shadow">
                        <div>
                            {{-- Header Card --}}
                            <div class="p-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <span class="w-6 h-6 rounded-full bg-red-600 text-white font-bold text-xs flex items-center justify-center flex-shrink-0">
                                        {{ $soal->nomor_urut }}
                                    </span>
                                    <span class="font-bold text-xs text-slate-800">{{ $soal->judul }}</span>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold {{ $soal->is_active ? 'bg-emerald-50 text-emerald-700 border border-emerald-200' : 'bg-slate-100 text-slate-500 border border-slate-200' }}">
                                    {{ $soal->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            {{-- Image Plate Thumbnail --}}
                            <div class="p-4 bg-slate-100/50 flex flex-col items-center justify-center border-b border-slate-100 relative group cursor-pointer"
                                 @click="previewImage('{{ $soal->gambar_url }}', '{{ $soal->judul }}')">
                                <img src="{{ $soal->gambar_url }}" alt="{{ $soal->judul }}" 
                                     class="w-32 h-32 object-contain rounded-full shadow-sm bg-white p-1 border border-slate-200 group-hover:scale-105 transition-transform">
                                <div class="absolute inset-0 bg-slate-900/40 rounded flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold flex items-center gap-1 bg-black/60 px-2.5 py-1 rounded">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7" />
                                        </svg>
                                        Perbesar
                                    </span>
                                </div>
                            </div>

                            {{-- Petunjuk & Options --}}
                            <div class="p-3.5 space-y-2.5">
                                <p class="text-[11px] text-slate-500 line-clamp-2 italic" title="{{ $soal->petunjuk }}">
                                    {{ $soal->petunjuk }}
                                </p>

                                <div class="space-y-1.5 pt-1">
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Pilihan Jawaban (3 Opsi):</span>
                                    @foreach(($soal->pilihan_opsi ?: []) as $opsiIdx => $opsi)
                                        @php
                                            $isCorrect = (string) $opsi === (string) $soal->kunci_jawaban;
                                        @endphp
                                        <div class="flex items-center justify-between px-2.5 py-1.5 rounded text-xs border transition-colors {{ $isCorrect ? 'bg-emerald-50 border-emerald-300 text-emerald-900 font-bold' : 'bg-slate-50 border-slate-200 text-slate-700' }}">
                                            <span class="flex items-center gap-1.5 truncate">
                                                <span class="font-mono text-[10px] text-slate-400">{{ chr(65 + $opsiIdx) }}.</span>
                                                <span class="truncate">{{ $opsi }}</span>
                                            </span>
                                            @if($isCorrect)
                                                <span class="flex-shrink-0 text-[10px] font-extrabold text-emerald-700 bg-emerald-100 px-1.5 py-0.2 rounded border border-emerald-300">
                                                    Kunci
                                                </span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action Toolbar --}}
                        <div class="p-3 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-1 text-xs">
                            <span class="font-semibold text-[11px] text-slate-500">Bobot: <strong class="text-slate-800">{{ $soal->bobot }} poin</strong></span>

                            <div class="flex items-center gap-1">
                                {{-- Toggle Active --}}
                                <form action="{{ route('kaur-spmb.administrasi.instrumen-buta-warna.toggle', $soal) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" 
                                            class="p-1.5 rounded border transition-colors {{ $soal->is_active ? 'bg-white hover:bg-slate-100 text-slate-600 border-slate-300' : 'bg-emerald-50 hover:bg-emerald-100 text-emerald-700 border-emerald-200' }}"
                                            title="{{ $soal->is_active ? 'Nonaktifkan Soal' : 'Aktifkan Soal' }}">
                                        @if($soal->is_active)
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" /></svg>
                                        @else
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        @endif
                                    </button>
                                </form>

                                {{-- Edit Button --}}
                                <button type="button" @click="openEditModal({{ json_encode($soal) }}, '{{ $soal->gambar_url }}')"
                                    class="p-1.5 rounded bg-white hover:bg-slate-100 text-blue-600 border border-slate-300 transition-colors"
                                    title="Edit Soal">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </button>

                                {{-- Delete Button --}}
                                <button type="button" @click="confirmDelete('{{ route('kaur-spmb.administrasi.instrumen-buta-warna.destroy', $soal) }}', '{{ $soal->judul }}')"
                                    class="p-1.5 rounded bg-white hover:bg-red-50 text-red-600 border border-slate-300 hover:border-red-300 transition-colors"
                                    title="Hapus Soal">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full bg-white rounded-lg border border-slate-200 p-12 text-center text-slate-400">
                        <svg class="w-12 h-12 mx-auto text-slate-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="font-semibold text-slate-600">Belum ada butir soal tes buta warna.</p>
                        <p class="text-xs text-slate-400 mt-1">Klik tombol "+ Tambah Soal Baru" atau jalankan seeder untuk memuat 10 pelat Ishihara bawaan.</p>
                    </div>
                @endforelse
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL TAMBAH SOAL                                         --}}
            {{-- ======================================================== --}}
            <div x-show="showAddModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showAddModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <h3 class="text-base font-bold text-slate-900">Tambah Butir Soal Tes Buta Warna</h3>
                            <button type="button" @click="showAddModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <form action="{{ route('kaur-spmb.administrasi.instrumen-buta-warna.store') }}" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                            @csrf

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Soal</label>
                                <input type="text" name="judul" value="Soal nomor {{ $soals->count() + 1 }}" required
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petunjuk Pengisian</label>
                                <textarea name="petunjuk" rows="2" class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">Silahkan pilih angka/jawaban yang benar dari 3 (tiga) pilihan jawaban berikut</textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Upload Gambar Pelat Ishihara</label>
                                <input type="file" name="gambar_file" accept="image/*" required @change="previewUploadImage($event, 'add')"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-slate-300 rounded-lg">
                                <template x-if="addImagePreview">
                                    <div class="mt-2 text-center">
                                        <img :src="addImagePreview" class="w-28 h-28 object-contain mx-auto rounded-full border border-slate-300 p-1 bg-slate-50">
                                    </div>
                                </template>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilihan Opsi Jawaban (3 Pilihan) & Kunci Jawaban</label>
                                <p class="text-[11px] text-slate-400 mb-2">Tandai radio button pada opsi yang merupakan jawaban benar.</p>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="0" x-model="addKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">A.</span>
                                        <input type="text" name="opsi[]" x-model="addOpsi[0]" placeholder="Contoh: 2" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="1" x-model="addKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">B.</span>
                                        <input type="text" name="opsi[]" x-model="addOpsi[1]" placeholder="Contoh: 8" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="2" x-model="addKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">C.</span>
                                        <input type="text" name="opsi[]" x-model="addOpsi[2]" placeholder="Contoh: Tidak Terlihat Angka" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                </div>
                                <input type="hidden" name="kunci_jawaban" :value="addOpsi[addKunciIdx]">
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bobot Poin</label>
                                    <input type="number" name="bobot" value="10" min="1" max="100" required
                                           class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div class="flex items-center pt-5">
                                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" checked class="rounded text-red-600 focus:ring-red-500">
                                        <span>Aktifkan Soal Ini</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 mt-6">
                                <button type="button" @click="showAddModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg shadow transition-colors">
                                    Simpan Soal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL EDIT SOAL                                           --}}
            {{-- ======================================================== --}}
            <div x-show="showEditModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div class="fixed inset-0 transition-opacity bg-slate-900/60" @click="showEditModal = false"></div>
                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                    <div class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white rounded-xl shadow-2xl border border-slate-200">
                        <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                            <h3 class="text-base font-bold text-slate-900">Edit Butir Soal Tes Buta Warna</h3>
                            <button type="button" @click="showEditModal = false" class="text-slate-400 hover:text-slate-600">&times;</button>
                        </div>

                        <form :action="editAction" method="POST" enctype="multipart/form-data" class="mt-4 space-y-4">
                            @csrf
                            @method('PUT')

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Judul Soal</label>
                                <input type="text" name="judul" x-model="editItem.judul" required
                                       class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Petunjuk Pengisian</label>
                                <textarea name="petunjuk" rows="2" x-model="editItem.petunjuk" class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500"></textarea>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Ganti Gambar Pelat Ishihara (Opsional)</label>
                                <input type="file" name="gambar_file" accept="image/*" @change="previewUploadImage($event, 'edit')"
                                       class="w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100 border border-slate-300 rounded-lg">
                                <div class="mt-2 text-center">
                                    <img :src="editImagePreview" class="w-28 h-28 object-contain mx-auto rounded-full border border-slate-300 p-1 bg-slate-50">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Pilihan Opsi Jawaban (3 Pilihan) & Kunci Jawaban</label>
                                <p class="text-[11px] text-slate-400 mb-2">Tandai radio button pada opsi yang merupakan kunci jawaban benar.</p>
                                
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="0" x-model="editKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">A.</span>
                                        <input type="text" name="opsi[]" x-model="editOpsi[0]" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="1" x-model="editKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">B.</span>
                                        <input type="text" name="opsi[]" x-model="editOpsi[1]" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="kunci_jawaban_radio" value="2" x-model="editKunciIdx" class="text-red-600 focus:ring-red-500">
                                        <span class="text-xs font-bold text-slate-500 w-5">C.</span>
                                        <input type="text" name="opsi[]" x-model="editOpsi[2]" required
                                               class="flex-1 text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                    </div>
                                </div>
                                <input type="hidden" name="kunci_jawaban" :value="editOpsi[editKunciIdx]">
                            </div>

                            <div class="grid grid-cols-2 gap-3 pt-1">
                                <div>
                                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Bobot Poin</label>
                                    <input type="number" name="bobot" x-model="editItem.bobot" min="1" max="100" required
                                           class="w-full text-xs rounded-lg border-slate-300 focus:border-red-500 focus:ring-red-500">
                                </div>
                                <div class="flex items-center pt-5">
                                    <label class="flex items-center gap-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                        <input type="checkbox" name="is_active" value="1" :checked="editItem.is_active" class="rounded text-red-600 focus:ring-red-500">
                                        <span>Aktifkan Soal Ini</span>
                                    </label>
                                </div>
                            </div>

                            <div class="flex items-center justify-end gap-2 border-t border-slate-200 pt-4 mt-6">
                                <button type="button" @click="showEditModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 hover:text-slate-900 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">
                                    Batal
                                </button>
                                <button type="submit" class="px-5 py-2 text-xs font-bold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow transition-colors">
                                    Perbarui Soal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ======================================================== --}}
            {{-- MODAL ZOOM IMAGE PREVIEW                                  --}}
            {{-- ======================================================== --}}
            <div x-show="showZoomModal" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
                <div class="flex items-center justify-center min-h-screen px-4 text-center">
                    <div class="fixed inset-0 bg-black/75 transition-opacity" @click="showZoomModal = false"></div>
                    <div class="relative bg-white rounded-2xl p-6 max-w-sm w-full mx-auto shadow-2xl z-10 text-center">
                        <h4 class="font-bold text-sm text-slate-900 mb-3" x-text="zoomTitle"></h4>
                        <img :src="zoomImageUrl" alt="Pelat Ishihara" class="w-64 h-64 object-contain mx-auto rounded-full border-4 border-slate-200 shadow-inner bg-slate-50">
                        <div class="mt-4">
                            <button type="button" @click="showZoomModal = false" class="px-4 py-1.5 text-xs font-bold bg-slate-800 text-white rounded-lg">
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- DELETE CONFIRMATION FORM --}}
            <form id="delete-soal-form" method="POST" class="hidden">
                @csrf
                @method('DELETE')
            </form>

        </div>
    </div>

    @push('scripts')
    <script>
        function instrumenButaWarnaPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showZoomModal: false,
                zoomImageUrl: '',
                zoomTitle: '',
                
                // Add modal state
                addImagePreview: null,
                addOpsi: ['', '', ''],
                addKunciIdx: 0,

                // Edit modal state
                editItem: {},
                editAction: '',
                editImagePreview: '',
                editOpsi: ['', '', ''],
                editKunciIdx: 0,

                openAddModal() {
                    this.addImagePreview = null;
                    this.addOpsi = ['', '', ''];
                    this.addKunciIdx = 0;
                    this.showAddModal = true;
                },

                openEditModal(item, imgUrl) {
                    this.editItem = item;
                    this.editAction = '{{ url("/kaur-spmb/administrasi/instrumen-buta-warna") }}/' + item.id;
                    this.editImagePreview = imgUrl;
                    
                    const opsi = Array.isArray(item.pilihan_opsi) ? item.pilihan_opsi : [];
                    this.editOpsi = [opsi[0] || '', opsi[1] || '', opsi[2] || ''];
                    
                    const idx = this.editOpsi.findIndex(o => String(o) === String(item.kunci_jawaban));
                    this.editKunciIdx = idx !== -1 ? idx : 0;

                    this.showEditModal = true;
                },

                previewImage(url, title) {
                    this.zoomImageUrl = url;
                    this.zoomTitle = title;
                    this.showZoomModal = true;
                },

                previewUploadImage(e, mode) {
                    const file = e.target.files[0];
                    if (!file) return;

                    const reader = new FileReader();
                    reader.onload = (event) => {
                        if (mode === 'add') {
                            this.addImagePreview = event.target.result;
                        } else {
                            this.editImagePreview = event.target.result;
                        }
                    };
                    reader.readAsDataURL(file);
                },

                confirmDelete(url, title) {
                    if (confirm('Apakah Anda yakin ingin menghapus ' + title + ' dari instrumen tes buta warna?')) {
                        const form = document.getElementById('delete-soal-form');
                        form.action = url;
                        form.submit();
                    }
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
