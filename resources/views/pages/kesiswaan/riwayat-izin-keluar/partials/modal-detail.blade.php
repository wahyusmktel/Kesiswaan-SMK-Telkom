<div x-data="{ show: false, item: {} }"
    @open-modal.window="if ($event.detail.name === 'detail-izin-keluar') { show = true; item = $event.detail.item; }"
    x-show="show" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="show" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="show = false"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Izin Keluar</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center gap-4 mb-6 pb-4 border-b border-gray-100">
                    <div
                        class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-lg border border-indigo-200">
                        <span x-text="item.siswa?.name ? item.siswa.name.charAt(0) : '?'"></span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900" x-text="item.siswa?.name"></h4>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <span
                                x-text="item.siswa?.master_siswa?.rombels?.length > 0 ? item.siswa.master_siswa.rombels[0].kelas.nama_kelas : 'Tanpa Kelas'"></span>
                            <span>•</span>
                            <span
                                x-text="item.created_at ? new Date(item.created_at).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '-'"></span>
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-200">
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wide">Tujuan / Keperluan</p>
                        <p class="text-sm text-gray-900 mt-1 font-medium" x-text="item.tujuan"></p>
                    </div>

                    <div>
                        <h5 class="text-sm font-bold text-gray-900 mb-3">Timeline Proses</h5>
                        <div class="relative border-l-2 border-gray-200 ml-3 space-y-6 pl-6 py-1">

                            <div class="relative">
                                <div
                                    class="absolute -left-[31px] bg-white border-2 border-gray-200 rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold text-gray-500 shadow-sm">
                                    1
                                </div>
                                <p class="text-xs font-bold text-gray-500 uppercase">Persetujuan Guru Kelas</p>
                                <p class="text-sm font-semibold text-gray-900"
                                    x-text="item.guru_kelas_approver?.name || 'Menunggu...'"></p>
                            </div>

                            <div class="relative">
                                <div
                                    class="absolute -left-[31px] bg-white border-2 border-gray-200 rounded-full w-6 h-6 flex items-center justify-center text-[10px] font-bold text-gray-500 shadow-sm">
                                    2
                                </div>
                                <p class="text-xs font-bold text-gray-500 uppercase">Persetujuan Guru Piket</p>
                                <p class="text-sm font-semibold text-gray-900"
                                    x-text="item.guru_piket_approver?.name || 'Menunggu...'"></p>
                            </div>

                            <div class="relative">
                                <div
                                    class="absolute -left-[31px] bg-white border-2 border-green-200 rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                                    <svg class="w-3 h-3 text-green-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-500 uppercase">Waktu Keluar (Security)</p>
                                <div class="text-sm font-semibold text-gray-900">
                                    <span x-text="item.security_verifier?.name || '-'"></span>
                                    <template x-if="item.waktu_keluar_sebenarnya">
                                        <span
                                            class="text-green-600 font-bold bg-green-50 px-2 py-0.5 rounded ml-2 text-xs"
                                            x-text="item.waktu_keluar_sebenarnya.substring(0, 5)"></span>
                                    </template>
                                </div>
                            </div>

                            <div class="relative">
                                <div
                                    class="absolute -left-[31px] bg-white border-2 border-blue-200 rounded-full w-6 h-6 flex items-center justify-center shadow-sm">
                                    <svg class="w-3 h-3 text-blue-600" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M7 16l-4-4m0 0l4-4m-4 4h18" />
                                    </svg>
                                </div>
                                <p class="text-xs font-bold text-gray-500 uppercase">Waktu Kembali</p>
                                <template x-if="item.waktu_kembali_sebenarnya">
                                    <p class="text-sm font-bold text-blue-600 bg-blue-50 px-2 py-1 rounded inline-block mt-1"
                                        x-text="item.waktu_kembali_sebenarnya.substring(0, 5)"></p>
                                </template>
                                <template x-if="!item.waktu_kembali_sebenarnya">
                                    <p class="text-sm text-gray-400 italic">Belum kembali ke sekolah</p>
                                </template>
                            </div>
                        </div>
                    </div>

                    <template x-if="item.status === 'ditolak'">
                        <div class="bg-red-50 p-4 rounded-lg border border-red-100 flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <div>
                                <p class="text-xs font-bold text-red-700 uppercase">Ditolak Oleh</p>
                                <p class="text-sm text-red-600 font-medium" x-text="item.penolak?.name || 'Sistem'">
                                </p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button type="button" @click="show = false"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
