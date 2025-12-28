<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Buat Nota Dinas Baru</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden p-8">
                <form action="{{ route('shared.nde.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Jenis Nota Dinas</label>
                            <select name="jenis_id" required class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-red-500 focus:border-red-500 font-medium">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisNde as $jenis)
                                    <option value="{{ $jenis->id }}">{{ $jenis->nama }} ({{ $jenis->kode }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Tanggal</label>
                            <input type="date" name="tanggal" required value="{{ date('Y-m-d') }}"
                                class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-red-500 focus:border-red-500 font-medium">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Perihal</label>
                        <input type="text" name="perihal" required placeholder="Masukkan perihal nota dinas"
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-red-500 focus:border-red-500 font-medium text-lg font-bold">
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Penerima</label>
                        <div class="bg-gray-50 rounded-2xl border border-gray-200 p-4 max-h-60 overflow-y-auto grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($users as $user)
                                <label class="flex items-center gap-3 p-3 bg-white rounded-xl border border-gray-100 hover:border-red-200 cursor-pointer transition-all transition-all">
                                    <input type="checkbox" name="penerima_ids[]" value="{{ $user->id }}" class="rounded text-red-600 focus:ring-red-500 border-gray-300 w-5 h-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800">{{ $user->name }}</span>
                                        <span class="text-[10px] text-gray-400 uppercase font-bold">{{ $user->roles->pluck('name')->join(', ') }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('penerima_ids')
                            <p class="mt-1 text-red-500 text-xs font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Isi Nota Dinas</label>
                        <textarea name="isi" required rows="10" placeholder="Tuliskan isi nota dinas di sini..."
                            class="w-full px-4 py-3 rounded-2xl bg-gray-50 border-gray-200 focus:ring-red-500 focus:border-red-500 font-medium"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-widest text-gray-500 mb-2">Lampiran (Opsional)</label>
                        <div class="relative group">
                            <input type="file" name="lampiran" id="lampiran" class="hidden" onchange="updateFileName(this)">
                            <label for="lampiran" class="flex items-center gap-4 p-4 border-2 border-dashed border-gray-200 rounded-2xl cursor-pointer group-hover:border-red-400 transition-colors">
                                <div class="p-3 bg-gray-100 text-gray-400 rounded-xl group-hover:bg-red-50 group-hover:text-red-600 transition-colors">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                </div>
                                <div>
                                    <p id="file-name" class="font-bold text-gray-600">Klik untuk unggah lampiran</p>
                                    <p class="text-xs text-gray-400">PDF, JPG, PNG, DOCX, XLSX (Max 5MB)</p>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 border-t border-gray-100">
                        <a href="{{ route('shared.nde.index') }}" 
                            class="flex-1 px-6 py-4 border border-gray-200 text-gray-600 rounded-2xl font-bold text-center hover:bg-gray-50 transition-colors">Batal</a>
                        <button type="submit" 
                            class="flex-1 px-6 py-4 bg-red-600 text-white rounded-2xl font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-100">Kirim Nota Dinas</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : 'Klik untuk unggah lampiran';
            document.getElementById('file-name').innerText = fileName;
        }
    </script>
</x-app-layout>
