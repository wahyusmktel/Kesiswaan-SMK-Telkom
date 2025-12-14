<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Izin Siswa</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="perizinanWaliKelas()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('wali-kelas.perizinan.index') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">

                            <div class="w-full lg:w-1/3">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari
                                    Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm h-10"
                                        placeholder="Nama siswa...">
                                </div>
                            </div>

                            <div class="w-full lg:w-auto flex-1">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status
                                    Pengajuan</label>
                                <div class="flex bg-gray-200/50 p-1 rounded-lg w-fit">
                                    @foreach (['' => 'Semua', 'diajukan' => 'Menunggu', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $key => $label)
                                        <button type="submit" name="status" value="{{ $key }}"
                                            class="px-4 py-1.5 text-xs font-medium rounded-md transition-all shadow-sm
                                            {{ request('status') == $key ? 'bg-white text-gray-800 shadow' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                            {{ $label }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="lg:hidden w-full">
                                <button type="submit"
                                    class="w-full bg-indigo-600 text-white rounded-lg py-2 text-sm font-bold">Terapkan
                                    Filter</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Tanggal Izin</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($perizinan as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs border border-indigo-200">
                                                {{ substr($izin->user->name, 0, 1) }}
                                            </div>
                                            <span class="font-semibold text-gray-900">{{ $izin->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ \Carbon\Carbon::parse($izin->tanggal_izin)->isoFormat('D MMMM Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="truncate block max-w-xs"
                                            title="{{ strip_tags($izin->keterangan) }}">
                                            {{ Str::limit(strip_tags($izin->keterangan), 40) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'diajukan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'disetujui' => 'bg-green-50 text-green-700 border-green-200',
                                                'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                            };
                                            $statusLabel = $izin->status == 'diajukan' ? 'Menunggu' : $izin->status;
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openDetailModal({{ json_encode($izin) }})"
                                                class="text-gray-400 hover:text-indigo-600 transition-colors p-1 rounded-lg hover:bg-indigo-50"
                                                title="Lihat Detail">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                </svg>
                                            </button>

                                            @if ($izin->status == 'diajukan')
                                                <form action="{{ route('wali-kelas.perizinan.approve', $izin->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" onclick="confirmApprove(this)"
                                                        class="text-gray-400 hover:text-green-600 transition-colors p-1 rounded-lg hover:bg-green-50"
                                                        title="Setujui">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    </button>
                                                </form>

                                                <button @click="openRejectModal({{ json_encode($izin) }})"
                                                    class="text-gray-400 hover:text-red-600 transition-colors p-1 rounded-lg hover:bg-red-50"
                                                    title="Tolak">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada pengajuan izin yang ditemukan.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $perizinan->withQueryString()->links() }}
                </div>
            </div>
        </div>

        <div x-show="isDetailOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isDetailOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="isDetailOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isDetailOpen"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    <div
                        class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-gray-900">Detail Izin Siswa</h3>
                        <button @click="isDetailOpen = false"
                            class="text-gray-400 hover:text-gray-500 focus:outline-none"><svg class="h-6 w-6"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div class="flex items-center gap-4 border-b border-gray-100 pb-4">
                            <div
                                class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                <span x-text="selectedItem.user?.name ? selectedItem.user.name.charAt(0) : '?'"></span>
                            </div>
                            <div>
                                <h4 class="text-lg font-bold text-gray-900" x-text="selectedItem.user?.name"></h4>
                                <p class="text-sm text-gray-500"
                                    x-text="selectedItem.tanggal_izin ? new Date(selectedItem.tanggal_izin).toLocaleDateString('id-ID', { weekday:'long', day:'numeric', month:'long', year:'numeric'}) : '-'">
                                </p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-gray-500 uppercase">Keterangan</p>
                            <div class="mt-1 p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm text-gray-800"
                                x-html="selectedItem.keterangan"></div>
                        </div>
                        <template x-if="selectedItem.dokumen_pendukung">
                            <div>
                                <p class="text-xs font-bold text-gray-500 uppercase mb-1">Dokumen</p>
                                <a :href="'/storage/' + selectedItem.dokumen_pendukung" target="_blank"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 w-full justify-center">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                    </svg>
                                    Lihat Lampiran
                                </a>
                            </div>
                        </template>
                        <template x-if="selectedItem.status === 'ditolak'">
                            <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                <p class="text-xs font-bold text-red-600 uppercase">Alasan Penolakan</p>
                                <p class="text-sm text-red-700 mt-1" x-text="selectedItem.alasan_penolakan"></p>
                            </div>
                        </template>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-gray-100">
                        <button type="button" @click="isDetailOpen = false"
                            class="w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:ml-3 sm:w-auto sm:text-sm">Tutup</button>
                    </div>
                </div>
            </div>
        </div>

        <div x-show="isRejectOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isRejectOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="isRejectOpen = false"></div>
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isRejectOpen"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">
                    <div class="bg-red-50 px-4 py-3 sm:px-6 border-b border-red-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-red-800">Tolak Pengajuan Izin</h3>
                        <button @click="isRejectOpen = false"
                            class="text-red-400 hover:text-red-600 focus:outline-none"><svg class="h-6 w-6"
                                fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>
                    <form :action="rejectUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div class="bg-white p-3 rounded-lg border border-gray-200">
                                <p class="text-xs text-gray-500 uppercase font-bold mb-1">Siswa</p>
                                <p class="text-gray-900 font-medium" x-text="selectedItem.user?.name"></p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Alasan Penolakan</label>
                                <textarea name="alasan_penolakan" rows="3" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                    placeholder="Contoh: Keterangan kurang jelas..."></textarea>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">Tolak
                                Izin</button>
                            <button type="button" @click="isRejectOpen = false"
                                class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmApprove(button) {
                Swal.fire({
                    title: 'Setujui Izin?',
                    text: "Siswa akan tercatat izin secara resmi.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16a34a', // Green
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Setujui!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

            function perizinanWaliKelas() {
                return {
                    isDetailOpen: false,
                    isRejectOpen: false,
                    selectedItem: {},
                    rejectUrl: '',

                    openDetailModal(item) {
                        this.isDetailOpen = true;
                        this.selectedItem = item;
                    },
                    openRejectModal(item) {
                        this.isRejectOpen = true;
                        this.selectedItem = item;
                        // URL Builder Manual karena route helper blade tidak bisa dinamis di JS client-side
                        // Pastikan URL prefix sesuai route list kamu.
                        // Default: /wali-kelas/perizinan/{id}/reject
                        this.rejectUrl = '{{ url('wali-kelas/perizinan') }}/' + item.id + '/reject';
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
