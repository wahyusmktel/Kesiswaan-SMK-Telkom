<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Keterlambatan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('monitoring-keterlambatan.index') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">
                            <div class="w-full lg:w-1/4">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                        placeholder="Nama / NIS...">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Dari</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}"
                                        class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/4 flex gap-2">
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Status</label>
                                    <select name="status" class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                        <option value="">Semua</option>
                                        <option value="dicatat_security" {{ request('status') == 'dicatat_security' ? 'selected' : '' }}>Dicatat Security</option>
                                        <option value="diverifikasi_piket" {{ request('status') == 'diverifikasi_piket' ? 'selected' : '' }}>Diverifikasi Piket</option>
                                        <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai / Masuk</option>
                                    </select>
                                </div>
                                <div class="w-1/2">
                                    <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Kelas</label>
                                    <select name="kelas_id" class="block w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 shadow-sm">
                                        <option value="">Semua</option>
                                        @foreach ($kelas as $item)
                                            <option value="{{ $item->id }}" {{ request('kelas_id') == $item->id ? 'selected' : '' }}>{{ $item->nama_kelas }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="w-full lg:w-auto flex gap-2">
                                <button type="submit" class="h-10 px-4 bg-red-600 hover:bg-red-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('monitoring-keterlambatan.index') }}"
                                    class="h-10 px-4 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center">Reset</a>
                                <div class="flex gap-2">
                                    <button type="submit" name="export" value="excel" formaction="{{ route('monitoring-keterlambatan.export') }}" class="h-10 px-4 bg-green-600 hover:bg-green-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        Excel
                                    </button>
                                    <button type="submit" name="export" value="pdf" formaction="{{ route('monitoring-keterlambatan.export') }}" class="h-10 px-4 bg-blue-600 hover:bg-blue-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                                        PDF
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Waktu Datang</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Pencatat</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($data as $late)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-semibold text-gray-900">{{ $late->siswa->user->name }}</div>
                                        <div class="text-xs text-gray-500">NIS: {{ $late->siswa->nis }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ $late->waktu_dicatat_security->format('d M Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                        <div class="text-xs">Sec: {{ $late->security->name }}</div>
                                        <div class="text-[10px] text-gray-400">Piket: {{ $late->guruPiket->name ?? '-' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($late->status) {
                                                'dicatat_security' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'diverifikasi_piket' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                                                'selesai' => 'bg-green-50 text-green-700 border-green-200',
                                                'terlambat' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-gray-50 text-gray-700 border-gray-200',
                                            };
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase {{ $statusClass }}">
                                            {{ str_replace('_', ' ', $late->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('monitoring-keterlambatan.show', $late->id) }}" class="inline-flex items-center px-3 py-1.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors text-xs font-semibold border border-gray-200">
                                            Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Tidak ada data keterlambatan ditemukan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $data->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
