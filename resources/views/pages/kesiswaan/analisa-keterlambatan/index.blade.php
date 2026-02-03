<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 w-full">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Analisa Keterlambatan Siswa</h2>
                <p class="text-sm text-gray-500 mt-1">Tahun Pelajaran: {{ $tahunAktif->nama_tahun ?? '-' }}</p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold border border-red-200">
                    Data Realtime
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            {{-- Summary Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {{-- Hari Ini --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-red-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Terlambat Hari Ini</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $summary['today'] }}</h3>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-400">Kasus tercatat hari ini</span>
                    </div>
                </div>

                {{-- Minggu Ini --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-orange-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M19 3h-1V1h-2v2H8V1H6v2H5c-1.11 0-1.99.9-1.99 2L3 19c0 1.1.89 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 16H5V8h14v11zM7 10h5v5H7z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Minggu Ini</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $summary['week'] }}</h3>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-400">Total minggu berjalan</span>
                    </div>
                </div>

                {{-- Bulan Ini --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M16 6l2.29 2.29-4.88 4.88-4-4L2 16.59 3.41 18l6-6 4 4 6.3-6.29L22 12V6z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Bulan Ini</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $summary['month'] }}</h3>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-400">Total bulan berjalan</span>
                    </div>
                </div>

                {{-- Total Kasus --}}
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group">
                    <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-16 h-16 text-purple-600" fill="currentColor" viewBox="0 0 24 24">
                            <path
                                d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zm6.93 6h-2.95c-.32-1.25-.78-2.45-1.38-3.56 1.84.63 3.37 1.91 4.33 3.56zM12 4.04c.83 1.2 1.48 2.53 1.91 3.96h-3.82c.43-1.43 1.08-2.76 1.91-3.96zM4.26 14C4.1 13.36 4 12.69 4 12s.1-1.36.26-2h3.38c-.08.66-.14 1.32-.14 2s.06 1.34.14 2H4.26zm.82 2h2.95c.32 1.25.78 2.45 1.38 3.56-1.84-.63-3.37-1.91-4.33-3.56zm2.95-8H5.08c.96-1.65 2.49-2.93 4.33-3.56-.6 1.11-1.06 2.31-1.38 3.56zM12 19.96c-.83-1.2-1.48-2.53-1.91-3.96h3.82c-.43 1.43-1.08 2.76-1.91 3.96zM14.34 14H9.66c-.09-.66-.16-1.32-.16-2s.07-1.34.16-2h4.68c.09.66.16 1.32.16 2s-.07 1.34-.16 2zm.25 5.56c.6-1.11 1.06-2.31 1.38-3.56h2.95c-.96 1.65-2.49 2.93-4.33 3.56zM16.36 14c.08-.66.14-1.32.14-2s-.06-1.34-.14-2h3.38c.16.64.26 1.31.26 2s-.1 1.36-.26 2h-3.38z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-gray-500">Total Kasus</p>
                    <h3 class="text-3xl font-black text-gray-900 mt-2">{{ $summary['total'] }}</h3>
                    <div class="mt-2 flex items-center text-xs">
                        <span class="text-gray-400">Akumulasi seluruh data</span>
                    </div>
                </div>
            </div>

            {{-- Main Charts --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Trend Chart --}}
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-bold text-gray-800">Tren Keterlambatan</h4>
                            <p class="text-xs text-gray-500">30 Hari terakhir</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="trendChart"></canvas>
                    </div>
                </div>

                {{-- Class Distribution --}}
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-bold text-gray-800">Distribusi Per Kelas</h4>
                            <p class="text-xs text-gray-500">Tahun Pelajaran Aktif</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Peak Times --}}
                <div
                    class="lg:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-bold text-gray-800">Analisis Waktu Kedatangan</h4>
                            <p class="text-xs text-gray-500">Jam puncak keterlambatan (06:00 - 18:00)</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="peakTimeChart"></canvas>
                    </div>
                </div>

                {{-- Reasons Distribution --}}
                <div
                    class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h4 class="font-bold text-gray-800">Alasan Terbanyak</h4>
                            <p class="text-xs text-gray-500">Kategorisasi alasan siswa</p>
                        </div>
                    </div>
                    <div class="h-80 flex items-center justify-center">
                        <canvas id="reasonsChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Top Repeat Offenders --}}
            <div
                class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow">
                <div class="px-6 py-4 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h4 class="font-bold text-gray-800 text-sm uppercase tracking-wider">Top 10 Siswa Sering
                            Terlambat</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Siswa dengan frekuensi keterlambatan tertinggi</p>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="text-gray-400 text-[10px] font-black uppercase tracking-widest border-b border-gray-50">
                                <th class="px-6 py-4">Peringkat</th>
                                <th class="px-6 py-4">Nama Siswa</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4 text-center">Total Terlambat</th>
                                <th class="px-6 py-4 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($topStudents as $student)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <span
                                            class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-sm {{ $loop->iteration <= 3 ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-600' }}">
                                            {{ $loop->iteration }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-gray-200 flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name={{ urlencode($student->user->name) }}&background=f1f5f9&color=64748b"
                                                    class="w-full h-full rounded-full" alt="">
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-800">{{ $student->user->name }}</p>
                                                <p class="text-xs text-gray-500">NIS: {{ $student->nis }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span
                                            class="text-sm text-gray-600">{{ $student->rombels->first()?->kelas->nama_kelas ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span
                                            class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-xs font-black border border-red-100">
                                            {{ $student->keterlambatans_count }} Kali
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('bk.monitoring-catatan.show', $student->id) }}"
                                            class="text-xs font-bold text-blue-600 hover:underline">
                                            Lihat Rekap
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center opacity-30">
                                            <svg class="w-12 h-12 mb-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-sm font-bold">Belum ada data tersedia</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Global Chart Defaults
                Chart.defaults.font.family = "'Plus Jakarta Sans', sans-serif";
                Chart.defaults.color = '#94a3b8';

                // Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                const trendGradient = trendCtx.createLinearGradient(0, 0, 0, 300);
                trendGradient.addColorStop(0, 'rgba(239, 68, 68, 0.1)');
                trendGradient.addColorStop(1, 'rgba(239, 68, 68, 0)');

                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: @json($trendChart['labels']),
                        datasets: [{
                            label: 'Kasus',
                            data: @json($trendChart['data']),
                            borderColor: '#ef4444',
                            borderWidth: 3,
                            pointRadius: 0,
                            pointHoverRadius: 5,
                            pointBackgroundColor: '#ef4444',
                            tension: 0.4,
                            fill: true,
                            backgroundColor: trendGradient
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] }, ticks: { stepSize: 1 } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Class Chart
                new Chart(document.getElementById('classChart'), {
                    type: 'bar',
                    data: {
                        labels: @json($classChart['labels']),
                        datasets: [{
                            label: 'Total Terlambat',
                            data: @json($classChart['data']),
                            backgroundColor: '#3b82f6',
                            borderRadius: 8,
                            barThickness: 20
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Peak Time Chart
                const peakCtx = document.getElementById('peakTimeChart').getContext('2d');
                const peakGradient = peakCtx.createLinearGradient(0, 0, 0, 300);
                peakGradient.addColorStop(0, 'rgba(139, 92, 246, 0.1)');
                peakGradient.addColorStop(1, 'rgba(139, 92, 246, 0)');

                new Chart(peakCtx, {
                    type: 'line',
                    data: {
                        labels: @json($peakTimeChart['labels']),
                        datasets: [{
                            label: 'Frekuensi',
                            data: @json($peakTimeChart['data']),
                            borderColor: '#8b5cf6',
                            backgroundColor: peakGradient,
                            fill: true,
                            tension: 0.4,
                            borderWidth: 3,
                            pointRadius: 4,
                            pointBackgroundColor: '#8b5cf6'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                            x: { grid: { display: false } }
                        }
                    }
                });

                // Reasons Chart
                new Chart(document.getElementById('reasonsChart'), {
                    type: 'doughnut',
                    data: {
                        labels: @json($reasonsChart['labels']),
                        datasets: [{
                            data: @json($reasonsChart['data']),
                            backgroundColor: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981', '#6366f1'],
                            borderWidth: 0,
                            hoverOffset: 10
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '70%',
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    padding: 20,
                                    usePointStyle: true,
                                    font: { size: 10, weight: 'bold' }
                                }
                            }
                        }
                    }
                });
            });
        </script>
    @endpush
</x-app-layout>