<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Monitoring Absensi Guru Mengajar') }}
        </h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Statistic Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Hadir</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $totalHadir }}</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Terlambat</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-yellow-50 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $totalTerlambat }}</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Tidak Hadir</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-red-50 rounded-lg">
                            <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $totalTidakHadir }}</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Izin</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $totalIzin }}</span>
                    </div>
                </div>
            </div>

            {{-- Filter & Actions --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form action="{{ route('kurikulum.monitoring-absensi-guru.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-4 items-end">
                    <div>
                        <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai</label>
                        <input type="date" name="start_date" id="start_date" value="{{ $startDate }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                    </div>
                    <div>
                        <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Akhir</label>
                        <input type="date" name="end_date" id="end_date" value="{{ $endDate }}" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                    </div>
                    <div>
                        <label for="guru_id" class="block text-sm font-medium text-gray-700 mb-1">Guru</label>
                        <select name="guru_id" id="guru_id" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                            <option value="">Semua Guru</option>
                            @foreach($gurus as $guru)
                                <option value="{{ $guru->id }}" {{ $guruId == $guru->id ? 'selected' : '' }}>{{ $guru->nama_lengkap }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                        <select name="status" id="status" class="w-full rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500 text-sm">
                            <option value="">Semua Status</option>
                            <option value="hadir" {{ $status == 'hadir' ? 'selected' : '' }}>Hadir</option>
                            <option value="terlambat" {{ $status == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                            <option value="tidak_hadir" {{ $status == 'tidak_hadir' ? 'selected' : '' }}>Tidak Hadir</option>
                            <option value="izin" {{ $status == 'izin' ? 'selected' : '' }}>Izin</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 py-2 px-4 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors text-sm font-medium">
                            Filter
                        </button>
                        <a href="{{ route('kurikulum.monitoring-absensi-guru.export', ['start_date' => $startDate, 'end_date' => $endDate, 'guru_id' => $guruId, 'status' => $status]) }}" class="flex-1 py-2 px-4 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors text-sm font-medium flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Excel
                        </a>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4">Tanggal & Waktu</th>
                                <th class="px-6 py-4">Guru</th>
                                <th class="px-6 py-4">Mapel & Kelas</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4">Dicatat Oleh</th>
                                <th class="px-6 py-4">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensi as $item)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ $item->tanggal->format('d/m/Y') }}</div>
                                        <div class="text-gray-500 text-xs mt-1">
                                            {{ $item->jadwalPelajaran->jam_mulai }} - {{ $item->jadwalPelajaran->jam_selesai }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">
                                        {{ $item->jadwalPelajaran->guru->nama_lengkap ?? 'Guru Tidak Tersedia' }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-gray-900">{{ $item->jadwalPelajaran->mataPelajaran->nama_mapel }}</div>
                                        <div class="text-gray-500 text-xs mt-1">
                                            {{ $item->jadwalPelajaran->rombel->kelas->nama_kelas ?? $item->jadwalPelajaran->rombel->nama_rombel }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($item->status == 'hadir')
                                            <span class="px-3 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-full">Hadir</span>
                                        @elseif($item->status == 'terlambat')
                                            <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-xs font-bold rounded-full">Terlambat</span>
                                            <div class="mt-1 text-xs text-yellow-600">{{ $item->waktu_absen ? $item->waktu_absen->format('H:i') : '-' }}</div>
                                        @elseif($item->status == 'tidak_hadir')
                                            <span class="px-3 py-1 bg-red-100 text-red-700 text-xs font-bold rounded-full">Tidak Hadir</span>
                                        @elseif($item->status == 'izin')
                                            <span class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">Izin</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        {{ $item->pencatat->name ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-gray-500 italic">
                                        {{ $item->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                        Tidak ada data absensi yang ditemukan.
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
        </div>
    </div>
</x-app-layout>
