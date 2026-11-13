<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Pengaturan &amp; Profil Halaman OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Kelola konten profil, visi-misi organisasi, dan sambutan ketua OSIS yang tampil di halaman publik</p>
            </div>
            <a href="{{ route('public.osis.index') }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 flex items-center gap-1.5 transition-colors">
                <span>Preview Web Publik</span>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
            </a>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <form action="{{ route('osis.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Identitas & Semboyan -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <span>🏢</span> Identitas Organisasi &amp; Periode
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Nama Organisasi</label>
                            <input type="text" name="nama_organisasi" value="{{ old('nama_organisasi', $setting->nama_organisasi) }}" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Periode Aktif</label>
                            <input type="text" name="periode_aktif" value="{{ old('periode_aktif', $setting->periode_aktif) }}" placeholder="Contoh: 2025/2026" required
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Semboyan / Motto OSIS</label>
                            <input type="text" name="semboyan" value="{{ old('semboyan', $setting->semboyan) }}" placeholder="Semboyan atau tagline organisasi"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Deskripsi / Tentang OSIS</label>
                            <textarea name="deskripsi" rows="3"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('deskripsi', $setting->deskripsi) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 2. Visi & Misi Organisasi -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <span>🎯</span> Visi &amp; Misi Organisasi
                    </h3>

                    <div class="space-y-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Visi Organisasi</label>
                            <textarea name="visi" rows="3"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800 focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('visi', $setting->visi) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Misi Organisasi (Tuliskan per baris / bernomor)</label>
                            <textarea name="misi" rows="6"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800 font-sans focus:bg-white focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">{{ old('misi', $setting->misi) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 3. Profil Ketua & Wakil Existing -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <span>👑</span> Pimpinan OSIS Saat Ini (Existing)
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Ketua -->
                        <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200 space-y-4">
                            <span class="text-xs font-bold text-rose-600 uppercase tracking-wider block">Ketua Umum Existing</span>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Nama Lengkap Ketua</label>
                                <input type="text" name="nama_ketua_existing" value="{{ old('nama_ketua_existing', $setting->nama_ketua_existing) }}"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-800">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Upload Foto Ketua</label>
                                <input type="file" name="foto_ketua_existing" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                                @if($setting->foto_ketua_existing)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $setting->foto_ketua_url }}" class="w-12 h-12 rounded-xl object-cover border">
                                        <span class="text-[11px] text-gray-500">Foto aktif tersimpan</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Wakil Ketua -->
                        <div class="p-5 rounded-2xl bg-gray-50 border border-gray-200 space-y-4">
                            <span class="text-xs font-bold text-blue-600 uppercase tracking-wider block">Wakil Ketua Existing</span>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Nama Lengkap Wakil</label>
                                <input type="text" name="nama_wakil_existing" value="{{ old('nama_wakil_existing', $setting->nama_wakil_existing) }}"
                                       class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2 text-sm text-gray-800">
                            </div>
                            <div>
                                <label class="block text-[11px] font-bold text-gray-600 mb-1">Upload Foto Wakil</label>
                                <input type="file" name="foto_wakil_existing" accept="image/*" class="w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                @if($setting->foto_wakil_existing)
                                    <div class="mt-2 flex items-center gap-2">
                                        <img src="{{ $setting->foto_wakil_url }}" class="w-12 h-12 rounded-xl object-cover border">
                                        <span class="text-[11px] text-gray-500">Foto aktif tersimpan</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Pesan & Visi Misi Ketua Existing -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Fokus / Visi Misi Pimpinan Existing</label>
                            <textarea name="visi_misi_ketua_existing" rows="2"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800 focus:bg-white">{{ old('visi_misi_ketua_existing', $setting->visi_misi_ketua_existing) }}</textarea>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Pesan / Sambutan Singkat Ketua</label>
                            <textarea name="pesan_ketua_existing" rows="2"
                                      class="w-full bg-gray-50 border border-gray-200 rounded-xl p-4 text-sm text-gray-800 focus:bg-white">{{ old('pesan_ketua_existing', $setting->pesan_ketua_existing) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- 4. Kontak & Sosial Media -->
                <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-100 shadow-sm space-y-6">
                    <h3 class="font-bold text-lg text-gray-800 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <span>📱</span> Kontak &amp; Media Sosial
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Instagram (@username)</label>
                            <input type="text" name="instagram" value="{{ old('instagram', $setting->instagram) }}" placeholder="osissmktelkomlampung"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">TikTok (@username)</label>
                            <input type="text" name="tiktok" value="{{ old('tiktok', $setting->tiktok) }}" placeholder="osissmktelkomlpg"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">YouTube Channel</label>
                            <input type="text" name="youtube" value="{{ old('youtube', $setting->youtube) }}" placeholder="OSIS SMK Telkom"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-gray-600 mb-1">Email Resmi</label>
                            <input type="email" name="email" value="{{ old('email', $setting->email) }}" placeholder="osis@smktelkom-lpg.sch.id"
                                   class="w-full bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 text-sm text-gray-800">
                        </div>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="submit" class="px-8 py-3.5 bg-rose-600 hover:bg-rose-700 text-white rounded-2xl font-bold text-sm shadow-lg shadow-rose-500/25 transition-all">
                        Simpan Perubahan Profil OSIS
                    </button>
                </div>
            </form>

        </div>
    </div>
</x-app-layout>
