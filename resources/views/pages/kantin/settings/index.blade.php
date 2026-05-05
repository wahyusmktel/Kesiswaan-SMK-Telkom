<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight flex items-center gap-2">
            <svg class="w-6 h-6 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>Pengaturan Kantin</span>
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-6 py-4 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            @endif

            <div class="bg-white rounded-[32px] shadow-xl border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-orange-500 to-amber-500 px-8 py-8 text-white relative overflow-hidden">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full blur-3xl pointer-events-none -translate-y-1/2 translate-x-1/3"></div>
                    <h3 class="text-3xl font-black mb-2 relative z-10">Profil & Pengaturan Kantin</h3>
                    <p class="text-orange-100 font-medium relative z-10">Informasi ini akan ditampilkan kepada siswa saat melihat menu kantin Anda.</p>
                </div>
                
                <form action="{{ route('kantin.settings.update') }}" method="POST" enctype="multipart/form-data" class="p-8">
                    @csrf
                    @method('PATCH')
                    
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                        {{-- Left Column: Image Banner --}}
                        <div class="lg:col-span-4 space-y-6">
                            <div class="bg-gray-50 rounded-[24px] p-6 border border-gray-100 text-center relative overflow-hidden group">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Foto / Banner Kantin</h4>
                                
                                <div class="relative w-full aspect-video rounded-xl overflow-hidden bg-gray-200 mb-4 border-2 border-dashed border-gray-300">
                                    @if($profile->banner_image)
                                        <img id="banner-preview" src="{{ asset('storage/' . $profile->banner_image) }}" class="w-full h-full object-cover">
                                    @else
                                        <img id="banner-preview" src="" class="w-full h-full object-cover hidden">
                                        <div id="banner-placeholder" class="absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                            <span class="text-sm font-bold">Belum Ada Banner</span>
                                        </div>
                                    @endif
                                </div>
                                
                                <label for="banner_image" class="w-full inline-flex justify-center items-center gap-2 px-4 py-3 bg-white border border-gray-200 text-gray-700 hover:bg-gray-50 font-bold rounded-xl cursor-pointer transition-colors shadow-sm">
                                    <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                    Ubah Banner
                                </label>
                                <input type="file" id="banner_image" name="banner_image" accept="image/*" class="hidden" onchange="previewImage(this)">
                                @error('banner_image') <span class="block text-red-500 text-xs font-bold mt-2">{{ $message }}</span> @enderror
                            </div>
                            
                            {{-- Operational Status --}}
                            <div class="bg-gray-50 rounded-[24px] p-6 border border-gray-100 text-center">
                                <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Status Operasional</h4>
                                <label class="relative inline-flex items-center cursor-pointer justify-center w-full">
                                    <input type="checkbox" name="is_open" value="1" class="sr-only peer" {{ old('is_open', $profile->is_open) ? 'checked' : '' }}>
                                    <div class="w-16 h-8 bg-red-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-emerald-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[4px] after:left-[6px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-emerald-500"></div>
                                    <span class="ml-4 text-base font-black text-gray-900 peer-checked:text-emerald-600">Buka / Melayani</span>
                                </label>
                                <p class="text-xs text-gray-500 mt-3">Matikan jika kantin sedang tutup agar siswa tidak bisa memesan.</p>
                            </div>
                        </div>

                        {{-- Right Column: Form Details --}}
                        <div class="lg:col-span-8 space-y-6">
                            <div class="space-y-2">
                                <label for="name" class="block text-sm font-bold text-gray-700">Nama Kantin / Lapak <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                                    </div>
                                    <input type="text" name="name" id="name" value="{{ old('name', $profile->name) }}" required
                                        class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3.5 pl-11 transition-colors"
                                        placeholder="Contoh: Kantin Sehat Ibu Siti">
                                </div>
                                @error('name') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="phone_number" class="block text-sm font-bold text-gray-700">Nomor WhatsApp / HP</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                    </div>
                                    <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number', $profile->phone_number) }}"
                                        class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-3.5 pl-11 transition-colors"
                                        placeholder="Contoh: 08123456789">
                                </div>
                                @error('phone_number') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-2">
                                <label for="description" class="block text-sm font-bold text-gray-700">Tentang Kantin (DeskripsiSingkat)</label>
                                <textarea name="description" id="description" rows="4"
                                    class="w-full bg-gray-50 border border-gray-200 text-gray-900 rounded-xl focus:ring-orange-500 focus:border-orange-500 block p-4 transition-colors"
                                    placeholder="Ceritakan sedikit tentang kantin Anda, jam operasional khusus, atau keunggulan menu Anda...">{{ old('description', $profile->description) }}</textarea>
                                @error('description') <span class="text-red-500 text-xs font-bold">{{ $message }}</span> @enderror
                            </div>

                            <div class="pt-6 flex justify-end">
                                <button type="submit" class="px-8 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-black rounded-xl transition-all shadow-xl shadow-orange-500/30 flex items-center gap-2 transform hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                    Simpan Pengaturan
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('banner-preview');
                    const placeholder = document.getElementById('banner-placeholder');
                    
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    if(placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</x-app-layout>
