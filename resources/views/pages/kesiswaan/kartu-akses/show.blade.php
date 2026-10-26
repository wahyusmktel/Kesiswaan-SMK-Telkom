<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Preview Kartu Akses</h2>
    </x-slot>

    <div class="py-6" x-data="{ currentDesign: '{{ $design }}', currentSide: 'front' }">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('kesiswaan.kartu-akses.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Preview Kartu Akses</h1>
                        <p class="text-sm text-gray-500">{{ $siswa->nama_lengkap }} ({{ $siswa->nis }})</p>
                    </div>
                </div>

                {{-- Switch Desain Preview --}}
                <div class="flex items-center gap-2 bg-gray-100 p-1.5 rounded-xl">
                    <button type="button" @click="currentDesign = 'standar_ts'"
                        :class="currentDesign === 'standar_ts' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all">
                        Standar TS
                    </button>
                    <button type="button" @click="currentDesign = 'standard'"
                        :class="currentDesign === 'standard' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all">
                        Standard
                    </button>
                    <button type="button" @click="currentDesign = 'minimalist'"
                        :class="currentDesign === 'minimalist' ? 'bg-white text-indigo-700 shadow-sm font-bold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-3 py-1.5 rounded-lg text-xs transition-all">
                        Minimalist
                    </button>
                </div>
            </div>

            {{-- Switch Sisi Kartu (Depan / Belakang) untuk Standar TS --}}
            <div x-show="currentDesign === 'standar_ts'" class="flex justify-center mb-5">
                <div class="inline-flex p-1 bg-gray-100 rounded-xl shadow-inner gap-1">
                    <button type="button" @click="currentSide = 'front'"
                        :class="currentSide === 'front' ? 'bg-white text-indigo-700 shadow font-bold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" /></svg>
                        Tampak Depan (Biodata)
                    </button>
                    <button type="button" @click="currentSide = 'back'"
                        :class="currentSide === 'back' ? 'bg-white text-indigo-700 shadow font-bold' : 'text-gray-600 hover:text-gray-900'"
                        class="px-4 py-2 rounded-lg text-xs transition-all flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Tampak Belakang (Ketentuan)
                    </button>
                </div>
            </div>

            {{-- Card Preview Area --}}
            <div class="flex justify-center mb-8">
                {{-- 1. Desain Standar TS (Depan & Belakang) --}}
                <div x-show="currentDesign === 'standar_ts'">
                    <div x-show="currentSide === 'front'" class="rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
                        @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts', ['siswa' => $siswa, 'barcode' => $barcode, 'kepsekData' => $kepsekData, 'qrCodeKepsek' => $qrCodeKepsek])
                    </div>
                    <div x-show="currentSide === 'back'" style="display: none;" class="rounded-2xl shadow-2xl overflow-hidden border border-gray-200">
                        @include('pages.kesiswaan.kartu-akses.partials.card-standar-ts-back')
                    </div>
                </div>

                {{-- 2. Desain Standard --}}
                <div x-show="currentDesign === 'standard'" style="display: none;">
                    <div class="stella-card bg-gradient-to-br from-slate-800 via-slate-900 to-slate-950 rounded-2xl shadow-2xl overflow-hidden" style="width: 340px; height: 215px;">
                        {{-- Card Header --}}
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 px-5 py-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-white font-bold text-sm tracking-wide">STELLA ACCESS CARD</h2>
                                        <p class="text-indigo-200 text-[10px]">SMK Telkom Lampung</p>
                                    </div>
                                </div>
                                <div class="w-6 h-6 bg-white/10 rounded-full"></div>
                            </div>
                        </div>

                        {{-- Card Body --}}
                        <div class="px-5 py-4">
                            <div class="flex gap-4">
                                {{-- Photo Placeholder --}}
                                <div class="w-20 h-24 bg-gradient-to-br from-gray-700 to-gray-800 rounded-xl flex items-center justify-center border-2 border-gray-600 flex-shrink-0">
                                    <svg class="w-10 h-10 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>

                                {{-- Info --}}
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-white font-bold text-sm truncate mb-2">{{ strtoupper($siswa->nama_lengkap) }}</h3>
                                    <div class="space-y-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-400 text-[10px] w-12">NIPD</span>
                                            <span class="text-white text-xs font-mono">{{ $siswa->nis }}</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-gray-400 text-[10px] w-12">KELAS</span>
                                            <span class="text-white text-xs">{{ $siswa->rombels->first()?->kelas?->nama_kelas ?? '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Barcode --}}
                            <div class="mt-3 bg-white rounded-lg p-3 flex flex-col items-center">
                                <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode" class="h-12 w-auto">
                                <span class="text-[10px] text-gray-600 font-mono mt-1">{{ $siswa->nis }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 3. Desain Minimalist --}}
                <div x-show="currentDesign === 'minimalist'" style="display: none;">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 p-4 flex flex-col items-center justify-center text-center" style="width: 340px; height: 140px;">
                        <img src="data:image/png;base64,{{ $barcode }}" alt="Barcode" class="h-14 w-full object-contain">
                        <div class="text-xs font-mono font-bold text-gray-800 mt-1">{{ $siswa->nis }}</div>
                        <div class="w-full mt-2 pt-2 border-t border-gray-100">
                            <p class="text-xs font-bold text-gray-900 truncate">{{ strtoupper($siswa->nama_lengkap) }}</p>
                            <p class="text-[10px] text-indigo-600 font-bold tracking-wider mt-0.5">STELLA ACCESS &bull; SMK TELKOM LAMPUNG</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex flex-wrap justify-center gap-3">
                <a :href="'{{ route('kesiswaan.kartu-akses.cetak', $siswa) }}?design=' + currentDesign + '&side=both'" target="_blank"
                    class="px-5 py-2.5 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Kartu (Dua Sisi)
                </a>
                <a :href="'{{ route('kesiswaan.kartu-akses.cetak', $siswa) }}?design=' + currentDesign + '&side=' + currentSide" target="_blank"
                    class="px-4 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all shadow-sm flex items-center gap-2 text-sm">
                    Cetak Sisi Aktif (<span x-text="currentSide === 'front' ? 'Depan' : 'Belakang'"></span>)
                </a>
                <a href="{{ route('kesiswaan.kartu-akses.index') }}"
                    class="px-4 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2 text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
