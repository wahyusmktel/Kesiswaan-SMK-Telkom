<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <a href="{{ route('kantin.menu.index') }}" class="text-gray-400 hover:text-gray-600 transition-colors">Katalog Menu</a>
            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            <span>Edit Menu</span>
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-[24px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 to-indigo-500 px-8 py-6 text-white">
                    <h3 class="text-2xl font-black mb-1">Edit Menu: {{ $menu->name }}</h3>
                    <p class="text-blue-100 font-medium text-sm">Ubah detail, harga, atau foto menu.</p>
                </div>
                
                <form action="{{ route('kantin.menu.update', $menu->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2 md:col-span-2">
                            <label for="name" class="block text-sm font-bold text-gray-700">Nama Menu <span class="text-red-500">*</span></label>
                            <input type="text" name="name" id="name" value="{{ old('name', $menu->name) }}" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors">
                            @error('name') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="category" class="block text-sm font-bold text-gray-700">Kategori <span class="text-red-500">*</span></label>
                            <select name="category" id="category" required
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors">
                                <option value="makanan" {{ old('category', $menu->category) == 'makanan' ? 'selected' : '' }}>Makanan</option>
                                <option value="minuman" {{ old('category', $menu->category) == 'minuman' ? 'selected' : '' }}>Minuman</option>
                                <option value="cemilan" {{ old('category', $menu->category) == 'cemilan' ? 'selected' : '' }}>Cemilan / Snack</option>
                            </select>
                            @error('category') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2">
                            <label for="price" class="block text-sm font-bold text-gray-700">Harga (Rp) <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <span class="text-gray-500 font-bold">Rp</span>
                                </div>
                                <input type="number" name="price" id="price" value="{{ old('price', (int)$menu->price) }}" required min="0" step="1000"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 pl-10 transition-colors">
                            </div>
                            @error('price') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label for="description" class="block text-sm font-bold text-gray-700">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-blue-500 focus:border-blue-500 block p-3 transition-colors">{{ old('description', $menu->description) }}</textarea>
                            @error('description') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div class="space-y-4 md:col-span-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Foto Menu Saat Ini</label>
                            
                            @if($menu->images && count($menu->images) > 0)
                            <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                @foreach($menu->images as $index => $image)
                                <div class="relative aspect-square rounded-xl overflow-hidden border-2 border-gray-200 group">
                                    <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <label class="cursor-pointer bg-red-500 text-white px-3 py-1.5 rounded-lg text-xs font-bold hover:bg-red-600 shadow-lg">
                                            <input type="checkbox" name="remove_images[{{ $index }}]" value="1" class="hidden">
                                            <span class="checkbox-label">Hapus Foto</span>
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            <p class="text-xs text-gray-500 mt-2">* Hover foto dan klik "Hapus Foto" jika ingin membuangnya.</p>
                            @else
                                <p class="text-sm text-gray-500 italic">Belum ada foto yang diunggah.</p>
                            @endif

                            <div class="mt-6 border-t border-gray-100 pt-6">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tambah Foto Baru <span class="text-gray-400 font-normal">(Sisa slot: {{ 5 - ($menu->images ? count($menu->images) : 0) }})</span></label>
                                
                                <div class="flex items-center justify-center w-full">
                                    <label for="images" class="flex flex-col items-center justify-center w-full h-40 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:bg-gray-100 transition-colors group">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <p class="mb-1 text-sm text-gray-500 font-bold"><span class="text-blue-500">Klik untuk unggah</span> atau seret file kesini</p>
                                        </div>
                                        <input id="images" name="images[]" type="file" class="hidden" accept="image/*" multiple max="5" onchange="previewFiles(this)"/>
                                    </label>
                                </div>
                                @error('images') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                                @error('images.*') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                                
                                <!-- Image Previews -->
                                <div id="image-preview-container" class="grid grid-cols-2 md:grid-cols-5 gap-4 mt-4 empty:hidden"></div>
                            </div>
                        </div>

                        <div class="space-y-2 md:col-span-2">
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_available" value="1" class="sr-only peer" {{ old('is_available', $menu->is_available) ? 'checked' : '' }}>
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                                <span class="ml-3 text-sm font-bold text-gray-700">Tersedia untuk Dipesan</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('kantin.menu.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Batal</a>
                        <button type="submit" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-500/30">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Toggle text hapus foto on click
        document.querySelectorAll('input[name^="remove_images"]').forEach(input => {
            input.addEventListener('change', function() {
                const label = this.nextElementSibling;
                if(this.checked) {
                    label.innerText = 'Dibatalkan';
                    this.parentElement.classList.replace('bg-red-500', 'bg-gray-500');
                    this.parentElement.classList.replace('hover:bg-red-600', 'hover:bg-gray-600');
                } else {
                    label.innerText = 'Hapus Foto';
                    this.parentElement.classList.replace('bg-gray-500', 'bg-red-500');
                    this.parentElement.classList.replace('hover:bg-gray-600', 'hover:bg-red-600');
                }
            });
        });

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
                        imgDiv.className = 'relative aspect-square rounded-xl overflow-hidden border-2 border-green-500 group';
                        
                        const img = document.createElement('img');
                        img.src = e.target.result;
                        img.className = 'w-full h-full object-cover';
                        
                        const badge = document.createElement('div');
                        badge.className = 'absolute top-1 left-1 bg-green-500 text-white text-[10px] font-bold px-2 py-0.5 rounded-lg';
                        badge.innerText = 'Baru ' + (index + 1);
                        
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
