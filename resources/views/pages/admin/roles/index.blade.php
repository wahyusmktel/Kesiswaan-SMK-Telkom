<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Manajemen Hak Akses (Role)</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="roleModalData()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">

                    <form action="{{ route('admin.roles.index') }}" method="GET" class="w-full sm:w-72 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="Cari Role...">
                    </form>

                    <button @click="openModal()"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Role
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Nama Role</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Guard</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jumlah Pengguna</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($roles as $role)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4 font-bold text-gray-800">
                                        {{ $role->name }}
                                        @if ($role->name == 'Super Admin')
                                            <span
                                                class="ml-2 px-2 py-0.5 rounded text-[10px] bg-red-100 text-red-600 border border-red-200 uppercase">System</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500 bg-gray-50/50 w-32">
                                        {{ $role->guard_name }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $role->users_count }} User
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if ($role->name !== 'Super Admin')
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    @click="editModal({ id: '{{ $role->id }}', name: '{{ $role->name }}' })"
                                                    class="text-gray-400 hover:text-indigo-600 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                    </svg>
                                                </button>

                                                <form action="{{ route('admin.roles.destroy', $role->id) }}"
                                                    method="POST" onsubmit="return confirmDelete(event)">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit"
                                                        class="text-gray-400 hover:text-red-600 transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <span class="text-xs text-gray-400 italic">Protected</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada role.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $roles->links() }}
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

                    <div class="bg-indigo-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                        <h3 class="text-lg font-bold leading-6 text-white"
                            x-text="isEdit ? 'Edit Role' : 'Tambah Role Baru'"></h3>
                        <button @click="isOpen = false" class="text-indigo-100 hover:text-white focus:outline-none"><svg
                                class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg></button>
                    </div>

                    <form :action="formAction" method="POST">
                        @csrf
                        <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Nama Role</label>
                                <input type="text" name="name" x-model="form.name" required
                                    placeholder="Contoh: Staff Gudang"
                                    class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                            <button type="submit"
                                class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                                Simpan
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
            function confirmDelete(e) {
                e.preventDefault();
                Swal.fire({
                    title: 'Hapus Role?',
                    text: "Pastikan tidak ada user yang menggunakan role ini.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) e.target.submit();
                });
            }

            function roleModalData() {
                return {
                    isOpen: false,
                    isEdit: false,
                    formAction: '{{ route('admin.roles.store') }}',
                    form: {
                        name: ''
                    },
                    openModal() {
                        this.isOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('admin.roles.store') }}';
                        this.form = {
                            name: ''
                        };
                    },
                    editModal(data) {
                        this.isOpen = true;
                        this.isEdit = true;
                        // Hati-hati dengan URL, pastikan prefix admin.roles sesuai
                        this.formAction = '{{ url('admin/roles') }}/' + data.id;
                        this.form = {
                            name: data.name
                        };
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
