<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Operator</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">
            
            {{-- Welcome & Stats Row --}}
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                {{-- Welcome Card --}}
                <div class="lg:col-span-2 relative overflow-hidden bg-gradient-to-br from-red-600 to-rose-700 rounded-3xl p-8 text-white shadow-xl">
                    <div class="absolute right-0 top-0 w-64 h-64 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl"></div>
                    <div class="relative z-10">
                        <h3 class="text-3xl font-black mb-2">Halo, {{ Auth::user()->name }}!</h3>
                        <p class="text-red-100 font-medium italic">Sistem Informasi Kesiswaan - Panel Operator</p>
                        
                        <div class="mt-8 flex flex-wrap gap-8 border-t border-white/20 pt-6">
                            <div>
                                <span class="text-3xl font-black block">{{ $totalStudents }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-red-200">Total Siswa</span>
                            </div>
                            <div class="border-l border-white/20 pl-8">
                                <span class="text-3xl font-black block">{{ $totalActiveRombel }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-red-200">Rombel Aktif</span>
                            </div>
                            <div class="border-l border-white/20 pl-8">
                                <span class="text-3xl font-black block">{{ $totalGuruKelas }}</span>
                                <span class="text-xs font-bold uppercase tracking-widest text-red-200">Guru Kelas</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Fast Stats Cards --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Tahun Pelajaran</h4>
                        <p class="text-2xl font-black text-gray-900">{{ $activeYear->tahun ?? '-' }}</p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-gray-50">
                        <span class="text-xs font-bold px-2 py-1 bg-blue-100 text-blue-700 rounded-lg">Semester {{ $activeYear->semester ?? '-' }}</span>
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm flex flex-col justify-between transition-all hover:shadow-md">
                    <div>
                        <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <h4 class="text-gray-500 text-xs font-bold uppercase tracking-widest mb-1">Update Terakhir</h4>
                        <p class="text-lg font-black text-gray-900">{{ $recentStudents->first()?->created_at->diffForHumans() ?? 'Belum ada data' }}</p>
                    </div>
                    <a href="{{ route('master-data.siswa.index') }}" class="mt-4 text-xs font-bold text-emerald-600 hover:underline flex items-center gap-1">
                        Lihat Data Siswa
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>

            {{-- Charts Section --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                {{-- Students per Class & Gender --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Statistik Siswa per Rombel</h4>
                            <p class="text-gray-500 text-xs">Perbandingan Laki-laki & Perempuan</p>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="classGenderChart"></canvas>
                    </div>
                </div>

                {{-- Students per Major --}}
                <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Distribusi Jurusan</h4>
                            <p class="text-gray-500 text-xs">Proporsi siswa berdasarkan program keahlian</p>
                        </div>
                    </div>
                    <div class="h-80 flex items-center justify-center">
                        <canvas id="majorChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                 {{-- Students per Level --}}
                 <div class="bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h4 class="font-black text-gray-900 text-lg">Siswa per Tingkat</h4>
                            <p class="text-gray-500 text-xs">Analisis jumlah per Jenjang (X, XI, XII)</p>
                        </div>
                    </div>
                    <div class="h-64">
                        <canvas id="levelChart"></canvas>
                    </div>
                </div>

                {{-- Recent Students List --}}
                <div class="lg:col-span-2 bg-white rounded-3xl border border-gray-100 p-8 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h4 class="font-black text-gray-900 text-lg">Siswa Baru Terdaftar</h4>
                        <a href="{{ route('master-data.siswa.index') }}" class="text-xs font-bold text-red-600 hover:underline">Semua Siswa</a>
                    </div>
                    <div class="space-y-4">
                        @forelse($recentStudents as $siswa)
                            <div class="flex items-center justify-between p-4 rounded-2xl bg-gray-50 hover:bg-gray-100 transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="w-10 h-10 rounded-full bg-red-100 text-red-600 flex items-center justify-center font-black text-sm">
                                        {{ substr($siswa->nama_lengkap, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-900 group-hover:text-red-700 transition-colors">{{ $siswa->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-500">NIS: {{ $siswa->nis }} • {{ $siswa->jenis_kelamin == 'L' ? 'Laki-laki' : 'Perempuan' }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-gray-400 capitalize">{{ $siswa->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 italic">Belum ada data siswa.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Chart.defaults.font.family = "'Plus Jakarta Sans', 'Inter', sans-serif";
            Chart.defaults.color = '#94a3b8';

            // 1. Class & Gender Chart
            const classData = @json($classStats);
            new Chart(document.getElementById('classGenderChart'), {
                type: 'bar',
                data: {
                    labels: classData.map(c => c.name),
                    datasets: [
                        {
                            label: 'Laki-laki',
                            data: classData.map(c => c.male),
                            backgroundColor: '#3b82f6',
                            borderRadius: 6,
                        },
                        {
                            label: 'Perempuan',
                            data: classData.map(c => c.female),
                            backgroundColor: '#fb7185',
                            borderRadius: 6,
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top', align: 'end', labels: { usePointStyle: true, boxWidth: 6 } }
                    },
                    scales: {
                        x: { stacked: true, grid: { display: false } },
                        y: { stacked: true, grid: { borderDash: [5, 5] } }
                    }
                }
            });

            // 2. Major Chart
            const majorData = @json($majorStats);
            new Chart(document.getElementById('majorChart'), {
                type: 'doughnut',
                data: {
                    labels: majorData.map(m => m.jurusan || 'Tanpa Jurusan'),
                    datasets: [{
                        data: majorData.map(m => m.total),
                        backgroundColor: ['#ef4444', '#3b82f6', '#10b981', '#f59e0b', '#6366f1', '#8b5cf6'],
                        borderWidth: 0,
                        hoverOffset: 12
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '75%',
                    plugins: {
                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } }
                    }
                }
            });

            // 3. Level Chart
            const levelData = @json($studentLevelStats);
            new Chart(document.getElementById('levelChart'), {
                type: 'bar',
                data: {
                    labels: ['Kelas X', 'Kelas XI', 'Kelas XII'],
                    datasets: [{
                        label: 'Jumlah Siswa',
                        data: [levelData.X, levelData.XI, levelData.XII],
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderColor: '#ef4444',
                        borderWidth: 2,
                        borderRadius: 12,
                        fill: true
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
        });
    </script>
    @endpush
</x-app-layout>
