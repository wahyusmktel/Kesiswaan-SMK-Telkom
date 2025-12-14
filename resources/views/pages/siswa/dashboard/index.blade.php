<x-app-layout>
    {{-- CSS untuk Animasi Gradient --}}
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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Siswa</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div
                class="relative rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 shadow-lg overflow-hidden p-6 sm:p-10 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/2 bg-white/10 transform skew-x-12"></div>
                <div class="relative z-10 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <div class="text-white">
                        <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👋
                        </h3>
                        <p class="mt-2 text-indigo-100 font-medium">Bagaimana kabarmu hari ini? Jangan lupa jaga
                            kesehatan ya.</p>
                    </div>
                    <a href="{{ route('izin.index') }}"
                        class="inline-flex items-center px-5 py-3 bg-white text-indigo-600 rounded-xl font-bold shadow-md hover:bg-indigo-50 hover:shadow-lg transition-all transform hover:-translate-y-1 group">
                        <svg class="w-5 h-5 mr-2 text-pink-500 group-hover:scale-110 transition-transform"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Buat Izin Baru
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div
                    class="bg-white rounded-2xl p-6 border border-indigo-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-indigo-500 uppercase tracking-wider">Total Diajukan</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-4xl font-black text-gray-800">{{ $totalDiajukan }}</h3>
                            <span class="ml-2 text-sm text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-indigo-50 rounded-full overflow-hidden">
                        <div class="h-full bg-indigo-500 w-full animate-pulse"></div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-green-600 uppercase tracking-wider">Disetujui</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-4xl font-black text-gray-800">{{ $totalDisetujui }}</h3>
                            <span class="ml-2 text-sm text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-green-50 rounded-full overflow-hidden">
                        <div class="h-full bg-green-500 w-full"
                            style="width: {{ $totalDiajukan > 0 ? ($totalDisetujui / $totalDiajukan) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-red-100 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="absolute right-0 top-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                        <svg class="w-24 h-24 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="relative z-10">
                        <p class="text-sm font-bold text-red-500 uppercase tracking-wider">Ditolak</p>
                        <div class="flex items-baseline mt-2">
                            <h3 class="text-4xl font-black text-gray-800">{{ $totalDitolak }}</h3>
                            <span class="ml-2 text-sm text-gray-500">kali</span>
                        </div>
                    </div>
                    <div class="mt-4 h-1 w-full bg-red-50 rounded-full overflow-hidden">
                        <div class="h-full bg-red-500 w-full"
                            style="width: {{ $totalDiajukan > 0 ? ($totalDitolak / $totalDiajukan) * 100 : 0 }}%">
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Statistik Izin Saya</h3>
                            <p class="text-sm text-gray-500">Proporsi status pengajuan izin</p>
                        </div>
                        <div class="p-2 bg-gray-50 rounded-lg">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                            </svg>
                        </div>
                    </div>

                    <div class="relative h-64 w-full flex items-center justify-center">
                        <canvas id="statusIzinChart"></canvas>
                        <div id="emptyChartMessage"
                            class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                            <svg class="w-12 h-12 mb-2 opacity-50" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <span class="text-sm">Belum ada data izin</span>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                        <h4 class="font-bold text-gray-800 mb-2">Panduan Izin</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Izin Sakit wajib melampirkan surat dokter.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Izin maksimal 3 hari berturut-turut.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Cek status izin secara berkala di menu Riwayat.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfigurasi Font Default
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const statusData = @json($statusChartData);

                // Cek jika data kosong
                if (!statusData.data || statusData.data.length === 0 || statusData.data.every(val => val === 0)) {
                    document.getElementById('emptyChartMessage').classList.remove('hidden');
                } else if (document.getElementById('statusIzinChart')) {
                    const ctxStatus = document.getElementById('statusIzinChart').getContext('2d');
                    new Chart(ctxStatus, {
                        type: 'doughnut', // Ubah jadi Doughnut biar lebih modern
                        data: {
                            labels: statusData.labels.map(label => label.charAt(0).toUpperCase() + label.slice(
                                1)),
                            datasets: [{
                                data: statusData.data,
                                // Warna disesuaikan dengan tema (Indigo, Green, Red)
                                backgroundColor: [
                                    '#6366f1', // Diajukan (Indigo)
                                    '#22c55e', // Disetujui (Green)
                                    '#ef4444', // Ditolak (Red)
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '70%', // Lubang tengah
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        usePointStyle: true,
                                        padding: 20,
                                        font: {
                                            size: 12,
                                            weight: 'bold'
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: 'rgba(255, 255, 255, 0.9)',
                                    titleColor: '#1e293b',
                                    bodyColor: '#475569',
                                    borderColor: '#e2e8f0',
                                    borderWidth: 1,
                                    padding: 10,
                                    boxPadding: 4
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
