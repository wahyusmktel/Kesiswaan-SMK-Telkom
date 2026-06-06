<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-bold text-gray-900">{{ $title ?? 'User Mesin Fingerprint' }}</h3>
                <p class="text-sm text-gray-500">Data user yang ditarik dari mesin, beserta status mapping pegawai.</p>
            </div>
            @if($showMappingButton ?? false)
                <a href="{{ route('fingerprint.mappings') }}" class="inline-flex items-center justify-center rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">
                    Mapping Pegawai
                </a>
            @endif
        </div>

        <form method="GET" action="{{ $action ?? route('fingerprint.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / User ID mesin / pegawai">
            <select name="device_id" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Semua Mesin</option>
                @foreach($allDevices as $deviceOption)
                    <option value="{{ $deviceOption->id }}" {{ (string) request('device_id') === (string) $deviceOption->id ? 'selected' : '' }}>
                        {{ $deviceOption->name }}
                    </option>
                @endforeach
            </select>
            <select name="mapping_status" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Semua Status</option>
                <option value="mapped" {{ request('mapping_status') === 'mapped' ? 'selected' : '' }}>Sudah Mapping</option>
                <option value="unmapped" {{ request('mapping_status') === 'unmapped' ? 'selected' : '' }}>Belum Mapping</option>
            </select>
            <div class="md:col-span-2 flex gap-2">
                <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Filter</button>
                <a href="{{ $action ?? route('fingerprint.index') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">User Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mapping Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Registrasi Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Sinkron Terakhir</th>
                    @if($editable ?? false)
                        <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($fingerprintUsers as $fpUser)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $fpUser->name ?: '-' }}</div>
                            <div class="text-xs text-gray-400">User ID: <span class="font-mono">{{ $fpUser->user_id }}</span> · UID: {{ $fpUser->uid ?? '-' }}</div>
                            <div class="text-xs text-gray-400">Role mesin: {{ $fpUser->role ?? '-' }} · Kartu: {{ $fpUser->cardno ?? '-' }}</div>
                        </td>
                        <td class="px-6 py-4">
                            @if($fpUser->appUser)
                                <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">Sudah Mapping</span>
                                <div class="mt-1 font-bold text-gray-900">{{ $fpUser->appUser->masterGuru?->nama_lengkap ?? $fpUser->appUser->name }}</div>
                                <div class="text-xs text-gray-400">{{ $fpUser->appUser->email }}</div>
                            @else
                                <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Belum Mapping</span>
                                <div class="mt-1 text-xs text-gray-400">Belum terhubung ke pegawai sistem.</div>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $fpUser->device?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $fpUser->device?->ip_address }}:{{ $fpUser->device?->port }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-700">{{ $fpUser->machine_registered_at?->format('d M Y H:i') ?? '-' }}</div>
                            <div class="text-xs text-gray-400">Jika mesin tidak mengirim tanggal, data ini kosong.</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm font-semibold text-gray-700">{{ $fpUser->last_synced_at?->format('d M Y H:i') ?? $fpUser->updated_at?->format('d M Y H:i') }}</div>
                        </td>
                        @if($editable ?? false)
                            <td class="px-6 py-4 text-right">
                                <form method="POST" action="{{ route('fingerprint.mappings.update', $fpUser) }}" class="flex justify-end gap-2">
                                    @csrf
                                    @method('PATCH')
                                    <select name="app_user_id" class="w-64 rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">-- Belum dimapping --</option>
                                        @foreach($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ (string) $fpUser->app_user_id === (string) $employee->id ? 'selected' : '' }}>
                                                {{ $employee->masterGuru?->nama_lengkap ?? $employee->name }} - {{ $employee->email }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <button class="rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">Simpan</button>
                                </form>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ ($editable ?? false) ? 6 : 5 }}" class="px-6 py-12 text-center text-gray-500">
                            Belum ada user mesin fingerprint. Klik Tarik User pada daftar mesin.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($fingerprintUsers->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $fingerprintUsers->links() }}</div>
    @endif
</div>
