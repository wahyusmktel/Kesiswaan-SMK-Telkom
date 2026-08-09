<x-app-layout>
    <div class="py-12 w-full flex flex-col bg-gray-50 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 w-full">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h2 class="text-3xl font-black text-gray-900 tracking-tight">Edit Sertifikat</h2>
                    <p class="text-gray-500 font-medium mt-1">Perbarui data atau ganti file sertifikat keahlian Anda.</p>
                </div>
                <div class="flex items-center gap-3">
                    <form action="{{ route('siswa.certificates.destroy', $certificate->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus sertifikat ini dari portofolio?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-10 px-4 bg-red-50 text-red-600 hover:bg-red-100 hover:text-red-700 font-bold rounded-xl flex items-center justify-center transition-colors">
                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Hapus
                        </button>
                    </form>
                    <a href="{{ route('siswa.certificates.index') }}" class="w-10 h-10 bg-white border border-gray-200 rounded-full flex items-center justify-center text-gray-500 hover:bg-gray-50 hover:text-gray-900 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-[32px] shadow-sm border border-gray-100 p-8 md:p-10">
                <form action="{{ route('siswa.certificates.update', $certificate->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        {{-- Left Column --}}
                        <div class="space-y-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Sertifikat / Pelatihan <span class="text-red-500">*</span></label>
                                <input type="text" name="name" id="name" value="{{ old('name', $certificate->name) }}" required class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label for="issuer" class="block text-sm font-bold text-gray-700 mb-2">Penyelenggara / Penerbit <span class="text-red-500">*</span></label>
                                <input type="text" name="issuer" id="issuer" value="{{ old('issuer', $certificate->issuer) }}" required class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                @error('issuer') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label for="issue_date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Terbit <span class="text-red-500">*</span></label>
                                    <input type="date" name="issue_date" id="issue_date" value="{{ old('issue_date', $certificate->issue_date->format('Y-m-d')) }}" required class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    @error('issue_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label for="expiration_date" class="block text-sm font-bold text-gray-700 mb-2">Berlaku s.d (Opsional)</label>
                                    <input type="date" name="expiration_date" id="expiration_date" value="{{ old('expiration_date', $certificate->expiration_date ? $certificate->expiration_date->format('Y-m-d') : '') }}" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                                    @error('expiration_date') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <div>
                                <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Ringkas (Opsional)</label>
                                <textarea name="description" id="description" rows="3" class="w-full rounded-xl border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">{{ old('description', $certificate->description) }}</textarea>
                                @error('description') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Right Column --}}
                        <div class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">File Sertifikat (PDF/Gambar)</label>
                                
                                <div class="mb-3 p-3 bg-gray-50 border border-gray-200 rounded-xl flex items-center justify-between">
                                    <div class="flex items-center gap-3 overflow-hidden">
                                        <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center border border-gray-200 flex-shrink-0">
                                            @if(Str::endsWith($certificate->file_path, '.pdf'))
                                                <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/></svg>
                                            @else
                                                <svg class="w-5 h-5 text-indigo-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"/></svg>
                                            @endif
                                        </div>
                                        <div class="truncate">
                                            <p class="text-xs font-bold text-gray-900 truncate">File Saat Ini</p>
                                            <a href="{{ Storage::url($certificate->file_path) }}" target="_blank" class="text-xs text-indigo-600 hover:underline">Lihat File</a>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-2xl hover:bg-gray-50 transition-colors relative" id="drop-zone">
                                    <div class="space-y-2 text-center relative z-10">
                                        <svg class="mx-auto h-8 w-8 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="file_path" class="relative cursor-pointer bg-transparent rounded-md font-bold text-indigo-600 hover:text-indigo-500">
                                                <span>Pilih File Baru</span>
                                                <input id="file_path" name="file_path" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png" onchange="updateFileName(this)">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">Biarkan kosong jika tidak ingin mengganti</p>
                                        <p id="file-name" class="text-sm font-bold text-indigo-600 mt-2 hidden"></p>
                                    </div>
                                </div>
                                @error('file_path') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>

                            <div class="bg-blue-50 border border-blue-100 p-5 rounded-2xl">
                                <h4 class="font-bold text-blue-900 mb-3 flex items-center gap-2">
                                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    Verifikasi Digital (Opsional)
                                </h4>
                                
                                <div class="space-y-4">
                                    <div>
                                        <label for="credential_id" class="block text-xs font-bold text-blue-800 mb-1">ID Kredensial</label>
                                        <input type="text" name="credential_id" id="credential_id" value="{{ old('credential_id', $certificate->credential_id) }}" class="w-full text-sm rounded-xl border-blue-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                    <div>
                                        <label for="credential_url" class="block text-xs font-bold text-blue-800 mb-1">URL Kredensial</label>
                                        <input type="url" name="credential_url" id="credential_url" value="{{ old('credential_url', $certificate->credential_url) }}" class="w-full text-sm rounded-xl border-blue-200 bg-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-10 pt-6 border-t border-gray-100 flex justify-end gap-3">
                        <a href="{{ route('siswa.certificates.index') }}" class="px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Batal</a>
                        <button type="submit" class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-colors shadow-lg shadow-indigo-600/30 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
    
    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('file-name');
            if (input.files && input.files.length > 0) {
                fileNameDisplay.textContent = 'File terpilih: ' + input.files[0].name;
                fileNameDisplay.classList.remove('hidden');
            } else {
                fileNameDisplay.textContent = '';
                fileNameDisplay.classList.add('hidden');
            }
        }
    </script>
</x-app-layout>
