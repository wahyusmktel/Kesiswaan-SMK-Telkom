<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Riwayat Persetujuan Izin</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('piket.persetujuan-izin-keluar.riwayat') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end lg:items-center">

                            <div class="w-full lg:w-1/3">
                                <label class="text-xs font-semibold text-gray-500 uppercase mb-1 block">Cari
                                    Siswa</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                        </svg>
                                    </div>
                                    <input type="text" name="search" value="{{ request('search') }}"
                                        class="pl-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                        placeholder="Nama siswa...">
                                </div>
                            </div>

                            <div class="w-full lg:w-1/3 flex gap-2">
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

                            <div class="w-full lg:w-auto flex gap-2">
                                <button type="submit"
                                    class="h-10 px-4 bg-red-600 hover:bg-red-500 text-white rounded-lg shadow-sm transition-colors text-sm font-semibold flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
                                    </svg>
                                    Filter
                                </button>
                                <a href="{{ route('piket.persetujuan-izin-keluar.riwayat') }}"
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
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Kelas</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Waktu Setuju</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status Akhir</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Oleh</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($riwayatIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                        {{ $izin->siswa->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-gray-600">
                                        {{ \Carbon\Carbon::parse($izin->guru_piket_approved_at)->format('d M Y, H:i') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'selesai' => 'bg-green-100 text-green-800',
                                                'terlambat' => 'bg-orange-100 text-orange-800',
                                                'ditolak' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase {{ $statusClass }}">
                                            {{ str_replace('_', ' ', $izin->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-gray-600">
                                        {{ $izin->guruPiketApprover->name ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-base font-medium">Belum ada riwayat persetujuan.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $riwayatIzin->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
