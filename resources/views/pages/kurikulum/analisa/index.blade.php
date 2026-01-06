<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">
                {{ __('Analisa & Monitoring Semester') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            
            
            {{-- Export Buttons & Info --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-4 rounded-2xl shadow-sm border border-gray-100">
                <div>
                    <h3 class="font-black text-gray-800">Laporan Audit Semester</h3>
                    <p class="text-xs text-gray-500">Tahun Pelajaran: {{ $tahunAktif->tahun }} | Semester: {{ $tahunAktif->semester }}</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('kurikulum.analisa-semester.pdf') }}" 
                        class="inline-flex items-center px-4 py-2 bg-rose-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-rose-700 active:bg-rose-900 transition ease-in-out duration-150 shadow-sm gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 9h1m1 0h1m-3 4h3m-3 4h3" /></svg>
                        Download PDF Report
                    </a>
                    <a href="{{ route('kurikulum.analisa-semester.export') }}" 
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 transition ease-in-out duration-150 shadow-sm gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        Export Excel (.xlsx)
                    </a>
                </div>
            </div>

            @if(isset($error))
                <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 001.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-red-700">{{ $error }}</p>
                        </div>
                    </div>
                </div>
            @else
                {{-- KPI Cards --}}
                <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-3 opacity-10 group-hover:opacity-20 transition-opacity">
                            <svg class="w-16 h-16 text-indigo-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path><path fill-rule="evenodd" d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path></svg>
                        </div>
                        <div class="text-gray-500 text-[10px] font-black uppercase tracking-wider mb-1">Kehadiran Efektif</div>
                        <div class="flex items-baseline gap-2">
                            <span class="text-3xl font-black text-indigo-600">{{ $kehadiranPersen }}%</span>
                        </div>
                        <div class="mt-4 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $kehadiranPersen }}%"></div>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-[10px] font-black uppercase tracking-wider mb-1">Tepat Waktu</div>
                        <div class="text-3xl font-black text-green-600">{{ $totalHadir }}</div>
                        <div class="text-[10px] text-green-400 mt-1 font-bold uppercase">Hadir</div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-[10px] font-black uppercase tracking-wider mb-1">Terlambat</div>
                        <div class="text-3xl font-black text-yellow-500">{{ $totalTerlambat }}</div>
                        <div class="text-[10px] text-yellow-400 mt-1 font-bold uppercase">Evaluasi</div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-[10px] font-black uppercase tracking-wider mb-1">Izin / Sakit</div>
                        <div class="text-3xl font-black text-blue-500">{{ $totalIzin }}</div>
                        <div class="text-[10px] text-blue-400 mt-1 font-bold uppercase">Permintaan</div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-[10px] font-black uppercase tracking-wider mb-1">Tanpa Keterangan</div>
                        <div class="text-3xl font-black text-rose-600">{{ $totalAlpa }}</div>
                        <div class="text-[10px] text-rose-400 mt-1 font-bold uppercase">Alpa</div>
                    </div>
                </div>

                {{-- Charts Row --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="lg:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" /></svg>
                            Tren Kehadiran Bulanan
                        </h3>
                        <div class="h-80">
                            <canvas id="trendChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <h3 class="font-bold text-gray-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z" /></svg>
                            Distribusi Status
                        </h3>
                        <div class="h-80 flex items-center justify-center">
                            <canvas id="statusChart"></canvas>
                        </div>
                    </div>
                </div>

                {{-- Leaderboards --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 bg-indigo-50/50 border-b border-gray-100">
                            <h3 class="font-bold text-indigo-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" /></svg>
                                Top 5 Guru Paling Disiplin
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($topDisiplin as $guru)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-green-100 text-green-700 flex items-center justify-center font-black text-sm">
                                            {{ $guru['persentase'] }}%
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $guru['nama'] }}</div>
                                            <div class="text-[10px] text-gray-500 font-extrabold">{{ $guru['hadir'] }} Hadir Tepat Waktu</div>
                                        </div>
                                    </div>
                                    <div class="text-xs font-black text-gray-400">#{{ $loop->iteration }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-5 bg-rose-50/50 border-b border-gray-100">
                            <h3 class="font-bold text-rose-800 flex items-center gap-2">
                                <svg class="w-5 h-5 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                                Top 5 Guru Sering Terlambat
                            </h3>
                        </div>
                        <div class="divide-y divide-gray-50">
                            @foreach($topTerlambat as $guru)
                                <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-700 flex items-center justify-center font-black text-sm">
                                            {{ $guru['terlambat'] }}x
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $guru['nama'] }}</div>
                                            <div class="text-[10px] text-gray-500 font-extrabold text-rose-600">Butuh Pendampingan</div>
                                        </div>
                                    </div>
                                    <div class="text-xs font-black text-gray-400">#{{ $loop->iteration }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if(!isset($error))
                // Trend Chart
                const trendCtx = document.getElementById('trendChart').getContext('2d');
                new Chart(trendCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($trends->keys()) !!},
                        datasets: [
                            {
                                label: 'Hadir Tepat Waktu',
                                data: {!! json_encode($trends->pluck('hadir')) !!},
                                borderColor: '#4F46E5',
                                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Terlambat',
                                data: {!! json_encode($trends->pluck('terlambat')) !!},
                                borderColor: '#F59E0B',
                                borderDash: [5, 5],
                                fill: false,
                                tension: 0.4
                            },
                            {
                                label: 'Izin',
                                data: {!! json_encode($trends->pluck('izin')) !!},
                                borderColor: '#3B82F6',
                                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                fill: true,
                                tension: 0.4
                            },
                            {
                                label: 'Alpa',
                                data: {!! json_encode($trends->pluck('alpa')) !!},
                                borderColor: '#EF4444',
                                borderDash: [2, 2],
                                fill: false,
                                tension: 0.4
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        },
                        scales: {
                            y: { beginAtZero: true }
                        }
                    }
                });

                // Status distribution
                const statusCtx = document.getElementById('statusChart').getContext('2d');
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Hadir', 'Terlambat', 'Izin', 'Alpa'],
                        datasets: [{
                            data: [{{ $totalHadir }}, {{ $totalTerlambat }}, {{ $totalIzin }}, {{ $totalAlpa }}],
                            backgroundColor: ['#10B981', '#F59E0B', '#3B82F6', '#EF4444'],
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { position: 'bottom' }
                        }
                    }
                });
            @endif
        });
    </script>
    @endpush
</x-app-layout>
