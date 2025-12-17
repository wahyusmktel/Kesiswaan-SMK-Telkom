<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Manajemen Pengguna</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Daftar Pengguna</h3>
                    <button @click="$dispatch('open-user-modal')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
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
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Pengguna</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider">Peran (Role)</th>
                                <th scope="col" class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($users as $user)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div
                                                class="flex-shrink-0 h-10 w-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full flex items-center justify-center border border-gray-300 text-gray-600 font-bold text-sm">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-semibold text-gray-900">{{ $user->name }}
                                                </div>
                                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse ($user->getRoleNames() as $role)
                                                <span
                                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-100 shadow-sm">
                                                    {{ $role }}
                                                </span>
                                            @empty
                                                <span class="text-gray-400 italic text-xs">Tidak ada peran</span>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                @click="$dispatch('edit-user', {
                                                    id: '{{ $user->id }}',
                                                    name: '{{ addslashes($user->name) }}',
                                                    email: '{{ $user->email }}',
                                                    roles: {{ json_encode($user->getRoleNames()) }}, 
                                                    updateUrl: '{{ route('users.update', $user->id) }}'
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                                Edit
                                            </button>

                                            <form action="{{ route('users.destroy', $user->id) }}" method="POST"
                                                class="inline-block delete-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-white text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors text-xs font-semibold border border-red-200">
                                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Belum ada data pengguna.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($users->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div x-data="userModalData()" x-init="initTomSelect()" @open-user-modal.window="openModal()"
        @edit-user.window="editModal($event.detail)" x-show="isOpen" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="isOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen" x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                class="relative transform overflow-visible rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-gray-900"
                        x-text="isEdit ? 'Edit Pengguna & Peran' : 'Tambah Pengguna Baru'"></h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEdit">
                        <input type="hidden" name="_method" value="PUT">
                    </template>

                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="name" x-model="form.name" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Email</label>
                            <input type="email" name="email" x-model="form.email" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Peran (Role)</label>
                            <select id="roles-select" name="roles[]" multiple placeholder="Pilih Peran..."
                                autocomplete="off">
                                @foreach ($roles as $role)
                                    <option value="{{ $role }}">{{ $role }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Bisa memilih lebih dari satu peran.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Password</label>
                            <input type="password" name="password" id="password"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="******">
                            <p x-show="isEdit" class="mt-1 text-xs text-gray-500">Kosongkan jika tidak ingin
                                mengganti.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"
                                placeholder="******">
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Buat Pengguna'"></span>
                        </button>
                        <button type="button" @click="closeModal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- TomSelect CSS & JS --}}
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            /* Container Input */
            .ts-control {
                border-radius: 0.5rem;
                padding: 0.5rem 0.75rem;
                border-color: #d1d5db;
                z-index: 10;
            }

            .ts-control.focus {
                border-color: #ef4444;
                box-shadow: 0 0 0 1px #ef4444;
            }

            /* Chips/Badge Item */
            .ts-control .item {
                background-color: #fef2f2;
                color: #b91c1c;
                border: 1px solid #fecaca;
                border-radius: 9999px;
                padding: 2px 10px;
            }

            /* Dropdown Menu - WAJIB Z-INDEX TINGGI */
            .ts-dropdown {
                z-index: 999999 !important;
                border-radius: 0.5rem;
                box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                margin-top: 4px;
            }
        </style>
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>

        <script>
            function confirmDelete(button) {
                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data pengguna ini akan dihapus secara permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }

            function userModalData() {
                return {
                    isOpen: false,
                    isEdit: false,
                    formAction: '{{ route('users.store') }}',
                    form: {
                        name: '',
                        email: '',
                    },
                    tomSelect: null,

                    initTomSelect() {
                        this.tomSelect = new TomSelect("#roles-select", {
                            plugins: ['remove_button'],
                            create: false,
                            // KUNCI UTAMA: Render dropdown di body agar tidak terpotong modal
                            dropdownParent: 'body'
                        });
                    },

                    openModal() {
                        this.isOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('users.store') }}';
                        this.form = {
                            name: '',
                            email: '',
                        };

                        // Reset TomSelect
                        if (this.tomSelect) {
                            this.tomSelect.clear();
                        }

                        document.getElementById('password').value = '';
                        document.getElementById('password_confirmation').value = '';
                    },

                    editModal(user) {
                        this.isOpen = true;
                        this.isEdit = true;
                        this.formAction = user.updateUrl;
                        this.form = {
                            name: user.name,
                            email: user.email,
                        };

                        // Set nilai role ke TomSelect
                        if (this.tomSelect) {
                            this.tomSelect.clear();
                            // user.roles dikirim dari controller sebagai array ['Admin', 'Guru']
                            this.tomSelect.setValue(user.roles);
                        }

                        document.getElementById('password').value = '';
                        document.getElementById('password_confirmation').value = '';
                    },

                    closeModal() {
                        this.isOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
