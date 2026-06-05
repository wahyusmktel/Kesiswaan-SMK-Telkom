<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Integrasi mesin GF1600 / ZKTeco untuk data pegawai</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Mesin</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $devices->total() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">User Fingerprint</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ \App\Models\FingerprintUser::count() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Log Absensi</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ \App\Models\FingerprintAttendance::count() }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h3 class="font-bold text-gray-900">Daftar Mesin Fingerprint</h3>
                    <p class="text-sm text-gray-500">Kelola koneksi perangkat, user, dan log absensi.</p>
                </div>
                <a href="{{ route('fingerprint.create') }}" class="inline-flex items-center justify-center rounded-xl bg-red-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-700">
                    Tambah Mesin
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Koneksi</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Serial/Lokasi</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Data</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($devices as $device)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $device->name }}</div>
                                    <div class="text-xs font-semibold {{ $device->is_active ? 'text-emerald-600' : 'text-gray-400' }}">
                                        {{ $device->is_active ? 'Aktif' : 'Nonaktif' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-mono text-sm text-gray-900">{{ $device->ip_address }}:{{ $device->port }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-700">{{ $device->serial_number ?: '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $device->location ?: 'Lokasi belum diisi' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-bold text-gray-900">{{ $device->fingerprint_users_count }} user</div>
                                    <div class="text-xs text-gray-400">{{ $device->attendances_count }} log</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-wrap justify-end gap-2">
                                        <form method="POST" action="{{ route('fingerprint.test-connection', $device->id) }}">
                                            @csrf
                                            <button class="rounded-lg border border-blue-200 bg-blue-50 px-3 py-2 text-xs font-bold text-blue-700 hover:bg-blue-100">Test Koneksi</button>
                                        </form>
                                        <form method="POST" action="{{ route('fingerprint.sync-users', $device->id) }}">
                                            @csrf
                                            <button class="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-xs font-bold text-emerald-700 hover:bg-emerald-100">Tarik User</button>
                                        </form>
                                        <form method="POST" action="{{ route('fingerprint.sync-attendances', $device->id) }}">
                                            @csrf
                                            <button class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">Tarik Log</button>
                                        </form>
                                        <a href="{{ route('fingerprint.edit', $device) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs font-bold text-gray-700 hover:bg-gray-50">Edit</a>
                                        <form method="POST" action="{{ route('fingerprint.destroy', $device) }}" onsubmit="return confirm('Hapus mesin fingerprint ini? Data user dan log dari mesin ini ikut terhapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-xs font-bold text-red-700 hover:bg-red-100">Hapus</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada mesin fingerprint. Tambahkan GF1600 terlebih dahulu.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($devices->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $devices->links() }}</div>
            @endif
        </div>

        @include('pages.fingerprint.partials.log-table', ['attendances' => $attendances, 'allDevices' => $allDevices, 'compact' => true])
    </div>
</x-app-layout>
