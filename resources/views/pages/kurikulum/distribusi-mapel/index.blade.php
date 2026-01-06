<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Distribusi Mata Pelajaran</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Statistik Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Guru Mengajar</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-indigo-50 rounded-lg">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['total_guru'] }}</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Total Jam Pelajaran (JP)</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['total_jp'] }} JP</span>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
                    <div class="text-sm font-medium text-gray-500 mb-1">Rata-rata JP per Guru</div>
                    <div class="flex items-center gap-2">
                        <div class="p-2 bg-blue-50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <span class="text-2xl font-bold text-gray-900">{{ $stats['avg_jp'] }} JP</span>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h3 class="font-bold text-gray-700 text-lg">Rekapitulasi Distribusi Mengajar</h3>
                        <p class="text-sm text-gray-500">
                            @if($tahunAktif)
                                TP: {{ $tahunAktif->tahun_ajaran }} ({{ $tahunAktif->semester }})
                            @else
                                Tahun Pelajaran Belum Aktif
                            @endif
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 font-bold border-b border-gray-100 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Guru</th>
                                <th class="px-6 py-4">Mata Pelajaran</th>
                                <th class="px-6 py-4">Kelas</th>
                                <th class="px-6 py-4 text-center">JP</th>
                                <th class="px-6 py-4 text-center">Total JP Guru</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @forelse($rekapitulasi as $item)
                                @php
                                    $rowCount = $item->distribusi->count();
                                @endphp
                                @foreach($item->distribusi as $index => $dist)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        @if($index === 0)
                                            <td rowspan="{{ $rowCount }}" class="px-6 py-4 align-top font-bold text-gray-900 border-r border-gray-50 bg-gray-50/20">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center text-xs font-black">
                                                        {{ strtoupper(substr($item->nama_guru, 0, 1)) }}
                                                    </div>
                                                    {{ $item->nama_guru }}
                                                </div>
                                            </td>
                                        @endif
                                        <td class="px-6 py-4 text-gray-700 font-medium">
                                            {{ $dist['nama_mapel'] }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs font-bold border border-gray-200">
                                                {{ $dist['kelas'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center font-bold text-indigo-600">
                                            {{ $dist['jumlah_jam'] }}
                                        </td>
                                        @if($index === 0)
                                            <td rowspan="{{ $rowCount }}" class="px-6 py-4 text-center align-middle border-l border-gray-50">
                                                <div class="inline-flex flex-col items-center">
                                                    <span class="text-xl font-black text-gray-900">{{ $item->total_jp }}</span>
                                                    <span class="text-[10px] text-gray-500 uppercase font-extrabold tracking-tighter">TOTAL JP</span>
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 italic">
                                        Data distribusi mata pelajaran belum tersedia untuk tahun pelajaran aktif.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
