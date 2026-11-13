<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
                    <a href="{{ route('osis.pilketos.kandidat.index') }}" class="hover:text-rose-600 transition-colors">Kandidat Paslon</a>
                    <span>/</span>
                    <span class="text-gray-800 font-semibold">Edit Paslon 0{{ $kandidat->nomor_urut }}</span>
                </div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Edit Pasangan Calon 0{{ $kandidat->nomor_urut }}</h2>
            </div>
            <a href="{{ route('osis.pilketos.kandidat.index') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8">
                <form action="{{ route('osis.pilketos.kandidat.update', $kandidat) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    @method('PUT')

                    <!-- Nomor Urut -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nomor Urut Paslon *</label>
                            <input type="number" name="nomor_urut" value="{{ old('nomor_urut', $kandidat->nomor_urut) }}" min="1" required class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:border-rose-500 focus:ring focus:ring-rose-200 text-lg font-bold">
                            @error('nomor_urut')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Tagline / Slogan Paslon</label>
                            <input type="text" name="tagline" value="{{ old('tagline', $kandidat->tagline) }}" placeholder="Contoh: Bergerak Bersama Membawa Perubahan" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:border-rose-500 focus:ring focus:ring-rose-200 text-sm">
                            @error('tagline')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-rose-600"></span>
                            Data Calon Ketua OSIS
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Ganti dari Database Siswa</label>
                                <select id="select_ketua" name="ketua_siswa_id" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs focus:border-rose-500 focus:ring focus:ring-rose-200">
                                    <option value="">-- Tetap Gunakan yang Ada / Isi Manual --</option>
                                    @foreach($students as $s)
                                        <option value="{{ $s->id }}" data-nama="{{ $s->nama_lengkap }}" data-nis="{{ $s->nis }}" data-kelas="{{ $s->rombels->first()?->kelas?->nama_kelas ?? '' }}" {{ old('ketua_siswa_id', $kandidat->ketua_siswa_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->nis }} - {{ $s->nama_lengkap }} ({{ $s->rombels->first()?->kelas?->nama_kelas ?? 'Kelas -' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap Ketua *</label>
                                <input type="text" id="nama_ketua" name="nama_ketua" value="{{ old('nama_ketua', $kandidat->nama_ketua) }}" required class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                                @error('nama_ketua')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">NIS Ketua</label>
                                    <input type="text" id="nis_ketua" name="nis_ketua" value="{{ old('nis_ketua', $kandidat->nis_ketua) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Kelas Ketua</label>
                                    <input type="text" id="kelas_ketua" name="kelas_ketua" value="{{ old('kelas_ketua', $kandidat->kelas_ketua) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                            Data Calon Wakil Ketua OSIS (Opsional)
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-600 mb-1">Ganti dari Database Siswa</label>
                                <select id="select_wakil" name="wakil_siswa_id" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs focus:border-rose-500 focus:ring focus:ring-rose-200">
                                    <option value="">-- Tetap Gunakan yang Ada / Isi Manual --</option>
                                    @foreach($students as $s)
                                        <option value="{{ $s->id }}" data-nama="{{ $s->nama_lengkap }}" data-nis="{{ $s->nis }}" data-kelas="{{ $s->rombels->first()?->kelas?->nama_kelas ?? '' }}" {{ old('wakil_siswa_id', $kandidat->wakil_siswa_id) == $s->id ? 'selected' : '' }}>
                                            {{ $s->nis }} - {{ $s->nama_lengkap }} ({{ $s->rombels->first()?->kelas?->nama_kelas ?? 'Kelas -' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-600 mb-1">Nama Lengkap Wakil</label>
                                <input type="text" id="nama_wakil" name="nama_wakil" value="{{ old('nama_wakil', $kandidat->nama_wakil) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                            </div>

                            <div class="grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">NIS Wakil</label>
                                    <input type="text" id="nis_wakil" name="nis_wakil" value="{{ old('nis_wakil', $kandidat->nis_wakil) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-600 mb-1">Kelas Wakil</label>
                                    <input type="text" id="kelas_wakil" name="kelas_wakil" value="{{ old('kelas_wakil', $kandidat->kelas_wakil) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6 space-y-4">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-blue-600"></span>
                            Visi, Misi &amp; Program Kerja
                        </h3>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Visi Paslon *</label>
                            <textarea name="visi" rows="3" required class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:border-rose-500 focus:ring focus:ring-rose-200 text-xs">{{ old('visi', $kandidat->visi) }}</textarea>
                            @error('visi')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Misi Paslon *</label>
                            <textarea name="misi" rows="4" required class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:border-rose-500 focus:ring focus:ring-rose-200 text-xs">{{ old('misi', $kandidat->misi) }}</textarea>
                            @error('misi')<p class="text-rose-500 text-xs mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Program Kerja Unggulan</label>
                            <textarea name="program_unggulan" rows="3" class="w-full px-4 py-3 rounded-2xl border-gray-200 focus:border-rose-500 focus:ring focus:ring-rose-200 text-xs">{{ old('program_unggulan', $kandidat->program_unggulan) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Link Video Kampanye / Debat</label>
                            <input type="url" name="video_url" value="{{ old('video_url', $kandidat->video_url) }}" class="w-full px-4 py-2.5 rounded-xl border-gray-200 text-xs">
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-6">
                        <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider mb-4 flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full bg-emerald-600"></span>
                            Foto Pasangan Calon &amp; Personal
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Foto Berdua Paslon</label>
                                @if($kandidat->foto_paslon)
                                    <img src="{{ $kandidat->foto_paslon_url }}" class="w-20 h-20 rounded-xl object-cover mb-2 border">
                                @endif
                                <input type="file" name="foto_paslon" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Foto Ketua</label>
                                @if($kandidat->foto_ketua)
                                    <img src="{{ asset('storage/' . $kandidat->foto_ketua) }}" class="w-20 h-20 rounded-xl object-cover mb-2 border">
                                @endif
                                <input type="file" name="foto_ketua" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Foto Wakil</label>
                                @if($kandidat->foto_wakil)
                                    <img src="{{ asset('storage/' . $kandidat->foto_wakil) }}" class="w-20 h-20 rounded-xl object-cover mb-2 border">
                                @endif
                                <input type="file" name="foto_wakil" accept="image/*" class="w-full text-xs text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-gray-100 file:text-gray-700 hover:file:bg-gray-200">
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-100">
                        <a href="{{ route('osis.pilketos.kandidat.index') }}" class="px-5 py-2.5 rounded-xl border border-gray-200 text-xs font-semibold text-gray-600 hover:bg-gray-50 transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-md shadow-rose-500/20 transition-all">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('select_ketua')?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                document.getElementById('nama_ketua').value = opt.dataset.nama || '';
                document.getElementById('nis_ketua').value = opt.dataset.nis || '';
                document.getElementById('kelas_ketua').value = opt.dataset.kelas || '';
            }
        });
        document.getElementById('select_wakil')?.addEventListener('change', function() {
            const opt = this.options[this.selectedIndex];
            if (opt.value) {
                document.getElementById('nama_wakil').value = opt.dataset.nama || '';
                document.getElementById('nis_wakil').value = opt.dataset.nis || '';
                document.getElementById('kelas_wakil').value = opt.dataset.kelas || '';
            }
        });
    </script>
</x-app-layout>
