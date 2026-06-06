<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Absensi Fingerprint</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pantau jam masuk dan jam pulang pegawai berdasarkan tanggal tertentu.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Pegawai Termapping</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($stats['total']) }}</p>
                <p class="mt-1 text-sm text-gray-500">Data user mesin yang sudah terhubung.</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Sudah Hadir</p>
                <p class="mt-3 text-3xl font-black text-emerald-700">{{ number_format($stats['present']) }}</p>
                <p class="mt-1 text-sm text-emerald-700/70">Memiliki scan pada tanggal ini.</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-600">Belum Scan Pulang</p>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($stats['incomplete']) }}</p>
                <p class="mt-1 text-sm text-amber-700/70">Baru terdeteksi satu waktu scan.</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-red-600">Belum Ada Scan</p>
                <p class="mt-3 text-3xl font-black text-red-700">{{ number_format($stats['absent']) }}</p>
                <p class="mt-1 text-sm text-red-700/70">Tidak ada log pada tanggal filter.</p>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4">
                <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-3">
                    <div>
                        <h3 class="font-bold text-gray-900">Monitoring Harian</h3>
                        <p class="text-sm text-gray-500">
                            Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }}. Jam masuk diambil dari scan pertama, jam pulang dari scan terakhir.
                        </p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <a href="{{ route('fingerprint.logs') }}" class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-700 hover:bg-gray-50">
                            Log Absensi
                        </a>
                        <a href="{{ route('fingerprint.monitoring.export', request()->query()) }}" class="inline-flex items-center justify-center gap-2 rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-bold text-white hover:bg-emerald-700">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v12m0 0l4-4m-4 4l-4-4M4 17v2a2 2 0 002 2h12a2 2 0 002-2v-2" />
                            </svg>
                            Export Excel
                        </a>
                    </div>
                </div>

                <form method="GET" action="{{ route('fingerprint.monitoring') }}" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                    <input type="date" name="date" value="{{ request('date', $date) }}" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    <input name="search" value="{{ request('search') }}" class="md:col-span-2 rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / email / kode pegawai">
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
                        <a href="{{ route('fingerprint.monitoring') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Pegawai</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">User ID Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Jam Masuk</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Jam Pulang</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Scan</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rows as $row)
                            @php
                                $firstScan = $row->first_scan ? \Carbon\Carbon::parse($row->first_scan) : null;
                                $lastScan = $row->last_scan ? \Carbon\Carbon::parse($row->last_scan) : null;
                                $hasCheckout = $firstScan && $lastScan && !$firstScan->equalTo($lastScan);
                                $checkinEnd = \Carbon\Carbon::parse($date . ' ' . $setting->checkin_end);
                                $checkoutStart = \Carbon\Carbon::parse($date . ' ' . $setting->checkout_start);
                                $lateMinutes = $firstScan && $firstScan->greaterThan($checkinEnd) ? (int) ceil($checkinEnd->diffInMinutes($firstScan)) : 0;
                                $earlyMinutes = $hasCheckout && $lastScan->lessThan($checkoutStart) ? (int) ceil($lastScan->diffInMinutes($checkoutStart)) : 0;
                                $notes = [];
                                if ($lateMinutes > 0) {
                                    $notes[] = 'Terlambat ' . $lateMinutes . ' menit';
                                }
                                if ($earlyMinutes > 0) {
                                    $notes[] = 'Pulang cepat ' . $earlyMinutes . ' menit';
                                }
                                $statusClass = $firstScan
                                    ? ($hasCheckout ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700')
                                    : 'bg-red-50 text-red-700';
                                $statusText = $firstScan ? ($hasCheckout ? 'Hadir Lengkap' : 'Belum Scan Pulang') : 'Belum Ada Scan';
                                $checkinBadgeClass = $lateMinutes > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
                                $checkoutBadgeClass = $earlyMinutes > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
                            @endphp
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $row->appUser?->masterGuru?->nama_lengkap ?? $row->appUser?->name ?? $row->name }}</div>
                                    <div class="text-xs text-gray-400">{{ $row->appUser?->email ?? '-' }}{{ $row->appUser?->masterGuru?->kode_guru ? ' | ' . $row->appUser->masterGuru->kode_guru : '' }}</div>
                                </td>
                                <td class="px-6 py-4 font-mono text-sm font-bold text-gray-700">{{ $row->user_id }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $row->device?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $row->device?->ip_address }}{{ $row->device?->port ? ':' . $row->device->port : '' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex min-w-24 justify-center rounded-full px-3 py-1.5 text-sm font-black {{ $checkinBadgeClass }}">{{ $firstScan?->format('H:i:s') ?? '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex min-w-24 justify-center rounded-full px-3 py-1.5 text-sm font-black {{ $checkoutBadgeClass }}">{{ $hasCheckout ? $lastScan->format('H:i:s') : '-' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-900">{{ (int) ($row->total_scan ?? 0) }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-black {{ $statusClass }}">{{ $statusText }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(count($notes))
                                        <div class="flex flex-wrap justify-end gap-1.5">
                                            @foreach($notes as $note)
                                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1.5 text-xs font-black text-amber-700">{{ $note }}</span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-sm font-semibold text-gray-400">Sesuai jadwal</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                    Belum ada pegawai termapping untuk filter ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($rows->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $rows->links() }}</div>
            @endif
        </div>
    </div>
</x-app-layout>
