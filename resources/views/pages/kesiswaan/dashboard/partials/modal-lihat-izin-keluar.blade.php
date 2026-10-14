{{-- Tambahkan x-data="{}" di sini untuk menginisialisasi komponen Alpine.js --}}
<div x-data="{}" x-show="$store.detailModal?.on" x-on:keydown.escape.window="$store.detailModal?.close()"
    style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
    aria-modal="true">

    <div class="flex items-end justify-center min-h-screen px-4 text-center md:items-center sm:block sm:p-0">
        <div x-on:click="$store.detailModal?.close()" x-show="$store.detailModal?.on"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75" aria-hidden="true">
        </div>

        <div x-show="$store.detailModal?.on" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            class="inline-block w-full max-w-lg p-6 my-8 overflow-hidden text-left align-middle transition-all transform bg-white shadow-xl rounded-lg">

            <h3 class="text-lg font-medium leading-6 text-gray-900" id="modal-title">
                Detail Izin Meninggalkan Kelas
            </h3>
            <div class="mt-4 space-y-2 text-sm">
                <p><strong>Siswa:</strong> <span x-text="$store.detailModal?.data?.siswa?.name || 'N/A'"></span></p>
                <p><strong>Kelas:</strong> <span
                        x-text="$store.detailModal?.data?.siswa?.master_siswa?.rombels?.[0]?.kelas?.nama_kelas || 'N/A'"></span>
                </p>
                <p><strong>Tujuan:</strong> <span x-text="$store.detailModal?.data?.tujuan || 'N/A'"></span></p>

                <template x-if="$store.detailModal?.data?.jadwal_pelajaran">
                    <div class="p-2 bg-gray-50 rounded-md">
                        <p><strong>Pada Jam Pelajaran:</strong></p>
                        <p class="ml-2">- Mapel: <span
                                x-text="$store.detailModal?.data?.jadwal_pelajaran?.mata_pelajaran?.nama_mapel || 'N/A'"></span>
                        </p>
                        <p class="ml-2">- Guru: <span
                                x-text="$store.detailModal?.data?.jadwal_pelajaran?.guru?.nama_lengkap || 'N/A'"></span>
                        </p>
                    </div>
                </template>

                <p><strong>Status:</strong> <span x-text="$store.detailModal?.data?.status?.replace(/_/g, ' ') || 'N/A'"
                        class="capitalize font-semibold"></span></p>
            </div>

            <div class="mt-6">
                <x-secondary-button @click="$store.detailModal?.close()">Tutup</x-secondary-button>
            </div>
        </div>
    </div>
</div>
