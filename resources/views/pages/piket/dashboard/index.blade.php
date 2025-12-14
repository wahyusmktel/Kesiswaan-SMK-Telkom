<x-app-layout>
    {{-- CSS Gradient Animation --}}
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Piket</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div
                class="relative rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 shadow-lg overflow-hidden p-8 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 text-white">
                    <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👮‍♂️</h3>
                    <p class="mt-2 text-amber-100 font-medium text-lg">Siap memantau aktivitas siswa hari ini? Tetap
                        semangat!</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    class="bg-white rounded-2xl p-6 border border-indigo-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-indigo-500 uppercase tracking-wider">Anda Proses</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $totalIzinDiprosesPiket }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Total Izin Keluar</p>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-amber-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-amber-500 uppercase tracking-wider">Izin Hari Ini</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $izinHariIni->count() }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Tidak Masuk Sekolah</p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                                Izin Tidak Masuk (Hari Ini)
                            </h3>
                            <span
                                class="text-xs font-medium text-gray-500 bg-white border px-2 py-1 rounded">{{ date('d M Y') }}</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($izinHariIni as $izin)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3 font-medium text-gray-900">{{ $izin->user->name }}</td>
                                            <td class="px-6 py-3 text-gray-500">
                                                {{ $izin->user->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                @php
                                                    $statusClass = match ($izin->status) {
                                                        'diajukan' => 'bg-yellow-100 text-yellow-800',
                                                        'disetujui' => 'bg-green-100 text-green-800',
                                                        'ditolak' => 'bg-red-100 text-red-800',
                                                        default => 'bg-gray-100 text-gray-800',
                                                    };
                                                @endphp
                                                <span
                                                    class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                                    {{ $izin->status }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                    <span>Semua siswa hadir (atau belum ada data).</span>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6">Tren Izin Keluar (30 Hari Terakhir)</h4>
                        <div class="h-64 w-full">
                            <canvas id="trenIzinPribadiChart"></canvas>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-4 text-center">Proporsi Status</h4>
                        <div class="h-48 flex justify-center">
                            <canvas id="statusIzinChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800 text-sm uppercase">Top Siswa Sering Keluar</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto custom-scrollbar">
                            @forelse ($topSiswaIzinKeluarGlobal as $siswa)
                                <div
                                    class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-xs font-bold text-gray-600">{{ $loop->iteration }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900 truncate w-32">
                                                {{ $siswa->name }}</p>
                                            <p class="text-[10px] text-gray-500">Global Stats</p>
                                        </div>
                                    </div>
                                    <span
                                        class="text-xs font-bold px-2 py-1 bg-red-50 text-red-600 rounded-full">{{ $siswa->izin_meninggalkan_kelas_count }}x</span>
                                </div>
                            @empty
                                <div class="p-6 text-center text-sm text-gray-400">Belum ada data.</div>
                            @endforelse
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Config Defaults
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                // Data
                const statusData = @json($statusChartData);
                const dailyDataPiket = @json($dailyChartDataPiket);

                // 1. Doughnut Chart: Status Izin
                if (document.getElementById('statusIzinChart')) {
                    new Chart(document.getElementById('statusIzinChart'), {
                        type: 'doughnut',
                        data: {
                            labels: statusData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                            datasets: [{
                                data: statusData.data,
                                backgroundColor: ['#f59e0b', '#10b981', '#ef4444'], // Amber, Green, Red
                                borderWidth: 0,
                                hoverOffset: 4
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
                                        usePointStyle: true,
                                        boxWidth: 8,
                                        padding: 15,
                                        font: {
                                            size: 11
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Line Chart: Tren Izin Keluar (Smooth)
                if (document.getElementById('trenIzinPribadiChart')) {
                    const ctx = document.getElementById('trenIzinPribadiChart').getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(99, 102, 241, 0.2)'); // Indigo pudar
                    gradient.addColorStop(1, 'rgba(99, 102, 241, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dailyDataPiket.labels,
                            datasets: [{
                                label: 'Diproses',
                                data: dailyDataPiket.data,
                                borderColor: '#6366f1',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    },
                                    grid: {
                                        borderDash: [2, 4]
                                    }
                                },
                                x: {
                                    grid: {
                                        display: false
                                    }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
