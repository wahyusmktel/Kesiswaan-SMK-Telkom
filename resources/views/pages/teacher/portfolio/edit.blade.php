<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-red-100 text-red-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    </span>
                    <div>
                        <h2 class="font-outfit font-black text-2xl text-slate-900 leading-tight">
                            Profil Publik & Portofolio Guru
                        </h2>
                        <p class="text-xs font-semibold text-slate-500">Kelola portofolio, kompetensi, dan profil Anda agar terindeks sempurna di mesin pencari Google</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('pegawai.show', $profile->slug) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-900 text-white hover:bg-slate-800 text-xs font-black shadow-lg shadow-slate-900/10 transition-all">
                    <span>Lihat Halaman Publik</span>
                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <a href="{{ route('pegawai.index') }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-black transition-all">
                    <span>Katalog Guru</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{
        activeTab: 'biodata',
        pendidikan: {{ Js::from(!empty($profile->riwayat_pendidikan) ? $profile->riwayat_pendidikan : [['jenjang' => 'S1', 'institusi' => '', 'jurusan' => '', 'tahun_lulus' => '']]) }},
        sertifikasi: {{ Js::from(!empty($profile->sertifikasi) ? $profile->sertifikasi : [['nama' => '', 'penerbit' => '', 'tahun' => '']]) }},
        addPendidikan() {
            this.pendidikan.push({ jenjang: '', institusi: '', jurusan: '', tahun_lulus: '' });
        },
        removePendidikan(index) {
            if (this.pendidikan.length > 1) {
                this.pendidikan.splice(index, 1);
            } else {
                this.pendidikan = [{ jenjang: '', institusi: '', jurusan: '', tahun_lulus: '' }];
            }
        },
        addSertifikasi() {
            this.sertifikasi.push({ nama: '', penerbit: '', tahun: '' });
        },
        removeSertifikasi(index) {
            if (this.sertifikasi.length > 1) {
                this.sertifikasi.splice(index, 1);
            } else {
                this.sertifikasi = [{ nama: '', penerbit: '', tahun: '' }];
            }
        }
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex items-center justify-between shadow-sm">
                    <div class="flex items-center gap-3">
                        <span class="grid h-8 w-8 place-items-center rounded-xl bg-emerald-100 text-emerald-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="text-sm font-bold">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            @if($errors->any())
                <div class="rounded-2xl border border-red-200 bg-red-50 p-4 text-red-800 shadow-sm">
                    <p class="text-xs font-black uppercase tracking-wider text-red-600 mb-2">Harap periksa kembali isian formulir:</p>
                    <ul class="list-disc list-inside text-sm space-y-1 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- SEO Readiness Banner --}}
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-slate-900 via-slate-800 to-red-950 p-6 sm:p-8 text-white shadow-xl">
                <div class="absolute -right-10 -bottom-10 w-72 h-72 rounded-full bg-red-600/20 blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
                    <div class="max-w-2xl">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-black uppercase tracking-wider border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                                Google Search Engine Ready
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full bg-white/10 text-slate-300 text-xs font-mono">
                                {{ $profile->is_published ? 'Status: Publik (Terindeks)' : 'Status: Draf (Belum Publik)' }}
                            </span>
                        </div>
                        <h3 class="font-outfit text-xl sm:text-2xl font-black text-white">
                            Profil Anda Otomatis Terhubung ke Google Index & Sitemap XML
                        </h3>
                        <p class="mt-2 text-xs sm:text-sm text-slate-300 leading-relaxed font-medium">
                            Setiap pembaruan data, keahlian, dan portofolio Anda secara otomatis memperbarui metadata 
                            <code class="text-red-400 font-mono bg-black/40 px-1 py-0.5 rounded">schema.org/ProfilePage</code> 
                            dan sitemap sekolah (<a href="{{ route('sitemap') }}" target="_blank" class="text-red-300 underline font-mono">/sitemap.xml</a>) 
                            sehingga siswa dan masyarakat luas dapat menemukan karya Anda langsung dari Google Search.
                        </p>
                    </div>

                    <div class="flex flex-col sm:flex-row lg:flex-col gap-3 shrink-0">
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                            <div class="text-2xl font-black font-outfit text-white">{{ $profile->views_count }}</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Total Kunjungan</div>
                        </div>
                        <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur-md text-center">
                            <div class="text-2xl font-black font-outfit text-red-400">{{ $profile->portfolios->count() }}</div>
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Karya Portofolio</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab Navigation --}}
            <div class="flex flex-wrap gap-2 border-b border-slate-200 pb-2">
                <button type="button" @click="activeTab = 'biodata'"
                    :class="activeTab === 'biodata' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-xl font-outfit font-black text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>1. Biodata & Keahlian</span>
                </button>

                <button type="button" @click="activeTab = 'pendidikan'"
                    :class="activeTab === 'pendidikan' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-xl font-outfit font-black text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"/></svg>
                    <span>2. Pendidikan & Sertifikasi</span>
                </button>

                <button type="button" @click="activeTab = 'portofolio'"
                    :class="activeTab === 'portofolio' ? 'bg-red-600 text-white shadow-lg shadow-red-600/20' : 'bg-white text-slate-600 hover:bg-slate-100 border border-slate-200'"
                    class="px-5 py-2.5 rounded-xl font-outfit font-black text-xs uppercase tracking-wider transition-all flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2 1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span>3. Portofolio & Karya ({{ $profile->portfolios->count() }})</span>
                </button>
            </div>

            {{-- Main Form for Biodata & Education --}}
            <form action="{{ route('teacher.portfolio.profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- TAB 1: BIODATA & KEAHLIAN --}}
                <div x-show="activeTab === 'biodata'" class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
                        <div class="border-b border-slate-100 pb-4 flex items-center justify-between">
                            <div>
                                <h4 class="font-outfit font-black text-lg text-slate-900">Identitas & Jabatan</h4>
                                <p class="text-xs text-slate-500 font-medium">Informasi ini akan ditampilkan pada header profil dan kartu pencarian</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <label class="inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="is_published" value="1" {{ $profile->is_published ? 'checked' : '' }} class="sr-only peer">
                                    <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                    <span class="ml-3 text-xs font-bold text-slate-700">Publikasikan ke Web</span>
                                </label>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-start">
                            {{-- Foto Profil --}}
                            <div class="md:col-span-4 flex flex-col items-center text-center p-6 rounded-2xl bg-slate-50 border border-slate-100">
                                <div class="w-32 h-32 rounded-3xl overflow-hidden bg-slate-200 shadow-md mb-4 relative group">
                                    @if($profile->foto_url)
                                        <img src="{{ $profile->foto_url }}" alt="{{ $profile->nama_lengkap }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center font-outfit font-black text-3xl text-slate-400">
                                            {{ strtoupper(substr($profile->nama_lengkap, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-2">Unggah Foto Resmi</label>
                                <input type="file" name="foto" accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-red-50 file:text-red-700 hover:file:bg-red-100 cursor-pointer">
                                <p class="text-[11px] text-slate-400 mt-2 font-medium">Format: JPG, PNG, WEBP. Maks 4MB. Disarankan foto portrait formal.</p>
                            </div>

                            {{-- Form Fields --}}
                            <div class="md:col-span-8 space-y-4">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Nama Lengkap (Sesuai Akun Master)</label>
                                    <input type="text" value="{{ $profile->nama_lengkap }}" disabled
                                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-100 text-slate-600 text-sm font-bold cursor-not-allowed">
                                    <p class="text-[11px] text-slate-400 mt-1">Nama akun utama diatur oleh Administrator / Kurikulum.</p>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Gelar Depan</label>
                                        <input type="text" name="gelar_depan" value="{{ old('gelar_depan', $profile->gelar_depan) }}" placeholder="Contoh: Dr., Drs., Ir."
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Gelar Belakang</label>
                                        <input type="text" name="gelar_belakang" value="{{ old('gelar_belakang', $profile->gelar_belakang) }}" placeholder="Contoh: S.Kom, M.T., Gr."
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Jabatan / Peran di Sekolah</label>
                                        <input type="text" name="jabatan" value="{{ old('jabatan', $profile->jabatan) }}" placeholder="Contoh: Guru Kejuruan RPL & Pembina Robotik"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                    </div>
                                    <div>
                                        <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Bidang Studi / Konsentrasi</label>
                                        <input type="text" name="bidang_studi" value="{{ old('bidang_studi', $profile->bidang_studi) }}" placeholder="Contoh: Rekayasa Perangkat Lunak"
                                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Kategori Pegawai</label>
                                    <select name="kategori" class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                        <option value="guru_produktif" {{ old('kategori', $profile->kategori) === 'guru_produktif' ? 'selected' : '' }}>Guru Produktif / Kejuruan (Vokasi)</option>
                                        <option value="guru_umum" {{ old('kategori', $profile->kategori) === 'guru_umum' ? 'selected' : '' }}>Guru Umum / Normatif Adaptif</option>
                                        <option value="tendik" {{ old('kategori', $profile->kategori) === 'tendik' ? 'selected' : '' }}>Tenaga Kependidikan (TENDIK/TPA)</option>
                                        <option value="manajemen" {{ old('kategori', $profile->kategori) === 'manajemen' ? 'selected' : '' }}>Manajemen & Pimpinan Sekolah</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-slate-100 pt-6 space-y-4">
                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Biografi Profesional & Ringkasan Karier</label>
                                <textarea name="bio" rows="4" placeholder="Ceritakan pengalaman profesional, dedikasi, serta latar belakang Anda..."
                                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 text-slate-800 text-sm leading-relaxed font-medium focus:border-red-500 focus:ring-1 focus:ring-red-500">{{ old('bio', $profile->bio) }}</textarea>
                                <p class="text-[11px] text-slate-400 mt-1">Bio ini akan menjadi deskripsi meta cuplikan pada hasil penelusuran Google.</p>
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Filosofi Mengajar / Nilai Utama</label>
                                <input type="text" name="filosofi_mengajar" value="{{ old('filosofi_mengajar', $profile->filosofi_mengajar) }}"
                                    placeholder="Contoh: Mengembangkan pemikiran komputasional berbasis solusi praktis industri."
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            </div>

                            <div>
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Keahlian & Spesialisasi (Pisahkan dengan koma)</label>
                                <input type="text" name="keahlian" value="{{ old('keahlian', is_array($profile->keahlian) ? implode(', ', $profile->keahlian) : $profile->keahlian) }}"
                                    placeholder="Contoh: Laravel, Flutter, Cloud Computing, UI/UX, Docker, Clean Code"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                                <p class="text-[11px] text-slate-400 mt-1">Keahlian ini digunakan oleh Google Bot untuk tagging spesialisasi pengajar.</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 pt-2">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">LinkedIn URL</label>
                                    <input type="url" name="sosial_media[linkedin]" value="{{ old('sosial_media.linkedin', $profile->sosial_media['linkedin'] ?? '') }}"
                                        placeholder="https://linkedin.com/in/..."
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">GitHub URL</label>
                                    <input type="url" name="sosial_media[github]" value="{{ old('sosial_media.github', $profile->sosial_media['github'] ?? '') }}"
                                        placeholder="https://github.com/..."
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Instagram URL</label>
                                    <input type="url" name="sosial_media[instagram]" value="{{ old('sosial_media.instagram', $profile->sosial_media['instagram'] ?? '') }}"
                                        placeholder="https://instagram.com/..."
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs font-semibold">
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Website Pribadi</label>
                                    <input type="url" name="sosial_media[website]" value="{{ old('sosial_media.website', $profile->sosial_media['website'] ?? '') }}"
                                        placeholder="https://..."
                                        class="w-full px-3 py-2 rounded-xl border border-slate-200 text-slate-800 text-xs font-semibold">
                                </div>
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-outfit font-black text-sm shadow-xl shadow-red-600/20 transition-all">
                                Simpan Perubahan Biodata
                            </button>
                        </div>
                    </div>
                </div>

                {{-- TAB 2: PENDIDIKAN & SERTIFIKASI --}}
                <div x-show="activeTab === 'pendidikan'" class="space-y-6">
                    {{-- Riwayat Pendidikan Repeater --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h4 class="font-outfit font-black text-lg text-slate-900">Riwayat Pendidikan Formal</h4>
                                <p class="text-xs text-slate-500 font-medium">Cantumkan almamater perguruan tinggi / sekolah Anda</p>
                            </div>
                            <button type="button" @click="addPendidikan()"
                                class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-black hover:bg-slate-800 transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Pendidikan</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in pendidikan" :key="index">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 relative space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-600" x-text="'Pendidikan #' + (index + 1)"></span>
                                        <button type="button" @click="removePendidikan(index)"
                                            class="text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                        <div class="sm:col-span-2">
                                            <input type="text" :name="'pendidikan[' + index + '][jenjang]'" x-model="item.jenjang" placeholder="S1 / S2 / D4"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold uppercase">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="text" :name="'pendidikan[' + index + '][institusi]'" x-model="item.institusi" placeholder="Nama Universitas / Institut"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="text" :name="'pendidikan[' + index + '][jurusan]'" x-model="item.jurusan" placeholder="Program Studi / Jurusan"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <input type="text" :name="'pendidikan[' + index + '][tahun_lulus]'" x-model="item.tahun_lulus" placeholder="Tahun Lulus"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>

                    {{-- Sertifikasi Repeater --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                            <div>
                                <h4 class="font-outfit font-black text-lg text-slate-900">Sertifikasi & Lisensi Profesi</h4>
                                <p class="text-xs text-slate-500 font-medium">BNSP, Cisco, AWS, Microsoft, Google Certified, dll.</p>
                            </div>
                            <button type="button" @click="addSertifikasi()"
                                class="px-3.5 py-1.5 rounded-xl bg-slate-900 text-white text-xs font-black hover:bg-slate-800 transition-all flex items-center gap-1.5">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                <span>Tambah Sertifikasi</span>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <template x-for="(item, index) in sertifikasi" :key="index">
                                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/60 relative space-y-3">
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs font-black uppercase tracking-wider text-slate-600" x-text="'Sertifikasi #' + (index + 1)"></span>
                                        <button type="button" @click="removeSertifikasi(index)"
                                            class="text-red-500 hover:text-red-700 text-xs font-bold transition-colors">
                                            Hapus
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                                        <div class="sm:col-span-6">
                                            <input type="text" :name="'sertifikasi[' + index + '][nama]'" x-model="item.nama" placeholder="Nama Sertifikasi (e.g. AWS Solutions Architect)"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-bold">
                                        </div>
                                        <div class="sm:col-span-4">
                                            <input type="text" :name="'sertifikasi[' + index + '][penerbit]'" x-model="item.penerbit" placeholder="Lembaga Penerbit (e.g. Amazon Web Services / BNSP)"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                        </div>
                                        <div class="sm:col-span-2">
                                            <input type="text" :name="'sertifikasi[' + index + '][tahun]'" x-model="item.tahun" placeholder="Tahun"
                                                class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs font-semibold">
                                        </div>
                                    </div>
                                </div>
                            </template>
                        </div>

                        <div class="pt-4 flex justify-end">
                            <button type="submit" class="px-6 py-3 rounded-xl bg-red-600 hover:bg-red-700 text-white font-outfit font-black text-sm shadow-xl shadow-red-600/20 transition-all">
                                Simpan Pendidikan & Sertifikasi
                            </button>
                        </div>
                    </div>
                </div>
            </form>

            {{-- TAB 3: PORTOFOLIO & KARYA --}}
            <div x-show="activeTab === 'portofolio'" class="space-y-6">
                {{-- Form Tambah Portofolio --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
                    <div class="border-b border-slate-100 pb-4">
                        <h4 class="font-outfit font-black text-lg text-slate-900">Tambah Karya & Proyek Baru</h4>
                        <p class="text-xs text-slate-500 font-medium">Unggah proyek riset, aplikasi, media ajar, atau karya industri yang telah Anda kembangkan</p>
                    </div>

                    <form action="{{ route('teacher.portfolio.project.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                            <div class="md:col-span-8">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Judul Karya / Proyek <span class="text-red-500">*</span></label>
                                <input type="text" name="judul" required placeholder="Contoh: Sistem Absensi IoT Berbasis RFID & Mobile Apps"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-bold focus:border-red-500 focus:ring-1 focus:ring-red-500">
                            </div>
                            <div class="md:col-span-4">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Kategori Karya</label>
                                <input type="text" name="kategori" placeholder="Aplikasi Web / Mobile / IoT / Penelitian"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold">
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Deskripsi Karya & Kontribusi</label>
                            <textarea name="deskripsi" rows="3" placeholder="Jelaskan tujuan proyek, teknologi yang dipakai, serta dampak atau hasil akhirnya..."
                                class="w-full px-4 py-2.5 rounded-2xl border border-slate-200 text-slate-800 text-sm font-medium focus:border-red-500 focus:ring-1 focus:ring-red-500"></textarea>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-4">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Tahun Penyelesaian</label>
                                <input type="text" name="tahun" value="{{ date('Y') }}" placeholder="{{ date('Y') }}"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold">
                            </div>
                            <div class="sm:col-span-8">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Tautan Proyek / Demo / Repository</label>
                                <input type="url" name="tautan_proyek" placeholder="https://..."
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">
                            <div class="sm:col-span-7">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Teknologi / Stack (Pisahkan dengan koma)</label>
                                <input type="text" name="teknologi" placeholder="Flutter, Firebase, Arduino, ESP32"
                                    class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-slate-800 text-sm font-semibold">
                            </div>
                            <div class="sm:col-span-5">
                                <label class="block text-xs font-black uppercase tracking-wider text-slate-700 mb-1">Gambar Cuplikan (Opsional)</label>
                                <input type="file" name="gambar" accept="image/*"
                                    class="w-full text-xs text-slate-500 file:mr-2 file:py-2 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-slate-100 file:text-slate-800 hover:file:bg-slate-200 cursor-pointer">
                            </div>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <label class="inline-flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_featured" value="1" class="rounded border-slate-300 text-red-600 focus:ring-red-500">
                                <span class="text-xs font-bold text-slate-700">Tandai sebagai Karya Unggulan (Featured)</span>
                            </label>

                            <button type="submit" class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-outfit font-black text-xs uppercase tracking-wider transition-all">
                                Tambahkan Karya
                            </button>
                        </div>
                    </form>
                </div>

                {{-- Daftar Portofolio yang Sudah Ada --}}
                <div class="bg-white rounded-3xl shadow-sm border border-slate-200 p-6 sm:p-8 space-y-6">
                    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                        <div>
                            <h4 class="font-outfit font-black text-lg text-slate-900">Daftar Karya Anda ({{ $profile->portfolios->count() }})</h4>
                            <p class="text-xs text-slate-500 font-medium">Karya yang dipublikasikan pada portofolio Anda</p>
                        </div>
                    </div>

                    @if($profile->portfolios->isEmpty())
                        <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 text-slate-400 mx-auto flex items-center justify-center mb-3">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                            </div>
                            <h5 class="font-outfit font-black text-sm text-slate-800">Belum ada karya ditambahkan</h5>
                            <p class="text-xs text-slate-400 font-medium mt-1">Gunakan formulir di atas untuk menambahkan portofolio pertama Anda.</p>
                        </div>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($profile->portfolios as $portfolio)
                                <div class="rounded-2xl border border-slate-200 overflow-hidden bg-slate-50/50 hover:border-slate-300 transition-all flex flex-col justify-between">
                                    <div>
                                        @if($portfolio->gambar_url)
                                            <div class="h-40 overflow-hidden bg-slate-100">
                                                <img src="{{ $portfolio->gambar_url }}" alt="{{ $portfolio->judul }}" class="w-full h-full object-cover">
                                            </div>
                                        @endif
                                        <div class="p-5">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <span class="px-2.5 py-0.5 rounded-full bg-slate-200 text-slate-700 text-[10px] font-bold uppercase tracking-wider">
                                                    {{ $portfolio->kategori }} &bull; {{ $portfolio->tahun }}
                                                </span>
                                                @if($portfolio->is_featured)
                                                    <span class="px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 text-[10px] font-black uppercase">
                                                        ★ Featured
                                                    </span>
                                                @endif
                                            </div>
                                            <h5 class="font-outfit font-black text-base text-slate-900">{{ $portfolio->judul }}</h5>
                                            @if($portfolio->deskripsi)
                                                <p class="text-xs text-slate-600 mt-1 line-clamp-2 leading-relaxed">{{ $portfolio->deskripsi }}</p>
                                            @endif

                                            @if(!empty($portfolio->teknologi))
                                                <div class="flex flex-wrap gap-1 mt-3">
                                                    @foreach($portfolio->teknologi as $tech)
                                                        <span class="px-2 py-0.5 rounded-md bg-white border border-slate-200 text-slate-600 text-[10px] font-semibold">
                                                            {{ $tech }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="px-5 pb-5 pt-2 border-t border-slate-200/50 flex items-center justify-between">
                                        @if($portfolio->tautan_proyek)
                                            <a href="{{ $portfolio->tautan_proyek }}" target="_blank" class="text-xs font-bold text-red-600 hover:text-red-700 flex items-center gap-1">
                                                <span>Lihat Proyek</span>
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                            </a>
                                        @else
                                            <span></span>
                                        @endif

                                        <form action="{{ route('teacher.portfolio.project.destroy', $portfolio->id) }}" method="POST"
                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus karya ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs font-bold text-slate-400 hover:text-red-600 transition-colors">
                                                Hapus Karya
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
