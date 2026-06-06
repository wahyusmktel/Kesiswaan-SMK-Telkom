<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Integrasi mesin GF1600 / ZKTeco untuk data pegawai</p>
        </div>
    </x-slot>

    <div class="space-y-6" x-data="{ syncOpen: false, syncAction: '', rangeType: '1_month' }">
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
                                        <button type="button" @click="syncAction = '{{ route('fingerprint.sync-attendances', $device->id) }}'; syncOpen = true" class="rounded-lg border border-amber-200 bg-amber-50 px-3 py-2 text-xs font-bold text-amber-700 hover:bg-amber-100">Tarik Log</button>
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

        @include('pages.fingerprint.partials.user-table', [
            'fingerprintUsers' => $fingerprintUsers,
            'allDevices' => $allDevices,
            'title' => 'User Mesin Fingerprint',
            'showMappingButton' => true,
            'action' => route('fingerprint.index'),
        ])

        <div x-show="syncOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-gray-900/60 backdrop-blur-sm p-4">
            <div class="min-h-full flex items-center justify-center">
                <form method="POST" :action="syncAction" class="w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden">
                    @csrf
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-xl font-black text-gray-900">Konfirmasi Tarik Log</h3>
                        <p class="text-sm text-gray-500 mt-1">Tarik log hanya memproses user mesin yang sudah dimapping ke pegawai sistem.</p>
                    </div>
                    <div class="p-6 space-y-4">
                        <label class="block">
                            <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang Data</span>
                            <select name="range_type" x-model="rangeType" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                                <option value="1_day">Hari ini</option>
                                <option value="2_days">2 hari terakhir</option>
                                <option value="1_month">1 bulan terakhir</option>
                                <option value="2_months">2 bulan terakhir</option>
                                <option value="custom">Kustom</option>
                                <option value="all">Semua data</option>
                            </select>
                        </label>
                        <div x-show="rangeType === 'custom'" class="grid grid-cols-2 gap-3">
                            <label class="block">
                                <span class="block text-xs font-bold text-gray-500 mb-1">Tanggal Awal</span>
                                <input type="date" name="date_from" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                            <label class="block">
                                <span class="block text-xs font-bold text-gray-500 mb-1">Tanggal Akhir</span>
                                <input type="date" name="date_to" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            </label>
                        </div>
                    </div>
                    <div class="p-6 bg-gray-50 flex justify-end gap-3">
                        <button type="button" @click="syncOpen = false" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">Batal</button>
                        <button class="rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-amber-700">Mulai Tarik Log</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
