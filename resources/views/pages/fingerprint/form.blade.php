<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">{{ $device->exists ? 'Edit Mesin Fingerprint' : 'Tambah Mesin Fingerprint' }}</h2>
            <p class="text-sm text-gray-500 mt-0.5">Konfigurasi perangkat GF1600 / ZKTeco</p>
        </div>
    </x-slot>

    <div class="max-w-4xl">
        @include('pages.fingerprint.partials.flash')

        <form method="POST" action="{{ $device->exists ? route('fingerprint.update', $device) : route('fingerprint.store') }}" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            @csrf
            @if($device->exists)
                @method('PUT')
            @endif

            <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Nama Mesin</label>
                    <input name="name" value="{{ old('name', $device->name) }}" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="GF1600">
                    @error('name') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">IP Address</label>
                    <input name="ip_address" value="{{ old('ip_address', $device->ip_address) }}" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="192.168.135.2">
                    @error('ip_address') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Port</label>
                    <input name="port" type="number" min="1" max="65535" value="{{ old('port', $device->port ?: 4370) }}" required class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="4370">
                    @error('port') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Serial Number</label>
                    <input name="serial_number" value="{{ old('serial_number', $device->serial_number) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Opsional">
                    @error('serial_number') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Lokasi</label>
                    <input name="location" value="{{ old('location', $device->location) }}" class="w-full rounded-xl border-gray-300 focus:border-red-500 focus:ring-red-500" placeholder="Contoh: Lobby utama / Ruang TU">
                    @error('location') <p class="mt-1 text-xs font-semibold text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox" name="is_active" value="1" class="rounded border-gray-300 text-red-600 focus:ring-red-500" {{ old('is_active', $device->is_active ?? true) ? 'checked' : '' }}>
                        <span class="text-sm font-bold text-gray-700">Mesin aktif</span>
                    </label>
                </div>
            </div>

            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row justify-end gap-3">
                <a href="{{ route('fingerprint.index') }}" class="rounded-xl border border-gray-200 bg-white px-5 py-2.5 text-center text-sm font-bold text-gray-600 hover:bg-gray-50">Batal</a>
                <button class="rounded-xl bg-red-600 px-5 py-2.5 text-sm font-bold text-white hover:bg-red-700">Simpan</button>
            </div>
        </form>
    </div>
</x-app-layout>
