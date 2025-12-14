<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Dispensasi</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div
                    class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-4">
                    <form action="{{ route('kesiswaan.persetujuan-dispensasi.index') }}" method="GET"
                        class="w-full sm:w-1/2 flex gap-2">
                        <div class="relative w-full">
                            <input type="text" name="search" value="{{ request('search') }}"
                                class="pl-4 pr-10 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm"
                                placeholder="Cari kegiatan atau pengaju...">
                            @if (request('status'))
                                <input type="hidden" name="status" value="{{ request('status') }}">
                            @endif
                            <button type="submit"
                                class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-500 hover:text-red-600">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </button>
                        </div>
                    </form>

                    <div class="flex p-1 space-x-1 bg-gray-200/50 rounded-lg">
                        @foreach (['' => 'Semua', 'diajukan' => 'Diajukan', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'] as $key => $label)
                            <a href="{{ route('kesiswaan.persetujuan-dispensasi.index', ['status' => $key, 'search' => request('search')]) }}"
                                class="px-4 py-2 text-xs font-medium rounded-md transition-all shadow-sm
                                      {{ request('status') == $key ? 'bg-white text-gray-800 shadow' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100' }}">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Kegiatan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Diajukan Oleh</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Waktu</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Siswa</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($daftarDispensasi as $item)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $item->nama_kegiatan }}</div>
                                        <div class="text-xs text-gray-500 truncate max-w-xs">
                                            {{ Str::limit($item->keterangan, 50) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-2">
                                            <div
                                                class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-600">
                                                {{ substr($item->diajukanOleh->name, 0, 1) }}
                                            </div>
                                            <span class="text-gray-700">{{ $item->diajukanOleh->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        <div>{{ \Carbon\Carbon::parse($item->waktu_mulai)->isoFormat('D MMM Y') }}
                                        </div>
                                        <div class="text-gray-400">
                                            {{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }} -
                                            {{ \Carbon\Carbon::parse($item->waktu_selesai)->format('H:i') }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-100">
                                            {{ $item->siswa_count }} Siswa
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($item->status) {
                                                'diajukan' => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                                'disetujui' => 'bg-green-50 text-green-700 border-green-200',
                                                'ditolak' => 'bg-red-50 text-red-700 border-red-200',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border capitalize {{ $statusClass }}">
                                            {{ $item->status }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right">
                                        <a href="{{ route('kesiswaan.persetujuan-dispensasi.show', $item->id) }}"
                                            class="inline-flex items-center px-3 py-1.5 bg-white text-gray-700 rounded-lg hover:bg-gray-50 border border-gray-200 shadow-sm transition-colors text-xs font-medium">
                                            Tinjau
                                            <svg class="ml-1.5 w-3 h-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 bg-gray-50/50">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                            <p class="text-base font-medium">Belum ada pengajuan dispensasi.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $daftarDispensasi->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
