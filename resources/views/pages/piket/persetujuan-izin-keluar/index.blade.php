<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Izin Keluar</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="persetujuanPiket()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <h3 class="font-bold text-gray-700">Daftar Pengajuan Menunggu</h3>

                    <div class="relative w-full sm:w-64">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text"
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="Cari siswa...">
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Tujuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($daftarIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold text-xs border border-indigo-200">
                                                {{ substr($izin->siswa->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900">{{ $izin->siswa->name }}</div>
                                                <div class="text-xs text-gray-500">NIS:
                                                    {{ $izin->siswa->masterSiswa->nis ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-700 border border-gray-200">
                                            {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="block truncate max-w-[200px]" title="{{ $izin->tujuan }}">
                                            {{ $izin->tujuan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusInfo = match ($izin->status) {
                                                'disetujui_guru_kelas' => [
                                                    'bg' => 'bg-yellow-50',
                                                    'text' => 'text-yellow-700',
                                                    'border' => 'border-yellow-200',
                                                    'label' => 'Menunggu Piket',
                                                ],
                                                'disetujui_guru_piket' => [
                                                    'bg' => 'bg-green-50',
                                                    'text' => 'text-green-700',
                                                    'border' => 'border-green-200',
                                                    'label' => 'Disetujui Piket',
                                                ],
                                                'ditolak' => [
                                                    'bg' => 'bg-red-50',
                                                    'text' => 'text-red-700',
                                                    'border' => 'border-red-200',
                                                    'label' => 'Ditolak',
                                                ],
                                                default => [
                                                    'bg' => 'bg-gray-50',
                                                    'text' => 'text-gray-700',
                                                    'border' => 'border-gray-200',
                                                    'label' => $izin->status,
                                                ],
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $statusInfo['bg'] }} {{ $statusInfo['text'] }} {{ $statusInfo['border'] }}">
                                            {{ $statusInfo['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">

                                            @if ($izin->status == 'disetujui_guru_kelas')
                                                <button @click="openRejectModal({{ json_encode($izin) }})"
                                                    class="inline-flex items-center px-3 py-1.5 bg-white text-red-600 rounded-lg hover:bg-red-50 border border-red-200 text-xs font-semibold transition-colors shadow-sm">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Tolak
                                                </button>

                                                <form
                                                    action="{{ route('piket.persetujuan-izin-keluar.approve', $izin->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="button" onclick="confirmApprove(this)"
                                                        class="inline-flex items-center px-3 py-1.5 bg-green-600 text-white rounded-lg hover:bg-green-500 border border-transparent text-xs font-bold transition-colors shadow-md">
                                                        <svg class="w-3.5 h-3.5 mr-1.5" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Setujui
                                                    </button>
                                                </form>
                                            @elseif (in_array($izin->status, ['disetujui_guru_piket', 'diverifikasi_security', 'selesai']))
                                                <a href="{{ route('piket.persetujuan-izin-keluar.print', $izin->id) }}"
                                                    target="_blank"
                                                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-lg hover:bg-indigo-100 border border-indigo-200 text-xs font-semibold transition-colors">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak Surat
                                                </a>
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
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada pengajuan izin yang perlu
                                                diproses.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $daftarIzin->links() }}
                </div>
            </div>
        </div>

        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
                @click="isOpen = false"></div>

            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div x-show="isOpen"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                    <div class="bg-red-50 px-4 py-3 sm:px-6 border-b border-red-100 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-red-800">Tolak Izin Siswa</h3>
                        <button @click="isOpen = false"
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
                                <p class="text-gray-900 font-medium" x-text="selectedItem.siswa?.name"></p>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Alasan Penolakan</label>
                                <textarea name="alasan_penolakan" rows="3" required
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                    placeholder="Contoh: Jam pelajaran masih panjang..."></textarea>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                                Tolak Izin
                            </button>
                            <button type="button" @click="isOpen = false"
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
                    text: "Siswa akan diizinkan meninggalkan lingkungan sekolah.",
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

            function persetujuanPiket() {
                return {
                    isOpen: false,
                    selectedItem: {},
                    rejectUrl: '',

                    openRejectModal(item) {
                        this.isOpen = true;
                        this.selectedItem = item;
                        // Build URL dinamis
                        this.rejectUrl = '{{ url('piket/persetujuan-izin-keluar') }}/' + item.id + '/reject';
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
