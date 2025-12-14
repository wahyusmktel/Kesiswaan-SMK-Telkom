<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Verifikasi Keterlambatan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3 shadow-sm">
                <svg class="w-6 h-6 text-blue-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <h4 class="text-sm font-bold text-blue-800">Menunggu Verifikasi</h4>
                    <p class="text-xs text-blue-600 mt-1">Daftar siswa di bawah ini telah dicatat terlambat oleh
                        Security dan menunggu tindak lanjut dari Guru Piket.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse ($daftarSiswaTerlambat as $item)
                    <div
                        class="bg-white border border-gray-200 rounded-2xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden relative group">

                        <div class="absolute top-4 right-4">
                            <span
                                class="inline-flex items-center px-2 py-1 rounded-md text-xs font-bold bg-red-50 text-red-600 border border-red-100">
                                {{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->diffForHumans() }}
                            </span>
                        </div>

                        <div class="p-6">
                            <div class="flex items-center gap-4 mb-4">
                                <div
                                    class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-lg border border-gray-200">
                                    {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                                </div>
                                <div>
                                    <h4 class="text-lg font-bold text-gray-900 leading-tight line-clamp-1"
                                        title="{{ $item->siswa->nama_lengkap }}">{{ $item->siswa->nama_lengkap }}</h4>
                                    <p class="text-sm text-gray-500">
                                        {{ $item->siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}</p>
                                </div>
                            </div>

                            <div class="space-y-3">
                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dicatat
                                        Oleh</p>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        <span
                                            class="text-sm font-medium text-gray-700">{{ $item->security->name }}</span>
                                    </div>
                                </div>

                                <div class="bg-gray-50 p-3 rounded-lg border border-gray-100">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Waktu
                                        Datang</p>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span
                                            class="text-sm font-mono font-bold text-gray-800">{{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-6 py-4 border-t border-gray-100">
                            <a href="{{ route('piket.verifikasi-terlambat.show', $item->id) }}"
                                class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 text-white rounded-lg font-bold text-sm shadow-md hover:bg-indigo-500 transition-all transform group-hover:-translate-y-0.5">
                                Proses Verifikasi
                                <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 8l4 4m0 0l-4 4m4-4H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                        <div class="bg-green-50 p-4 rounded-full mb-4">
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Semua Beres!</h3>
                        <p class="text-gray-500 max-w-sm mt-1">Tidak ada siswa terlambat yang perlu diverifikasi saat
                            ini.</p>
                    </div>
                @endforelse
            </div>

        </div>
    </div>
</x-app-layout>
