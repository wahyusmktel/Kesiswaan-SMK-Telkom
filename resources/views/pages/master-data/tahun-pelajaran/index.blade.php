<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Master Tahun Pelajaran</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Daftar Tahun Pelajaran</h3>
                    <button @click="$dispatch('open-tapel-modal')"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Data
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tahun Ajaran</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Semester</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-center">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($tahunPelajaran as $item)
                                <tr
                                    class="bg-white hover:bg-gray-50/80 transition-colors {{ $item->is_active ? 'bg-green-50/30' : '' }}">
                                    <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->tahun }}</td>
                                    <td class="px-6 py-4">{{ $item->semester }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($item->is_active)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">
                                                AKTIF
                                            </span>
                                        @else
                                            <form
                                                action="{{ route('master-data.tahun-pelajaran.activate', $item->id) }}"
                                                method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="text-xs text-gray-500 hover:text-indigo-600 underline">Aktifkan</button>
                                            </form>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="$dispatch('edit-tapel', { id: '{{ $item->id }}', tahun: '{{ $item->tahun }}', semester: '{{ $item->semester }}', url: '{{ route('master-data.tahun-pelajaran.update', $item->id) }}' })"
                                                class="text-gray-400 hover:text-indigo-600">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            @if (!$item->is_active)
                                                <form
                                                    action="{{ route('master-data.tahun-pelajaran.destroy', $item->id) }}"
                                                    method="POST" class="inline-block delete-form">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete(this)"
                                                        class="text-gray-400 hover:text-red-600">
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
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada data.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div x-data="tapelModal()" @open-tapel-modal.window="openModal()" @edit-tapel.window="editModal($event.detail)"
        x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-gray-100">
                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-gray-900" x-text="isEdit ? 'Edit Data' : 'Tambah Data'"></h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500"><svg class="h-6 w-6"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                            <input type="text" name="tahun" x-model="form.tahun" required placeholder="2024/2025"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Semester</label>
                            <select name="semester" x-model="form.semester"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="Ganjil">Ganjil</option>
                                <option value="Genap">Genap</option>
                            </select>
                        </div>
                        <div class="flex items-center">
                            <input id="is_active" name="is_active" type="checkbox"
                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded"
                                x-show="!isEdit">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900" x-show="!isEdit">Set
                                sebagai Aktif</label>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Simpan</button>
                        <button type="button" @click="closeModal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmDelete(button) {
                Swal.fire({
                    title: 'Hapus Data?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) button.closest('form').submit();
                });
            }

            function tapelModal() {
                return {
                    isOpen: false,
                    isEdit: false,
                    formAction: '',
                    form: {
                        tahun: '',
                        semester: 'Ganjil'
                    },
                    openModal() {
                        this.isOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('master-data.tahun-pelajaran.store') }}';
                        this.form = {
                            tahun: '',
                            semester: 'Ganjil'
                        };
                    },
                    editModal(data) {
                        this.isOpen = true;
                        this.isEdit = true;
                        this.formAction = data.url;
                        this.form = {
                            tahun: data.tahun,
                            semester: data.semester
                        };
                    },
                    closeModal() {
                        this.isOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
