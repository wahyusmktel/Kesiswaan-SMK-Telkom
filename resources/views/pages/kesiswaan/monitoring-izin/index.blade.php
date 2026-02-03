<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Perizinan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <!-- Tab Navigation -->
            <div class="mb-6 border-b border-gray-200">
                <nav class="flex space-x-8" aria-label="Tabs">
                    <a href="{{ route('kesiswaan.monitoring-izin.index', array_merge(request()->query(), ['type' => 'masuk'])) }}"
                        class="shrink-0 pb-4 px-1 border-b-2 font-medium text-sm {{ $type === 'masuk' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Izin Tidak Masuk
                        <span
                            class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ $type === 'masuk' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-900' }}">
                            {{ $counts['masuk'] }}
                        </span>
                    </a>
                    <a href="{{ route('kesiswaan.monitoring-izin.index', array_merge(request()->query(), ['type' => 'keluar'])) }}"
                        class="shrink-0 pb-4 px-1 border-b-2 font-medium text-sm {{ $type === 'keluar' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Izin Keluar Kelas
                        <span
                            class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ $type === 'keluar' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-900' }}">
                            {{ $counts['keluar'] }}
                        </span>
                    </a>
                    <a href="{{ route('kesiswaan.monitoring-izin.index', array_merge(request()->query(), ['type' => 'dispensasi'])) }}"
                        class="shrink-0 pb-4 px-1 border-b-2 font-medium text-sm {{ $type === 'dispensasi' ? 'border-red-500 text-red-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        Dispensasi
                        <span
                            class="ml-2 py-0.5 px-2.5 rounded-full text-xs font-medium {{ $type === 'dispensasi' ? 'bg-red-100 text-red-600' : 'bg-gray-100 text-gray-900' }}">
                            {{ $counts['dispensasi'] }}
                        </span>
                    </a>
                </nav>
            </div>

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('kesiswaan.monitoring-izin.index') }}" method="GET">
                        <input type="hidden" name="type" value="{{ $type }}">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">

                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari
                                    Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                        placeholder="Nama siswa...">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Dari</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                                <div class="w-1/2">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
                                    <select name="status"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                        <option value="">Semua</option>
                                        @if($type === 'keluar')
                                            <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                                                Diajukan</option>
                                            <option value="disetujui_guru_kelas" {{ request('status') == 'disetujui_guru_kelas' ? 'selected' : '' }}>Disetujui
                                                Guru Kelas</option>
                                            <option value="disetujui_guru_piket" {{ request('status') == 'disetujui_guru_piket' ? 'selected' : '' }}>Disetujui
                                                Guru Piket</option>
                                            <option value="diverifikasi_security" {{ request('status') == 'diverifikasi_security' ? 'selected' : '' }}>Diverifikasi
                                                Security</option>
                                            <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>
                                                Selesai</option>
                                            <option value="terlambat" {{ request('status') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                                Ditolak</option>
                                        @else
                                            <option value="diajukan" {{ request('status') == 'diajukan' ? 'selected' : '' }}>
                                                Diajukan</option>
                                            <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                            <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>
                                                Ditolak</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label
                                        class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Kelas</label>
                                    <select name="kelas_id"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                        <option value="">Semua</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" {{ request('kelas_id') == $item->id ? 'selected' : '' }}>
                                                {{ $item->nama_kelas }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="w-full lg:w-auto flex gap-2">
                                <button type="submit"
                                    class="h-10 px-4 bg-red-600 hover:bg-red-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('kesiswaan.monitoring-izin.index', ['type' => $type]) }}"
                                    class="h-10 px-4 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center">
                                    Reset
                                </a>
                            </div>

                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            @if($type === 'masuk')
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tanggal Izin</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Diproses Oleh</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                                </tr>
                            @elseif($type === 'keluar')
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Tujuan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Waktu (Keluar - Masuk)</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                                </tr>
                            @elseif($type === 'dispensasi')
                                <tr>
                                    <th class="px-6 py-4 font-bold tracking-wider">Kegiatan</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Jumlah Siswa</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Waktu Mulai</th>
                                    <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                    <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                                </tr>
                            @endif
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($perizinan as $item)
                                @if($type === 'masuk')
                                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200"
                                                    x-data="{ item: {{ json_encode($item) }} }">
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <div class="font-semibold text-gray-900">{{ $item->user->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $item->user->email }}</div>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                            {{ $item->user->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                                        {{ \Carbon\Carbon::parse($item->tanggal_izin)->isoFormat('D MMM Y') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap">
                                                        @php
                                                            $statusClass = match ($item->status) {
                                                                'diajukan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                                'disetujui' => 'bg-green-50 text-green-700 border-green-200',
                                                                'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                            };
                                                        @endphp
                                    <span
                                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                                            {{ $item->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                        {{ $item->approver->name ?? '-' }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                                        <button
                                                            @click="$dispatch('open-modal', { name: 'lihat-izin-monitoring', item: item })"
                                                            class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                            Detail
                                                        </button>
                                                    </td>
                                                </tr>
                                @elseif($type === 'keluar')
                                                            <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200"
                                                                x-data="{ item: {{ json_encode($item) }} }">
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="font-semibold text-gray-900">{{ $item->siswa->name }}</div>
                                                                    <div class="text-xs text-gray-500">
                                                                        {{ $item->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                                                        {{ $item->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4">
                                                                    <span class="block truncate max-w-[150px]" title="{{ $item->tujuan }}">
                                                                        {{ $item->tujuan }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <div class="text-xs space-y-1">
                                                                        <div class="flex items-center gap-1 text-red-600">
                                                                            {{ $item->waktu_keluar_sebenarnya ? \Carbon\Carbon::parse($item->waktu_keluar_sebenarnya)->format('H:i') : '-' }}
                                                                            (Keluar)
                                                                        </div>
                                                                        <div class="flex items-center gap-1 text-green-600">
                                                                            {{ $item->waktu_kembali_sebenarnya ? \Carbon\Carbon::parse($item->waktu_kembali_sebenarnya)->format('H:i') : '-' }}
                                                                            (Masuk)
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    @php
                                                                        $statusClass = match ($item->status) {
                                                                            'selesai' => 'bg-gray-50 text-gray-700 border-gray-200',
                                                                            'terlambat' => 'bg-red-50 text-red-700 border-red-200',
                                                                            'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                                                            'disetujui_guru_piket' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                                            'diverifikasi_security' => 'bg-orange-50 text-orange-700 border-orange-200',
                                                                            default => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                                        };
                                                                    @endphp
                                     <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                                                        {{ str_replace('_', ' ', $item->status) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                                    <button @click="$dispatch('open-modal', { name: 'detail-izin-keluar', item: item })"
                                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                                        Detail
                                                                    </button>
                                                                </td>
                                                            </tr>
                                @elseif($type === 'dispensasi')
                                                            <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200"
                                                                x-data="{ item: {{ json_encode($item) }} }">
                                                                <td class="px-6 py-4">
                                                                    <div class="font-semibold text-gray-900">{{ $item->nama_kegiatan }}</div>
                                                                    <div class="text-xs text-gray-500">{{ Str::limit($item->keterangan, 50) }}</div>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    <span class="px-2 py-1 rounded bg-indigo-50 text-indigo-700 font-bold">
                                                                        {{ $item->siswa->count() }} Siswa
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                                                    {{ \Carbon\Carbon::parse($item->waktu_mulai)->isoFormat('D MMM Y HH:mm') }}
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap">
                                                                    @php
                                                                        $statusClass = match ($item->status) {
                                                                            'diajukan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                                            'disetujui' => 'bg-green-50 text-green-700 border-green-200',
                                                                            'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                                                            default => 'bg-gray-50 text-gray-700 border-gray-200',
                                                                        };
                                                                    @endphp
                                     <span
                                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                                                        {{ $item->status }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                                                    <a href="{{ route('kesiswaan.persetujuan-dispensasi.show', $item->id) }}"
                                                                        class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                                                        Buka
                                                                    </a>
                                                                </td>
                                                            </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada data ditemukan.</p>
                                            <p class="text-xs mt-1">Coba ubah filter pencarian Anda.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $perizinan->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>

    @include('pages.kesiswaan.monitoring-izin.partials.modal-lihat-izin')
    @include('pages.kesiswaan.riwayat-izin-keluar.partials.modal-detail')
</x-app-layout>