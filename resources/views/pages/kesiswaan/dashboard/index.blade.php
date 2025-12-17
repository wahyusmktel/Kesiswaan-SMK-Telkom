<x-app-layout>
    {{-- Custom CSS untuk Animasi Gradient (Bisa dipindah ke app.css nanti) --}}
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
        {{-- Header dibuat w-full juga agar selaras --}}
        <div class="w-full flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Kesiswaan</h2>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        {{-- UBAHAN 1: Mengganti max-w-7xl menjadi w-full agar full width --}}
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="flex items-center gap-2 mb-4 px-1">
                <div class="w-1.5 h-6 bg-red-600 rounded-full shadow-sm"></div>
                <h3 class="text-lg font-bold text-gray-800 tracking-tight">Aktifitas Perizinan Tidak Sekolah Hari ini
                </h3>
            </div>

            {{-- Widget Statistik dengan Animasi Gradient --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">

                {{-- CARD 1: MERAH (Pengajuan) --}}
                <div
                    class="rounded-xl p-5 border border-red-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-red-50 via-white to-red-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-red-800 uppercase tracking-wider">Pengajuan Hari Ini</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ $summary['today_submissions'] ?? 0 }}
                            </h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-red-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-medium">
                        <span class="text-red-700 flex items-center bg-red-100/50 px-2 py-0.5 rounded-full">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                            +{{ $summary['today_delta'] ?? 0 }}
                        </span>
                        <span class="text-gray-500 ml-2">dari kemarin</span>
                    </div>
                </div>

                {{-- CARD 2: KUNING (Disetujui) --}}
                <div
                    class="rounded-xl p-5 border border-amber-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-amber-50 via-white to-amber-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-amber-800 uppercase tracking-wider">Disetujui</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ $summary['approved'] ?? 0 }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-amber-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-medium">
                        <span class="text-amber-700 bg-amber-100/50 px-2 py-0.5 rounded-full">Rate
                            {{ $summary['approve_rate'] ?? '0%' }}</span>
                        <span class="text-gray-500 ml-2">persetujuan</span>
                    </div>
                </div>

                {{-- CARD 3: HIJAU (Selesai) --}}
                <div
                    class="rounded-xl p-5 border border-green-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-green-50 via-white to-green-100">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-green-800 uppercase tracking-wider">Selesai</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ $summary['completed'] ?? 0 }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-green-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-medium">
                        <span
                            class="text-green-700 bg-green-100/50 px-2 py-0.5 rounded-full">{{ $summary['completed_week'] ?? 0 }}</span>
                        <span class="text-gray-500 ml-2">minggu ini</span>
                    </div>
                </div>

                {{-- CARD 4: ABU-ABU (Ditolak) --}}
                <div
                    class="rounded-xl p-5 border border-gray-200 shadow-sm relative overflow-hidden animate-gradient bg-gradient-to-br from-gray-50 via-white to-gray-200">
                    <div class="flex justify-between items-start z-10 relative">
                        <div>
                            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider">Ditolak</p>
                            <h3 class="mt-1 text-2xl font-black text-gray-800">{{ $summary['rejected'] ?? 0 }}</h3>
                        </div>
                        <div class="p-2 bg-white/60 backdrop-blur rounded-lg text-gray-600 shadow-sm">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center text-xs font-medium">
                        <span class="text-gray-700 bg-gray-100/50 px-2 py-0.5 rounded-full">Trend
                            {{ $summary['rejected_trend'] ?? '0%' }}</span>
                        <span class="text-gray-500 ml-2">bulan ini</span>
                    </div>
                </div>
            </div>

            {{-- BAGIAN GRAFIK 1 --}}
            <div>
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-red-600 rounded-full shadow-sm"></div>
                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Analisa Izin Tidak Masuk Sekolah</h3>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-1 space-y-6">
                        <div
                            class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Proporsi Status</h4>
                            <div class="h-48 flex items-center justify-center">
                                <canvas id="statusIzinChart"></canvas>
                            </div>
                        </div>

                        <div
                            class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm flex flex-col h-[380px] hover:shadow-md transition-shadow duration-300">
                            <h4
                                class="font-bold text-gray-800 mb-4 flex items-center justify-between text-sm uppercase">
                                <span>Aktivitas Terkini</span>
                                <span
                                    class="text-xs font-normal normal-case text-gray-400 bg-gray-100 px-2 py-1 rounded-full">Realtime</span>
                            </h4>
                            <div class="flex-1 overflow-y-auto pr-2 space-y-4 custom-scrollbar">
                                @forelse ($latestActivities as $activity)
                                    <div
                                        class="flex items-start gap-3 pb-3 border-b border-gray-50 last:border-0 hover:bg-gray-50 p-2 rounded-lg transition-colors">
                                        <div class="flex-shrink-0 mt-0.5">
                                            @if ($activity->status == 'diajukan')
                                                <div
                                                    class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                </div>
                                            @elseif ($activity->status == 'disetujui')
                                                <div
                                                    class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M5 13l4 4L19 7" />
                                                    </svg>
                                                </div>
                                            @else
                                                <div
                                                    class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600 shadow-sm">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm text-gray-800 leading-snug">
                                                <span class="font-bold">{{ $activity->user->name }}</span>
                                                @if ($activity->status == 'diajukan')
                                                    mengajukan izin baru.
                                                @else
                                                    telah <span
                                                        class="{{ $activity->status == 'disetujui' ? 'text-green-600' : 'text-red-600' }} font-semibold">{{ $activity->status }}</span>.
                                                @endif
                                            </p>
                                            <p class="text-xs text-gray-400 mt-1 flex items-center">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                {{ $activity->updated_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <div class="h-full flex flex-col items-center justify-center text-gray-400">
                                        <svg class="w-10 h-10 mb-3 opacity-30" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                        </svg>
                                        <span class="text-sm">Belum ada aktivitas</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div
                            class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm h-full hover:shadow-md transition-shadow duration-300">
                            <div class="flex items-center justify-between mb-6">
                                <h4 class="font-bold text-gray-800 text-sm uppercase">Tren Pengajuan (30 Hari)</h4>
                            </div>
                            <div class="w-full h-[450px]">
                                <canvas id="trenIzinChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- BAGIAN GRAFIK 2 --}}
            <div>
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-amber-500 rounded-full shadow-sm"></div>
                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Monitoring Keluar Kelas</h3>
                </div>

                {{-- Tabel Full Width --}}
                <div
                    class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6 hover:shadow-md transition-shadow duration-300">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
                        <h4 class="font-bold text-gray-800 text-sm uppercase">Aktivitas Hari Ini</h4>
                        <span
                            class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full text-xs font-bold border border-amber-200 shadow-sm">{{ count($izinKeluarHariIni) }}
                            Siswa</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full whitespace-nowrap">
                            <thead>
                                <tr class="text-left bg-gray-50/50 border-b border-gray-200">
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Jam
                                    </th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Siswa</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Kelas</th>
                                    <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Status</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">
                                        Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($izinKeluarHariIni as $izin)
                                    <tr class="hover:bg-amber-50/30 transition-colors group" x-data="{ item: {{ json_encode($izin) }} }">
                                        <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                            {{ \Carbon\Carbon::parse($izin->created_at)->format('H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm font-bold text-gray-800">
                                            {{ $izin->siswa->name }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-600">
                                            {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @php
                                                $statusClasses = [
                                                    'selesai' => 'bg-gray-100 text-gray-700 border-gray-200',
                                                    'terlambat' => 'bg-red-50 text-red-700 border-red-200',
                                                    'disetujui_guru_piket' =>
                                                        'bg-green-50 text-green-700 border-green-200',
                                                    'diverifikasi_security' =>
                                                        'bg-green-50 text-green-700 border-green-200',
                                                    'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                                ];
                                                $defaultClass = 'bg-amber-50 text-amber-800 border-amber-200';
                                                $class = $statusClasses[$izin->status] ?? $defaultClass;
                                            @endphp
                                            <span
                                                class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-bold border shadow-sm {{ $class }}">
                                                {{ str_replace('_', ' ', Str::title($izin->status)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="$store.detailModal.open(item)"
                                                class="text-gray-400 group-hover:text-amber-600 transition-colors">
                                                <span
                                                    class="text-xs font-bold underline decoration-dotted">Detail</span>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-400">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 mb-3 text-gray-200" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <span class="font-medium">Belum ada siswa izin keluar hari ini.</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Top Siswa --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Top 10 Sering Keluar</h4>
                        <div class="space-y-2 max-h-64 overflow-y-auto pr-2 custom-scrollbar">
                            @forelse ($topSiswaIzinKeluar as $siswa)
                                <div
                                    class="flex items-center justify-between p-2.5 rounded-lg hover:bg-gray-50 transition-colors border border-transparent hover:border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <span
                                            class="w-6 h-6 flex items-center justify-center rounded-md bg-gray-800 text-xs font-bold text-white shadow-sm">{{ $loop->iteration }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-700 truncate max-w-[120px]">{{ $siswa->name }}</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold px-2.5 py-1 bg-red-100 text-red-700 rounded-full border border-red-200">{{ $siswa->izin_meninggalkan_kelas_count }}x</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center py-4">Belum ada data.</p>
                            @endforelse
                        </div>
                    </div>

                    {{-- Per Rombel --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Per Rombel</h4>
                        <div class="h-64">
                            <canvas id="rombelIzinKeluarChart"></canvas>
                        </div>
                    </div>

                    {{-- Proporsi Tujuan --}}
                    <div
                        class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Proporsi Tujuan</h4>
                        <div class="h-64 flex items-center justify-center">
                            <canvas id="tujuanIzinKeluarChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('pages.kesiswaan.dashboard.partials.modal-lihat-izin-keluar')

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Konfigurasi Default Chart.js agar font lebih enak dilihat
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';
                Chart.defaults.scale.grid.color = '#f1f5f9';

                // Data dari Controller
                const statusData = @json($statusChartData);
                const dailyData = @json($dailyChartData);
                const rombelIzinKeluarData = @json($rombelIzinKeluarChartData);
                const tujuanIzinKeluarData = @json($tujuanIzinKeluarChartData);

                // 1. Doughnut Chart: Status Izin
                if (document.getElementById('statusIzinChart')) {
                    new Chart(document.getElementById('statusIzinChart'), {
                        type: 'doughnut',
                        data: {
                            labels: statusData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                            datasets: [{
                                data: statusData.data,
                                backgroundColor: ['#f43f5e', '#22c55e', '#eab308', '#94a3b8'],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%',
                            plugins: {
                                legend: {
                                    position: 'right',
                                    labels: {
                                        boxWidth: 12,
                                        usePointStyle: true,
                                        font: {
                                            weight: 'bold'
                                        }
                                    }
                                }
                            }
                        }
                    });
                }

                // 2. Line Chart: Tren Harian (Smooth & Gradient)
                if (document.getElementById('trenIzinChart')) {
                    const ctx = document.getElementById('trenIzinChart').getContext('2d');
                    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
                    gradient.addColorStop(0, 'rgba(244, 63, 94, 0.2)');
                    gradient.addColorStop(1, 'rgba(244, 63, 94, 0)');

                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: dailyData.labels,
                            datasets: [{
                                label: 'Pengajuan',
                                data: dailyData.data,
                                borderColor: '#f43f5e',
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.4, // Lebih smooth
                                fill: true,
                                pointRadius: 0,
                                pointHoverRadius: 6,
                                pointBackgroundColor: '#f43f5e'
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
                            interaction: {
                                intersect: false,
                                mode: 'index'
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

                // 3. Bar Chart: Izin Keluar per Rombel
                if (document.getElementById('rombelIzinKeluarChart')) {
                    new Chart(document.getElementById('rombelIzinKeluarChart'), {
                        type: 'bar',
                        data: {
                            labels: rombelIzinKeluarData.labels,
                            datasets: [{
                                label: 'Total',
                                data: rombelIzinKeluarData.data,
                                backgroundColor: '#f59e0b',
                                borderRadius: 6,
                                barThickness: 24
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

                // 4. Doughnut Chart: Tujuan Izin Keluar
                if (document.getElementById('tujuanIzinKeluarChart')) {
                    new Chart(document.getElementById('tujuanIzinKeluarChart'), {
                        type: 'doughnut',
                        data: {
                            labels: tujuanIzinKeluarData.labels,
                            datasets: [{
                                data: tujuanIzinKeluarData.data,
                                backgroundColor: ['#f43f5e', '#3b82f6', '#22c55e', '#eab308',
                                    '#94a3b8'
                                ],
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '65%',
                            plugins: {
                                legend: {
                                    position: 'bottom',
                                    labels: {
                                        boxWidth: 10,
                                        usePointStyle: true,
                                        font: {
                                            size: 11,
                                            weight: 'bold'
                                        },
                                        padding: 20
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
