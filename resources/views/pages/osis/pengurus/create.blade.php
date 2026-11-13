<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Tambah Pengurus OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Daftarkan siswa sebagai pengurus inti atau koordinator sekbid</p>
            </div>
            <a href="{{ route('osis.pengurus.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm">
                <form action="{{ route('osis.pengurus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pilih dari Master Siswa (Opsional)</label>
                        <select name="master_siswa_id" id="siswa_select" onchange="autoFillStudent(this)"
                                class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                            <option value="">-- Input Bebas / Manual --</option>
                            @foreach($students as $s)
                                <option value="{{ $s->id }}" data-nama="{{ $s->nama_lengkap }}">{{ $s->nis }} - {{ $s->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Lengkap</label>
                            <input type="text" name="nama" id="nama_input" value="{{ old('nama') }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Jabatan</label>
                            <input type="text" name="jabatan" value="{{ old('jabatan') }}" placeholder="Contoh: Sekretaris I, Koordinator Sekbid 1" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Sekbid / Bidang</label>
                            <input type="text" name="sekbid" value="{{ old('sekbid') }}" placeholder="Contoh: Badan Pengurus Harian (BPH) / Sekbid 1"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Kelas</label>
                            <input type="text" name="kelas" value="{{ old('kelas') }}" placeholder="Contoh: XI RPL 1"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Urutan Tampil (Sort)</label>
                            <input type="number" name="urutan" value="{{ old('urutan', 0) }}"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Foto Pengurus</label>
                            <input type="file" name="foto" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                        </div>
                    </div>

                    <div class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_active" id="is_active" value="1" checked class="w-4 h-4 rounded text-rose-600 focus:ring-rose-500">
                        <label for="is_active" class="text-xs font-bold text-gray-700">Status Aktif (Tampil di Web)</label>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                        <a href="{{ route('osis.pengurus.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 rounded-xl text-xs font-bold">Batal</a>
                        <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-md shadow-rose-500/20">
                            Simpan Pengurus
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function autoFillStudent(el) {
            const opt = el.options[el.selectedIndex];
            if (opt.value) {
                document.getElementById('nama_input').value = opt.getAttribute('data-nama');
            }
        }
    </script>
</x-app-layout>
