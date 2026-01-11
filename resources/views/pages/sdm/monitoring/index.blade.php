<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Perizinan & Kehadiran Guru') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            {{-- Stats Overview --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 bg-purple-100 text-purple-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Izin Sekolah</p>
                        <h3 class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_izin_sekolah'] }}</h3>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Izin Luar</p>
                        <h3 class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_izin_luar'] }}</h3>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 bg-red-100 text-red-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Terlambat</p>
                        <h3 class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_terlambat'] }}</h3>
                    </div>
                </div>

                <div class="bg-white p-5 rounded-3xl border border-gray-200 shadow-sm flex items-center gap-3">
                    <div class="w-12 h-12 bg-yellow-100 text-yellow-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-[9px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Menunggu</p>
                        <h3 class="text-2xl font-black text-gray-900 leading-none">{{ $stats['total_pending'] }}</h3>
                    </div>
                </div>
            </div>

            {{-- Chart Widget --}}
            <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                <div class="flex justify-between items-center mb-8">
                    <div>
                        <h3 class="text-xl font-black text-gray-900">Tren Izin Guru</h3>
                        <p class="text-sm text-gray-500">Statistik persetujuan izin 7 hari terakhir</p>
                    </div>
                    <div class="hidden md:block">
                        <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100 italic">
                            Data Diperbarui Real-time
                        </span>
                    </div>
                </div>
                <div class="h-[400px]">
                    <canvas id="monitoringChart"></canvas>
                </div>
            </div>

            {{-- Additional Info Grid --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                {{-- Quick Insight --}}
                <div class="bg-gradient-to-br from-gray-900 to-gray-800 p-8 rounded-3xl text-white shadow-lg flex flex-col justify-between">
                    <div>
                        <h4 class="text-lg font-bold mb-4">Ringkasan Sistem</h4>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Total Guru Terdaftar</span>
                                <span class="font-bold">{{ \App\Models\MasterGuru::count() }} Orang</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-gray-400">Rata-rata Izin Harian</span>
                                <span class="font-bold">{{ number_format((collect($chartData['izin_sekolah'])->avg() + collect($chartData['izin_luar'])->avg() + collect($chartData['terlambat'])->avg()), 1) }} Kasus</span>
                            </div>
                        </div>
                    </div>
                    <div class="mt-8">
                        <a href="{{ route('sdm.rekapitulasi.index') }}" class="w-full inline-flex items-center justify-center px-6 py-3 bg-white text-gray-900 rounded-2xl font-black hover:bg-gray-100 transition-colors">
                            Lihat Laporan Lengkap
                        </a>
                    </div>
                </div>

                {{-- Legend or Info --}}
                <div class="bg-white p-8 rounded-3xl border border-gray-200 shadow-sm">
                    <h4 class="text-lg font-bold text-gray-800 mb-4">Informasi Monitoring</h4>
                    <p class="text-gray-600 text-sm leading-relaxed mb-4">
                        Data grafik di samping menunjukkan jumlah perizinan yang telah disetujui (valid) setiap harinya. Digunakan untuk memantau fluktuasi kehadiran tenaga pendidik guna menjamin stabilitas kegiatan belajar mengajar.
                    </p>
                    <div class="flex items-center gap-3 p-4 bg-blue-50 rounded-2xl text-blue-700 border border-blue-100">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs font-medium italic">Klik titik data pada grafik untuk detail angka.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('monitoringChart').getContext('2d');
            
            // Create Gradient
            const gradient = ctx.createLinearGradient(0, 0, 0, 400);
            gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)');
            gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartData['labels']) !!},
                    datasets: [
                        {
                            label: 'Lingkungan Sekolah',
                            data: {!! json_encode($chartData['izin_sekolah']) !!},
                            borderColor: '#8B5CF6',
                            borderWidth: 3,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#8B5CF6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Luar Sekolah / Absen',
                            data: {!! json_encode($chartData['izin_luar']) !!},
                            borderColor: '#F97316',
                            borderWidth: 3,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#F97316',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        },
                        {
                            label: 'Terlambat',
                            data: {!! json_encode($chartData['terlambat']) !!},
                            borderColor: '#EF4444',
                            borderWidth: 3,
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.4,
                            pointBackgroundColor: '#EF4444',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            align: 'end',
                            labels: {
                                usePointStyle: true,
                                boxWidth: 6,
                                font: {
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            backgroundColor: '#fff',
                            titleColor: '#111827',
                            bodyColor: '#4b5563',
                            borderColor: '#e5e7eb',
                            borderWidth: 1,
                            padding: 12,
                            displayColors: false,
                            callbacks: {
                                label: function(context) {
                                    return context.dataset.label + ': ' + context.parsed.y + ' Kasus';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                display: true,
                                color: '#f3f4f6',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    weight: 'bold'
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
    @endpush
</x-app-layout>
