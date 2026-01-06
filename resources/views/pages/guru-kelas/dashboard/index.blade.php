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
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Dashboard Guru Pengajar</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
                <div class="lg:col-span-3">
                    <div
                        class="relative rounded-2xl bg-gradient-to-r from-cyan-600 via-blue-600 to-indigo-600 shadow-lg overflow-hidden p-8 animate-gradient">
                        <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-2xl"></div>
                        <div class="relative z-10 text-white">
                            <h3 class="text-3xl font-extrabold tracking-tight">Selamat Mengajar, {{ Auth::user()->name }}! 👨‍🏫
                            </h3>
                            <p class="mt-2 text-cyan-100 font-medium text-lg">
                                Hari ini {{ \Carbon\Carbon::now()->isoFormat('dddd, D MMMM Y') }}. Siapkan materi terbaikmu!
                            </p>
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
                                @if($kegiatanSaatIni->keterangan)
                                    <p class="mt-2 text-[10px] italic text-amber-100 line-clamp-2">"{{ $kegiatanSaatIni->keterangan }}"</p>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-6 flex flex-col items-center justify-center text-center h-full group hover:border-cyan-300 transition-colors">
                            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mb-3 group-hover:bg-cyan-50 transition-colors">
                                <svg class="w-6 h-6 text-gray-300 group-hover:text-cyan-400 Transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <p class="text-gray-400 text-xs font-bold uppercase tracking-widest">Tidak Ada Kegiatan Spesial</p>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Quick Actions --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a href="{{ route('guru.jadwal-saya') }}" class="group bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-cyan-200 transition-all flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-lg bg-cyan-50 text-cyan-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Jadwal Saya</span>
                </a>
                <a href="{{ route('guru.izin.create') }}" class="group bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-red-200 transition-all flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-lg bg-red-50 text-red-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    </div>
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Ajukan Izin</span>
                </a>
                <a href="{{ route('guru.lms.index') }}" class="group bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-blue-200 transition-all flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">LMS / Materi</span>
                </a>
                <a href="{{ route('profile.edit') }}" class="group bg-white p-4 rounded-xl border border-gray-100 shadow-sm hover:shadow-md hover:border-amber-200 transition-all flex flex-col items-center text-center">
                    <div class="w-12 h-12 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    </div>
                    <span class="text-xs font-black text-gray-800 uppercase tracking-wider">Update Profil</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <div class="lg:col-span-2 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-cyan-600 rounded-full"></span>
                                Jadwal Mengajar Hari Ini
                            </h4>
                            <span
                                class="bg-cyan-50 text-cyan-700 px-3 py-1 rounded-full text-xs font-bold border border-cyan-100">
                                {{ $jadwalHariIni->count() }} Kelas
                            </span>
                        </div>

                        <div class="relative border-l-2 border-gray-100 ml-3 space-y-6 pl-6 py-2">
                            @forelse ($jadwalHariIni as $jadwal)
                                <div class="relative group">
                                    <div
                                        class="absolute -left-[33px] top-1 h-4 w-4 rounded-full border-2 border-white shadow-sm
                                        {{ \Carbon\Carbon::now()->between($jadwal->jam_mulai, $jadwal->jam_selesai) ? 'bg-green-500 animate-pulse' : 'bg-cyan-500' }}">
                                    </div>

                                    <div
                                        class="bg-gray-50 hover:bg-cyan-50 p-4 rounded-xl border border-gray-100 transition-colors">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h5 class="font-bold text-gray-900 text-lg">
                                                    {{ $jadwal->rombel->kelas->nama_kelas }}</h5>
                                                <p class="text-sm text-gray-600 font-medium">
                                                    {{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                            </div>
                                            <div class="text-right">
                                                <span
                                                    class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jam
                                                    Ke-{{ $jadwal->jam_ke }}</span>
                                                <span class="block font-mono text-sm font-bold text-cyan-700">
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="py-8 text-center text-gray-400 italic">
                                    Tidak ada jadwal mengajar hari ini. Happy free day! 🎉
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div
                            class="px-6 py-4 border-b border-gray-100 bg-orange-50/50 flex justify-between items-center">
                            <h4 class="font-bold text-gray-800 flex items-center gap-2">
                                <span class="w-1.5 h-6 bg-orange-500 rounded-full"></span>
                                Siswa Sedang Di Luar Kelas
                            </h4>
                            @if ($siswaSedangKeluar->count() > 0)
                                <span class="flex h-3 w-3 relative">
                                    <span
                                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-orange-500"></span>
                                </span>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-sm text-left">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3">Kelas</th>
                                        <th class="px-6 py-3">Tujuan</th>
                                        <th class="px-6 py-3">Durasi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($siswaSedangKeluar as $izin)
                                        <tr class="hover:bg-orange-50/30 transition-colors">
                                            <td class="px-6 py-3 font-semibold text-gray-900">{{ $izin->siswa->name }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-600">{{ $izin->rombel->kelas->nama_kelas }}
                                            </td>
                                            <td class="px-6 py-3 text-gray-600">{{ $izin->tujuan }}</td>
                                            <td class="px-6 py-3">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded bg-orange-100 text-orange-700 text-xs font-bold">
                                                    {{ \Carbon\Carbon::parse($izin->waktu_keluar_sebenarnya)->diffForHumans() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                                Semua siswa aman di dalam kelas.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Top Siswa Izin Keluar</h4>
                            <div class="h-48">
                                <canvas id="topSiswaIzinKeluarChart"></canvas>
                            </div>
                        </div>
                        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                            <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Tujuan Populer</h4>
                            <div class="h-48 flex justify-center">
                                <canvas id="tujuanIzinKeluarChart"></canvas>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="lg:col-span-1 space-y-8">

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-red-50/50">
                            <h4 class="font-bold text-gray-800 text-sm uppercase">Izin Tidak Masuk Hari Ini</h4>
                        </div>
                        <div class="divide-y divide-gray-100 max-h-[400px] overflow-y-auto custom-scrollbar">
                            @forelse ($siswaIzinHariIni as $izin)
                                <div class="p-4 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="flex-shrink-0 mt-1">
                                            <span
                                                class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-red-100 text-red-600">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $izin->user->name }}</p>
                                            <p class="text-xs text-gray-500 mb-1">
                                                {{ $izin->user->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                            </p>
                                            <p class="text-xs text-gray-700 bg-gray-100 p-2 rounded italic">
                                                "{{ Str::limit(strip_tags($izin->keterangan), 40) }}"
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-6 text-center text-gray-400 text-sm">
                                    Nihil. Semua siswa hadir (berdasarkan data izin).
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6">
                        <h4 class="font-bold text-gray-800 mb-4 text-sm uppercase">Daftar Kelas Ajar</h4>
                        <div class="space-y-3">
                            @forelse ($kelasDiajar as $kelas)
                                <div
                                    class="flex items-center justify-between p-3 rounded-xl bg-gray-50 border border-gray-100 hover:border-cyan-200 hover:bg-cyan-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="h-8 w-8 rounded-lg bg-cyan-100 text-cyan-600 flex items-center justify-center font-bold text-xs">
                                            {{ substr($kelas->kelas->nama_kelas, 0, 1) }}
                                        </div>
                                        <span
                                            class="text-sm font-semibold text-gray-700">{{ $kelas->kelas->nama_kelas }}</span>
                                    </div>
                                    <span
                                        class="text-xs font-bold text-gray-500 bg-white px-2 py-1 rounded border">{{ $kelas->siswa_count }}
                                        Siswa</span>
                                </div>
                            @empty
                                <p class="text-sm text-gray-400 text-center">Belum ada data kelas.</p>
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
                Chart.defaults.font.family = "'Figtree', 'Inter', sans-serif";
                Chart.defaults.color = '#64748b';

                const topSiswaData = @json($topSiswaIzinKeluarChartData);
                const tujuanData = @json($tujuanIzinKeluarChartData);

                // 1. Bar Chart: Top Siswa Keluar
                if (document.getElementById('topSiswaIzinKeluarChart') && topSiswaData.data.length > 0) {
                    new Chart(document.getElementById('topSiswaIzinKeluarChart').getContext('2d'), {
                        type: 'bar',
                        data: {
                            labels: topSiswaData.labels,
                            datasets: [{
                                label: 'Kali Izin',
                                data: topSiswaData.data,
                                backgroundColor: 'rgba(249, 115, 22, 0.7)', // Orange
                                borderRadius: 4,
                                barThickness: 20
                            }]
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    display: false
                                }
                            },
                            scales: {
                                x: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    },
                                    grid: {
                                        display: true,
                                        borderDash: [2, 2]
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

                // 2. Doughnut Chart: Tujuan
                if (document.getElementById('tujuanIzinKeluarChart') && tujuanData.data.length > 0) {
                    new Chart(document.getElementById('tujuanIzinKeluarChart').getContext('2d'), {
                        type: 'doughnut',
                        data: {
                            labels: tujuanData.labels,
                            datasets: [{
                                data: tujuanData.data,
                                backgroundColor: [
                                    '#06b6d4', '#3b82f6', '#8b5cf6', '#ec4899', '#f59e0b'
                                ],
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
                                        padding: 10,
                                        font: {
                                            size: 10
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
