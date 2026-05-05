<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('super-admin.berita.index') }}"
                class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center hover:bg-gray-200 transition-colors">
                <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
            </a>
            <div>
                <h2 class="text-lg font-bold text-gray-800 leading-tight">Tambah Berita</h2>
                <p class="text-xs text-gray-500">Buat berita atau informasi baru</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('super-admin.berita.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- Main Content --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul') }}" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                        placeholder="Masukkan judul berita...">
                    @error('judul')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Kategori <span class="text-red-500">*</span></label>
                        <select name="kategori" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            @foreach (['Akademik', 'Kesiswaan', 'Kegiatan', 'Prestasi', 'Pengumuman', 'Lainnya'] as $kat)
                                <option value="{{ $kat }}" {{ old('kategori') == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Ringkasan</label>
                    <textarea name="ringkasan" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400 resize-none"
                        placeholder="Ringkasan singkat berita (opsional)...">{{ old('ringkasan') }}</textarea>
                    @error('ringkasan')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Gambar Cover</label>
                    <div x-data="{ preview: null }" class="space-y-3">
                        <div class="flex items-center gap-4">
                            <label
                                class="flex-1 cursor-pointer border-2 border-dashed border-gray-200 rounded-xl p-6 hover:border-red-300 hover:bg-red-50/30 transition-all text-center group">
                                <input type="file" name="gambar" accept="image/*" class="hidden"
                                    @change="preview = URL.createObjectURL($event.target.files[0])">
                                <svg class="w-8 h-8 text-gray-300 mx-auto mb-2 group-hover:text-red-400 transition-colors" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="text-xs font-bold text-gray-500">Klik untuk upload gambar</p>
                                <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WEBP (maks 5MB)</p>
                            </label>
                        </div>
                        <template x-if="preview">
                            <img :src="preview" class="w-full max-h-48 object-cover rounded-xl border border-gray-100">
                        </template>
                    </div>
                    @error('gambar')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Konten Berita <span class="text-red-500">*</span></label>
                    <textarea name="konten" rows="12" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 placeholder-gray-400"
                        placeholder="Tulis konten berita di sini...">{{ old('konten') }}</textarea>
                    @error('konten')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('super-admin.berita.index') }}"
                    class="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold text-sm rounded-xl hover:bg-gray-200 transition-colors">
                    Batal
                </a>
                <button type="submit"
                    class="px-8 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold text-sm rounded-xl shadow-lg shadow-red-600/20 hover:shadow-red-600/40 hover:scale-[1.02] transition-all">
                    Simpan Berita
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
