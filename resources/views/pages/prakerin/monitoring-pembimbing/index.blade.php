<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Manajemen Jurnal Siswa Prakerin</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-emerald-100 bg-emerald-50 p-5 text-sm text-emerald-900">
                <p class="font-bold">Panduan pembimbing internal</p>
                <p class="mt-1">Tinjau jurnal siswa secara rutin, beri catatan yang konkret, dan gunakan status Sudah Ditinjau sebagai tanda bahwa perkembangan siswa sudah dipantau. Konsistensi pembimbing membantu siswa lebih disiplin dan percaya diri selama prakerin.</p>
            </div>

            <div class="grid gap-4 md:grid-cols-3">
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Siswa Bimbingan</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $siswaBimbingan->count() }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Jurnal Menunggu</p>
                    <p class="mt-2 text-2xl font-bold text-yellow-600">{{ $siswaBimbingan->sum('jurnal_menunggu_count') }}</p>
                </div>
                <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-5">
                    <p class="text-sm text-gray-500">Sudah Ditinjau</p>
                    <p class="mt-2 text-2xl font-bold text-emerald-600">{{ $siswaBimbingan->sum('jurnal_ditinjau_count') }}</p>
                </div>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="border-b border-gray-100 p-6">
                    <h3 class="font-bold text-gray-900">Daftar Siswa Bimbingan</h3>
                    <p class="text-sm text-gray-500">Pilih siswa untuk mereview jurnal kegiatan dan memberikan catatan pembimbing.</p>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Rombel/Industri</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Periode</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Jurnal</th>
                                <th class="px-6 py-3 text-right text-xs font-bold uppercase text-gray-500">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($siswaBimbingan as $item)
                                <tr>
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $item->siswa?->nama_lengkap ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $item->siswa?->nis ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <p class="font-semibold">{{ $item->rombelPkl?->nama_rombel ?? '-' }}</p>
                                        <p class="text-gray-500">{{ $item->industri?->nama_industri ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        {{ $item->tanggal_mulai ? \Carbon\Carbon::parse($item->tanggal_mulai)->format('d M Y') : '-' }}
                                        -
                                        {{ $item->tanggal_selesai ? \Carbon\Carbon::parse($item->tanggal_selesai)->format('d M Y') : '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <div class="flex flex-wrap gap-2">
                                            <span class="rounded-full bg-gray-100 px-3 py-1 font-semibold text-gray-700">Total {{ $item->jurnals_count }}</span>
                                            <span class="rounded-full bg-yellow-100 px-3 py-1 font-semibold text-yellow-800">Menunggu {{ $item->jurnal_menunggu_count }}</span>
                                            <span class="rounded-full bg-emerald-100 px-3 py-1 font-semibold text-emerald-800">Ditinjau {{ $item->jurnal_ditinjau_count }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('pembimbing-prakerin.monitoring.show', $item) }}" class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700">Review Jurnal</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-10 text-center text-gray-500">Anda tidak memiliki siswa bimbingan aktif.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
