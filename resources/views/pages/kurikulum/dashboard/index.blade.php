<x-app-layout>
    {{-- CSS Animasi Gradient --}}
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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">
            Dashboard Kurikulum
            @if (isset($tahunAktif))
                <span class="text-sm font-normal text-gray-500 ml-2">
                    (T.A. {{ $tahunAktif->tahun }} - {{ $tahunAktif->semester }})
                </span>
            @endif
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div
                class="relative rounded-2xl bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 shadow-lg overflow-hidden p-8 animate-gradient">
                <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                <div class="relative z-10 text-white">
                    <h3 class="text-3xl font-extrabold tracking-tight">Selamat Datang, Kurikulum! 👋</h3>
                    <p class="mt-2 text-blue-100 font-medium text-lg">Kelola jadwal, guru, dan mata pelajaran dengan
                        mudah dan efisien.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    class="bg-white rounded-2xl p-6 border border-indigo-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 bg-indigo-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-indigo-500 uppercase tracking-wider">Total Guru</p>
                            <h3 class="mt-2 text-4xl font-black text-gray-800">{{ $totalGuru }}</h3>
                        </div>
                        <div class="p-3 bg-indigo-100 text-indigo-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-blue-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 bg-blue-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-blue-500 uppercase tracking-wider">Mata Pelajaran</p>
                            <h3 class="mt-2 text-4xl font-black text-gray-800">{{ $totalMapel }}</h3>
                        </div>
                        <div class="p-3 bg-blue-100 text-blue-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div
                    class="bg-white rounded-2xl p-6 border border-green-100 shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                    <div
                        class="absolute -right-4 -top-4 bg-green-50 w-24 h-24 rounded-full opacity-50 group-hover:scale-110 transition-transform">
                    </div>
                    <div class="relative z-10 flex justify-between items-start">
                        <div>
                            <p class="text-sm font-bold text-green-500 uppercase tracking-wider">Rombel Aktif</p>
                            <h3 class="mt-2 text-4xl font-black text-gray-800">{{ $totalRombel }}</h3>
                        </div>
                        <div class="p-3 bg-green-100 text-green-600 rounded-xl">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Top 5 Stats Widgets --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                <!-- Top Rajin -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-lg">
                            🏆
                        </div>
                        <h4 class="font-bold text-gray-800">Top 5 Rajin</h4>
                    </div>
                    <div class="space-y-3">
                        @forelse($topRajin as $index => $guru)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-green-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-400 w-4">#{{ $index + 1 }}</span>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 line-clamp-1">{{ $guru->nama_lengkap }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">{{ $guru->total }}</span>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 text-xs py-4">Belum ada data</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Terlambat -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 font-bold text-lg">
                            🏃
                        </div>
                        <h4 class="font-bold text-gray-800">Sering Terlambat</h4>
                    </div>
                    <div class="space-y-3">
                        @forelse($topTerlambat as $index => $guru)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-yellow-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-400 w-4">#{{ $index + 1 }}</span>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 line-clamp-1">{{ $guru->nama_lengkap }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">{{ $guru->total }}</span>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 text-xs py-4">Belum ada data</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Absen -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-lg">
                            🤒
                        </div>
                        <h4 class="font-bold text-gray-800">Sering Absen</h4>
                    </div>
                    <div class="space-y-3">
                        @forelse($topAbsen as $index => $guru)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-red-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-400 w-4">#{{ $index + 1 }}</span>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 line-clamp-1">{{ $guru->nama_lengkap }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">{{ $guru->total }}</span>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 text-xs py-4">Belum ada data</div>
                        @endforelse
                    </div>
                </div>

                <!-- Top Izin -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm relative overflow-hidden group">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                            📝
                        </div>
                        <h4 class="font-bold text-gray-800">Sering Izin</h4>
                    </div>
                    <div class="space-y-3">
                        @forelse($topIzin as $index => $guru)
                            <div class="flex items-center justify-between p-2 rounded-lg hover:bg-blue-50 transition-colors">
                                <div class="flex items-center gap-3">
                                    <span class="text-xs font-bold text-gray-400 w-4">#{{ $index + 1 }}</span>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-800 line-clamp-1">{{ $guru->nama_lengkap }}</span>
                                    </div>
                                </div>
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">{{ $guru->total }}</span>
                            </div>
                        @empty
                            <div class="text-center text-gray-400 text-xs py-4">Belum ada data</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-6">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-blue-600 rounded-full"></span>
                            Jadwal Hari Ini: {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}
                        </h3>
                        <span
                            class="text-xs font-medium text-gray-500 bg-gray-100 px-3 py-1 rounded-full">{{ count($jadwalHariIni) }}
                            Kelas Aktif</span>
                    </div>

                    <div
                        class="grid grid-cols-1 md:grid-cols-2 gap-4 max-h-[500px] overflow-y-auto pr-2 custom-scrollbar">
                        @forelse ($jadwalHariIni as $namaKelas => $jadwals)
                            <div
                                class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden hover:shadow-md transition-shadow">
                                <div
                                    class="bg-gray-50 px-4 py-3 border-b border-gray-200 flex justify-between items-center">
                                    <h4 class="font-bold text-gray-800">{{ $namaKelas }}</h4>
                                    <span
                                        class="text-xs font-semibold bg-white border border-gray-200 text-gray-600 px-2 py-0.5 rounded">{{ count($jadwals) }}
                                        Mapel</span>
                                </div>
                                <div class="p-4 space-y-3">
                                    @foreach ($jadwals as $jadwal)
                                        <div
                                            class="flex items-start gap-3 pb-3 border-b border-gray-100 last:border-0 last:pb-0">
                                            <div class="flex-shrink-0 w-12 text-center">
                                                <span
                                                    class="block text-xs font-bold text-gray-500">{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}</span>
                                                <span
                                                    class="block text-[10px] text-gray-400">{{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</span>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-sm font-bold text-gray-800 leading-tight">
                                                    {{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5 truncate">
                                                    {{ $jadwal->guru->nama_lengkap }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full py-12 flex flex-col items-center justify-center text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <p class="font-medium">Tidak ada jadwal pelajaran hari ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <span class="w-1.5 h-6 bg-indigo-600 rounded-full"></span>
                            Top Mapel (Jam Terbanyak)
                        </h3>
                        <div class="relative h-64 w-full flex items-center justify-center">
                            <canvas id="mapelChart"></canvas>
                        </div>
                        <div class="mt-6 space-y-2">
                            <p class="text-xs text-center text-gray-400">Distribusi jam pelajaran per minggu</p>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-2xl p-5 border border-indigo-100">
                        <h4 class="font-bold text-indigo-900 mb-2 text-sm uppercase">Info Sistem</h4>
                        <ul class="space-y-2 text-xs text-indigo-800 font-medium">
                            <li class="flex justify-between">
                                <span>Versi Aplikasi</span>
                                <span class="font-bold">1.0</span>
                            </li>
                            <li class="flex justify-between">
                                <span>Terakhir Update</span>
                                <span>{{ date('d M Y') }}</span>
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
                // Konfigurasi Font
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const mapelChartData = @json($mapelChartData);

                if (document.getElementById('mapelChart') && mapelChartData.data.length > 0) {
                    const ctxMapel = document.getElementById('mapelChart').getContext('2d');
                    new Chart(ctxMapel, {
                        type: 'doughnut', // Ubah ke Doughnut biar modern
                        data: {
                            labels: mapelChartData.labels,
                            datasets: [{
                                label: 'Total Jam',
                                data: mapelChartData.data,
                                backgroundColor: [
                                    '#6366f1', '#3b82f6', '#0ea5e9', '#06b6d4',
                                    '#14b8a6', '#10b981', '#8b5cf6'
                                ], // Warna gradasi Biru-Ungu-Hijau
                                borderWidth: 0,
                                hoverOffset: 4
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            cutout: '75%', // Lubang tengah besar
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
            });
        </script>
    @endpush
</x-app-layout>
