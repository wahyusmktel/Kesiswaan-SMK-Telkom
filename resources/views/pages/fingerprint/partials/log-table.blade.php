<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h3 class="font-bold text-gray-900">{{ ($compact ?? false) ? 'Riwayat Log Absensi Terbaru' : 'Riwayat Log Absensi' }}</h3>
                <p class="text-sm text-gray-500">Filter berdasarkan tanggal, nama/user ID, dan mesin.</p>
            </div>
            @if($compact ?? false)
                <a href="{{ route('fingerprint.logs') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">
                    Lihat Semua Log
                </a>
            @endif
        </div>

        <form method="GET" action="{{ ($compact ?? false) ? route('fingerprint.index') : route('fingerprint.logs') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
            <input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / User ID">
            <select name="device_id" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                <option value="">Semua Mesin</option>
                @foreach($allDevices as $deviceOption)
                    <option value="{{ $deviceOption->id }}" {{ (string) request('device_id') === (string) $deviceOption->id ? 'selected' : '' }}>
                        {{ $deviceOption->name }}
                    </option>
                @endforeach
            </select>
            <div class="flex gap-2">
                <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Filter</button>
                <a href="{{ ($compact ?? false) ? route('fingerprint.index') : route('fingerprint.logs') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-100">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Waktu</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Pegawai</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">User ID Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                    <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Status/Punch</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50/60">
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $attendance->timestamp?->format('d M Y') }}</div>
                            <div class="text-xs text-gray-400">{{ $attendance->timestamp?->format('H:i:s') }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $attendance->appUser?->masterGuru?->nama_lengkap ?? $attendance->appUser?->name ?? 'Belum terhubung' }}</div>
                            <div class="text-xs text-gray-400">{{ $attendance->appUser?->email ?? 'Mapping pegawai belum ditemukan' }}</div>
                        </td>
                        <td class="px-6 py-4 font-mono text-sm text-gray-700">{{ $attendance->user_id }}</td>
                        <td class="px-6 py-4">
                            <div class="font-bold text-gray-900">{{ $attendance->device?->name ?? '-' }}</div>
                            <div class="text-xs text-gray-400">{{ $attendance->device?->ip_address }}:{{ $attendance->device?->port }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Status: {{ $attendance->status ?? '-' }}</span>
                            <span class="inline-flex rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Punch: {{ $attendance->punch ?? '-' }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                            Belum ada log absensi fingerprint.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attendances->hasPages())
        <div class="px-6 py-4 border-t border-gray-100">{{ $attendances->links() }}</div>
    @endif
</div>
