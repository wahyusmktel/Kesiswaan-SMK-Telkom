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
                <h2 class="text-lg font-bold text-gray-800 leading-tight">Edit Berita</h2>
                <p class="text-xs text-gray-500">{{ Str::limit($berita->judul, 50) }}</p>
            </div>
        </div>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <form action="{{ route('super-admin.berita.update', $berita) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- Main Content --}}
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Judul Berita <span class="text-red-500">*</span></label>
                    <input type="text" name="judul" value="{{ old('judul', $berita->judul) }}" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
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
                                <option value="{{ $kat }}" {{ old('kategori', $berita->kategori) == $kat ? 'selected' : '' }}>{{ $kat }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Status <span class="text-red-500">*</span></label>
                        <select name="status" required
                            class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500">
                            <option value="draft" {{ old('status', $berita->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                            <option value="published" {{ old('status', $berita->status) == 'published' ? 'selected' : '' }}>Published</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Ringkasan</label>
                    <textarea name="ringkasan" rows="2"
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500 resize-none"
                        placeholder="Ringkasan singkat berita (opsional)...">{{ old('ringkasan', $berita->ringkasan) }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Gambar Cover</label>
                    <div x-data="{ preview: '{{ $berita->gambar ? Storage::url($berita->gambar) : '' }}' }" class="space-y-3">
                        @if ($berita->gambar)
                            <div class="relative">
                                <img :src="preview" class="w-full max-h-48 object-cover rounded-xl border border-gray-100">
                            </div>
                        @endif
                        <label
                            class="flex-1 cursor-pointer border-2 border-dashed border-gray-200 rounded-xl p-4 hover:border-red-300 hover:bg-red-50/30 transition-all text-center group">
                            <input type="file" name="gambar" accept="image/*" class="hidden"
                                @change="preview = URL.createObjectURL($event.target.files[0])">
                            <p class="text-xs font-bold text-gray-500">{{ $berita->gambar ? 'Ganti gambar' : 'Upload gambar' }}</p>
                            <p class="text-[10px] text-gray-400 mt-1">JPEG, PNG, WEBP (maks 5MB)</p>
                        </label>
                        <template x-if="preview && !'{{ $berita->gambar }}'">
                            <img :src="preview" class="w-full max-h-48 object-cover rounded-xl border border-gray-100">
                        </template>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-black text-gray-500 uppercase tracking-widest mb-2">Konten Berita <span class="text-red-500">*</span></label>
                    <textarea name="konten" rows="12" required
                        class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm font-medium focus:ring-2 focus:ring-red-500 focus:border-red-500"
                        placeholder="Tulis konten berita di sini...">{{ old('konten', $berita->konten) }}</textarea>
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
                    Perbarui Berita
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
