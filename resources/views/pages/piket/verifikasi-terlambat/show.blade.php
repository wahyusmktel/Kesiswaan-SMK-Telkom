<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Proses Keterlambatan</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            <form action="{{ route('piket.verifikasi-terlambat.update', $keterlambatan->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                    <div class="lg:col-span-1 space-y-6">
                        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl overflow-hidden sticky top-24">
                            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 p-6 text-center text-white">
                                <div
                                    class="h-20 w-20 mx-auto rounded-full bg-white/20 backdrop-blur-sm border-4 border-white/30 flex items-center justify-center text-2xl font-bold mb-3 shadow-lg">
                                    {{ substr($keterlambatan->siswa->nama_lengkap, 0, 1) }}
                                </div>
                                <h3 class="text-xl font-bold">{{ $keterlambatan->siswa->nama_lengkap }}</h3>
                                <p
                                    class="text-indigo-100 text-sm font-medium bg-white/10 inline-block px-3 py-1 rounded-full mt-2">
                                    {{ $keterlambatan->siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                </p>
                            </div>

                            <div class="p-6 space-y-4">
                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Waktu
                                        Kedatangan</p>
                                    <div class="flex items-center gap-2 text-gray-800">
                                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span
                                            class="font-mono font-bold text-lg">{{ \Carbon\Carbon::parse($keterlambatan->waktu_dicatat_security)->format('H:i') }}
                                            WIB</span>
                                    </div>
                                </div>

                                <div>
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dicatat
                                        Oleh</p>
                                    <div class="flex items-center gap-2 text-gray-800">
                                        <div
                                            class="h-6 w-6 rounded-full bg-gray-200 flex items-center justify-center text-xs font-bold text-gray-500">
                                            {{ substr($keterlambatan->security->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium text-sm">{{ $keterlambatan->security->name }}</span>
                                    </div>
                                </div>

                                <div class="pt-4 border-t border-gray-100">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alasan
                                        Siswa</p>
                                    <div
                                        class="bg-amber-50 p-3 rounded-lg border border-amber-100 text-amber-900 text-sm italic">
                                        "{{ $keterlambatan->alasan_siswa }}"
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-2">
                        <div class="bg-white border border-gray-200 shadow-sm rounded-2xl p-6 sm:p-8">

                            <div class="flex items-center gap-3 mb-6">
                                <div class="p-2 bg-indigo-100 text-indigo-600 rounded-lg">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800">Tindak Lanjut Guru Piket</h3>
                                    <p class="text-sm text-gray-500">Tentukan tindakan dan berikan izin masuk kelas.</p>
                                </div>
                            </div>

                            <div class="space-y-6">
                                <div>
                                    <x-input-label for="tindak_lanjut_piket" class="text-gray-700 font-bold mb-2">
                                        {{ __('Tindakan / Sanksi (Opsional)') }}
                                    </x-input-label>
                                    <textarea name="tindak_lanjut_piket" id="tindak_lanjut_piket" rows="4"
                                        class="block w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 transition-colors bg-gray-50 focus:bg-white text-sm"
                                        placeholder="Contoh: Diberi nasehat, lari keliling lapangan, membersihkan sampah...">{{ old('tindak_lanjut_piket') }}</textarea>
                                    <p class="mt-2 text-xs text-gray-500 flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Kosongkan jika hanya diberikan izin masuk lisan.
                                    </p>
                                </div>

                                <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-100">
                                    <button type="submit"
                                        class="flex-1 inline-flex justify-center items-center px-6 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none shadow-lg hover:shadow-indigo-500/30 transition ease-in-out duration-150 transform hover:-translate-y-0.5">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Verifikasi & Cetak Surat
                                    </button>

                                    <a href="{{ route('piket.verifikasi-terlambat.index') }}"
                                        class="inline-flex justify-center items-center px-6 py-3 bg-white border border-gray-300 rounded-xl font-bold text-sm text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 transition ease-in-out duration-150">
                                        Batal
                                    </a>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>
</x-app-layout>
