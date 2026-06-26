<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Seting Jurnal & Waktu PKL</h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Periode PKL</p>
                    <p class="mt-2 font-bold text-gray-900">
                        {{ $setting->tanggal_mulai?->format('d M Y') ?? '-' }} - {{ $setting->tanggal_selesai?->format('d M Y') ?? '-' }}
                    </p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Check-in</p>
                    <p class="mt-2 font-bold text-gray-900">
                        {{ $setting->jam_check_in_mulai ? substr((string) $setting->jam_check_in_mulai, 0, 5) : '-' }}
                        -
                        {{ $setting->jam_check_in_selesai ? substr((string) $setting->jam_check_in_selesai, 0, 5) : '-' }}
                    </p>
                </div>
                <div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-5">
                    <p class="text-sm text-gray-500">Check-out</p>
                    <p class="mt-2 font-bold text-gray-900">
                        {{ $setting->jam_check_out_mulai ? substr((string) $setting->jam_check_out_mulai, 0, 5) : '-' }}
                        -
                        {{ $setting->jam_check_out_selesai ? substr((string) $setting->jam_check_out_selesai, 0, 5) : '-' }}
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('prakerin.setting.update') }}" class="bg-white border border-gray-100 shadow-sm rounded-2xl overflow-hidden">
                @csrf
                @method('PUT')

                <div class="border-b border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-900">Konfigurasi PKL</h3>
                    <p class="text-sm text-gray-500">Atur periode, waktu absensi, dan instruksi jurnal harian siswa.</p>
                </div>

                <div class="grid gap-6 p-6 lg:grid-cols-[1fr_0.85fr]">
                    <div class="space-y-6">
                        <section class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Periode Pelaksanaan</h4>
                                <p class="text-sm text-gray-500">Tanggal ini menjadi acuan masa aktif PKL dan jurnal siswa.</p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Mulai PKL</span>
                                    <input type="date" name="tanggal_mulai" value="{{ optional($setting->tanggal_mulai)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200">
                                </label>
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Selesai PKL</span>
                                    <input type="date" name="tanggal_selesai" value="{{ optional($setting->tanggal_selesai)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200">
                                </label>
                            </div>
                        </section>

                        <section class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Waktu Absensi</h4>
                                <p class="text-sm text-gray-500">Siswa melakukan check-in dan check-out sesuai rentang waktu berikut.</p>
                            </div>
                            <div class="grid gap-4 sm:grid-cols-2">
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Check-in mulai</span>
                                    <input type="time" name="jam_check_in_mulai" value="{{ $setting->jam_check_in_mulai ? substr((string) $setting->jam_check_in_mulai, 0, 5) : '' }}" class="w-full rounded-xl border-gray-200">
                                </label>
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Check-in selesai</span>
                                    <input type="time" name="jam_check_in_selesai" value="{{ $setting->jam_check_in_selesai ? substr((string) $setting->jam_check_in_selesai, 0, 5) : '' }}" class="w-full rounded-xl border-gray-200">
                                </label>
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Check-out mulai</span>
                                    <input type="time" name="jam_check_out_mulai" value="{{ $setting->jam_check_out_mulai ? substr((string) $setting->jam_check_out_mulai, 0, 5) : '' }}" class="w-full rounded-xl border-gray-200">
                                </label>
                                <label class="space-y-1">
                                    <span class="text-sm font-semibold text-gray-700">Check-out selesai</span>
                                    <input type="time" name="jam_check_out_selesai" value="{{ $setting->jam_check_out_selesai ? substr((string) $setting->jam_check_out_selesai, 0, 5) : '' }}" class="w-full rounded-xl border-gray-200">
                                </label>
                            </div>
                        </section>

                        <section class="space-y-4">
                            <div>
                                <h4 class="font-semibold text-gray-900">Instruksi Jurnal Harian</h4>
                                <p class="text-sm text-gray-500">Teks ini muncul pada halaman jurnal siswa PKL.</p>
                            </div>
                            <textarea name="instruksi_jurnal" rows="7" class="w-full rounded-xl border-gray-200" placeholder="Contoh: Tuliskan kegiatan utama, alat yang digunakan, kendala, dan tindak lanjut harian.">{{ $setting->instruksi_jurnal }}</textarea>
                        </section>
                    </div>

                    <div class="space-y-4">
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                            <h4 class="font-semibold text-gray-900">Aturan Absensi</h4>
                            <div class="mt-4 space-y-3">
                                <label class="flex items-start gap-3 rounded-xl bg-white p-4 shadow-sm">
                                    <input type="checkbox" name="wajib_foto_absensi" value="1" @checked($setting->wajib_foto_absensi) class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span>
                                        <span class="block font-semibold text-gray-900">Wajib foto absensi</span>
                                        <span class="block text-sm text-gray-500">Siswa harus mengunggah foto saat absensi.</span>
                                    </span>
                                </label>
                                <label class="flex items-start gap-3 rounded-xl bg-white p-4 shadow-sm">
                                    <input type="checkbox" name="wajib_lokasi" value="1" @checked($setting->wajib_lokasi) class="mt-1 rounded border-gray-300 text-red-600 focus:ring-red-500">
                                    <span>
                                        <span class="block font-semibold text-gray-900">Wajib GPS lokasi</span>
                                        <span class="block text-sm text-gray-500">Koordinat check-in/check-out harus tersimpan.</span>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <div class="rounded-2xl border border-red-100 bg-red-50 p-5">
                            <h4 class="font-semibold text-red-900">Catatan Operasional</h4>
                            <p class="mt-2 text-sm text-red-800">Pastikan periode dan jam absensi sudah final sebelum siswa mulai mengisi jurnal agar data rekap tetap konsisten.</p>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end border-t border-gray-100 bg-gray-50 px-6 py-4">
                    <button class="rounded-xl bg-red-600 px-5 py-2.5 font-semibold text-white hover:bg-red-700">
                        Simpan Seting PKL
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
