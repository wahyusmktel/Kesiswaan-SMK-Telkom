<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Input Izin Meninggalkan Kelas</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-gray-800 text-base">Form Izin Siswa (Input Guru Piket)</h3>
                        <p class="text-xs text-gray-500 mt-0.5">Khusus membantu siswa yang tidak membawa smartphone atau berhalangan input mandiri.</p>
                    </div>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        Auto Disetujui Piket
                    </span>
                </div>

                <form action="{{ route('piket.persetujuan-izin-keluar.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6" x-data="piketIzinForm()">
                    @csrf
                    
                    {{-- Student Search --}}
                    <div>
                        <label for="siswa-search" class="block text-sm font-bold text-gray-700 mb-2">
                            Pilih Nama Siswa / NIS <span class="text-rose-500">*</span>
                        </label>
                        <select id="siswa-search" name="master_siswa_id" placeholder="Ketik nama atau NIS siswa..." required></select>
                        <x-input-error :messages="$errors->get('master_siswa_id')" class="mt-2" />
                    </div>

                    {{-- Pelajaran Saat Ini (Dynamic) --}}
                    <div>
                        <template x-if="loadingSchedule">
                            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 flex items-center gap-3 text-sm text-gray-500 animate-pulse">
                                <svg class="animate-spin h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Memeriksa jadwal pelajaran aktif siswa...</span>
                            </div>
                        </template>

                        <div x-show="currentSchedule && !loadingSchedule" x-cloak class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="h-10 w-10 bg-indigo-600 rounded-full flex items-center justify-center text-white flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-indigo-600 uppercase tracking-wider">Pelajaran Saat Ini</p>
                                    <h4 class="text-gray-900 font-extrabold text-base" x-text="currentSchedule.mapel"></h4>
                                    <p class="text-xs text-gray-600" x-text="`Guru: ${currentSchedule.guru} | Jam Ke-${currentSchedule.jam_ke} (${currentSchedule.waktu})`"></p>
                                </div>
                            </div>
                            <input type="hidden" name="jadwal_pelajaran_id" :value="currentSchedule.id">
                        </div>

                        <div x-show="!currentSchedule && !loadingSchedule && hasSelectedStudent" x-cloak class="bg-amber-50 border border-amber-200 rounded-xl p-3.5 flex items-center gap-3 text-xs text-amber-800">
                            <svg class="w-5 h-5 text-amber-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span>Tidak ada jadwal pelajaran yang sedang berlangsung untuk siswa ini (jam istirahat / jam kosong). Izin tetap dapat dicatat.</span>
                        </div>
                    </div>

                    {{-- Jenis Izin --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">
                            Jenis Izin <span class="text-rose-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-xl border bg-white p-3.5 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'keluar_sekolah' ? 'ring-2 ring-red-500 border-red-500 bg-red-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="keluar_sekolah" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                            </svg>
                                        </div>
                                        <div class="text-xs">
                                            <p class="font-bold text-gray-900 text-sm">Keluar Sekolah</p>
                                            <p class="text-gray-500">Meninggalkan area sekolah</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'keluar_sekolah'" class="h-5 w-5 text-red-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-xl border bg-white p-3.5 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'dalam_lingkungan' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="dalam_lingkungan" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div class="text-xs">
                                            <p class="font-bold text-gray-900 text-sm">Dalam Lingkungan</p>
                                            <p class="text-gray-500">UKS, Perpus, BK, dll</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'dalam_lingkungan'" class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
                        <x-input-error :messages="$errors->get('jenis_izin')" class="mt-2" />
                    </div>

                    {{-- Tujuan / Keperluan --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Tujuan / Keperluan <span class="text-rose-500">*</span>
                        </label>

                        {{-- Opsi Jika Keluar Sekolah: Input Text --}}
                        <div x-show="jenisIzin === 'keluar_sekolah'" class="space-y-1">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="tujuan_keluar" name="tujuan"
                                    :disabled="jenisIzin !== 'keluar_sekolah'"
                                    :required="jenisIzin === 'keluar_sekolah'"
                                    value="{{ old('tujuan') }}"
                                    placeholder="Contoh: Fotokopi materi pelajaran di ruko depan / Ambil barang di rumah"
                                    class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm transition-colors">
                            </div>
                            <p class="text-xs text-gray-500">Tuliskan lokasi dan tujuan siswa meninggalkan area lingkungan sekolah.</p>
                        </div>

                        {{-- Opsi Jika Dalam Lingkungan: Select Dropdown --}}
                        <div x-show="jenisIzin === 'dalam_lingkungan'" class="space-y-1" style="display: none;">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <select id="tujuan_dalam" name="tujuan"
                                    :disabled="jenisIzin !== 'dalam_lingkungan'"
                                    :required="jenisIzin === 'dalam_lingkungan'"
                                    class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-colors">
                                    <option value="">-- Pilih Fasilitas / Keperluan Sekolah --</option>
                                    <option value="UKS (Unit Kesehatan Sekolah)" {{ old('tujuan') === 'UKS (Unit Kesehatan Sekolah)' ? 'selected' : '' }}>1. UKS (Unit Kesehatan Sekolah)</option>
                                    <option value="Perpustakaan" {{ old('tujuan') === 'Perpustakaan' ? 'selected' : '' }}>2. Perpustakaan</option>
                                    <option value="Ruang BK (Bimbingan Konseling)" {{ old('tujuan') === 'Ruang BK (Bimbingan Konseling)' ? 'selected' : '' }}>3. Ruang BK (Bimbingan Konseling)</option>
                                    <option value="Laboratorium Komputer / Praktik" {{ old('tujuan') === 'Laboratorium Komputer / Praktik' ? 'selected' : '' }}>4. Laboratorium Komputer / Praktik</option>
                                    <option value="Tata Usaha (TU)" {{ old('tujuan') === 'Tata Usaha (TU)' ? 'selected' : '' }}>5. Tata Usaha (TU)</option>
                                    <option value="Koperasi / Kantin Sekolah" {{ old('tujuan') === 'Koperasi / Kantin Sekolah' ? 'selected' : '' }}>6. Koperasi / Kantin Sekolah</option>
                                    <option value="Toilet / Kamar Mandi" {{ old('tujuan') === 'Toilet / Kamar Mandi' ? 'selected' : '' }}>7. Toilet / Kamar Mandi</option>
                                    <option value="Keperluan Lainnya dalam Lingkungan Sekolah" {{ old('tujuan') === 'Keperluan Lainnya dalam Lingkungan Sekolah' ? 'selected' : '' }}>8. Keperluan Lainnya dalam Lingkungan Sekolah</option>
                                </select>
                            </div>
                            <p class="text-xs text-gray-500">Pilih tempat atau fasilitas tujuan siswa di dalam lingkungan sekolah.</p>
                        </div>
                        <x-input-error :messages="$errors->get('tujuan')" class="mt-2" />
                    </div>

                    {{-- Estimasi Kembali --}}
                    <div>
                        <label for="estimasi_kembali" class="block text-sm font-bold text-gray-700 mb-1">
                            Perkiraan Kembali <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative w-full sm:w-1/2">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="time" name="estimasi_kembali" id="estimasi_kembali" 
                                value="{{ old('estimasi_kembali', now()->addMinutes(30)->format('H:i')) }}"
                                class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm transition-colors" required>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Masukkan jam berapa siswa berencana / diperkirakan kembali ke kelas (format 24 jam).</p>
                        <x-input-error :messages="$errors->get('estimasi_kembali')" class="mt-2" />
                    </div>

                    {{-- Keterangan / Alasan Lengkap --}}
                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-1">
                            Keterangan / Alasan Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" required minlength="5"
                            placeholder="Jelaskan secara detail alasan atau kebutuhan izin keluar siswa..."
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm transition-colors">{{ old('keterangan') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">Jelaskan alasan izin siswa secara jelas (wajib diisi minimal 5 karakter).</p>
                        <x-input-error :messages="$errors->get('keterangan')" class="mt-2" />
                    </div>

                    {{-- Unggah Foto (Foto Siswa / Bukti Kondisi) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Unggah Foto (Foto Siswa di Meja Piket / Bukti Kondisi)
                        </label>
                        <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-dashed rounded-xl transition-all"
                            :class="fotoPreview ? 'border-red-400 bg-red-50/20' : 'border-gray-300 hover:border-red-400 bg-gray-50/50'">
                            
                            <div class="space-y-2 text-center w-full">
                                <template x-if="!fotoPreview">
                                    <div class="flex flex-col items-center">
                                        <div class="p-3 bg-red-50 rounded-full text-red-600 mb-2">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <label for="foto" class="cursor-pointer">
                                            <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-lg text-xs font-bold text-white bg-red-600 hover:bg-red-700 shadow-sm transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Ambil Foto / Pilih File
                                            </span>
                                            <input id="foto" name="foto" type="file" x-ref="fotoInput" accept="image/*" capture="user"
                                                @change="handleFotoChange($event)" class="sr-only">
                                        </label>
                                        <p class="text-xs text-gray-500 mt-2">Bisa berupa foto siswa langsung di meja piket atau foto bukti fisik jika ada (JPG, PNG, maks 5MB).</p>
                                    </div>
                                </template>

                                <template x-if="fotoPreview">
                                    <div class="relative inline-block w-full text-center">
                                        <img :src="fotoPreview" alt="Preview Foto" class="max-h-48 mx-auto rounded-lg shadow-md border border-gray-200 object-cover">
                                        <div class="mt-2 flex items-center justify-center gap-2">
                                            <label for="foto" class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-red-50 text-red-700 hover:bg-red-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                                </svg>
                                                Ganti Foto
                                            </label>
                                            <button type="button" @click="removeFoto()" class="inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-rose-50 text-rose-700 hover:bg-rose-100 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('foto')" class="mt-2" />
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <a href="{{ route('piket.persetujuan-izin-keluar.index') }}" 
                            class="px-5 py-2.5 border border-gray-300 rounded-xl text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors shadow-sm">
                            Batal
                        </a>
                        <button type="submit" 
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-xl text-sm font-bold hover:bg-red-700 transition-all shadow-md shadow-red-200">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan & Setujui Izin
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control { border-radius: 0.75rem; padding: 0.65rem 0.85rem; border-color: #d1d5db; font-size: 0.875rem; }
            .ts-control.focus { border-color: #ef4444; box-shadow: 0 0 0 1px #ef4444; }
            .ts-dropdown { border-radius: 0.75rem; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1); margin-top: 5px; }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
        <script>
            function piketIzinForm() {
                return {
                    jenisIzin: '{{ old('jenis_izin', 'keluar_sekolah') }}',
                    currentSchedule: null,
                    loadingSchedule: false,
                    hasSelectedStudent: false,
                    fotoPreview: null,
                    handleFotoChange(event) {
                        const file = event.target.files[0];
                        if (file) {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.fotoPreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        } else {
                            this.fotoPreview = null;
                        }
                    },
                    removeFoto() {
                        this.fotoPreview = null;
                        if (this.$refs.fotoInput) {
                            this.$refs.fotoInput.value = '';
                        }
                    },
                    async fetchSchedule(siswaId) {
                        if (!siswaId) {
                            this.currentSchedule = null;
                            this.hasSelectedStudent = false;
                            return;
                        }
                        this.hasSelectedStudent = true;
                        this.loadingSchedule = true;
                        try {
                            const response = await fetch(`{{ url('piket/api/siswa') }}/${siswaId}/schedule`);
                            const data = await response.json();
                            if (response.ok) {
                                this.currentSchedule = data;
                            } else {
                                this.currentSchedule = null;
                            }
                        } catch (e) {
                            console.error('Failed to fetch schedule', e);
                            this.currentSchedule = null;
                        } finally {
                            this.loadingSchedule = false;
                        }
                    }
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                const ts = new TomSelect('#siswa-search', {
                    valueField: 'id',
                    labelField: 'nama_lengkap',
                    searchField: ['nama_lengkap', 'nis'],
                    load: function(query, callback) {
                        if (!query.length) return callback();
                        var url = '{{ route('api.siswa.search') }}?query=' + encodeURIComponent(query);
                        fetch(url)
                            .then(response => response.json())
                            .then(json => {
                                callback(json);
                            }).catch(() => {
                                callback();
                            });
                    },
                    render: {
                        option: function(item, escape) {
                            return `<div class="py-2.5 px-3.5 border-b border-gray-50">
                                <div class="font-bold text-gray-800 text-sm">${escape(item.nama_lengkap)}</div>
                                <div class="text-xs text-gray-500">NIS: ${escape(item.nis)} | Kelas: ${escape(item.kelas)}</div>
                            </div>`;
                        },
                        item: function(item, escape) {
                            return `<div class="font-medium text-gray-900 text-sm">${escape(item.nama_lengkap)} (NIS: ${escape(item.nis)})</div>`;
                        }
                    },
                    placeholder: 'Ketik Nama atau NIS Siswa...',
                    maxItems: 1,
                });

                ts.on('change', function(value) {
                    const alpineContainer = document.querySelector('[x-data="piketIzinForm()"]');
                    if (alpineContainer && alpineContainer.__x) {
                        alpineContainer.__x.$data.fetchSchedule(value);
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>
