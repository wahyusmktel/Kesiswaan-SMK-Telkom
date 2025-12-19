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

                <form action="{{ route('super-admin.settings.update') }}" method="POST" enctype="multipart/form-data">
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
