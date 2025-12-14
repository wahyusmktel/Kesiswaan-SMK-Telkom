<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Kelola Rombel: {{ $rombel->kelas->nama_kelas }}
            </h2>
            <div class="flex items-center gap-4 text-sm">
                <span
                    class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full font-semibold">{{ $rombel->tahun_ajaran }}</span>
                <span class="text-gray-500">Wali Kelas: <span
                        class="font-semibold text-gray-700">{{ $rombel->waliKelas->name }}</span></span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden sticky top-24">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                            <h3 class="font-bold text-gray-800">Tambah Siswa</h3>
                            <p class="text-xs text-gray-500">Masukkan siswa ke rombel ini</p>
                        </div>
                        <div class="p-6">
                            <form action="{{ route('master-data.rombel.add-siswa', $rombel->id) }}" method="POST">
                                @csrf
                                <div>
                                    <label for="siswa_ids" class="block text-sm font-medium text-gray-700 mb-2">Pilih
                                        Siswa (Tersedia)</label>
                                    <select name="siswa_ids[]" id="siswa_ids" multiple
                                        class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm h-64">
                                        @forelse ($siswaTersedia as $siswa)
                                            <option value="{{ $siswa->id }}"
                                                class="py-1 px-2 hover:bg-red-50 cursor-pointer">
                                                {{ $siswa->nis }} - {{ $siswa->nama_lengkap }}
                                            </option>
                                        @empty
                                            <option disabled class="text-gray-400 italic p-2 text-center">-- Semua siswa
                                                sudah masuk rombel --</option>
                                        @endforelse
                                    </select>
                                    <p class="mt-2 text-xs text-gray-500 flex items-center">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Tahan tombol <span class="font-bold mx-1">CTRL</span> (Windows) atau <span
                                            class="font-bold mx-1">CMD</span> (Mac) untuk memilih banyak.
                                    </p>
                                </div>
                                <div class="mt-4 pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-red-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none shadow-md transition ease-in-out duration-150 gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4v16m8-8H4" />
                                        </svg>
                                        Tambahkan ke Rombel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800">Daftar Siswa</h3>
                            <span
                                class="px-3 py-1 bg-white border border-gray-200 rounded-full text-xs font-bold text-gray-600 shadow-sm">
                                Total: {{ $siswaDiRombel->count() }}
                            </span>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left text-gray-500">
                                <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                                    <tr>
                                        <th class="px-6 py-4 font-bold tracking-wider w-20">No</th>
                                        <th class="px-6 py-4 font-bold tracking-wider">NIS</th>
                                        <th class="px-6 py-4 font-bold tracking-wider">Nama Lengkap</th>
                                        <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($siswaDiRombel as $siswa)
                                        <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                            <td class="px-6 py-3 text-center font-mono text-gray-400">
                                                {{ $loop->iteration }}</td>
                                            <td class="px-6 py-3 font-mono text-gray-900">{{ $siswa->nis }}</td>
                                            <td class="px-6 py-3 font-semibold text-gray-800">
                                                {{ $siswa->nama_lengkap }}</td>
                                            <td class="px-6 py-3 text-right">
                                                <form
                                                    action="{{ route('master-data.rombel.remove-siswa', ['rombel' => $rombel->id, 'siswa' => $siswa->id]) }}"
                                                    method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="button" onclick="confirmRemove(this)"
                                                        class="text-red-500 hover:text-red-700 font-medium text-xs border border-red-200 bg-red-50 px-3 py-1.5 rounded-lg transition-colors hover:bg-red-100">
                                                        Keluarkan
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4"
                                                class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                                    </svg>
                                                    <p class="text-base font-medium">Belum ada siswa di rombel ini.</p>
                                                    <p class="text-xs mt-1">Gunakan form di sebelah kiri untuk
                                                        menambahkan.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function confirmRemove(button) {
                Swal.fire({
                    title: 'Keluarkan Siswa?',
                    text: "Siswa ini akan dihapus dari rombel, tapi data siswa tetap ada.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc2626',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Keluarkan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        button.closest('form').submit();
                    }
                });
            }
        </script>
    @endpush
</x-app-layout>
