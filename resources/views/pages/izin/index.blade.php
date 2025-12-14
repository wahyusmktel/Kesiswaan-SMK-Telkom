<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Riwayat Perizinan Saya</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="izinPageData()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">

                    <form action="{{ route('izin.index') }}" method="GET" class="w-full sm:w-1/2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="Cari berdasarkan keterangan...">
                    </form>

                    <button @click="openCreateModal()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-md transition ease-in-out duration-150 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Ajukan Izin Baru
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tanggal Izin</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Keterangan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Dokumen</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($perizinan as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-700">
                                        {{ \Carbon\Carbon::parse($izin->tanggal_izin)->isoFormat('D MMMM Y') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="truncate max-w-xs text-gray-900 font-medium"
                                            title="{{ strip_tags($izin->keterangan) }}">
                                            {{ Str::limit(strip_tags($izin->keterangan), 50) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($izin->dokumen_pendukung)
                                            <a href="{{ Storage::url($izin->dokumen_pendukung) }}" target="_blank"
                                                class="text-indigo-600 hover:text-indigo-900 flex items-center gap-1 text-xs font-medium">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                                                </svg>
                                                Lihat File
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs italic">-</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'diajukan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'disetujui' => 'bg-green-50 text-green-700 border-green-200',
                                                'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                            {{ $izin->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="openDetailModal({{ json_encode($izin) }})"
                                                class="text-gray-500 hover:text-indigo-600 transition-colors p-1 rounded-md hover:bg-indigo-50"
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
                                                <button @click="openEditModal({{ json_encode($izin) }})"
                                                    class="text-gray-500 hover:text-amber-600 transition-colors p-1 rounded-md hover:bg-amber-50"
                                                    title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <form action="{{ route('izin.destroy', $izin->id) }}" method="POST"
                                                    class="inline-block" onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-gray-500 hover:text-red-600 transition-colors p-1 rounded-md hover:bg-red-50"
                                                        title="Batalkan">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
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
                                            <p class="text-base font-medium">Belum ada riwayat perizinan.</p>
                                            <p class="text-xs mt-1">Klik tombol di atas untuk mengajukan izin.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $perizinan->appends(['search' => request('search')])->links() }}
                </div>
            </div>
        </div>

        @include('pages.izin.partials.modal-form')
        @include('pages.izin.partials.modal-detail')

    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(e) {
                e.preventDefault();
                let form = e.target;
                Swal.fire({
                    title: 'Batalkan Pengajuan?',
                    text: "Izin yang dibatalkan akan dihapus permanen.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Batalkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }

            function izinPageData() {
                return {
                    // State untuk Modal Form (Create/Edit)
                    isFormOpen: false,
                    isEdit: false,
                    formAction: '{{ route('izin.store') }}',
                    form: {
                        tanggal_izin: '',
                        keterangan: ''
                    },

                    // State untuk Modal Detail
                    isDetailOpen: false,
                    detailItem: {},

                    // Actions Form
                    openCreateModal() {
                        this.isFormOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('izin.store') }}';
                        this.form = {
                            tanggal_izin: '',
                            keterangan: ''
                        };
                        // Reset file input manual
                        if (document.getElementById('dokumen_pendukung')) document.getElementById('dokumen_pendukung').value =
                            '';
                    },
                    openEditModal(item) {
                        this.isFormOpen = true;
                        this.isEdit = true;
                        // Ganti URL update dengan ID yang benar
                        this.formAction = '{{ url('izin') }}/' + item.id;
                        this.form = {
                            tanggal_izin: item.tanggal_izin,
                            keterangan: item.keterangan
                        };
                    },
                    closeFormModal() {
                        this.isFormOpen = false;
                    },

                    // Actions Detail
                    openDetailModal(item) {
                        this.isDetailOpen = true;
                        this.detailItem = item;
                    },
                    closeDetailModal() {
                        this.isDetailOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
