<div x-data="{ show: false, item: {} }"
    @open-modal.window="if ($event.detail.name === 'detail-izin-piket') { show = true; item = $event.detail.item; }"
    x-show="show" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
    role="dialog" aria-modal="true">

    <div x-show="show" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="show = false">
    </div>

    <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
        <div x-show="show"
            class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

            <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Izin Siswa</h3>
                <button @click="show = false" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="px-4 py-5 sm:p-6 space-y-6">
                <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                    <div
                        class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                        <span x-text="item.user?.name ? item.user.name.charAt(0) : '?'"></span>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-900" x-text="item.user?.name"></h4>
                        <p class="text-sm text-gray-500"
                            x-text="item.user?.master_siswa?.rombels?.length > 0 ? item.user.master_siswa.rombels[0].kelas.nama_kelas : 'Tanpa Kelas'">
                        </p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Tanggal Izin</p>
                        <p class="text-sm font-semibold text-gray-900 mt-1"
                            x-text="item.tanggal_izin ? new Date(item.tanggal_izin).toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric'}) : '-'">
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase">Status</p>
                        <span
                            class="inline-flex mt-1 items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize"
                            :class="{
                                'bg-yellow-50 text-yellow-700 border-yellow-200': item.status === 'diajukan',
                                'bg-green-50 text-green-700 border-green-200': item.status === 'disetujui',
                                'bg-red-50 text-red-700 border-red-200': item.status === 'ditolak'
                            }"
                            x-text="item.status">
                        </span>
                    </div>
                </div>

                <div>
                    <p class="text-xs font-bold text-gray-500 uppercase">Alasan / Keterangan</p>
                    <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-800"
                        x-html="item.keterangan || '-'"></div>
                </div>

                <template x-if="item.dokumen_pendukung">
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase mb-1">Dokumen Pendukung</p>
                        <a :href="'/storage/' + item.dokumen_pendukung" target="_blank"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 shadow-sm transition-colors w-full justify-center">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            Lihat Lampiran
                        </a>
                    </div>
                </template>

                <template x-if="item.approver">
                    <div class="pt-4 border-t border-gray-100 text-xs text-gray-400 text-center">
                        Diproses oleh <span class="font-bold text-gray-600" x-text="item.approver.name"></span>
                    </div>
                </template>
            </div>

            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                <button type="button" @click="show = false"
                    class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</div>
