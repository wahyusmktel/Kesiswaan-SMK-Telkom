<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pengajuan Dispensasi</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-gradient-to-r from-indigo-600 to-blue-500 rounded-2xl p-6 shadow-lg text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-bold">Buat Pengajuan Baru</h3>
                    <p class="text-indigo-100 mt-1">Ajukan dispensasi untuk kegiatan lomba, organisasi, atau tugas luar
                        sekolah.</p>
                </div>
                <a href="{{ route('dispensasi.pengajuan.create') }}"
                    class="inline-flex items-center px-6 py-3 bg-white text-indigo-700 rounded-xl font-bold text-sm shadow-md hover:bg-indigo-50 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Buat Dispensasi
                </a>
            </div>

            <div>
                <h3 class="font-bold text-gray-800 text-lg mb-4 px-1">Riwayat Pengajuan</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse ($riwayatDispensasi as $item)
                        <div
                            class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all relative overflow-hidden group">
                            <div class="absolute top-4 right-4">
                                @php
                                    $statusClass = match ($item->status) {
                                        'disetujui' => 'bg-green-100 text-green-700 border-green-200',
                                        'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                        default => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                    };
                                @endphp
                                <span
                                    class="px-2.5 py-1 rounded-lg text-xs font-bold border uppercase {{ $statusClass }}">
                                    {{ $item->status }}
                                </span>
                            </div>

                            <div class="p-6 pb-4">
                                <div class="mb-4">
                                    <h4 class="font-bold text-gray-900 text-lg leading-tight line-clamp-2"
                                        title="{{ $item->nama_kegiatan }}">
                                        {{ $item->nama_kegiatan }}
                                    </h4>
                                    <p class="text-sm text-gray-500 mt-1">
                                        {{ \Carbon\Carbon::parse($item->waktu_mulai)->isoFormat('D MMMM Y') }}
                                    </p>
                                </div>

                                <div class="flex items-center gap-4 text-sm text-gray-600 mb-4">
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                        </svg>
                                        <span class="font-bold">{{ $item->siswa_count }}</span> Siswa
                                    </div>
                                    <div
                                        class="flex items-center gap-1.5 bg-gray-50 px-2 py-1 rounded border border-gray-100">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>{{ \Carbon\Carbon::parse($item->waktu_mulai)->format('H:i') }}</span>
                                    </div>
                                </div>

                                @if ($item->status == 'ditolak')
                                    <div
                                        class="bg-red-50 p-3 rounded-xl border border-red-100 text-xs text-red-700 italic">
                                        "{{ Str::limit($item->alasan_penolakan, 50) }}"
                                    </div>
                                @endif
                            </div>

                            @if ($item->status == 'disetujui')
                                <div class="bg-gray-50 px-6 py-3 border-t border-gray-100">
                                    <a href="{{ route('kesiswaan.persetujuan-dispensasi.print', $item->id) }}"
                                        target="_blank"
                                        class="text-indigo-600 hover:text-indigo-800 text-sm font-bold flex items-center justify-center gap-2 group-hover:underline">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Cetak Surat
                                    </a>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div
                            class="col-span-full py-12 flex flex-col items-center justify-center text-center bg-white rounded-2xl border-2 border-dashed border-gray-300">
                            <div class="bg-gray-100 p-4 rounded-full mb-3">
                                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h3 class="text-lg font-bold text-gray-900">Belum Ada Pengajuan</h3>
                            <p class="text-gray-500">Mulai buat pengajuan dispensasi baru sekarang.</p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $riwayatDispensasi->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
