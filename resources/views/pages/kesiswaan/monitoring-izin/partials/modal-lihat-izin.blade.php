<div x-data="{ show: false, item: {} }"
    @open-modal.window="if ($event.detail.name === 'lihat-izin-monitoring') { show = true; item = $event.detail.item; }"
    x-show="show" x-on:keydown.escape.window="show = false" style="display: none;"
    class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
        <div x-on:click="show = false" x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"
            aria-hidden="true">
        </div>

        <div x-show="show" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                Detail Pengajuan Izin
            </h3>
            <div class="mt-4 space-y-2">
                <p><strong>Nama Siswa:</strong> <span x-text="item.user.name"></span></p>
                <p><strong>Kelas:</strong> <span
                        x-text="item.user.master_siswa && item.user.master_siswa.rombels.length > 0 ? item.user.master_siswa.rombels[0].kelas.nama_kelas : 'N/A'"></span>
                </p>
                <p><strong>Tanggal Izin:</strong> <span
                        x-text="new Date(item.tanggal_izin).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' })"></span>
                </p>
                <p><strong>Status:</strong> <span x-text="item.status" class="capitalize font-semibold"></span></p>

                <template x-if="item.approver">
                    <p><strong>Diproses Oleh:</strong> <span x-text="item.approver.name"></span></p>
                </template>

                <div>
                    <p><strong>Keterangan:</strong></p>
                    <div class="mt-1 p-2 border rounded-md prose max-w-none" x-html="item.keterangan"></div>
                </div>

                <template x-if="item.dokumen_pendukung">
                    <p><strong>Dokumen Pendukung:</strong> <a
                            :href="item.dokumen_url || (item.id ? ('/izin/' + item.id + '/dokumen') : ('/storage/' + item.dokumen_pendukung.replace(/^public\//, '')))" target="_blank"
                            class="text-blue-600 hover:underline">Lihat Dokumen</a></p>
                </template>

                <template x-if="item.status === 'ditolak' && item.alasan_penolakan">
                    <div class="mt-2 p-3 bg-red-50 border border-red-200 rounded-md">
                        <p class="text-sm font-semibold text-red-700">Alasan Penolakan:</p>
                        <p class="text-sm text-red-600 italic" x-text="item.alasan_penolakan"></p>
                    </div>
                </template>
            </div>

            <div class="mt-6">
                <x-secondary-button @click="show = false">Tutup</x-secondary-button>
            </div>
        </div>
    </div>
</div>
