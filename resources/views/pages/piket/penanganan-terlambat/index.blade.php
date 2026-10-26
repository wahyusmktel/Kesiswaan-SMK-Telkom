<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Penanganan Siswa Terlambat</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="penangananTerlambat()">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Scanner Feedback Toast / Alert --}}
            <div x-show="scanNotice" 
                x-cloak 
                x-transition 
                class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    </div>
                    <div>
                        <p class="font-bold text-sm" x-text="scanNotice"></p>
                        <p class="text-xs text-emerald-600">Data siswa langsung dipilih dan siap diproses.</p>
                    </div>
                </div>
                <button type="button" @click="scanNotice = ''" class="text-emerald-500 hover:text-emerald-700">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

                {{-- Left Column: Search & List (2 Cols) --}}
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                        {{-- Header 1. Cari Siswa --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-wrap items-center justify-between gap-3 min-h-[68px]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-black text-xs">1</span>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-base leading-none">Cari Siswa</h3>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Ketik nama, NIS, atau scan barcode kartu pelajar</p>
                                </div>
                            </div>
                            <button type="button" @click="openScannerModal()"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-red-50 text-gray-700 hover:text-red-600 text-xs font-bold rounded-lg border border-gray-300 hover:border-red-300 shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500">
                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                </svg>
                                <span>Scan Barcode</span>
                            </button>
                        </div>

                        {{-- Search Input Bar --}}
                        <div class="p-6 border-b border-gray-100">
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="searchSiswa()"
                                    class="pl-11 pr-24 block w-full rounded-lg border-gray-300 bg-white text-sm focus:border-red-500 focus:ring-red-500 shadow-sm h-11"
                                    placeholder="Ketik Nama atau NIS siswa..." autofocus>
                                <div class="absolute inset-y-0 right-1 flex items-center pr-1.5">
                                    <button type="button" @click="openScannerModal()" 
                                        class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-50 hover:bg-red-100 text-red-600 rounded-md text-xs font-bold transition-colors"
                                        title="Scan Barcode / QR Kartu Pelajar">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                                        </svg>
                                        <span>Scan</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Search Results Area --}}
                        <div class="p-6 overflow-y-auto max-h-[460px]">
                            <div x-show="isLoading" class="flex justify-center items-center py-8">
                                <svg class="animate-spin h-8 w-8 text-red-500" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                            </div>

                            <div x-show="!isLoading && results.length === 0 && searchQuery.length >= 2"
                                class="text-center py-8 text-gray-500">
                                <svg class="w-12 h-12 mx-auto text-gray-300 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Siswa tidak ditemukan.
                            </div>

                            <div x-show="!isLoading && results.length === 0 && searchQuery.length < 2"
                                class="text-center py-8 text-gray-400">
                                <svg class="w-12 h-12 mx-auto text-gray-200 mb-2" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                                Ketik nama, NIS siswa, atau klik <span class="font-bold text-red-600">Scan Barcode</span>.
                            </div>

                            <div x-show="!isLoading && results.length > 0"
                                class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <template x-for="siswa in results" :key="siswa.id">
                                    <div @click="selectSiswa(siswa)"
                                        class="cursor-pointer border rounded-xl p-4 hover:border-red-300 hover:bg-red-50/50 transition-all group relative"
                                        :class="{
                                            'border-red-500 bg-red-50 ring-1 ring-red-500': selectedSiswa?.id === siswa.id,
                                            'border-gray-200': selectedSiswa?.id !== siswa.id
                                        }">

                                        <div class="flex items-start gap-3">
                                            <div
                                                class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm flex-shrink-0 group-hover:bg-white group-hover:text-red-500 transition-colors">
                                                <span x-text="siswa.nama_lengkap.charAt(0)"></span>
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-gray-800 text-sm group-hover:text-red-700 leading-snug"
                                                    x-text="siswa.nama_lengkap"></h4>
                                                <p class="text-xs text-gray-500 mt-0.5">NIS: <span class="font-mono font-medium"
                                                        x-text="siswa.nis"></span></p>
                                                <p class="text-xs text-gray-500">Kelas: <span class="font-semibold text-gray-700"
                                                        x-text="siswa.kelas || 'Belum ada'"></span></p>
                                            </div>
                                        </div>

                                        <div x-show="selectedSiswa?.id === siswa.id"
                                            class="absolute top-3 right-3 text-red-500">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7" />
                                            </svg>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    {{-- Today's Lateness List --}}
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                            <div>
                                <h3 class="font-bold text-gray-800 text-base leading-none">Terlambat Hari Ini</h3>
                                <p class="text-[11px] text-gray-500 mt-0.5">Daftar siswa yang telah dicatat keterlambatannya</p>
                            </div>
                            <span class="px-2.5 py-1 bg-red-100 text-red-700 rounded-md text-xs font-black">{{ $terlambatHariIni->count() }} Siswa</span>
                        </div>
                        <div class="p-0 overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50 text-gray-500 uppercase text-[10px] font-black tracking-widest border-b border-gray-100">
                                    <tr>
                                        <th class="px-6 py-3">Siswa</th>
                                        <th class="px-6 py-3 text-center">Waktu</th>
                                        <th class="px-6 py-3 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($terlambatHariIni as $late)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="font-bold text-gray-900 leading-none mb-1">{{ $late->siswa->user->name ?? $late->siswa->nama_lengkap }}</div>
                                                <div class="text-[10px] text-gray-400 font-medium">{{ $late->siswa->rombels->first()?->kelas->nama_kelas ?? '-' }} • {{ $late->siswa->nis }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded font-black text-[10px] border border-gray-200">
                                                    {{ $late->waktu_dicatat_security->format('H:i') }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                <a href="{{ route('piket.penanganan-terlambat.print', $late->id) }}" target="_blank" class="inline-flex items-center p-1.5 bg-white text-gray-400 hover:text-red-600 rounded-lg border border-gray-200 hover:border-red-200 hover:bg-red-50 transition-all" title="Cetak Ulang Slip">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                                </a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-gray-400 italic">
                                                Belum ada data keterlambatan hari ini.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Right Column: Process Card (1 Col) --}}
                <div class="lg:col-span-1">
                    <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden sticky top-6">
                        {{-- Header 2. Proses Keterlambatan --}}
                        <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between gap-3 min-h-[68px]">
                            <div class="flex items-center gap-2.5">
                                <span class="w-7 h-7 rounded-lg bg-red-100 text-red-600 flex items-center justify-center font-black text-xs">2</span>
                                <div>
                                    <h3 class="font-bold text-gray-800 text-base leading-none">Proses Keterlambatan</h3>
                                    <p class="text-[11px] text-gray-500 mt-0.5">Input alasan & tindak lanjut</p>
                                </div>
                            </div>
                            <div>
                                <span x-show="selectedSiswa" class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-md text-[11px] font-bold">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Terpilih
                                </span>
                                <span x-show="!selectedSiswa" class="inline-flex items-center px-2.5 py-1 bg-gray-100 text-gray-400 rounded-md text-[11px] font-medium">
                                    Menunggu
                                </span>
                            </div>
                        </div>

                        <div class="p-6">
                            <form action="{{ route('piket.penanganan-terlambat.store') }}" method="POST"
                                x-show="selectedSiswa">
                                @csrf
                                <input type="hidden" name="master_siswa_id" :value="selectedSiswa?.id">

                                <div class="bg-red-50/70 border border-red-100 rounded-xl p-4 mb-5">
                                    <div class="flex items-center justify-between mb-1">
                                        <p class="text-[10px] font-black text-red-600 uppercase tracking-wider">Siswa Terpilih</p>
                                        <button type="button" @click="resetSelection()" class="text-xs text-gray-400 hover:text-red-600 font-medium">Ganti</button>
                                    </div>
                                    <p class="text-base font-bold text-gray-900 leading-snug" x-text="selectedSiswa?.nama_lengkap"></p>
                                    <p class="text-xs text-gray-600 mt-1">
                                        <span class="font-mono font-semibold" x-text="selectedSiswa?.nis"></span> • 
                                        <span class="font-medium text-gray-800" x-text="selectedSiswa?.kelas"></span>
                                    </p>
                                </div>

                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Alasan Terlambat</label>
                                        <textarea name="alasan" id="input-alasan" rows="3" required
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                            placeholder="Contoh: Ban bocor, bangun kesiangan..."></textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5">Tindak Lanjut <span
                                                class="font-normal text-gray-400 normal-case">(Opsional)</span></label>
                                        <textarea name="tindak_lanjut" rows="2"
                                            class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                                            placeholder="Contoh: Diberi nasehat, lari keliling lapangan..."></textarea>
                                    </div>
                                </div>

                                <div class="mt-6 pt-5 border-t border-gray-100">
                                    <button type="submit"
                                        class="w-full inline-flex justify-center items-center px-4 py-3 bg-red-600 border border-transparent rounded-lg font-bold text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none shadow-md hover:shadow-red-500/20 transition ease-in-out duration-150">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                        </svg>
                                        Simpan & Cetak
                                    </button>
                                    <button type="button" @click="resetSelection()"
                                        class="mt-2.5 w-full text-center text-xs text-gray-500 hover:text-gray-700 font-medium py-1">Batal</button>
                                </div>
                            </form>

                            <div x-show="!selectedSiswa" class="text-center py-12 text-gray-400">
                                <div class="w-14 h-14 mx-auto mb-3 rounded-full bg-gray-50 flex items-center justify-center text-gray-300">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                    </svg>
                                </div>
                                <p class="font-bold text-sm text-gray-700 mb-1">Belum Ada Siswa Dipilih</p>
                                <p class="text-xs text-gray-400 max-w-xs mx-auto">Cari nama/NIS atau gunakan tombol <span class="font-bold text-red-600">Scan Barcode</span> di sebelah kiri untuk memilih siswa.</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Barcode / QR Scanner Modal --}}
        <div x-show="scannerOpen" 
            x-cloak 
            class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center p-4 sm:p-6"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0">

            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="stopScanner()"></div>

            {{-- Modal Dialog --}}
            <div class="relative bg-white rounded-2xl overflow-hidden shadow-2xl max-w-md w-full border border-gray-100 transform transition-all z-10"
                @click.away="stopScanner()">

                {{-- Modal Header --}}
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/70 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-red-100 text-red-600 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z" />
                            </svg>
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900 text-sm leading-tight">Scan Barcode Siswa</h4>
                            <p class="text-[11px] text-gray-500">Arahkan kamera ke kartu pelajar siswa</p>
                        </div>
                    </div>
                    <button type="button" @click="stopScanner()" class="p-1.5 rounded-lg text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                {{-- Scanner Viewport --}}
                <div class="p-5">
                    <div class="relative bg-black rounded-xl overflow-hidden min-h-[260px] flex items-center justify-center shadow-inner">
                        {{-- Reader element --}}
                        <div id="barcode-reader" class="w-full"></div>

                        {{-- Scanning Guide Box (Visual Aid) --}}
                        <div x-show="!scannerLoading && !scannerError" class="pointer-events-none absolute inset-0 flex items-center justify-center p-6">
                            <div class="w-full max-w-[280px] h-[160px] border-2 border-red-500/80 rounded-xl relative shadow-[0_0_0_9999px_rgba(0,0,0,0.35)]">
                                <div class="absolute inset-x-0 top-0 h-0.5 bg-red-500 shadow-[0_0_8px_rgba(239,68,68,1)] animate-bounce"></div>
                            </div>
                        </div>

                        {{-- Loading Spinner --}}
                        <div x-show="scannerLoading" class="absolute inset-0 bg-gray-900/90 flex flex-col items-center justify-center gap-2 text-white z-20">
                            <svg class="animate-spin h-8 w-8 text-red-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs font-semibold text-gray-300">Menyiapkan Kamera...</span>
                        </div>

                        {{-- Error message --}}
                        <div x-show="scannerError" class="absolute inset-0 bg-gray-900/95 flex flex-col items-center justify-center p-6 text-center text-white z-20">
                            <svg class="w-10 h-10 text-red-500 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <p class="text-xs font-medium text-red-200 mb-3" x-text="scannerError"></p>
                            <button type="button" @click="initScanner()" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-bold transition-colors">
                                Coba Lagi
                            </button>
                        </div>
                    </div>

                    {{-- Hint / Instructions --}}
                    <div class="mt-4 flex items-center justify-between text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="font-medium text-gray-600">Mendeteksi Barcode 1D / QR Code...</span>
                        </span>
                        <button type="button" @click="stopScanner()" class="text-red-600 hover:text-red-700 font-bold">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function penangananTerlambat() {
                return {
                    searchQuery: '{{ request('search') }}',
                    isLoading: false,
                    results: [],
                    selectedSiswa: null,
                    scanNotice: '',

                    // Scanner State
                    scannerOpen: false,
                    scannerLoading: false,
                    scannerError: null,
                    html5QrCode: null,
                    audioCtx: null,

                    init() {
                        if (this.searchQuery) {
                            this.searchSiswa();
                        }
                    },

                    playBeep() {
                        try {
                            if (!this.audioCtx) {
                                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                            }
                            if (this.audioCtx.state === 'suspended') {
                                this.audioCtx.resume();
                            }
                            const osc = this.audioCtx.createOscillator();
                            const gain = this.audioCtx.createGain();
                            osc.connect(gain);
                            gain.connect(this.audioCtx.destination);
                            osc.frequency.setValueAtTime(880, this.audioCtx.currentTime); // A5 note
                            gain.gain.setValueAtTime(0.15, this.audioCtx.currentTime);
                            osc.start();
                            osc.stop(this.audioCtx.currentTime + 0.12);
                        } catch (e) {
                            console.warn('Audio feedback failed:', e);
                        }
                    },

                    async openScannerModal() {
                        this.scannerOpen = true;
                        this.scannerError = null;
                        this.scannerLoading = true;

                        // Pastikan audio context siap dari user gesture klik tombol
                        try {
                            if (!this.audioCtx) {
                                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                            }
                            if (this.audioCtx.state === 'suspended') {
                                this.audioCtx.resume();
                            }
                        } catch (e) {}

                        // Tunggu modal dirender di DOM
                        this.$nextTick(async () => {
                            await this.initScanner();
                        });
                    },

                    async initScanner() {
                        try {
                            if (typeof Html5Qrcode === 'undefined') {
                                this.scannerError = 'Library pemindai belum siap. Silakan muat ulang halaman.';
                                this.scannerLoading = false;
                                return;
                            }

                            if (this.html5QrCode) {
                                try {
                                    await this.html5QrCode.stop();
                                } catch (e) {}
                                this.html5QrCode = null;
                            }

                            this.html5QrCode = new Html5Qrcode("barcode-reader");

                            const config = {
                                fps: 15,
                                qrbox: (viewfinderWidth, viewfinderHeight) => {
                                    const width = Math.min(viewfinderWidth * 0.85, 300);
                                    const height = Math.min(viewfinderHeight * 0.65, 170);
                                    return { width: Math.floor(width), height: Math.floor(height) };
                                },
                                aspectRatio: 1.333333
                            };

                            await this.html5QrCode.start(
                                { facingMode: "environment" },
                                config,
                                (decodedText, decodedResult) => {
                                    this.onBarcodeScanned(decodedText);
                                },
                                (errorMessage) => {
                                    // Ignore scanning frame mismatches
                                }
                            );

                            this.scannerLoading = false;
                        } catch (err) {
                            console.error("Camera start error:", err);
                            this.scannerLoading = false;
                            this.scannerError = "Gagal mengakses kamera. Pastikan izin kamera telah diberikan di browser Anda.";
                        }
                    },

                    async stopScanner() {
                        if (this.html5QrCode) {
                            try {
                                await this.html5QrCode.stop();
                            } catch (e) {
                                console.warn("Scanner stop error:", e);
                            }
                            this.html5QrCode = null;
                        }
                        this.scannerOpen = false;
                        this.scannerLoading = false;
                        this.scannerError = null;
                    },

                    async onBarcodeScanned(decodedText) {
                        const rawCode = (decodedText || '').trim();
                        if (!rawCode) return;

                        // Audio feedback
                        this.playBeep();

                        // Tutup scanner
                        await this.stopScanner();

                        // Set query ke barcode yang di-scan
                        this.searchQuery = rawCode;
                        this.isLoading = true;

                        try {
                            const response = await fetch(`{{ route('api.siswa.search') }}?query=${encodeURIComponent(rawCode)}`);
                            const data = await response.json();
                            this.results = data;

                            if (Array.isArray(data) && data.length > 0) {
                                // Cari exact NIS atau pilih yang pertama
                                const exact = data.find(s => s.nis === rawCode) || data[0];
                                this.selectSiswa(exact);
                                this.scanNotice = `Siswa ${exact.nama_lengkap} (${exact.nis}) berhasil ditemukan melalui scan barcode!`;
                            } else {
                                this.scanNotice = `Barcode (${rawCode}) terbaca, namun data siswa tidak ditemukan.`;
                            }
                        } catch (error) {
                            console.error('Error fetching barcode student:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    async searchSiswa() {
                        if (this.searchQuery.length < 2) {
                            this.results = [];
                            return;
                        }

                        this.isLoading = true;

                        try {
                            const response = await fetch(`{{ route('api.siswa.search') }}?query=${encodeURIComponent(this.searchQuery)}`);
                            const data = await response.json();
                            this.results = data;
                        } catch (error) {
                            console.error('Error fetching data:', error);
                        } finally {
                            this.isLoading = false;
                        }
                    },

                    selectSiswa(siswa) {
                        this.selectedSiswa = siswa;

                        // Auto focus pada field alasan terlambat
                        this.$nextTick(() => {
                            const textarea = document.getElementById('input-alasan');
                            if (textarea) {
                                textarea.focus();
                            }
                        });

                        // Scroll ke form jika di layar mobile
                        if (window.innerWidth < 1024) {
                            setTimeout(() => {
                                window.scrollTo({
                                    top: document.body.scrollHeight,
                                    behavior: 'smooth'
                                });
                            }, 100);
                        }
                    },

                    resetSelection() {
                        this.selectedSiswa = null;
                        this.searchQuery = '';
                        this.results = [];
                        this.scanNotice = '';
                    }
                }
            }
        </script>
        @if (session('print_url'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    window.open("{{ session('print_url') }}", '_blank');
                });
            </script>
        @endif
    @endpush
</x-app-layout>
