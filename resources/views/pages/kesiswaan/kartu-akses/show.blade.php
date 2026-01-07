<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Preview Kartu Akses</h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('kesiswaan.kartu-akses.index') }}" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Preview Kartu Akses</h1>
                    <p class="text-sm text-gray-500">{{ $siswa->nama_lengkap }}</p>
                </div>
            </div>

            {{-- Card Preview --}}
            <div class="flex justify-center mb-8">
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

            {{-- Actions --}}
            <div class="flex justify-center gap-4">
                <a href="{{ route('kesiswaan.kartu-akses.cetak', $siswa) }}" target="_blank"
                    class="px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-xl hover:from-indigo-700 hover:to-purple-700 transition-all shadow-lg flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Cetak Kartu
                </a>
                <a href="{{ route('kesiswaan.kartu-akses.index') }}"
                    class="px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-all flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
