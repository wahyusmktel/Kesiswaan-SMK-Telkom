<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800">Seting Jurnal & Waktu PKL</h2></x-slot>
    <div class="py-8"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8"><div class="bg-white border border-gray-100 shadow-sm rounded-2xl p-6">
        <form method="POST" action="{{ route('prakerin.setting.update') }}" class="grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
            <label class="space-y-1"><span class="text-sm font-semibold">Mulai PKL</span><input type="date" name="tanggal_mulai" value="{{ optional($setting->tanggal_mulai)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1"><span class="text-sm font-semibold">Selesai PKL</span><input type="date" name="tanggal_selesai" value="{{ optional($setting->tanggal_selesai)->format('Y-m-d') }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1"><span class="text-sm font-semibold">Check-in mulai</span><input type="time" name="jam_check_in_mulai" value="{{ $setting->jam_check_in_mulai }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1"><span class="text-sm font-semibold">Check-in selesai</span><input type="time" name="jam_check_in_selesai" value="{{ $setting->jam_check_in_selesai }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1"><span class="text-sm font-semibold">Check-out mulai</span><input type="time" name="jam_check_out_mulai" value="{{ $setting->jam_check_out_mulai }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="space-y-1"><span class="text-sm font-semibold">Check-out selesai</span><input type="time" name="jam_check_out_selesai" value="{{ $setting->jam_check_out_selesai }}" class="w-full rounded-xl border-gray-200"></label>
            <label class="sm:col-span-2 space-y-1"><span class="text-sm font-semibold">Instruksi jurnal harian</span><textarea name="instruksi_jurnal" rows="5" class="w-full rounded-xl border-gray-200">{{ $setting->instruksi_jurnal }}</textarea></label>
            <label class="flex items-center gap-2"><input type="checkbox" name="wajib_foto_absensi" value="1" @checked($setting->wajib_foto_absensi)> Wajib foto absensi</label>
            <label class="flex items-center gap-2"><input type="checkbox" name="wajib_lokasi" value="1" @checked($setting->wajib_lokasi)> Wajib GPS lokasi</label>
            <button class="sm:col-span-2 rounded-xl bg-red-600 px-5 py-2 text-white font-semibold">Simpan Seting PKL</button>
        </form>
    </div></div></div>
</x-app-layout>
