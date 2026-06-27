<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Monitor Jurnal Kegiatan Prakerin</h2>
    </x-slot>

    <div class="py-8">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="rounded-2xl border border-blue-100 bg-blue-50 p-5 text-sm text-blue-900">
                <p class="font-bold">Monitor koordinator</p>
                <p class="mt-1">Halaman ini menampilkan seluruh jurnal siswa prakerin untuk memantau keaktifan siswa dan respons pembimbing internal pada semua rombel.</p>
            </div>

            <div class="flex justify-end">
                <a href="{{ route('prakerin.jurnal.analytics') }}" class="rounded-xl bg-gray-900 px-4 py-2 text-sm font-semibold text-white hover:bg-black">Lihat Analisis</a>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Tanggal/Siswa</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Rombel/Industri</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Pembimbing</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Kegiatan</th>
                                <th class="px-6 py-3 text-left text-xs font-bold uppercase text-gray-500">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($jurnals as $jurnal)
                                <tr class="align-top">
                                    <td class="px-6 py-4">
                                        <p class="font-semibold text-gray-900">{{ $jurnal->penempatan?->siswa?->nama_lengkap ?? '-' }}</p>
                                        <p class="text-xs text-gray-500">{{ $jurnal->tanggal?->format('d M Y') ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <p class="font-semibold">{{ $jurnal->penempatan?->rombelPkl?->nama_rombel ?? '-' }}</p>
                                        <p class="text-gray-500">{{ $jurnal->penempatan?->industri?->nama_industri ?? '-' }}</p>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ $jurnal->penempatan?->guruPembimbing?->nama_lengkap ?? '-' }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-700">{{ Str::limit($jurnal->kegiatan_dilakukan, 140) }}</td>
                                    <td class="px-6 py-4">
                                        <span class="rounded-full px-3 py-1 text-xs font-bold {{ $jurnal->status_verifikasi === 'disetujui' ? 'bg-emerald-100 text-emerald-800' : ($jurnal->status_verifikasi === 'revisi' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                            {{ $jurnal->status_verifikasi === 'disetujui' ? 'Sudah Ditinjau' : Str::title($jurnal->status_verifikasi) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-10 text-center text-gray-500">Belum ada jurnal siswa.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="border-t border-gray-100 p-6">{{ $jurnals->links() }}</div>
            </div>
        </div>
    </div>
</x-app-layout>
