<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Master Data Rombel</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Daftar Rombongan Belajar</h3>
                    <button @click="$dispatch('open-rombel-modal')"
                        class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Rombel
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tahun Ajaran</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Wali Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jumlah Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($rombel as $item)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $item->tahun_ajaran }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-900 font-semibold">
                                        {{ $item->kelas->nama_kelas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        {{ $item->waliKelas->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1 text-gray-600">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            {{ $item->siswa_count ?? 0 }} Siswa
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('master-data.rombel.show', $item->id) }}"
                                                class="inline-flex items-center px-3 py-1.5 bg-green-50 text-green-700 rounded-lg hover:bg-green-100 transition-colors text-xs font-semibold border border-green-200">
                                                <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                </svg>
                                                Kelola Siswa
                                            </a>

                                            <button
                                                @click="$dispatch('edit-rombel', {
                                                    id: '{{ $item->id }}',
                                                    tahun_ajaran: '{{ $item->tahun_ajaran }}',
                                                    kelas_id: '{{ $item->kelas_id }}',
                                                    wali_kelas_id: '{{ $item->wali_kelas_id }}',
                                                    updateUrl: '{{ route('master-data.rombel.update', $item->id) }}'
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                Edit
                                            </button>

                                            <form action="{{ route('master-data.rombel.destroy', $item->id) }}"
                                                method="POST" class="delete-form inline-block">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="inline-flex items-center px-3 py-1.5 bg-white text-red-600 rounded-lg hover:bg-red-50 hover:text-red-700 transition-colors text-xs font-semibold border border-red-200">
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <p class="text-base font-medium">Belum ada data rombel.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $rombel->links() }}
                </div>
            </div>
        </div>
    </div>

    <div x-data="rombelModalData()" @open-rombel-modal.window="openModal()" @edit-rombel.window="editModal($event.detail)"
        x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-gray-900"
                        x-text="isEdit ? 'Edit Rombel' : 'Tambah Rombel Baru'"></h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form :action="formAction" method="POST">
                    @csrf
                    <template x-if="isEdit"><input type="hidden" name="_method" value="PUT"></template>

                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tahun Ajaran</label>
                            <select name="tahun_ajaran" x-model="form.tahun_ajaran" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">-- Pilih Tahun --</option>
                                @foreach ($tahun_ajaran_list as $tahun)
                                    <option value="{{ $tahun }}">{{ $tahun }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Kelas</label>
                            <select name="kelas_id" x-model="form.kelas_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach ($kelas as $id => $nama)
                                    <option value="{{ $id }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Wali Kelas</label>
                            <select name="wali_kelas_id" x-model="form.wali_kelas_id" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                <option value="">-- Pilih Wali Kelas --</option>
                                @foreach ($wali_kelas as $id => $nama)
                                    <option value="{{ $id }}">{{ $nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-lg bg-red-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 sm:ml-3 sm:w-auto transition-colors">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Data'"></span>
                        </button>
                        <button type="button" @click="closeModal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
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
                    title: 'Apakah Anda yakin?',
                    text: "Data rombel ini akan dihapus permanen!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) button.closest('form').submit();
                });
            }

            function rombelModalData() {
                return {
                    isOpen: false,
                    isEdit: false,
                    formAction: '{{ route('master-data.rombel.store') }}',
                    form: {
                        tahun_ajaran: '',
                        kelas_id: '',
                        wali_kelas_id: ''
                    },
                    openModal() {
                        this.isOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('master-data.rombel.store') }}';
                        this.form = {
                            tahun_ajaran: '{{ $tahun_aktif }}', // Set Default ke Tahun Aktif
                            kelas_id: '',
                            wali_kelas_id: ''
                        };
                    },
                    editModal(data) {
                        this.isOpen = true;
                        this.isEdit = true;
                        this.formAction = data.updateUrl;
                        this.form = {
                            tahun_ajaran: data.tahun_ajaran,
                            kelas_id: data.kelas_id,
                            wali_kelas_id: data.wali_kelas_id
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
