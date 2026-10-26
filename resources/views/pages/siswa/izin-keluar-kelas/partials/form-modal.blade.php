<div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="isOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center">
                <h3 class="text-lg font-bold text-white flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Form Izin Keluar
                </h3>
                <button @click="closeModal"
                    class="text-indigo-100 hover:text-white focus:outline-none transition-colors">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form method="post" action="{{ route('siswa.izin-keluar-kelas.store') }}" enctype="multipart/form-data"
                x-data="{ 
                    jenisIzin: 'keluar_sekolah',
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
                    }
                }">
                @csrf
                <div class="px-6 py-6 space-y-5">

                    @if ($jadwalSaatIni)
                        <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4">
                            <div class="flex items-start gap-3">
                                <div class="p-2 bg-indigo-100 rounded-lg text-indigo-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-indigo-500 uppercase">Pelajaran Saat Ini</p>
                                    <p class="font-bold text-gray-900">{{ $jadwalSaatIni->mataPelajaran->nama_mapel }}
                                    </p>
                                    <p class="text-sm text-gray-600">{{ $jadwalSaatIni->guru->nama_lengkap }}</p>
                                </div>
                            </div>
                            <input type="hidden" name="jadwal_pelajaran_id" value="{{ $jadwalSaatIni->id }}">
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-100 rounded-xl p-4 text-red-700 text-sm">
                            Tidak ada jadwal pelajaran aktif saat ini.
                        </div>
                    @endif

                    {{-- Jenis Izin --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Izin</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'keluar_sekolah' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="keluar_sekolah" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <div class="text-xs">
                                            <p class="font-bold text-gray-900">Keluar Sekolah</p>
                                            <p class="text-gray-500">Meninggalkan sekolah</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'keluar_sekolah'" class="h-4 w-4 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>

                            <label class="relative flex cursor-pointer rounded-xl border bg-white p-3 shadow-sm focus:outline-none transition-all hover:bg-gray-50 border-gray-200"
                                :class="jenisIzin === 'dalam_lingkungan' ? 'ring-2 ring-indigo-500 border-indigo-500 bg-indigo-50/30' : ''">
                                <input type="radio" name="jenis_izin" value="dalam_lingkungan" x-model="jenisIzin" class="sr-only">
                                <div class="flex items-center justify-between w-full">
                                    <div class="flex items-center">
                                        <div class="text-xs">
                                            <p class="font-bold text-gray-900">Dalam Lingkungan</p>
                                            <p class="text-gray-500">UKS, Perpus, BK, dll</p>
                                        </div>
                                    </div>
                                    <svg x-show="jenisIzin === 'dalam_lingkungan'" class="h-4 w-4 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                            </label>
                        </div>
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
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                </div>
                                <input type="text" id="tujuan_keluar" name="tujuan"
                                    :disabled="jenisIzin !== 'keluar_sekolah'"
                                    :required="jenisIzin === 'keluar_sekolah'"
                                    placeholder="Contoh: Fotokopi materi pelajaran di ruko depan / Ambil barang di rumah"
                                    class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-colors">
                            </div>
                            <p class="text-xs text-gray-500">Tuliskan lokasi dan tujuan Anda meninggalkan area lingkungan sekolah.</p>
                        </div>

                        {{-- Opsi Jika Dalam Lingkungan: Select Dropdown --}}
                        <div x-show="jenisIzin === 'dalam_lingkungan'" class="space-y-1" style="display: none;">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                </div>
                                <select id="tujuan_dalam" name="tujuan"
                                    :disabled="jenisIzin !== 'dalam_lingkungan'"
                                    :required="jenisIzin === 'dalam_lingkungan'"
                                    class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-colors">
                                    <option value="">-- Pilih Fasilitas / Keperluan Sekolah --</option>
                                    <option value="UKS (Unit Kesehatan Sekolah)">1. UKS (Unit Kesehatan Sekolah)</option>
                                    <option value="Perpustakaan">2. Perpustakaan</option>
                                    <option value="Ruang BK (Bimbingan Konseling)">3. Ruang BK (Bimbingan Konseling)</option>
                                    <option value="Laboratorium Komputer / Praktik">4. Laboratorium Komputer / Praktik</option>
                                    <option value="Tata Usaha (TU)">5. Tata Usaha (TU)</option>
                                    <option value="Koperasi / Kantin Sekolah">6. Koperasi / Kantin Sekolah</option>
                                    <option value="Toilet / Kamar Mandi">7. Toilet / Kamar Mandi</option>
                                    <option value="Keperluan Lainnya dalam Lingkungan Sekolah">8. Keperluan Lainnya dalam Lingkungan Sekolah</option>
                                </select>
                            </div>
                            <p class="text-xs text-gray-500">Pilih tempat atau fasilitas tujuan Anda di dalam lingkungan sekolah.</p>
                        </div>
                    </div>

                    <div>
                        <label for="estimasi_kembali" class="block text-sm font-bold text-gray-700 mb-1">
                            Perkiraan Kembali <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <input type="time" id="estimasi_kembali" name="estimasi_kembali" required
                                class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Masukkan jam berapa Anda berencana kembali ke kelas.</p>
                    </div>

                    <div>
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-1">
                            Keterangan / Alasan Lengkap <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="keterangan" name="keterangan" rows="3" required minlength="5"
                            placeholder="Jelaskan secara detail alasan atau kebutuhan izin keluar Anda kepada guru kelas..."
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm transition-colors"></textarea>
                        <p class="mt-1 text-xs text-gray-500">Jelaskan alasan izin Anda secara jelas dan sopan (wajib diisi minimal 5 karakter).</p>
                    </div>

                    {{-- Unggah Foto Bukti/Selfie (Wajib) --}}
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-1">
                            Unggah Foto (Selfie / Kondisi Saat Ini) <span class="text-rose-500">*</span>
                        </label>
                        <div class="mt-1 flex justify-center px-4 pt-4 pb-4 border-2 border-dashed rounded-xl transition-all"
                            :class="fotoPreview ? 'border-indigo-400 bg-indigo-50/20' : 'border-gray-300 hover:border-indigo-400 bg-gray-50/50'">
                            
                            <div class="space-y-2 text-center w-full">
                                <template x-if="!fotoPreview">
                                    <div class="flex flex-col items-center">
                                        <div class="p-3 bg-indigo-50 rounded-full text-indigo-600 mb-2">
                                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <label for="foto" class="cursor-pointer">
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Ambil Foto / Pilih File
                                            </span>
                                            <input id="foto" name="foto" type="file" x-ref="fotoInput" accept="image/*" capture="user" required
                                                @change="handleFotoChange($event)" class="sr-only">
                                        </label>
                                        <p class="text-xs text-gray-500 mt-2">Bisa berupa foto selfie Anda atau foto kondisi saat ini (JPG, PNG, maks 5MB).</p>
                                    </div>
                                </template>

                                <template x-if="fotoPreview">
                                    <div class="relative inline-block w-full text-center">
                                        <img :src="fotoPreview" alt="Preview Foto" class="max-h-48 mx-auto rounded-lg shadow-md border border-gray-200 object-cover">
                                        <div class="mt-2 flex items-center justify-center gap-2">
                                            <label for="foto" class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 text-xs font-semibold rounded-md bg-indigo-50 text-indigo-700 hover:bg-indigo-100 transition-colors">
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
                    </div>

                </div>

                <div class="bg-gray-50 px-6 py-4 flex flex-row-reverse border-t border-gray-100 rounded-b-2xl">
                    <button type="submit"
                        class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-bold text-white shadow-lg hover:bg-indigo-500 hover:shadow-indigo-500/30 transition-all sm:ml-3 sm:w-auto transform hover:-translate-y-0.5">
                        Kirim Pengajuan
                    </button>
                    <button type="button" @click="closeModal"
                        class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-4 py-2.5 text-sm font-bold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all sm:mt-0 sm:w-auto">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
