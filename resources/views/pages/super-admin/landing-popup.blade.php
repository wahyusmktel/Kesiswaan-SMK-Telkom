<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-800">Popup Halaman Utama</h2>
            <p class="mt-1 text-sm text-gray-500">Atur pesan yang tampil kepada pengunjung landing page SISFO.</p>
        </div>
    </x-slot>

    @php
        $popupType = old('landing_popup_type', $setting->landing_popup_type ?? 'registration');
        $popupFrequency = old('landing_popup_frequency', $setting->landing_popup_frequency ?? 'daily');
    @endphp

    <div class="w-full py-6">
        <div class="mx-auto max-w-6xl px-4 sm:px-6 lg:px-8"
            x-data="{
                popupType: @js($popupType),
                title: @js(old('landing_popup_title', $setting->landing_popup_title ?? 'Selamat Datang, Calon Siswa Baru!')),
                description: @js(old('landing_popup_description', $setting->landing_popup_description ?? 'Registrasikan data calon siswa baru melalui layanan SISFO SMK Telkom Lampung. Prosesnya cepat, aman, dan dapat dipantau secara daring.')),
                ctaText: @js(old('landing_popup_cta_text', $setting->landing_popup_cta_text ?? 'Registrasi Sekarang'))
            }">
            <form action="{{ route('super-admin.landing-popup.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid gap-6 lg:grid-cols-[minmax(0,1fr)_380px]">
                    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-5">
                            <div>
                                <h3 class="font-bold text-gray-900">Konfigurasi Popup</h3>
                                <p class="mt-1 text-xs text-gray-500">Perubahan akan langsung berlaku setelah disimpan.</p>
                            </div>
                            <label class="relative inline-flex cursor-pointer items-center">
                                <input type="checkbox" name="landing_popup_enabled" value="1" class="peer sr-only"
                                    {{ old('landing_popup_enabled', $setting->landing_popup_enabled ?? true) ? 'checked' : '' }}>
                                <span class="h-6 w-11 rounded-full bg-gray-200 after:absolute after:left-[2px] after:top-[2px] after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-red-600 peer-checked:after:translate-x-full peer-focus:ring-4 peer-focus:ring-red-100"></span>
                                <span class="ml-3 text-sm font-semibold text-gray-700">Aktif</span>
                            </label>
                        </div>

                        <div class="space-y-6 p-6">
                            <fieldset>
                                <legend class="mb-3 text-sm font-semibold text-gray-700">Jenis Popup</legend>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <label class="cursor-pointer rounded-lg border p-4 transition"
                                        :class="popupType === 'registration' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" name="landing_popup_type" value="registration" x-model="popupType" class="sr-only">
                                        <span class="block text-sm font-bold text-gray-900">Registrasi Siswa Baru</span>
                                        <span class="mt-1 block text-xs leading-5 text-gray-500">Ajakan dengan tombol menuju formulir registrasi.</span>
                                    </label>
                                    <label class="cursor-pointer rounded-lg border p-4 transition"
                                        :class="popupType === 'mood' ? 'border-red-500 bg-red-50 ring-1 ring-red-500' : 'border-gray-200 hover:border-gray-300'">
                                        <input type="radio" name="landing_popup_type" value="mood" x-model="popupType" class="sr-only">
                                        <span class="block text-sm font-bold text-gray-900">Mood Harian</span>
                                        <span class="mt-1 block text-xs leading-5 text-gray-500">Mengembalikan popup perasaan harian sebelumnya.</span>
                                    </label>
                                </div>
                                @error('landing_popup_type') <p class="mt-2 text-xs text-red-600">{{ $message }}</p> @enderror
                            </fieldset>

                            <div x-show="popupType === 'registration'" x-cloak class="space-y-5">
                                <div>
                                    <label for="landing_popup_title" class="block text-sm font-semibold text-gray-700">Judul</label>
                                    <input id="landing_popup_title" name="landing_popup_title" type="text" x-model="title" maxlength="120"
                                        class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('landing_popup_title') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div>
                                    <label for="landing_popup_description" class="block text-sm font-semibold text-gray-700">Deskripsi</label>
                                    <textarea id="landing_popup_description" name="landing_popup_description" rows="4" x-model="description" maxlength="500"
                                        class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                    @error('landing_popup_description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid gap-5 sm:grid-cols-2">
                                    <div>
                                        <label for="landing_popup_cta_text" class="block text-sm font-semibold text-gray-700">Teks Tombol</label>
                                        <input id="landing_popup_cta_text" name="landing_popup_cta_text" type="text" x-model="ctaText" maxlength="50"
                                            class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        @error('landing_popup_cta_text') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label for="landing_popup_cta_url" class="block text-sm font-semibold text-gray-700">Tautan Tombol</label>
                                        <input id="landing_popup_cta_url" name="landing_popup_cta_url" type="text"
                                            value="{{ old('landing_popup_cta_url', $setting->landing_popup_cta_url ?? '/registrasi-siswa-baru') }}"
                                            placeholder="/registrasi-siswa-baru"
                                            class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                        @error('landing_popup_cta_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                            </div>

                            <div x-show="popupType === 'registration'" x-cloak>
                                <label for="landing_popup_frequency" class="block text-sm font-semibold text-gray-700">Frekuensi Tampil</label>
                                <select id="landing_popup_frequency" name="landing_popup_frequency"
                                    class="mt-2 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-red-500 focus:ring-red-500">
                                    <option value="daily" {{ $popupFrequency === 'daily' ? 'selected' : '' }}>Sekali per hari</option>
                                    <option value="session" {{ $popupFrequency === 'session' ? 'selected' : '' }}>Sekali per sesi browser</option>
                                    <option value="always" {{ $popupFrequency === 'always' ? 'selected' : '' }}>Setiap membuka halaman</option>
                                </select>
                                @error('landing_popup_frequency') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                            </div>
                            <div x-show="popupType === 'mood'" x-cloak class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                                Popup mood mengikuti aturan sekali per hari agar satu pengunjung tidak mengirim penilaian berulang pada hari yang sama.
                            </div>
                        </div>

                        <div class="flex items-center justify-between border-t border-gray-100 bg-gray-50 px-6 py-4">
                            <a href="{{ route('welcome') }}" target="_blank" class="text-sm font-semibold text-gray-600 hover:text-gray-900">Buka halaman utama</a>
                            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-bold text-white shadow-sm transition hover:bg-red-700 focus:ring-4 focus:ring-red-100">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Simpan Pengaturan
                            </button>
                        </div>
                    </section>

                    <aside class="lg:sticky lg:top-6 lg:self-start">
                        <div class="overflow-hidden rounded-lg border border-gray-200 bg-slate-950 shadow-xl">
                            <div class="flex items-center justify-between border-b border-white/10 px-4 py-3">
                                <span class="text-xs font-bold uppercase text-slate-400">Pratinjau</span>
                                <span class="rounded-full bg-emerald-500/15 px-2 py-1 text-[10px] font-bold text-emerald-300">Landing Page</span>
                            </div>
                            <div class="p-5">
                                <div class="rounded-lg border border-white/10 bg-slate-900 p-6 text-center shadow-2xl">
                                    <template x-if="popupType === 'registration'">
                                        <div>
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-red-600 text-white shadow-lg shadow-red-950/40">
                                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6v12m6-6H6m13-7H5a2 2 0 00-2 2v10a2 2 0 002 2h14a2 2 0 002-2V7a2 2 0 00-2-2z" /></svg>
                                            </div>
                                            <p class="mt-5 text-[10px] font-bold uppercase text-red-400">Penerimaan Siswa Baru</p>
                                            <h3 class="mt-2 text-xl font-black text-white" x-text="title || 'Judul popup'"></h3>
                                            <p class="mt-3 text-xs leading-5 text-slate-400" x-text="description || 'Deskripsi popup'"></p>
                                            <div class="mt-5 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-bold text-white" x-text="ctaText || 'Tombol CTA'"></div>
                                        </div>
                                    </template>
                                    <template x-if="popupType === 'mood'">
                                        <div>
                                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-lg bg-amber-400 text-2xl">&#9829;</div>
                                            <h3 class="mt-5 text-xl font-black text-white">Hai, Selamat Datang!</h3>
                                            <p class="mt-2 text-xs leading-5 text-slate-400">Sebelum melanjutkan, ceritakan dulu bagaimana perasaanmu hari ini?</p>
                                            <div class="mt-5 flex justify-center gap-2 text-xl"><span>1</span><span>2</span><span>3</span><span>4</span><span>5</span></div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </aside>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
