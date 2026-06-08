<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Rekap Log Absensi Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Rekapitulasi absensi pegawai yang sudah dimapping dari mesin fingerprint.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Rekapitulasi Pegawai</h3>
                        <p class="text-sm text-gray-500">
                            Periode {{ $dateFrom?->format('d M Y') ?? 'awal' }} - {{ $dateTo?->format('d M Y') ?? 'akhir' }}.
                        </p>
                    </div>
                    <a href="{{ route('fingerprint.mappings') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">
                        Mapping Pegawai
                    </a>
                </div>

                <form method="GET" action="{{ route('fingerprint.logs') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom?->format('Y-m-d')) }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo?->format('Y-m-d')) }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input name="search" value="{{ request('search') }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / email pegawai">
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
                        <a href="{{ route('fingerprint.logs') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Hari Hadir</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Total Scan</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Pertama</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Terakhir</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($summaries as $summary)
                            <tr class="hover:bg-gray-50/60">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $summary->appUser?->masterGuru?->nama_lengkap ?? $summary->appUser?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $summary->appUser?->email ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-bold text-emerald-700">{{ $summary->total_days }} hari</span>
                                </td>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ $summary->total_logs }} scan</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $summary->first_scan ? \Carbon\Carbon::parse($summary->first_scan)->format('d M Y H:i') : '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $summary->last_scan ? \Carbon\Carbon::parse($summary->last_scan)->format('d M Y H:i') : '-' }}</td>
                                <td class="px-6 py-4 text-right">
                                    @if($summary->appUser)
                                        <a href="{{ route('fingerprint.logs.detail', ['user' => $summary->appUser, 'range' => '1_month']) }}" class="inline-flex rounded-xl bg-red-600 px-4 py-2 text-xs font-bold text-white hover:bg-red-700">
                                            Lihat Detail
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada log absensi untuk pegawai yang sudah dimapping.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($summaries->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $summaries->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
