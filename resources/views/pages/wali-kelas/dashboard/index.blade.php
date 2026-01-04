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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Wali Kelas</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div
                class="relative rounded-2xl bg-gradient-to-r from-teal-500 via-emerald-500 to-green-500 shadow-lg overflow-hidden p-8 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 text-white">
                    <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👨‍🏫</h3>
                    @if ($rombel = Auth::user()->rombels->first())
                        <p class="mt-2 text-teal-100 font-medium text-lg">
                            Wali Kelas dari <span
                                class="font-bold bg-white/20 px-2 py-1 rounded">{{ $rombel->kelas->nama_kelas }}</span>.
                            Semangat memantau siswa hari ini!
                        </p>
                    @else
                        <p class="mt-2 text-teal-100 font-medium text-lg">Selamat datang di panel wali kelas.</p>
                    @endif
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-1 space-y-6">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-teal-500 rounded-full"></span>
                            Status Izin Kelas
                        </h4>
                        <div class="relative h-64 w-full flex items-center justify-center">
                            <canvas id="statusIzinChart"></canvas>
                            <div id="emptyPieMessage"
                                class="hidden absolute inset-0 flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" />
                                </svg>
                                <span class="text-xs">Belum ada data</span>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-2xl p-5 border border-green-100">
                        <h4 class="font-bold text-green-900 mb-3 text-sm uppercase italic">Pintasan Cepat</h4>
                        <div class="space-y-2">
                            <a href="{{ route('wali-kelas.perizinan.index') }}"
                                class="block w-full py-2.5 px-4 bg-white text-green-700 text-sm font-bold rounded-xl shadow-sm hover:bg-green-600 hover:text-white transition-all text-center border border-green-200">
                                Periksa Pengajuan Izin
                            </a>
                            <a href="{{ route('monitoring-keterlambatan.index') }}"
                                class="block w-full py-2.5 px-4 bg-white text-emerald-700 text-sm font-bold rounded-xl shadow-sm hover:bg-emerald-600 hover:text-white transition-all text-center border border-emerald-200">
                                Monitoring Keterlambatan
                            </a>
                        </div>
                    </div>

                    {{-- NEW: Top 5 Late Students --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-red-500 rounded-full"></span>
                            Top 5 Kasus Terlambat
                        </h4>
                        <div class="space-y-4">
                            @forelse($topLateStudents as $late)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-red-50 text-red-600 flex items-center justify-center font-bold text-xs">
                                            {{ $loop->iteration }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800 leading-none">{{ $late->siswa->user->name }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $late->siswa->nis }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-black text-red-600">{{ $late->total }}</span>
                                        <span class="text-[10px] text-gray-400 block uppercase">Kasus</span>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-gray-400 text-sm italic py-4">Belum ada data</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2 space-y-8">

                    {{-- NEW: Today's Lateness Widget --}}
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-red-50 flex justify-between items-center">
                            <h4 class="font-bold text-red-800 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                Terlambat Hari Ini
                            </h4>
                            <span class="px-2 py-0.5 bg-red-600 text-white rounded-full text-[10px] font-black uppercase tracking-widest">{{ $terlambatHariIni->count() }} Siswa</span>
                        </div>
                        <div class="max-h-[300px] overflow-y-auto custom-scrollbar">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Waktu</th>
                                        <th class="px-6 py-3 text-right">Detail</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($terlambatHariIni as $late)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900">{{ $late->siswa->user->name }}</div>
                                                <div class="text-[10px] text-gray-400 font-medium">{{ $late->siswa->nis }}</div>
                                            </td>
                                            <td class="px-6 py-4">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded font-black text-[10px] border border-gray-200">
                                                    {{ $late->waktu_dicatat_security->format('H:i') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('monitoring-keterlambatan.show', $late->id) }}" class="text-teal-600 hover:text-teal-800 font-bold text-xs uppercase tracking-widest">Lihat</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="p-8 text-center text-gray-400 italic">Belum ada siswa terlambat hari ini.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800">Aktivitas Terkini</h4>
                            <span class="text-xs font-medium text-gray-500 bg-white border px-2 py-1 rounded">Realtime
                                Feed</span>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[350px] overflow-y-auto custom-scrollbar">
                            @forelse ($latestActivities as $activity)
                                <div class="p-4 hover:bg-gray-50 transition-colors flex items-start gap-4">
                                    <div class="flex-shrink-0 mt-1">
                                        @if ($activity->status == 'diajukan')
                                            <div
                                                class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                </svg>
                                            </div>
                                        @elseif ($activity->status == 'disetujui')
                                            <div
                                                class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @else
                                            <div
                                                class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm text-gray-800">
                                            <span class="font-bold">{{ $activity->user->name }}</span>
                                            @if ($activity->status == 'diajukan')
                                                mengajukan izin baru.
                                            @else
                                                telah <span
                                                    class="font-bold {{ $activity->status == 'disetujui' ? 'text-green-600' : 'text-red-600' }}">{{ $activity->status }}</span>.
                                            @endif
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            {{ $activity->updated_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>
                            @empty
                                <div class="p-8 text-center text-gray-400">
                                    <p>Belum ada aktivitas di kelas Anda.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-emerald-500 rounded-full"></span>
                            Statistik Keterlambatan Kelas (30 Hari Terakhir)
                        </h4>
                        <div class="h-64 w-full">
                            <canvas id="latenessTrendChart"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6">Tren Izin Harian (15 Hari Terakhir)</h4>
                        <div class="h-64 w-full">
                            <canvas id="trenIzinChart"></canvas>
                        </div>
                    </div>

                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-800 flex items-center gap-2">
                        <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                        Izin Meninggalkan Kelas Terakhir
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                            <tr>
                                <th class="px-6 py-3">Siswa</th>
                                <th class="px-6 py-3">Tujuan</th>
                                <th class="px-6 py-3">Waktu</th>
                                <th class="px-6 py-3 text-right">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($izinKeluarTerakhir as $izin)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-3 font-medium text-gray-900">{{ $izin->siswa->name }}</td>
                                    <td class="px-6 py-3 text-gray-600 truncate max-w-xs">{{ $izin->tujuan }}</td>
                                    <td class="px-6 py-3 text-gray-500 font-mono text-xs">
                                        {{ $izin->updated_at->diffForHumans() }}</td>
                                    <td class="px-6 py-3 text-right">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'selesai' => 'bg-gray-100 text-gray-700',
                                                'terlambat' => 'bg-orange-100 text-orange-700',
                                                'disetujui_guru_piket' => 'bg-green-100 text-green-700',
                                                'diverifikasi_security' => 'bg-blue-100 text-blue-700',
                                                'ditolak' => 'bg-red-100 text-red-700',
                                                default => 'bg-yellow-100 text-yellow-700',
                                            };
                                        @endphp
                                        <span
                                            class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                            {{ str_replace('_', ' ', $izin->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada aktivitas izin meninggalkan kelas.
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
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const statusData = @json($statusChartData);
                const dailyData = @json($dailyChartData);
                const latenessData = @json($latenessChartData);

                // 1. Doughnut Chart (Status Izin)
                if (document.getElementById('statusIzinChart')) {
                    // Cek data kosong
                    if (!statusData.data || statusData.data.length === 0 || statusData.data.every(v => v === 0)) {
                        document.getElementById('emptyPieMessage').classList.remove('hidden');
                    } else {
                        const ctxStatus = document.getElementById('statusIzinChart').getContext('2d');
                        new Chart(ctxStatus, {
                            type: 'doughnut',
                            data: {
                                labels: statusData.labels.map(l => l.charAt(0).toUpperCase() + l.slice(1)),
                                datasets: [{
                                    data: statusData.data,
                                    backgroundColor: ['#f59e0b', '#10b981',
                                    '#ef4444'], // Amber, Green, Red
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
                }

                // 2. Line Chart (Tren Harian) - Smooth Gradient
                if (document.getElementById('trenIzinChart')) {
                    const ctxTren = document.getElementById('trenIzinChart').getContext('2d');
                    const gradient = ctxTren.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(20, 184, 166, 0.2)'); // Teal pudar
                    gradient.addColorStop(1, 'rgba(20, 184, 166, 0)');

                    new Chart(ctxTren, {
                        type: 'line',
                        data: {
                            labels: dailyData.labels,
                            datasets: [{
                                label: 'Jumlah Pengajuan',
                                data: dailyData.data,
                                borderColor: '#14b8a6', // Teal-500
                                backgroundColor: gradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 3,
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
                // 3. Lateness Trend Chart (Emerald)
                if (document.getElementById('latenessTrendChart')) {
                    const ctxLate = document.getElementById('latenessTrendChart').getContext('2d');
                    const lateGradient = ctxLate.createLinearGradient(0, 0, 0, 300);
                    lateGradient.addColorStop(0, 'rgba(16, 185, 129, 0.2)'); // Emerald
                    lateGradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

                    new Chart(ctxLate, {
                        type: 'line',
                        data: {
                            labels: latenessData.labels,
                            datasets: [{
                                label: 'Siswa Terlambat',
                                data: latenessData.data,
                                borderColor: '#10b981', // Emerald-500
                                backgroundColor: lateGradient,
                                borderWidth: 3,
                                tension: 0.4,
                                fill: true,
                                pointRadius: 2,
                                pointHoverRadius: 5
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: { stepSize: 1 },
                                    grid: { borderDash: [2, 4] }
                                },
                                x: {
                                    grid: { display: false },
                                    ticks: {
                                        autoSkip: true,
                                        maxTicksLimit: 10
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
