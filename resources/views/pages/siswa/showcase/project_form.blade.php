<x-app-layout>
    <div class="py-12 w-full flex flex-col bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 w-full">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">{{ isset($project) ? 'Edit Project' : 'Upload Project Karya' }}</h2>
                    <p class="text-gray-500 font-medium mt-1">Tambahkan hasil karya terbaikmu ke dalam galeri portofolio.</p>
                </div>
                <a href="{{ route('siswa.showcase.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                </a>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8 md:p-10">
                <form action="{{ isset($project) ? route('siswa.showcase.project.update', $project->id) : route('siswa.showcase.project.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @if(isset($project)) @method('PUT') @endif
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Left Column --}}
                        <div class="space-y-6">
                            <div>
                                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Project / Karya <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="title" value="{{ old('title', $project->title ?? '') }}" required class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Cth: Sistem Presensi Wajah (Face Recognition)">
                                @error('title') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Karya</label>
                                <textarea name="description" id="description" rows="5" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="Ceritakan tentang project ini, teknologi yang digunakan, atau latar belakang pembuatannya...">{{ old('description', $project->description ?? '') }}</textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="space-y-4">
                                <h4 class="font-bold text-gray-900 border-b border-gray-100 pb-2">Tautan Eksternal (Opsional)</h4>
                                
                                <div>
                                    <label for="project_url" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                        URL Demo / Preview Project
                                    </label>
                                    <input type="url" name="project_url" id="project_url" value="{{ old('project_url', $project->project_url ?? '') }}" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="https://contoh-karya.com">
                                </div>

                                <div>
                                    <label for="github_url" class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-700" fill="currentColor" viewBox="0 0 24 24"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0112 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0022 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                                        URL Repository (Github/Gitlab)
                                    </label>
                                    <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $project->github_url ?? '') }}" class="w-full text-sm rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors" placeholder="https://github.com/username/repo">
                                </div>
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Screenshot / Foto Project {{ !isset($project) ? '(Wajib)' : '' }}</label>
                                
                                @if(isset($project) && $project->image_path)
                                <div class="mb-4 rounded-2xl overflow-hidden border border-gray-200 relative group aspect-video bg-gray-100">
                                    <img src="{{ Storage::url($project->image_path) }}" alt="Preview" class="w-full h-full object-cover">
                                    <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center backdrop-blur-sm">
                                        <a href="{{ Storage::url($project->image_path) }}" target="_blank" class="px-4 py-2 bg-white/20 text-white rounded-lg hover:bg-white/40 transition-colors font-bold text-sm">Lihat Gambar Asli</a>
                                    </div>
                                </div>
                                @endif

                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:bg-gray-50 transition-colors relative bg-gray-50" id="drop-zone">
                                    <div class="space-y-2 text-center relative z-10">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="image_path" class="relative cursor-pointer bg-transparent rounded-md font-bold text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                                <span>{{ isset($project) ? 'Ganti Foto' : 'Pilih Foto' }}</span>
                                                <input id="image_path" name="image_path" type="file" class="sr-only" accept=".jpg,.jpeg,.png,.gif" {{ !isset($project) ? 'required' : '' }} onchange="previewImage(this)">
                                            </label>
                                            <p class="pl-1">atau tarik & lepas</p>
                                        </div>
                                        <p class="text-xs text-gray-500">PNG, JPG, GIF maksimal 5MB</p>
                                        <p id="file-name" class="text-sm font-bold text-blue-600 mt-2 hidden"></p>
                                    </div>
                                    
                                    {{-- Image Preview element --}}
                                    <img id="image-preview" class="absolute inset-0 w-full h-full object-cover rounded-2xl hidden" alt="Image preview">
                                    <div id="image-preview-overlay" class="absolute inset-0 bg-black/40 rounded-2xl hidden flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity backdrop-blur-sm cursor-pointer" onclick="document.getElementById('image_path').click()">
                                        <span class="text-white font-bold bg-black/50 px-3 py-1 rounded-lg text-sm">Ganti Foto</span>
                                    </div>
                                </div>
                                @error('image_path') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('siswa.showcase.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-blue-600/30 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Simpan Karya
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    
    <script>
        function previewImage(input) {
            const fileNameDisplay = document.getElementById('file-name');
            const preview = document.getElementById('image-preview');
            const overlay = document.getElementById('image-preview-overlay');
            const dropZone = document.getElementById('drop-zone');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    overlay.classList.remove('hidden');
                    // Hide the default content in drop zone
                    dropZone.querySelector('.z-10').classList.add('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
                
                fileNameDisplay.textContent = 'File terpilih: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                preview.src = "";
                preview.classList.add('hidden');
                overlay.classList.add('hidden');
                dropZone.querySelector('.z-10').classList.remove('hidden');
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
