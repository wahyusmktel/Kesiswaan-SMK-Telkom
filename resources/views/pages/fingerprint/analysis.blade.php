<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Analisa Total Kehadiran Pegawai</h2>
            <p class="text-sm text-gray-500 mt-0.5">Grafik, ranking, evaluasi, dan PDF apresiasi berdasarkan absensi fingerprint.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @include('pages.fingerprint.partials.flash')

        <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
            <form method="GET" action="{{ route('fingerprint.analysis') }}" class="grid grid-cols-1 md:grid-cols-6 gap-3 items-end">
                <label class="block">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Awal</span>
                    <input type="date" name="date_from" value="{{ request('date_from', $dateFrom->toDateString()) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                </label>
                <label class="block">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Tanggal Akhir</span>
                    <input type="date" name="date_to" value="{{ request('date_to', $dateTo->toDateString()) }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                </label>
                <label class="block md:col-span-2">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Cari Pegawai</span>
                    <input name="search" value="{{ request('search') }}" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500" placeholder="Nama / email / kode pegawai">
                </label>
                <label class="block">
                    <span class="block text-xs font-black uppercase tracking-widest text-gray-400 mb-2">Mesin</span>
                    <select name="device_id" class="w-full rounded-xl border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                        <option value="">Semua Mesin</option>
                        @foreach($allDevices as $deviceOption)
                            <option value="{{ $deviceOption->id }}" @selected((string) request('device_id') === (string) $deviceOption->id)>{{ $deviceOption->name }}</option>
                        @endforeach
                    </select>
                </label>
                <div class="flex gap-2">
                    <button class="flex-1 rounded-xl bg-gray-900 px-4 py-2.5 text-sm font-bold text-white hover:bg-red-600">Analisa</button>
                    <a href="{{ route('fingerprint.analysis') }}" class="rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-50">Reset</a>
                </div>
            </form>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl border border-gray-100 bg-white p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Pegawai</p>
                <p class="mt-3 text-3xl font-black text-gray-900">{{ number_format($summary['employees']) }}</p>
            </div>
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-emerald-600">Rasio Hadir</p>
                <p class="mt-3 text-3xl font-black text-emerald-700">{{ $summary['attendance_rate'] }}%</p>
            </div>
            <div class="rounded-2xl border border-red-100 bg-red-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-red-600">Skor Rata-rata</p>
                <p class="mt-3 text-3xl font-black text-red-700">{{ $summary['average_score'] }}</p>
            </div>
            <div class="rounded-2xl border border-amber-100 bg-amber-50 p-5 shadow-sm">
                <p class="text-xs font-black uppercase tracking-widest text-amber-600">Terlambat</p>
                <p class="mt-3 text-3xl font-black text-amber-700">{{ number_format($summary['late_days']) }}</p>
            </div>
            <div class="rounded-2xl border border-gray-100 bg-gray-950 p-5 shadow-sm text-white">
                <p class="text-xs font-black uppercase tracking-widest text-gray-400">Tidak Hadir</p>
                <p class="mt-3 text-3xl font-black">{{ number_format($summary['absent_days']) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-[1.35fr_0.85fr] gap-6">
            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Grafik Analisa</p>
                        <h3 class="mt-2 text-xl font-black text-gray-900">Tren Kehadiran Pegawai</h3>
                    </div>
                    <p class="text-sm font-semibold text-gray-500">{{ $dateFrom->translatedFormat('d F Y') }} - {{ $dateTo->translatedFormat('d F Y') }}</p>
                </div>
                <div class="h-80">
                    <canvas id="attendanceAnalysisChart"></canvas>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <p class="text-xs font-black uppercase tracking-widest text-gray-400">Apresiasi</p>
                        <h3 class="mt-2 text-xl font-black text-gray-900">10 Pegawai Terbaik</h3>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-black text-emerald-700">Sertifikat PDF</span>
                </div>
                <div class="mt-5 space-y-3">
                    @forelse($topTen as $employee)
                        <div class="rounded-2xl border border-gray-100 bg-gray-50 p-4">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full {{ $employee['rank'] <= 3 ? 'bg-emerald-600 text-white' : 'bg-white text-gray-700 ring-1 ring-gray-200' }} text-sm font-black">{{ $employee['rank'] }}</div>
                                <div class="min-w-0 flex-1">
                                    <p class="truncate text-sm font-black text-gray-900">{{ $employee['name'] }}</p>
                                    <p class="truncate text-xs font-semibold text-gray-400">{{ $employee['employment_status'] }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xl font-black text-gray-900">{{ $employee['score'] }}</p>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-gray-400">Skor</p>
                                </div>
                            </div>
                            <div class="mt-3 flex items-center justify-between gap-3">
                                <span class="text-xs font-bold text-gray-500">Hadir {{ $employee['attendance_rate'] }}% - Disiplin {{ $employee['discipline_rate'] }}%</span>
                                <a href="{{ route('fingerprint.analysis.pdf', array_merge(['user' => $employee['user_id']], request()->query())) }}" class="rounded-xl bg-emerald-600 px-3 py-2 text-xs font-black text-white hover:bg-emerald-700">Unduh Sertifikat</a>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl border border-dashed border-gray-200 bg-gray-50 p-5 text-center text-sm font-bold text-gray-500">
                            Belum ada data ranking pada filter ini.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-100 bg-white shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="font-bold text-gray-900">Ranking dan Evaluasi Pegawai</h3>
                <p class="text-sm text-gray-500">Pegawai ranking 1-10 mendapat sertifikat apresiasi, ranking berikutnya mendapat report evaluasi kehadiran.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Pegawai</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Skor</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Hadir</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Terlambat</th>
                            <th class="px-6 py-3 text-center text-xs font-black uppercase tracking-wider text-gray-400">Tidak Hadir</th>
                            <th class="px-6 py-3 text-left text-xs font-black uppercase tracking-wider text-gray-400">Evaluasi</th>
                            <th class="px-6 py-3 text-right text-xs font-black uppercase tracking-wider text-gray-400">PDF</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($rankings as $employee)
                            <tr class="hover:bg-gray-50/70">
                                <td class="px-6 py-4">
                                    <span class="inline-flex h-9 w-9 items-center justify-center rounded-full {{ $employee['rank'] <= 10 ? 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100' : 'bg-gray-100 text-gray-700' }} text-sm font-black">{{ $employee['rank'] }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900">{{ $employee['name'] }}</div>
                                    <div class="text-xs text-gray-400">{{ $employee['employee_code'] ?: $employee['email'] }} - {{ $employee['employment_status'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex rounded-full px-3 py-1.5 text-sm font-black {{ $employee['score'] >= 85 ? 'bg-emerald-50 text-emerald-700' : ($employee['score'] >= 70 ? 'bg-amber-50 text-amber-700' : 'bg-red-50 text-red-700') }}">{{ $employee['score'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-700">{{ $employee['required_present_days'] }}/{{ $employee['required_days'] }}</td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-amber-700">{{ $employee['late_days'] }}</td>
                                <td class="px-6 py-4 text-center text-sm font-bold text-red-700">{{ $employee['absent_days'] }}</td>
                                <td class="px-6 py-4 max-w-md">
                                    <div class="font-bold text-gray-900">{{ $employee['evaluation']['title'] }}</div>
                                    <div class="text-xs leading-relaxed text-gray-500">{{ $employee['evaluation']['message'] }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('fingerprint.analysis.pdf', array_merge(['user' => $employee['user_id']], request()->query())) }}" class="inline-flex rounded-xl {{ $employee['rank'] <= 10 ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-gray-900 hover:bg-red-600' }} px-4 py-2 text-xs font-black text-white">
                                        {{ $employee['rank'] <= 10 ? 'Sertifikat' : 'Report Evaluasi' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-12 text-center text-gray-500">Belum ada pegawai termapping pada filter ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chartEl = document.getElementById('attendanceAnalysisChart');
            if (!chartEl || typeof Chart === 'undefined') return;

            const data = @json($chartData);
            new Chart(chartEl.getContext('2d'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        { label: 'Hadir', data: data.present, borderColor: '#10b981', backgroundColor: 'rgba(16,185,129,.12)', fill: true, tension: .42, pointRadius: 3 },
                        { label: 'Disiplin', data: data.discipline, borderColor: '#2563eb', backgroundColor: 'rgba(37,99,235,.08)', fill: true, tension: .42, pointRadius: 3 },
                        { label: 'Terlambat', data: data.late, borderColor: '#f59e0b', backgroundColor: 'rgba(245,158,11,.08)', fill: false, tension: .42, pointRadius: 3 },
                        { label: 'Tidak Hadir', data: data.absent, borderColor: '#ef4444', backgroundColor: 'rgba(239,68,68,.08)', fill: false, tension: .42, pointRadius: 3 },
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: { mode: 'index', intersect: false },
                    plugins: {
                        legend: { labels: { usePointStyle: true, boxWidth: 8, font: { weight: '700' } } },
                        tooltip: { backgroundColor: '#111827', padding: 12, titleFont: { weight: '800' }, bodyFont: { weight: '700' } },
                    },
                    scales: {
                        y: { beginAtZero: true, ticks: { precision: 0 }, grid: { color: 'rgba(148,163,184,.18)' } },
                        x: { grid: { display: false } },
                    },
                },
            });
        });
    </script>
</x-app-layout>
