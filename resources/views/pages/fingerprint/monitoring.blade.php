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

        <div class="grid grid-cols-1 xl:grid-cols-[1.35fr_0.85fr] gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm overflow-hidden">
                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Analisa Kehadiran</p>
                        <h3 class="mt-2 text-xl font-black text-gray-900">Kondisi Absensi Harian</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Ringkasan kualitas kehadiran tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('d F Y') }} berdasarkan filter aktif.
                        </p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 min-w-56">
                        <div class="rounded-2xl bg-gray-950 p-4 text-white">
                            <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Kehadiran</p>
                            <p class="mt-2 text-3xl font-black">{{ $analysisData['attendance_rate'] }}%</p>
                        </div>
                        <div class="rounded-2xl bg-red-50 p-4 text-red-700">
                            <p class="text-[10px] font-black uppercase tracking-widest text-red-400">Disiplin</p>
                            <p class="mt-2 text-3xl font-black">{{ $analysisData['discipline_rate'] }}%</p>
                        </div>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-1 lg:grid-cols-[220px_1fr] gap-6 items-center">
                    <div class="mx-auto">
                        @php
                            $completePercent = $analysisData['segments'][0]['percent'] ?? 0;
                            $incompletePercent = $analysisData['segments'][1]['percent'] ?? 0;
                            $absentPercent = $analysisData['segments'][2]['percent'] ?? 0;
                            $completeEnd = $completePercent;
                            $incompleteEnd = $completePercent + $incompletePercent;
                        @endphp
                        <div class="relative h-52 w-52 rounded-full"
                            style="background: conic-gradient(#10b981 0 {{ $completeEnd }}%, #f59e0b {{ $completeEnd }}% {{ $incompleteEnd }}%, #ef4444 {{ $incompleteEnd }}% {{ $incompleteEnd + $absentPercent }}%, #e5e7eb {{ $incompleteEnd + $absentPercent }}% 100%);">
                            <div class="absolute inset-7 rounded-full bg-white shadow-inner flex flex-col items-center justify-center text-center">
                                <p class="text-4xl font-black text-gray-900">{{ number_format($stats['present']) }}</p>
                                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Hadir</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            @foreach($analysisData['segments'] as $segment)
                                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                                    <div class="flex items-center gap-2">
                                        <span class="h-3 w-3 rounded-full {{ $segment['class'] }}"></span>
                                        <p class="text-xs font-black uppercase tracking-widest text-gray-500">{{ $segment['label'] }}</p>
                                    </div>
                                    <p class="mt-3 text-2xl font-black text-gray-900">{{ number_format($segment['value']) }}</p>
                                    <p class="text-xs font-semibold text-gray-400">{{ $segment['percent'] }}% dari data</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="space-y-4">
                            @foreach($analysisData['bars'] as $bar)
                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-3">
                                        <span class="text-sm font-black text-gray-700">{{ $bar['label'] }}</span>
                                        <span class="text-xs font-black text-gray-400">{{ number_format($bar['value']) }} pegawai - {{ $bar['percent'] }}%</span>
                                    </div>
                                    <div class="h-3 overflow-hidden rounded-full bg-gray-100">
                                        <div class="h-full rounded-full bg-gradient-to-r {{ $bar['class'] }}" style="width: {{ max($bar['percent'], $bar['value'] > 0 ? 4 : 0) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Top Disiplin</p>
                        <h3 class="mt-2 text-xl font-black text-gray-900">10 Pegawai Paling Disiplin</h3>
                    </div>
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </span>
                </div>

                <div class="mt-5 space-y-3">
                    @forelse($topDisciplinedEmployees as $employee)
                        @php
                            $firstScanTop = $employee->first_scan ? \Carbon\Carbon::parse($employee->first_scan) : null;
                            $lastScanTop = $employee->last_scan ? \Carbon\Carbon::parse($employee->last_scan) : null;
                            $hasCheckoutTop = $firstScanTop && $lastScanTop && !$firstScanTop->equalTo($lastScanTop);
                        @endphp
                        <div class="flex items-center gap-3 rounded-2xl border border-gray-100 bg-gray-50 p-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $loop->iteration <= 3 ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 ring-1 ring-gray-200' }} text-sm font-black">
                                {{ $loop->iteration }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-black text-gray-900">{{ $employee->appUser?->masterGuru?->nama_lengkap ?? $employee->appUser?->name ?? $employee->name }}</p>
                                <p class="truncate text-xs font-semibold text-gray-400">{{ $employee->monitoring_rule_label }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-black text-gray-900">{{ $firstScanTop?->format('H:i') ?? '-' }}</p>
                                <p class="text-xs font-semibold text-gray-400">{{ $hasCheckoutTop ? $lastScanTop->format('H:i') : 'Belum pulang' }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-5 text-center">
                            <p class="text-sm font-bold text-gray-600">Belum ada pegawai yang masuk kriteria disiplin pada filter ini.</p>
                            <p class="mt-1 text-xs text-gray-400">Kriteria: wajib hadir, tidak terlambat, dan tidak pulang cepat.</p>
                        </div>
                    @endforelse
                </div>
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
                                $notes = $row->monitoring_notes ?? ['Sesuai jadwal'];
                                $checkinBadgeClass = ((int) $row->monitoring_late_minutes) > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
                                $checkoutBadgeClass = ((int) $row->monitoring_early_minutes) > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
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
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-black {{ $row->monitoring_status_class }}">{{ $row->monitoring_status_text }}</span>
                                    <div class="mt-1 text-[11px] font-semibold text-gray-400">{{ $row->monitoring_rule_label }}</div>
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
