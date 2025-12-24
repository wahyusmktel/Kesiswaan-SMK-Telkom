<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Absensi Guru - Per Kelas') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Section --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form action="{{ route('kurikulum.monitoring-absensi-per-kelas.index') }}" method="GET" class="flex flex-col md:flex-row gap-4 items-end">
                    <div class="w-full md:w-1/3">
                        <label for="rombel_id" class="block text-sm font-medium text-gray-700 mb-1">Rombongan Belajar (Kelas)</label>
                        <select name="rombel_id" id="rombel_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                            <option value="">-- Pilih Kelas --</option>
                            @foreach($rombels as $rombel)
                                <option value="{{ $rombel->id }}" {{ $rombelId == $rombel->id ? 'selected' : '' }}>{{ $rombel->kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="w-full md:w-1/4">
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                        <input type="month" name="month" id="month" value="{{ $month }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                    </div>
                    <div class="flex gap-2 w-full md:w-auto">
                        <button type="submit" class="flex-1 md:flex-none py-2 px-6 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors text-sm font-medium">
                            Tampilkan
                        </button>
                        <a href="{{ route('kurikulum.monitoring-absensi-per-kelas.export', ['rombel_id' => $rombelId, 'month' => $month]) }}" class="flex-1 md:flex-none py-2 px-6 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Export
                        </a>
                    </div>
                </form>
            </div>

            @if($rombelId)
                {{-- Top Widgets: Teacher Highlights --}}
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center text-green-600 font-bold text-lg">
                            🏆
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">Paling Rajin</div>
                            <div class="font-bold text-gray-900 line-clamp-1">{{ $mostActive->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs text-green-600 font-medium">{{ $mostActive->hadir_count ?? 0 }} Kehadiran</div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center text-yellow-600 font-bold text-lg">
                            🏃
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">Paling Sering Terlambat</div>
                            <div class="font-bold text-gray-900 line-clamp-1">{{ $mostLate->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs text-yellow-600 font-medium">{{ $mostLate->terlambat_count ?? 0 }} Terlambat</div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center text-red-600 font-bold text-lg">
                            🤒
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">Paling Sering Absen</div>
                            <div class="font-bold text-gray-900 line-clamp-1">{{ $mostAbsent->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs text-red-600 font-medium">{{ $mostAbsent->tidak_hadir_count ?? 0 }} Tidak Hadir</div>
                        </div>
                    </div>
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-lg">
                            📝
                        </div>
                        <div>
                            <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">Paling Sering Izin</div>
                            <div class="font-bold text-gray-900 line-clamp-1">{{ $mostPermit->nama_lengkap ?? '-' }}</div>
                            <div class="text-xs text-blue-600 font-medium">{{ $mostPermit->izin_count ?? 0 }} Izin</div>
                        </div>
                    </div>
                </div>

                {{-- Chart Section --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Tren Kehadiran Guru (Harian)</h3>
                    <div class="relative h-80 w-full">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>

                {{-- Detailed Log --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Detail Absensi</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4">Waktu</th>
                                    <th class="px-6 py-4">Guru</th>
                                    <th class="px-6 py-4">Mapel</th>
                                    <th class="px-6 py-4">Status</th>
                                    <th class="px-6 py-4">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($absensi as $item)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</div>
                                            <div class="text-xs text-gray-500">{{ $item->jadwalPelajaran->jam_mulai }} - {{ $item->jadwalPelajaran->jam_selesai }}</div>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900">
                                            {{ $item->jadwalPelajaran->guru->nama_lengkap ?? 'Guru Tidak Tersedia' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            {{ $item->jadwalPelajaran->mataPelajaran->nama_mapel }}
                                        </td>
                                        <td class="px-6 py-4">
                                            @if($item->status == 'hadir')
                                                <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Hadir</span>
                                            @elseif($item->status == 'terlambat')
                                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Terlambat</span>
                                            @elseif($item->status == 'tidak_hadir')
                                                <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Tidak Hadir</span>
                                            @elseif($item->status == 'izin')
                                                <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Izin</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 text-gray-500 italic">
                                            {{ $item->keterangan ?? '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                            Tidak ada data untuk periode ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($absensi->hasPages())
                        <div class="p-6 border-t border-gray-100">
                            {{ $absensi->withQueryString()->links() }}
                        </div>
                    @endif
                </div>

                @push('scripts')
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('attendanceChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: @json($labels),
                                datasets: [
                                    {
                                        label: 'Hadir',
                                        data: @json($dataHadir),
                                        borderColor: '#16a34a',
                                        backgroundColor: '#16a34a',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'Terlambat',
                                        data: @json($dataTerlambat),
                                        borderColor: '#ca8a04',
                                        backgroundColor: '#ca8a04',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'Tidak Hadir',
                                        data: @json($dataTidakHadir),
                                        borderColor: '#dc2626',
                                        backgroundColor: '#dc2626',
                                        tension: 0.3
                                    },
                                    {
                                        label: 'Izin',
                                        data: @json($dataIzin),
                                        borderColor: '#2563eb',
                                        backgroundColor: '#2563eb',
                                        tension: 0.3
                                    }
                                ]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: false,
                                interaction: {
                                    mode: 'index',
                                    intersect: false,
                                },
                                plugins: {
                                    legend: {
                                        position: 'bottom'
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        ticks: {
                                            stepSize: 1
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
                @endpush

            @else
                {{-- Empty State --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pilih Kelas</h3>
                    <p class="text-gray-500">Silakan pilih kelas terlebih dahulu untuk melihat analisis kehadiran guru.</p>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
