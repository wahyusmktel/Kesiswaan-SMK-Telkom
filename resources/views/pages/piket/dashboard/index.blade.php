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

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div
                        class="relative rounded-2xl bg-gradient-to-r from-amber-500 via-orange-500 to-red-500 shadow-lg overflow-hidden p-8 animate-gradient h-full">
                        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                        <div class="relative z-10 text-white">
                            <h3 class="text-3xl font-extrabold tracking-tight">Halo, {{ Auth::user()->name }}! 👮‍♂️</h3>
                            <p class="mt-2 text-amber-100 font-medium text-lg">Siap memantau aktivitas siswa hari ini? Tetap
                                semangat!</p>
                        </div>
                    </div>
                </div>
                <div class="lg:col-span-1">
                    @if ($kegiatanSaatIni)
                        <div class="bg-gradient-to-br from-amber-400 to-orange-500 rounded-2xl shadow-lg p-6 text-white relative overflow-hidden group h-full">
                            <div class="absolute -right-4 -bottom-4 opacity-20 transform group-hover:scale-110 transition-transform">
                                <svg class="w-32 h-32" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.5 2 2 6.5 2 12s4.5 10 10 10 10-4.5 10-10S17.5 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67V7z"/></svg>
                            </div>
                            <div class="relative z-10">
                                <span class="bg-white/20 text-white text-[10px] font-black px-2 py-0.5 rounded-full uppercase tracking-widest mb-2 inline-block border border-white/30">Kegiatan Saat Ini</span>
                                <h4 class="text-xl font-black leading-tight mb-1">{{ str_replace('_', ' ', strtoupper($kegiatanSaatIni->tipe_kegiatan)) }}</h4>
                                <p class="text-amber-50 text-xs font-bold font-mono">
                                    {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($kegiatanSaatIni->jam_selesai)->format('H:i') }}
                                </p>
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6 flex flex-col items-center justify-center text-center h-full group hover:border-amber-300 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3 group-hover:bg-amber-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-300 group-hover:text-amber-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Tidak Ada Kegiatan Spesial</p>
                        </div>
                    @endif
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

                <!-- NEW: Keterlambatan Hari Ini -->
                <div
                    class="bg-white rounded-2xl p-6 border border-red-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-red-500 uppercase tracking-wider">Terlambat Hari Ini</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $keterlambatanHariIni }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Siswa Terlambat</p>
                    </div>
                </div>

                <!-- NEW: Total Keterlambatan -->
                <div
                    class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group hover:shadow-md transition-all">
                    <div class="absolute right-0 top-0 p-4 opacity-10">
                        <svg class="w-16 h-16 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Total Terlambat</p>
                        <h3 class="mt-2 text-3xl font-black text-gray-800">{{ $totalKeterlambatan }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Akumulasi Data</p>
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

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h3 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                Siswa Terlambat (Hari Ini)
                            </h3>
                            <span class="text-xs font-medium text-gray-500 bg-white border px-2 py-1 rounded">Security Log</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Waktu</th>
                                        <th class="px-6 py-3 text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($detailKeterlambatan as $late)
                                        <tr class="hover:bg-gray-50 transition-colors">
                                            <td class="px-6 py-3">
                                                <div class="font-medium text-gray-900">{{ $late->siswa->user->name }}</div>
                                                <div class="text-[10px] text-gray-400">Security: {{ $late->security->name }}</div>
                                            </td>
                                            <td class="px-6 py-3 text-gray-500">
                                                {{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-500 font-mono text-xs">
                                                {{ $late->waktu_dicatat_security->format('H:i') }}
                                            </td>
                                            <td class="px-6 py-3 text-right">
                                                @php
                                                    $lateStatusClass = match ($late->status) {
                                                        'diajukan', 'menunggu_verifikasi' => 'bg-yellow-101 text-yellow-801',
                                                        'diverifikasi', 'masuk_kelas' => 'bg-green-101 text-green-801',
                                                        'ditolak' => 'bg-red-101 text-red-801',
                                                        default => 'bg-gray-101 text-gray-801',
                                                    };
                                                @endphp
                                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase {{ $lateStatusClass }}">
                                                    {{ str_replace('_', ' ', $late->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                                <div class="flex flex-col items-center">
                                                    <svg class="w-10 h-10 mb-2 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                    </svg>
                                                    <span>Tidak ada siswa terlambat hari ini.</span>
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

                    <!-- NEW: Analisa Keterlambatan -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                             <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"/></svg>
                             Analisa Keterlambatan (30 Hari Terakhir)
                        </h4>
                        <div class="h-64 w-full">
                            <canvas id="analisaKeterlambatanChart"></canvas>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800 text-sm uppercase flex items-center gap-2">
                                <span class="relative flex h-2 w-2">
                                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                  <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                                </span>
                                Aktivitas Terbaru
                            </h4>
                        </div>
                        <div class="divide-y divide-gray-100">
                            @forelse ($recentActivity as $activity)
                                <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            @if($activity['type'] == 'Keterlambatan')
                                                <div class="p-2 bg-red-50 rounded-lg">
                                                    <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @else
                                                <div class="p-2 bg-amber-50 rounded-lg">
                                                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-gray-900 truncate">{{ $activity['name'] }}</p>
                                            <p class="text-xs text-gray-500">{{ $activity['type'] }} • {{ $activity['time']->diffForHumans() }}</p>
                                        </div>
                                        <div class="flex-shrink-0">
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase {{ $activity['color'] == 'red' ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' }}">
                                                {{ $activity['status'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-6 py-8 text-center text-gray-400 text-sm italic">Belum ada aktivitas terekam hari ini.</div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-4 text-center">Proporsi Status Izin</h4>
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

                    <!-- NEW: Top Kelas Terlambat -->
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50">
                            <h4 class="font-bold text-gray-800 text-sm uppercase">Kelas Paling Sering Terlambat</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[300px] overflow-y-auto custom-scrollbar">
                            @forelse ($topKelasTerlambat as $kelas)
                                <div class="px-6 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="w-6 h-6 flex items-center justify-center rounded bg-gray-200 text-xs font-bold text-gray-600">{{ $loop->iteration }}</span>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $kelas->nama_kelas }}</p>
                                            <p class="text-[10px] text-gray-500">{{ $kelas->nama_wali_kelas ?? 'Belum ada Wali Kelas' }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold px-2 py-1 bg-red-100 text-red-700 rounded-full">{{ $kelas->total }}x</span>
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

                // 3. New: Analisa Keterlambatan Chart
                if (document.getElementById('analisaKeterlambatanChart')) {
                    const ctx = document.getElementById('analisaKeterlambatanChart').getContext('2d');
                    const dailyDataKeterlambatan = @json($analisaKeterlambatanChart);
                    
                    const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                    gradient.addColorStop(0, 'rgba(239, 68, 68, 0.2)'); // Red pudar
                    gradient.addColorStop(1, 'rgba(239, 68, 68, 0)');

                    new Chart(ctx, {
                        type: 'bar', // Using bar for contrast with line chart
                        data: {
                            labels: dailyDataKeterlambatan.labels,
                            datasets: [{
                                label: 'Terlambat',
                                data: dailyDataKeterlambatan.data,
                                backgroundColor: '#ef4444',
                                borderRadius: 4,
                                barPercentage: 0.6
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
                                    ticks: { stepSize: 1 },
                                    grid: { borderDash: [2, 4] }
                                },
                                x: {
                                    grid: { display: false }
                                }
                            }
                        }
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
