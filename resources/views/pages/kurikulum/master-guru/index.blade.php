<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Master Data Guru</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">

                    <form action="{{ route('kurikulum.master-guru.index') }}" method="GET"
                        class="w-full sm:w-72 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </div>
                        <input type="text" name="search" value="{{ request('search') }}"
                            class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-indigo-500 focus:ring-indigo-500 shadow-sm"
                            placeholder="Cari NUPTK atau Nama...">
                    </form>

                    <div class="flex items-center gap-2">
                        <button @click="$dispatch('open-import-modal')"
                            class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Import Excel
                        </button>

                        <button @click="$dispatch('open-guru-modal')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-sm transition ease-in-out duration-150 gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Guru
                        </button>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Kode Guru</th>
                                <th class="px-6 py-4 font-bold tracking-wider">NUPTK</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Nama Lengkap</th>
                                <th class="px-6 py-4 font-bold tracking-wider">L/P</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status Akun</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($guru as $item)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ $item->kode_guru ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ $item->nuptk ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-900">
                                        {{ $item->nama_lengkap }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center justify-center w-6 h-6 rounded text-xs font-bold {{ $item->jenis_kelamin == 'L' ? 'bg-blue-100 text-blue-700' : 'bg-pink-100 text-pink-700' }}">
                                            {{ $item->jenis_kelamin }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if ($item->user)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-100 ring-1 ring-green-600/20">
                                                Aktif
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-100 ring-1 ring-gray-600/20">
                                                Belum Ada
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center justify-end gap-2">

                                            @if (!$item->user)
                                                <form
                                                    action="{{ route('kurikulum.master-guru.generate-akun', $item->id) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    <button type="button" onclick="confirmGenerate(this)"
                                                        class="inline-flex items-center px-2 py-1.5 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-100 border border-blue-200 text-xs font-semibold transition-colors"
                                                        title="Generate Akun">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none"
                                                            stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                                        </svg>
                                                        Akun
                                                    </button>
                                                </form>
                                            @endif

                                            <button
                                                @click="$dispatch('edit-guru', {
                                                    id: '{{ $item->id }}',
                                                    kode_guru: '{{ $item->kode_guru ?? '' }}',
                                                    nuptk: '{{ $item->nuptk ?? '' }}',
                                                    nama_lengkap: '{{ addslashes($item->nama_lengkap) }}',
                                                    jenis_kelamin: '{{ $item->jenis_kelamin }}',
                                                    updateUrl: '{{ route('kurikulum.master-guru.update', $item->id) }}'
                                                })"
                                                class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                Edit
                                            </button>

                                            <form action="{{ route('kurikulum.master-guru.destroy', $item->id) }}"
                                                method="POST" class="inline-block delete-form">
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
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Belum ada data guru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $guru->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    <div x-data="guruModalData()" @open-guru-modal.window="openModal()" @edit-guru.window="editModal($event.detail)"
        x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true">

        <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="closeModal"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                <div class="bg-gray-50 px-4 py-3 sm:px-6 border-b border-gray-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-gray-900"
                        x-text="isEdit ? 'Edit Data Guru' : 'Tambah Guru Baru'"></h3>
                    <button @click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none"><svg
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
                            <label class="block text-sm font-medium text-gray-700">Kode Guru <span
                                    class="text-gray-400 font-normal">(Opsional, angka)</span></label>
                            <input type="number" name="kode_guru" x-model="form.kode_guru" placeholder="Kode guru (angka)"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">NUPTK <span
                                    class="text-gray-400 font-normal">(Opsional)</span></label>
                            <input type="text" name="nuptk" x-model="form.nuptk" placeholder="Nomor NUPTK"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                            <input type="text" name="nama_lengkap" x-model="form.nama_lengkap" required
                                placeholder="Nama lengkap dengan gelar"
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Kelamin</label>
                            <select name="jenis_kelamin" x-model="form.jenis_kelamin" required
                                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto transition-colors">
                            <span x-text="isEdit ? 'Simpan Perubahan' : 'Simpan Data'"></span>
                        </button>
                        <button type="button" @click="closeModal"
                            class="mt-3 inline-flex w-full justify-center rounded-lg bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto transition-colors">Batal</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div x-data="{ isOpen: false }" @open-import-modal.window="isOpen = true" x-show="isOpen" style="display: none;"
        class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">

        <div x-show="isOpen" class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity"
            @click="isOpen = false"></div>

        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
            <div x-show="isOpen"
                class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-gray-100">

                <div class="bg-emerald-600 px-4 py-3 sm:px-6 flex justify-between items-center">
                    <h3 class="text-lg font-bold leading-6 text-white">Import Data Guru</h3>
                    <button @click="isOpen = false" class="text-emerald-100 hover:text-white focus:outline-none"><svg
                            class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg></button>
                </div>

                <form action="{{ route('kurikulum.master-guru.import') }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="px-4 py-5 sm:p-6 space-y-4">
                        <div class="bg-emerald-50 border border-emerald-100 rounded-lg p-3 text-sm text-emerald-800">
                            <p class="font-bold mb-1">Ketentuan File:</p>
                            <p>Gunakan file Excel (.xlsx) dengan kolom: <code
                                    class="bg-emerald-100 px-1 rounded">kode_guru</code>, <code
                                    class="bg-emerald-100 px-1 rounded">nuptk</code>, <code
                                    class="bg-emerald-100 px-1 rounded">nama_lengkap</code>, dan <code
                                    class="bg-emerald-100 px-1 rounded">jenis_kelamin</code>.</p>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Pilih File</label>
                            <input type="file" name="file_import" required accept=".xlsx, .xls, .csv"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-colors border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                        </div>
                    </div>

                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-gray-100">
                        <button type="submit"
                            class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 sm:ml-3 sm:w-auto transition-colors">
                            Mulai Import
                        </button>
                        <button type="button" @click="isOpen = false"
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
                    title: 'Hapus Data Guru?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!'
                }).then((result) => {
                    if (result.isConfirmed) button.closest('form').submit();
                });
            }

            function confirmGenerate(button) {
                Swal.fire({
                    title: 'Generate Akun?',
                    text: "Sistem akan membuat akun login default untuk guru ini.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#2563eb',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Generate!'
                }).then((result) => {
                    if (result.isConfirmed) button.closest('form').submit();
                });
            }

            function guruModalData() {
                return {
                    isOpen: false,
                    isEdit: false,
                    formAction: '{{ route('kurikulum.master-guru.store') }}',
                    form: {
                        kode_guru: '',
                        nuptk: '',
                        nama_lengkap: '',
                        jenis_kelamin: 'L'
                    },
                    openModal() {
                        this.isOpen = true;
                        this.isEdit = false;
                        this.formAction = '{{ route('kurikulum.master-guru.store') }}';
                        this.form = {
                            kode_guru: '',
                            nuptk: '',
                            nama_lengkap: '',
                            jenis_kelamin: 'L'
                        };
                    },
                    editModal(data) {
                        this.isOpen = true;
                        this.isEdit = true;
                        this.formAction = data.updateUrl;
                        this.form = {
                            kode_guru: data.kode_guru,
                            nuptk: data.nuptk,
                            nama_lengkap: data.nama_lengkap,
                            jenis_kelamin: data.jenis_kelamin
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
