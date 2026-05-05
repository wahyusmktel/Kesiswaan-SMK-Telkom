<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="{{ route('kantin.menu.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">Katalog Menu</a>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Tambah Menu Baru</span>
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[24px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-8 py-6 text-white">
                    <h3 class="text-2xl font-black mb-1">Informasi Menu</h3>
                    <p class="text-orange-100 font-medium text-sm">Lengkapi detail menu makanan atau minuman baru.</p>
                </div>
                
                <form action="{{ route('kantin.menu.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label for="name" class="block text-sm font-bold text-gray-700">Nama Menu <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name') }}" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition-colors"
                                placeholder="Contoh: Nasi Goreng Spesial">
                            @error('name') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="category" class="block text-sm font-bold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" id="category" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition-colors">
                                <option value="makanan" {{ old('category') == 'makanan' ? 'selected' : '' }}>Makanan</option>
                                <option value="minuman" {{ old('category') == 'minuman' ? 'selected' : '' }}>Minuman</option>
                                <option value="cemilan" {{ old('category') == 'cemilan' ? 'selected' : '' }}>Cemilan / Snack</option>
                            </select>
                            @error('category') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="price" class="block text-sm font-bold text-gray-700">Harga (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price') }}" required min="0" step="1000"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 pl-10 transition-colors"
                                    placeholder="15000">
                            </div>
                            @error('price') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3 transition-colors"
                                placeholder="Jelaskan isi/komposisi dari menu ini...">{{ old('description') }}</textarea>
                            @error('description') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Foto Menu (Maksimal 5 Foto)</label>
                            
                            <div class="flex items-center justify-center w-full">
                                <label for="images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors group">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-orange-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <p class="mb-1 text-sm text-gray-500 font-bold"><span class="text-orange-500">Klik untuk unggah</span> atau seret file kesini</p>
                                        <p class="text-xs text-gray-400">PNG, JPG atau JPEG (Maks. 2MB per file)</p>
                                    </div>
                                    <input id="images" name="images[]" type="file" class="hidden" accept="image/*" multiple max="5" onchange="previewFiles(this)"/>
                                </label>
                            </div>
                            @error('images') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            @error('images.*') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            
                            <!-- Image Previews -->
                            <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4 empty:hidden"></div>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" class="sr-only peer" checked>
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-orange-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-sm font-bold text-gray-700">Tersedia untuk Dipesan</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('kantin.menu.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Batal</a>
                        <button type="submit" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-orange-500/30">Simpan Menu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewFiles(input) {
            const previewContainer = document.getElementById('image-preview-container');
            previewContainer.innerHTML = '';
            
            if (input.files && input.files.length > 0) {
                if(input.files.length > 5) {
                    alert('Maksimal hanya 5 foto yang diizinkan!');
                    input.value = ''; // Reset
                    return;
                }
                
                Array.from(input.files).forEach((file, index) => {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imgDiv = document.createElement('div');
                        imgDiv.className = 'relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 group';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';
                        
                        const badge = document.createElement('div');
                        badge.className = 'absolute top-1 left-1 bg-black/60 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg';
                        badge.innerText = 'Foto ' + (index + 1);
                        
                        imgDiv.appendChild(img);
                        imgDiv.appendChild(badge);
                        previewContainer.appendChild(imgDiv);
                    }
                    reader.readAsDataURL(file);
                });
            }
        }
    </script>
</x-app-layout>
