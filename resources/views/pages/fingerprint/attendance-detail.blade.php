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

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
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
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Skor Disiplin</p>
                <p class="text-3xl font-black text-gray-900 mt-2">{{ $disciplineRate }}%</p>
            </div>
        </div>

        @php
            $toneClass = match($appreciation['tone']) {
                'emerald' => 'from-emerald-500 to-teal-600 text-white',
                'blue' => 'from-blue-500 to-indigo-600 text-white',
                'amber' => 'from-amber-400 to-orange-500 text-white',
                default => 'from-gray-700 to-gray-900 text-white',
            };
        @endphp
        <div class="rounded-2xl bg-gradient-to-r {{ $toneClass }} p-6 shadow-sm overflow-hidden relative">
            <div class="absolute right-6 top-6 h-20 w-20 rounded-full bg-white/10"></div>
            <div class="relative max-w-3xl">
                <p class="text-xs font-black uppercase tracking-widest text-white/70">Apresiasi Pegawai</p>
                <h3 class="mt-2 text-2xl font-black tracking-tight">{{ $appreciation['title'] }}</h3>
                <p class="mt-2 text-sm leading-relaxed text-white/85">{{ $appreciation['message'] }}</p>
                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-black">Terlambat: {{ $lateDays }} hari</span>
                    <span class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-black">Pulang cepat: {{ $earlyDays }} hari</span>
                    <span class="rounded-full bg-white/15 px-3 py-1.5 text-xs font-black">Disiplin: {{ $disciplineRate }}%</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Rekap Harian</h3>
                <p class="text-sm text-gray-500">Scan pertama dan terakhir per hari.</p>
            </div>
            <div class="p-6 border-b border-gray-100">
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                        <div>
                            <h4 class="font-black text-gray-900">Tren Kedisiplinan Harian</h4>
                            <p class="text-sm text-gray-500">Visual menit terlambat dan pulang cepat per hari.</p>
                        </div>
                        <div class="flex gap-2 text-xs font-bold">
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-amber-100 px-3 py-1.5 text-amber-700"><span class="h-2 w-2 rounded-full bg-amber-500"></span>Terlambat</span>
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-blue-100 px-3 py-1.5 text-blue-700"><span class="h-2 w-2 rounded-full bg-blue-500"></span>Pulang cepat</span>
                        </div>
                    </div>
                    <div class="h-72">
                        <canvas id="attendanceTrendChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Tanggal</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Masuk</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Scan Keluar</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Total Scan</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($dailyRecaps as $recap)
                            @php
                                $checkinBadgeClass = ((int) $recap->monitoring_late_minutes) > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
                                $checkoutBadgeClass = ((int) $recap->monitoring_early_minutes) > 0 ? 'bg-amber-100 text-amber-800 ring-1 ring-amber-200' : 'bg-gray-100 text-gray-800';
                            @endphp
                            <tr>
                                <td class="px-6 py-4 font-bold text-gray-900">{{ \Carbon\Carbon::parse($recap->tanggal)->format('d M Y') }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700"><span class="inline-flex rounded-full px-3 py-1.5 text-xs font-black {{ $checkinBadgeClass }}">{{ \Carbon\Carbon::parse($recap->scan_masuk)->format('H:i:s') }}</span></td>
                                <td class="px-6 py-4 text-sm text-gray-700"><span class="inline-flex rounded-full px-3 py-1.5 text-xs font-black {{ $checkoutBadgeClass }}">{{ \Carbon\Carbon::parse($recap->scan_keluar)->format('H:i:s') }}</span></td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $recap->total_scan }}</td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-xs font-black {{ $recap->monitoring_status_class }}">{{ $recap->monitoring_status_text }}</span>
                                    <div class="mt-1 text-[11px] font-semibold text-gray-400">{{ $recap->monitoring_rule_label }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        @foreach($recap->monitoring_notes as $note)
                                            <span class="inline-flex rounded-full bg-amber-50 px-3 py-1.5 text-xs font-black text-amber-700">{{ $note }}</span>
                                        @endforeach
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="px-6 py-10 text-center text-gray-500">Tidak ada data rekap.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @include('pages.fingerprint.partials.log-table', ['attendances' => $attendances, 'allDevices' => collect(), 'compact' => false, 'showFilters' => false])
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartEl = document.getElementById('attendanceTrendChart');
            if (!chartEl || typeof Chart === 'undefined') return;

            const data = @json($chartData);
            const ctx = chartEl.getContext('2d');
            const amberGradient = ctx.createLinearGradient(0, 0, 0, 260);
            amberGradient.addColorStop(0, 'rgba(245, 158, 11, 0.28)');
            amberGradient.addColorStop(1, 'rgba(245, 158, 11, 0.02)');
            const blueGradient = ctx.createLinearGradient(0, 0, 0, 260);
            blueGradient.addColorStop(0, 'rgba(59, 130, 246, 0.22)');
            blueGradient.addColorStop(1, 'rgba(59, 130, 246, 0.02)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'Menit terlambat',
                            data: data.lateMinutes,
                            borderColor: '#f59e0b',
                            backgroundColor: amberGradient,
                            fill: true,
                            tension: 0.42,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#f59e0b',
                            borderWidth: 3,
                        },
                        {
                            label: 'Menit pulang cepat',
                            data: data.earlyMinutes,
                            borderColor: '#3b82f6',
                            backgroundColor: blueGradient,
                            fill: true,
                            tension: 0.42,
                            pointRadius: 4,
                            pointHoverRadius: 7,
                            pointBackgroundColor: '#3b82f6',
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
                            callbacks: {
                                label: (context) => `${context.dataset.label}: ${context.parsed.y} menit`,
                            },
                        },
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#64748b', font: { weight: '600' } } },
                        y: { beginAtZero: true, grid: { color: 'rgba(148, 163, 184, 0.18)' }, ticks: { color: '#64748b', precision: 0 } },
                    },
                },
            });
        });
    </script>
</x-app-layout>
