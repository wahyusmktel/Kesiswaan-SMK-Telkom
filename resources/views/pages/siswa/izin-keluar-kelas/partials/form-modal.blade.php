<div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

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

            <form method="post" action="{{ route('siswa.izin-keluar-kelas.store') }}" x-data="{ jenisIzin: 'keluar_sekolah' }">
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

                    <div>
                        <label for="tujuan" class="block text-sm font-bold text-gray-700 mb-1">Tujuan /
                            Keperluan</label>
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
                            <input type="text" id="tujuan" name="tujuan" required
                                placeholder="Contoh: Toilet, UKS, Ruang BK"
                                class="pl-10 block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors">
                        </div>
                    </div>

                    <div>
                        <label for="estimasi_kembali" class="block text-sm font-bold text-gray-700 mb-1">Perkiraan
                            Kembali</label>
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
                        <label for="keterangan" class="block text-sm font-bold text-gray-700 mb-1">Keterangan Tambahan
                            <span class="font-normal text-gray-400">(Opsional)</span></label>
                        <textarea id="keterangan" name="keterangan" rows="2" placeholder="Detail tambahan jika diperlukan..."
                            class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors"></textarea>
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
