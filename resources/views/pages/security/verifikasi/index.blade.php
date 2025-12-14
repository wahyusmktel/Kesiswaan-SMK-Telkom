<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Pos Keamanan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <div
                class="bg-gradient-to-r from-slate-800 to-slate-700 rounded-2xl p-6 shadow-lg text-white flex flex-col sm:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-2xl font-bold">Verifikasi Gerbang</h3>
                    <p class="text-slate-300 mt-1">Gunakan pemindai untuk verifikasi surat izin siswa.</p>
                </div>
                <a href="{{ route('security.verifikasi.scan') }}"
                    class="inline-flex items-center px-8 py-4 bg-white text-slate-800 rounded-xl font-black text-lg shadow-lg hover:bg-slate-100 hover:scale-105 transition-all transform">
                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 14.5v.01M12 18.5v.01M12 10.5v.01M16 18.5v.01M16 14.5v.01M16 10.5v.01M8 18.5v.01M8 14.5v.01M8 10.5v.01M4 11l.001-.001M4 15l.001-.001M4 19l.001-.001M20 19l.001-.001M20 15l.001-.001M20 11l.001-.001" />
                    </svg>
                    SCAN QR CODE
                </a>
            </div>

            <div>
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-blue-600 rounded-full shadow-sm"></div>
                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Siap Keluar
                        ({{ $daftarIzin->where('status', 'disetujui_guru_piket')->count() }})</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($daftarIzin->where('status', 'disetujui_guru_piket') as $izin)
                        <div
                            class="bg-white border border-blue-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10">
                                <svg class="w-16 h-16 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold">
                                        {{ substr($izin->siswa->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 leading-tight">{{ $izin->siswa->name }}</h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $izin->siswa->masterSiswa?->rombels->first()?->kelas->nama_kelas ?? '-' }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 bg-gray-50 p-2 rounded mb-4 border border-gray-100">
                                    "{{ Str::limit($izin->tujuan, 40) }}"
                                </p>
                                <form action="{{ route('security.verifikasi.keluar', $izin->id) }}" method="POST">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-blue-600 text-white rounded-lg font-bold text-sm hover:bg-blue-500 transition-colors">
                                        Verifikasi Keluar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            Tidak ada siswa yang menunggu keluar.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="mt-8">
                <div class="flex items-center gap-2 mb-4 px-1">
                    <div class="w-1.5 h-6 bg-orange-500 rounded-full shadow-sm"></div>
                    <h3 class="text-lg font-bold text-gray-800 tracking-tight">Sedang Di Luar
                        ({{ $daftarIzin->where('status', 'diverifikasi_security')->count() }})</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse ($daftarIzin->where('status', 'diverifikasi_security') as $izin)
                        <div
                            class="bg-white border border-orange-200 rounded-xl p-5 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden">
                            <div class="absolute top-0 right-0 p-3 opacity-10">
                                <svg class="w-16 h-16 text-orange-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>

                            <div class="relative z-10">
                                <div class="flex items-center gap-3 mb-3">
                                    <div
                                        class="h-10 w-10 rounded-full bg-orange-100 flex items-center justify-center text-orange-700 font-bold">
                                        {{ substr($izin->siswa->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-900 leading-tight">{{ $izin->siswa->name }}</h4>
                                        <p class="text-xs text-gray-500">Keluar:
                                            {{ \Carbon\Carbon::parse($izin->waktu_keluar_sebenarnya)->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('security.verifikasi.kembali', $izin->id) }}" method="POST"
                                        class="flex-1">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="w-full inline-flex justify-center items-center px-4 py-2 bg-green-600 text-white rounded-lg font-bold text-sm hover:bg-green-500 transition-colors">
                                            Catat Kembali
                                        </button>
                                    </form>
                                    <a href="{{ route('security.verifikasi.print', $izin->id) }}" target="_blank"
                                        class="inline-flex items-center justify-center px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 border border-gray-200">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div
                            class="col-span-full py-8 text-center text-gray-400 bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                            Tidak ada siswa di luar lingkungan sekolah.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
