<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Riwayat Absensi PKL</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Riwayat Absensi PKL</h3>
                    <p class="text-sm text-gray-500">{{ $penempatan->industri?->nama_industri ?? '-' }} - {{ $penempatan->rombelPkl?->nama_rombel ?? '-' }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('siswa.jurnal-prakerin.index') }}" class="rounded-xl border border-gray-200 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Kembali</a>
                    <a href="{{ route('siswa.jurnal-prakerin.absensi.pdf') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Export PDF</a>
                </div>
            </div>

            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                <p class="font-bold">Panduan riwayat absensi</p>
                <p class="mt-1">Gunakan halaman ini untuk mengecek check-in dan check-out selama PKL. Tombol Export PDF akan mengunduh seluruh riwayat absensi sebagai laporan.</p>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Tanggal</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check-in</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Check-out</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Catatan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($absensis as $absensi)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">{{ $absensi->tanggal?->format('d M Y') ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $absensi->check_in_at ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $absensi->check_out_at ?? '-' }}</td>
                                    <td class="px-6 py-4"><span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold uppercase text-gray-600">{{ $absensi->status ?? '-' }}</span></td>
                                    <td class="px-6 py-4 text-sm text-gray-500">{{ $absensi->catatan ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada riwayat absensi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-6">{{ $absensis->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
