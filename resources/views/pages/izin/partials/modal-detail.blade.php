<div x-show="isDetailOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="isDetailOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
        @click="closeDetailModal"></div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="isDetailOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Izin</h3>
                <button @click="closeDetailModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-4 py-5 sm:p-6 space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Tanggal</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1"
                            x-text="detailItem.tanggal_izin ? new Date(detailItem.tanggal_izin).toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'}) : '-'">
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Status</p>
                        <span
                            class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize"
                            :class="{
                                'bg-yellow-50 text-yellow-700 border-yellow-200': detailItem.status === 'diajukan',
                                'bg-green-50 text-green-700 border-green-200': detailItem.status === 'disetujui',
                                'bg-red-50 text-red-700 border-red-200': detailItem.status === 'ditolak'
                            }"
                            x-text="detailItem.status">
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Keterangan</p>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-700"
                        x-html="detailItem.keterangan"></div>
                </div>

                <template x-if="detailItem.dokumen_pendukung">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Lampiran</p>
                        <a :href="'/storage/' + detailItem.dokumen_pendukung" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors w-full justify-center">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            Lihat Dokumen
                        </a>
                    </div>
                </template>

                <template x-if="detailItem.status === 'ditolak' && detailItem.alasan_penolakan">
                    <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                        <p class="text-xs font-bold text-red-600 uppercase">Alasan Penolakan</p>
                        <p class="text-sm text-red-700 mt-1 italic" x-text="detailItem.alasan_penolakan"></p>
                    </div>
                </template>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button type="button" @click="closeDetailModal"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
