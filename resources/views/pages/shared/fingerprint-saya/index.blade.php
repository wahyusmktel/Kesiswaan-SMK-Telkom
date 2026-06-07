<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Absensi Fingerprint Saya</h2>
            <p class="text-sm text-gray-500 mt-0.5">Pantau kehadiran fingerprint dan evaluasi kedisiplinan pribadi.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('shared.fingerprint-today-card', ['summary' => $today])

        <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Statistik Kehadiran</p>
                    <h3 class="mt-2 text-xl font-black text-gray-900">Jam Absen Masuk dan Pulang</h3>
                    <p class="mt-1 text-sm text-gray-500">Visual waktu scan pertama dan terakhir pada periode yang dipilih.</p>
                </div>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-100 px-3 py-1.5 text-emerald-700">
                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>Jam Masuk
                    </span>
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-blue-700">
                        <span class="h-2 w-2 rounded-full bg-blue-500"></span>Jam Pulang
                    </span>
                </div>
            </div>

            @if(count($chartData['labels']))
                <div class="mt-6 h-72">
                    <canvas id="myFingerprintAttendanceChart"></canvas>
                </div>
            @else
                <div class="mt-6 flex h-72 items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-gray-50 text-sm font-semibold text-gray-400">
                    Belum ada data untuk ditampilkan pada chart.
                </div>
            @endif
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h3 class="font-bold text-gray-900">Riwayat Kehadiran</h3>
                    <p class="text-sm text-gray-500">
                        Periode {{ $dateFrom?->format('d M Y') ?? 'awal' }} - {{ $dateTo?->format('d M Y') ?? 'akhir' }}.
                    </p>
                </div>
                <form method="GET" action="{{ route('fingerprint-saya.index') }}" class="flex gap-2">
                    <select name="range" class="rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <option value="1_week" {{ request('range') === '1_week' ? 'selected' : '' }}>1 minggu</option>
                        <option value="1_month" {{ request('range', '1_month') === '1_month' ? 'selected' : '' }}>1 bulan</option>
                        <option value="all" {{ request('range') === 'all' ? 'selected' : '' }}>Selamanya</option>
                    </select>
                    <button class="rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Tampilkan</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 p-6 border-b border-gray-100 bg-gray-50/50">
                <div class="rounded-2xl bg-white p-5 border border-gray-100">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Hari Hadir</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ $dailyRecaps->count() }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 border border-gray-100">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Total Scan</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ $logs->total() }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 border border-gray-100">
                    <p class="text-xs font-black uppercase tracking-widest text-gray-400">Rata-rata Scan</p>
                    <p class="mt-2 text-3xl font-black text-gray-900">{{ $dailyRecaps->count() ? number_format($logs->total() / $dailyRecaps->count(), 1) : '0' }}</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Jam Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Jam Pulang</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Total Scan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailyRecaps as $recap)
                            <tr>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ \Carbon\Carbon::parse($recap->tanggal)->format('d M Y') }}</td>
                                <td class="px-6 py-4"><span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-700">{{ \Carbon\Carbon::parse($recap->scan_masuk)->format('H:i:s') }}</span></td>
                                <td class="px-6 py-4"><span class="rounded-full bg-blue-50 px-3 py-1.5 text-xs font-black text-blue-700">{{ $recap->total_scan > 1 ? \Carbon\Carbon::parse($recap->scan_keluar)->format('H:i:s') : '-' }}</span></td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $recap->total_scan }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada data absensi fingerprint pada periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Log Detail Fingerprint</h3>
                <p class="text-sm text-gray-500">Seluruh scan yang tercatat dari mesin fingerprint.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Waktu</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Mesin</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Status/Punch</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($logs as $log)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $log->timestamp?->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->timestamp?->format('H:i:s') }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $log->device?->name ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">{{ $log->device?->ip_address }}:{{ $log->device?->port }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="rounded-full bg-blue-50 px-2.5 py-1 text-xs font-bold text-blue-700">Status: {{ $log->status ?? '-' }}</span>
                                    <span class="rounded-full bg-amber-50 px-2.5 py-1 text-xs font-bold text-amber-700">Punch: {{ $log->punch ?? '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-gray-500">Belum ada log fingerprint.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">{{ $logs->links() }}</div>
            @endif
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartEl = document.getElementById('myFingerprintAttendanceChart');
            if (!chartEl || typeof Chart === 'undefined') return;

            const data = @json($chartData);
            const ctx = chartEl.getContext('2d');
            const emeraldGradient = ctx.createLinearGradient(0, 0, 0, 260);
            emeraldGradient.addColorStop(0, 'rgba(16, 185, 129, 0.22)');
            emeraldGradient.addColorStop(1, 'rgba(16, 185, 129, 0.02)');
            const blueGradient = ctx.createLinearGradient(0, 0, 0, 260);
            blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.20)');
            blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

            const formatTime = (minutes) => {
                if (minutes === null || minutes === undefined || Number.isNaN(minutes)) return '-';
                const total = Math.round(minutes);
                const h = Math.floor(total / 60).toString().padStart(2, '0');
                const m = (total % 60).toString().padStart(2, '0');
                return `${h}:${m}`;
            };

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Jam masuk',
                            data: data.checkinTimes,
                            borderColor: '#10b981',
                            backgroundColor: emeraldGradient,
                            fill: true,
                            tension: 0.42,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            borderWidth: 3,
                        },
                        {
                            label: 'Jam pulang',
                            data: data.checkoutTimes,
                            borderColor: '#3b82f6',
                            backgroundColor: blueGradient,
                            fill: true,
                            tension: 0.42,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            borderWidth: 3,
                        },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#111827',
                            padding: 12,
                            titleFont: { weight: 'bold' },
                            bodyFont: { weight: '600' },
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${formatTime(context.parsed.y)}`,
                            },
                        },
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { weight: '600' } },
                        },
                        y: {
                            grid: { color: 'rgba(148, 163, 184, 0.18)' },
                            ticks: {
                                color: '#64748b',
                                callback: (value) => formatTime(value),
                            },
                        },
                    },
                },
            });
        });
    </script>
</x-app-layout>
