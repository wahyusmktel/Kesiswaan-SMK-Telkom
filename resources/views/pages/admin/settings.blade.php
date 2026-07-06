<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Konfigurasi Aplikasi</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-700">Pengaturan Identitas Sekolah</h3>
                    <p class="text-xs text-gray-500">Kelola informasi dasar dan branding aplikasi di sini.</p>
                </div>

                <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data"
                    x-data="{ selectedTheme: '{{ old('theme', $setting->theme ?? 'default') }}' }">
                    @csrf
                    <div class="p-6 space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- School Name -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="school_name" class="block text-sm font-medium text-gray-700">Nama Sekolah</label>
                                <input type="text" name="school_name" id="school_name" value="{{ old('school_name', $setting->school_name) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm" required>
                                @error('school_name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Logo -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Logo Sekolah</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 overflow-hidden">
                                        @if($setting->logo)
                                            <img src="{{ Storage::url($setting->logo) }}" alt="Logo" class="object-contain w-full h-full">
                                        @else
                                            <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        @endif
                                    </div>
                                    <input type="file" name="logo" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                </div>
                                <p class="text-[10px] text-gray-400">Format: JPG, PNG, SVG. Maks 2MB.</p>
                                @error('logo') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Kop Surat UKK -->
                            <div class="col-span-1 md:col-span-2 space-y-2">
                                <label class="block text-sm font-medium text-gray-700">
                                    Kop Surat — Lembar Penilaian UKK
                                </label>
                                <p class="text-[10px] text-gray-500 -mt-1">
                                    Gambar ini ditampilkan sebagai kop surat pada PDF Lembar Penilaian Ujian Kompetensi Keahlian (UKK).
                                    Gunakan gambar kop sekolah dengan lebar penuh (format landscape/horizontal).
                                </p>
                                <div class="flex items-start gap-4">
                                    <div class="flex-shrink-0 w-full max-w-xs h-20 bg-gray-100 rounded-lg border border-gray-200 overflow-hidden flex items-center justify-center">
                                        @if($setting->kop_surat_ukk)
                                            <img src="{{ Storage::url($setting->kop_surat_ukk) }}" alt="Kop Surat UKK" class="object-contain w-full h-full">
                                        @else
                                            <div class="text-center text-gray-300 px-4">
                                                <svg class="w-7 h-7 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                                <span class="text-[10px]">Belum ada kop surat</span>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <input type="file" name="kop_surat_ukk" accept="image/jpeg,image/png,image/jpg"
                                            class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                        <p class="text-[10px] text-gray-400 mt-1">Format: JPG, PNG. Maks 4MB. Disarankan ukuran 2480×350 px (landscape).</p>
                                        @if($setting->kop_surat_ukk)
                                            <p class="text-[10px] text-green-600 mt-1 font-semibold">&#10003; Kop surat sudah dikonfigurasi. Upload ulang untuk mengganti.</p>
                                        @endif
                                    </div>
                                </div>
                                @error('kop_surat_ukk') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Favicon -->
                            <div class="space-y-2">
                                <label class="block text-sm font-medium text-gray-700">Favicon</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center border border-gray-200 overflow-hidden">
                                        @if($setting->favicon)
                                            <img src="{{ Storage::url($setting->favicon) }}" alt="Favicon" class="object-contain w-full h-full">
                                        @else
                                            <span class="text-[10px] text-gray-300">ICO</span>
                                        @endif
                                    </div>
                                    <input type="file" name="favicon" class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-red-50 file:text-red-700 hover:file:bg-red-100">
                                </div>
                                <p class="text-[10px] text-gray-400">Format: PNG, ICO. Maks 1MB.</p>
                                @error('favicon') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                                <input type="text" name="phone" id="phone" value="{{ old('phone', $setting->phone) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700">Email Sekolah</label>
                                <input type="email" name="email" id="email" value="{{ old('email', $setting->email) }}"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Address -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700">Alamat Lengkap</label>
                                <textarea name="address" id="address" rows="3"
                                    class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">{{ old('address', $setting->address) }}</textarea>
                                @error('address') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Theme Selection -->
                            <div class="col-span-1 md:col-span-2 pt-4 border-t border-gray-100">
                                <label class="block text-sm font-medium text-gray-700 mb-3">Tema Landing Page</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="default" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'default' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Default Mode</span>
                                            <span class="block text-sm text-gray-500">Tema Gelap (Dark Blue & Red)</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="light-red" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'light-red' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Light Mode</span>
                                            <span class="block text-sm text-gray-500">Tema Terang (Merah Putih)</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="tech-red" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'tech-red' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Tech Red</span>
                                            <span class="block text-sm text-gray-500">Tema modern (Graphite, Red Glow)</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="campus-flow" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'campus-flow' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Campus Flow</span>
                                            <span class="block text-sm text-gray-500">Layout editorial terang dan dinamis</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="telkom-corporate" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'telkom-corporate' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Telkom Corporate</span>
                                            <span class="block text-sm text-gray-500">Terinspirasi UI telkom.co.id</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="transformasi" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'transformasi' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Transformasi</span>
                                            <span class="block text-sm text-gray-500">Base baru dengan scroll showcase</span>
                                        </span>
                                    </label>

                                    <label class="relative flex cursor-pointer rounded-lg border bg-white p-4 shadow-sm focus:outline-none hover:border-gray-300">
                                        <input type="radio" name="theme" value="ajaran-baru" x-model="selectedTheme" class="mt-0.5 h-4 w-4 text-red-600 border-gray-300 focus:ring-red-500" {{ old('theme', $setting->theme ?? 'default') === 'ajaran-baru' ? 'checked' : '' }}>
                                        <span class="ml-3 flex flex-col">
                                            <span class="block text-sm font-medium text-gray-900">Ajaran Baru</span>
                                            <span class="block text-sm text-gray-500">Hero slider, mood check, dan module scroll</span>
                                        </span>
                                    </label>
                                </div>
                                @error('theme') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <div x-show="selectedTheme === 'transformasi'" x-cloak class="col-span-1 md:col-span-2 rounded-2xl border border-red-100 bg-red-50/40 p-5">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-3 mb-4">
                                    <div>
                                        <h3 class="text-sm font-black text-gray-900 uppercase tracking-wider">Konfigurasi Slider Transformasi</h3>
                                        <p class="text-xs text-gray-500 mt-1">Upload sampai 8 gambar. Disarankan rasio landscape 16:9 atau 4:3, ukuran maksimal 4 MB per gambar.</p>
                                    </div>
                                    <span class="inline-flex w-fit rounded-full bg-red-600 px-3 py-1 text-[10px] font-black uppercase tracking-widest text-white">Scroll Showcase</span>
                                </div>

                                @php $transformasiImages = $setting->transformasi_slider_images ?? []; @endphp
                                @if(count($transformasiImages))
                                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-4">
                                        @foreach($transformasiImages as $imagePath)
                                            <label class="group relative overflow-hidden rounded-xl border border-white bg-white shadow-sm">
                                                <img src="{{ Storage::url($imagePath) }}" alt="Slider Transformasi" class="h-28 w-full object-cover">
                                                <div class="absolute inset-x-0 bottom-0 bg-black/60 p-2">
                                                    <span class="flex items-center gap-2 text-[11px] font-bold text-white">
                                                        <input type="checkbox" name="delete_transformasi_slider_images[]" value="{{ $imagePath }}" class="rounded border-white/40 text-red-600 focus:ring-red-500">
                                                        Hapus
                                                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif

                                <label class="flex min-h-32 cursor-pointer flex-col items-center justify-center rounded-2xl border-2 border-dashed border-red-200 bg-white/80 p-5 text-center hover:border-red-400 hover:bg-white">
                                    <svg class="h-9 w-9 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a2 2 0 012.828 0L14 15m-2-2 1-1a2 2 0 012.828 0L20 16M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="mt-2 text-sm font-bold text-gray-900">Tambah gambar slider</span>
                                    <span class="text-xs text-gray-500">Bisa pilih beberapa file sekaligus</span>
                                    <input type="file" name="transformasi_slider_images[]" multiple accept=".jpg,.jpeg,.png,.webp" class="hidden">
                                </label>
                                @error('transformasi_slider_images.*') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>

                            <!-- Registration Toggle -->
                            <div class="col-span-1 md:col-span-2 pt-4 border-t border-gray-100">
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" name="allow_registration" value="1" class="sr-only peer" {{ old('allow_registration', $setting->allow_registration) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-red-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-red-600"></div>
                                    <span class="ml-3 text-sm font-medium text-gray-700">Izinkan Pendaftaran Akun Baru</span>
                                </label>
                                <p class="mt-1 text-xs text-gray-500">Jika dinonaktifkan, halaman pendaftaran akan menampilkan pesan bahwa pendaftaran ditutup.</p>
                            </div>
                        </div>
                    </div>

                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex items-center justify-end">
                        <button type="submit" class="inline-flex items-center px-6 py-2 bg-red-600 border border-transparent rounded-lg font-bold text-sm text-white shadow-sm hover:bg-red-500 transition-all gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
