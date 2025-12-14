<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Riwayat Log Gerbang</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">

                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('security.verifikasi.riwayat') }}" method="GET"
                        class="flex flex-col lg:flex-row gap-4">
                        <div class="relative flex-1">
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Cari Nama Siswa..."
                                class="pl-4 w-full rounded-lg border-gray-300 focus:ring-indigo-500 focus:border-indigo-500 text-sm h-10">
                        </div>
                        <div class="flex gap-2">
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                class="rounded-lg border-gray-300 text-sm h-10">
                            <input type="date" name="end_date" value="{{ request('end_date') }}"
                                class="rounded-lg border-gray-300 text-sm h-10">
                            <button type="submit"
                                class="px-4 bg-indigo-600 text-white rounded-lg text-sm font-bold hover:bg-indigo-500 h-10">Filter</button>
                        </div>
                    </form>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold">Siswa</th>
                                <th class="px-6 py-4 font-bold">Kelas</th>
                                <th class="px-6 py-4 font-bold">Keluar</th>
                                <th class="px-6 py-4 font-bold">Kembali</th>
                                <th class="px-6 py-4 font-bold">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($riwayatIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80">
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ $izin->siswa->name }}</td>
                                    <td class="px-6 py-4">
                                        {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-red-600">
                                        {{ \Carbon\Carbon::parse($izin->waktu_keluar_sebenarnya)->format('d/m H:i') }}
                                    </td>
                                    <td class="px-6 py-4 font-mono text-green-600">
                                        {{ $izin->waktu_kembali_sebenarnya ? \Carbon\Carbon::parse($izin->waktu_kembali_sebenarnya)->format('d/m H:i') : '-' }}
                                    </td>
                                    <td class="px-6 py-4">{{ $izin->securityVerifier->name ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">Belum ada riwayat.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200">{{ $riwayatIzin->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
