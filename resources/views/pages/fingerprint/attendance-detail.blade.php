<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Absensi Pegawai</h2>
            <p class="text-sm text-gray-500 mt-0.5">{{ $user->masterGuru?->nama_lengkap ?? $user->name }} · {{ $user->email }}</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
            <form method="GET" action="{{ route('fingerprint.logs.detail', $user) }}" class="grid grid-cols-1 md:grid-cols-5 gap-3 items-end">
                <label class="block">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Rentang</span>
                    <select name="range" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <option value="1_month" {{ request('range', '1_month') === '1_month' ? 'selected' : '' }}>1 bulan terakhir</option>
                        <option value="all" {{ request('range') === 'all' ? 'selected' : '' }}>Selamanya</option>
                        <option value="day" {{ request('range') === 'day' ? 'selected' : '' }}>Hari tertentu</option>
                    </select>
                </label>
                <label class="block">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal</span>
                    <input type="date" name="date" value="{{ request('date') }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                </label>
                <div class="md:col-span-3 flex gap-2">
                    <button class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Tampilkan</button>
                    <a href="{{ route('fingerprint.logs') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Kembali Rekap</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Hari Hadir</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $dailyRecaps->count() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Scan</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $attendances->total() }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Periode</p>
                <p class="text-sm font-bold text-gray-900 mt-2">{{ $dateFrom?->format('d M Y') ?? 'Awal' }} - {{ $dateTo?->format('d M Y') ?? 'Akhir' }}</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Rekap Harian</h3>
                <p class="text-sm text-gray-500">Scan pertama dan terakhir per hari.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Keluar</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Total Scan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailyRecaps as $recap)
                            <tr>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ \Carbon\Carbon::parse($recap->tanggal)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($recap->scan_masuk)->format('H:i:s') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ \Carbon\Carbon::parse($recap->scan_keluar)->format('H:i:s') }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $recap->total_scan }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="px-6 py-10 text-center text-gray-500">Tidak ada data rekap.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('pages.fingerprint.partials.log-table', ['attendances' => $attendances, 'allDevices' => collect(), 'compact' => false, 'showFilters' => false])
    </div>
</x-app-layout>
