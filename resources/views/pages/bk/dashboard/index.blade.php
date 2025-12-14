<x-app-layout>
    {{-- CSS Gradient --}}
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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Bimbingan Konseling</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div
                class="relative rounded-2xl bg-gradient-to-r from-violet-600 via-purple-600 to-fuchsia-600 shadow-lg overflow-hidden p-8 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 text-white">
                    <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👩‍🏫</h3>
                    <p class="mt-2 text-violet-100 font-medium text-lg">Pantau kedisiplinan dan absensi siswa dengan
                        mudah hari ini.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-violet-600 rounded-full"></span>
                                Top 5 Siswa Sering Izin
                            </h4>
                            <span
                                class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full border border-gray-200">Semua
                                Waktu</span>
                        </div>

                        <div class="relative h-72 w-full">
                            <canvas id="topSiswaChart"></canvas>
                            <div id="emptyBarMessage"
                                class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                <span class="text-sm">Belum ada data yang cukup.</span>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="p-3 bg-red-100 text-red-600 rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Perlu Perhatian</p>
                                <h3 class="text-2xl font-bold text-gray-800">3 Siswa</h3>
                                <p class="text-xs text-gray-500">Izin > 5x bulan ini</p>
                            </div>
                        </div>
                        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-sm flex items-center gap-4">
                            <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Izin</p>
                                <h3 class="text-2xl font-bold text-gray-800">
                                    {{ collect($topSiswaChartData['data'] ?? [])->sum() }}
                                </h3>
                                <p class="text-xs text-gray-500">Akumulasi semua siswa</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div
                        class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden h-full flex flex-col">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-purple-500 rounded-full"></span>
                                Izin Hari Ini
                            </h4>
                            <span
                                class="bg-purple-100 text-purple-700 px-2 py-1 rounded-md text-xs font-bold">{{ $izinHariIni->count() }}</span>
                        </div>

                        <div class="flex-1 overflow-y-auto max-h-[500px] p-2 space-y-1 custom-scrollbar">
                            @forelse ($izinHariIni as $izin)
                                <div
                                    class="p-3 rounded-xl hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100 group">
                                    <div class="flex justify-between items-start mb-1">
                                        <p
                                            class="text-sm font-bold text-gray-900 line-clamp-1 w-2/3 group-hover:text-violet-600 transition-colors">
                                            {{ $izin->user->name }}</p>
                                        @php
                                            $statusColor = match ($izin->status) {
                                                'disetujui' => 'bg-green-100 text-green-700',
                                                'ditolak' => 'bg-red-100 text-red-700',
                                                default => 'bg-yellow-100 text-yellow-700',
                                            };
                                        @endphp
                                        <span
                                            class="text-[10px] font-bold px-2 py-0.5 rounded-full uppercase {{ $statusColor }}">
                                            {{ $izin->status }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-500 mb-2">
                                        {{ $izin->user->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                    </p>
                                    <div class="text-xs text-gray-600 bg-gray-100 p-2 rounded-lg italic">
                                        "{{ Str::limit(strip_tags($izin->keterangan), 50) }}"
                                    </div>
                                </div>
                            @empty
                                <div
                                    class="flex flex-col items-center justify-center h-48 text-gray-400 text-center p-6">
                                    <svg class="w-12 h-12 mb-3 opacity-30" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm font-medium">Hore! Tidak ada siswa izin hari ini.</p>
                                </div>
                            @endforelse
                        </div>

                        @if ($izinHariIni->count() > 0)
                            <div class="p-4 border-t border-gray-100 bg-gray-50 text-center">
                                <a href="{{ route('bk.monitoring.index') }}"
                                    class="text-xs font-bold text-violet-600 hover:text-violet-800 hover:underline">
                                    Lihat Semua Data &rarr;
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const topSiswaData = @json($topSiswaChartData);

                if (document.getElementById('topSiswaChart')) {
                    if (topSiswaData.data.length === 0) {
                        document.getElementById('emptyBarMessage').classList.remove('hidden');
                    } else {
                        const ctxTopSiswa = document.getElementById('topSiswaChart').getContext('2d');
                        new Chart(ctxTopSiswa, {
                            type: 'bar',
                            data: {
                                labels: topSiswaData.labels,
                                datasets: [{
                                    label: 'Jumlah Izin',
                                    data: topSiswaData.data,
                                    backgroundColor: 'rgba(124, 58, 237, 0.8)', // Violet-600
                                    hoverBackgroundColor: 'rgba(124, 58, 237, 1)',
                                    borderRadius: 6,
                                    barThickness: 24,
                                }]
                            },
                            options: {
                                indexAxis: 'y', // Horizontal Bar
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    },
                                    tooltip: {
                                        backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                        titleColor: '#1e293b',
                                        bodyColor: '#475569',
                                        borderColor: '#e2e8f0',
                                        borderWidth: 1,
                                        padding: 10
                                    }
                                },
                                scales: {
                                    x: {
                                        beginAtZero: true,
                                        grid: {
                                            display: true,
                                            borderDash: [2, 2]
                                        },
                                        ticks: {
                                            stepSize: 1
                                        }
                                    },
                                    y: {
                                        grid: {
                                            display: false
                                        }
                                    }
                                }
                            }
                        });
                    }
                }
            });
        </script>
    @endpush
</x-app-layout>
