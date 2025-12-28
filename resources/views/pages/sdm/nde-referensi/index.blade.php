<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Master Referensi Jenis NDE</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="text-2xl font-black text-gray-800">Manajemen Jenis NDE</h3>
                    <p class="text-gray-500">Kelola master data jenis nota dinas elektronik.</p>
                </div>
                <button onclick="document.getElementById('modal-add').classList.remove('hidden')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-2xl font-bold flex items-center gap-2 transition-all shadow-lg shadow-indigo-200">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Tambah Jenis
                </button>
            </div>

            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-[10px] tracking-widest">
                            <tr>
                                <th class="px-6 py-4">Nama Jenis</th>
                                <th class="px-6 py-4">Kode</th>
                                <th class="px-6 py-4 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($jenisNde as $jenis)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="px-6 py-4 font-bold text-gray-900">{{ $jenis->nama }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md font-mono text-xs">{{ $jenis->kode }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex justify-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button onclick="openEditModal({{ $jenis->id }}, '{{ $jenis->nama }}', '{{ $jenis->kode }}')" 
                                                class="p-2 text-indigo-600 hover:bg-indigo-50 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <form action="{{ route('sdm.nde-referensi.destroy', $jenis->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus jenis NDE ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                            </div>
                                            <p class="text-gray-400 font-medium">Belum ada data referensi NDE.</p>
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

    {{-- Modal Add --}}
    <div id="modal-add" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('modal-add').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-gray-800">Tambah Jenis NDE</h3>
                    <button onclick="document.getElementById('modal-add').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form action="{{ route('sdm.nde-referensi.store') }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Nama Jenis</label>
                        <input type="text" name="nama" required placeholder="Contoh: Surat Tugas"
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Kode</label>
                        <input type="text" name="kode" required placeholder="Contoh: ST"
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('modal-add').classList.add('hidden')"
                            class="flex-1 px-4 py-3 border border-gray-200 text-gray-600 rounded-2xl font-bold hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal Edit --}}
    <div id="modal-edit" class="fixed inset-0 z-50 overflow-y-auto hidden">
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" onclick="document.getElementById('modal-edit').classList.add('hidden')"></div>
            <div class="relative bg-white rounded-3xl shadow-2xl max-w-lg w-full p-8 overflow-hidden">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-black text-gray-800">Edit Jenis NDE</h3>
                    <button onclick="document.getElementById('modal-edit').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form id="form-edit" method="POST" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Nama Jenis</label>
                        <input type="text" name="nama" id="edit-nama" required
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                    </div>
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Kode</label>
                        <input type="text" name="kode" id="edit-kode" required
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-indigo-500 focus:border-indigo-500 font-medium">
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('modal-edit').classList.add('hidden')"
                            class="flex-1 px-4 py-3 border border-gray-200 text-gray-600 rounded-2xl font-bold hover:bg-gray-50 transition-colors">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-3 bg-indigo-600 text-white rounded-2xl font-bold hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100">Perbarui</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openEditModal(id, nama, kode) {
            const form = document.getElementById('form-edit');
            form.action = `/sdm/nde-referensi/${id}`;
            document.getElementById('edit-nama').value = nama;
            document.getElementById('edit-kode').value = kode;
            document.getElementById('modal-edit').classList.remove('hidden');
        }
    </script>
</x-app-layout>
