<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Manajemen Aturan & Poin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            {{-- Bagian Kategori --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Kategori Peraturan</h3>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-category')" 
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-bold shadow-sm">
                        + Tambah Kategori
                    </button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($categories as $category)
                    <div class="p-4 rounded-xl border border-gray-100 bg-gray-50 flex flex-col justify-between">
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $category->name }}</h4>
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $category->description ?? 'Tidak ada deskripsi' }}</p>
                            <span class="inline-block mt-2 px-2 py-0.5 rounded bg-red-100 text-red-600 text-[10px] font-bold">
                                {{ $category->peraturans->count() }} Peraturan
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Bagian Peraturan --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-bold text-gray-800">Daftar Pasal & Ayat (Pasal Tata Tertib)</h3>
                    <button x-data="" x-on:click.prevent="$dispatch('open-modal', 'add-regulation')" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors text-sm font-bold shadow-sm">
                        + Tambah Peraturan
                    </button>
                </div>

                <div class="overflow-x-auto rounded-xl border border-gray-100">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-600 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Pasal/Ayat</th>
                                <th class="px-4 py-3">Deskripsi</th>
                                <th class="px-4 py-3 text-center">Poin</th>
                                <th class="px-4 py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @foreach($peraturans as $reg)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-md bg-gray-100 text-gray-600 text-[10px] font-bold">
                                        {{ $reg->category->name }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-semibold text-gray-700">
                                    {{ $reg->pasal }} {{ $reg->ayat ? '/ '.$reg->ayat : '' }}
                                </td>
                                <td class="px-4 py-3 text-gray-600 max-w-md">
                                    {{ $reg->deskripsi }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="px-2 py-1 rounded-full bg-red-50 text-red-600 font-bold">
                                        {{ $reg->bobot_poin }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <div class="flex justify-end gap-2">
                                        <button class="text-blue-600 hover:text-blue-800">Edit</button>
                                        <form action="{{ route('kesiswaan.poin-peraturan.destroy', $reg->id) }}" method="POST" onsubmit="return confirm('Hapus peraturan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-800">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4 text-xs">
                    {{ $peraturans->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <x-modal name="add-category" focusable>
        <form method="post" action="{{ route('kesiswaan.poin-peraturan.storeCategory') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Tambah Kategori Baru</h2>
            <div class="mt-6">
                <x-input-label for="name" value="Nama Kategori" />
                <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" required placeholder="Contoh: Kedisiplinan" />
            </div>
            <div class="mt-6">
                <x-input-label for="description" value="Deskripsi" />
                <textarea id="description" name="description" class="mt-1 block w-full border-gray-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm"></textarea>
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-danger-button class="ms-3">Simpan Kategori</x-danger-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="add-regulation" focusable>
        <form method="post" action="{{ route('kesiswaan.poin-peraturan.store') }}" class="p-6">
            @csrf
            <h2 class="text-lg font-medium text-gray-900">Tambah Peraturan/Pasal Baru</h2>
            <div class="mt-6">
                <x-input-label for="poin_category_id" value="Kategori" />
                <select id="poin_category_id" name="poin_category_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                    <option value="">Pilih Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4 mt-6">
                <div>
                    <x-input-label for="pasal" value="Pasal" />
                    <x-text-input id="pasal" name="pasal" type="text" class="mt-1 block w-full" required placeholder="Contoh: Pasal 1" />
                </div>
                <div>
                    <x-input-label for="ayat" value="Ayat (Opsional)" />
                    <x-text-input id="ayat" name="ayat" type="text" class="mt-1 block w-full" placeholder="Contoh: Ayat 1" />
                </div>
            </div>
            <div class="mt-6">
                <x-input-label for="deskripsi" value="Isi Peraturan / Deskripsi Pelanggaran" />
                <textarea id="deskripsi" name="deskripsi" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required></textarea>
            </div>
            <div class="mt-6">
                <x-input-label for="bobot_poin" value="Bobot Poin Pelanggaran" />
                <x-text-input id="bobot_poin" name="bobot_poin" type="number" class="mt-1 block w-full" required min="0" />
            </div>
            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button>
                <x-primary-button class="ms-3 bg-indigo-600 hover:bg-indigo-700">Simpan Peraturan</x-primary-button>
            </div>
        </form>
    </x-modal>

</x-app-layout>
