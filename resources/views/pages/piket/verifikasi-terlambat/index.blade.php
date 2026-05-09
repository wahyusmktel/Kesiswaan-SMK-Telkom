<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-gray-800 leading-tight">Verifikasi Keterlambatan</h2>
            @if ($daftarSiswaTerlambat->isNotEmpty())
                <button
                    x-data
                    @click="$dispatch('open-verify-all-modal')"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-500 active:bg-rose-700 text-white text-sm font-bold rounded-xl shadow-md transition-all duration-200 hover:-translate-y-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Verifikasi Semua
                    <span class="bg-white/25 text-white rounded-full px-2 py-0.5 text-xs font-black">
                        {{ $daftarSiswaTerlambat->count() }}
                    </span>
                </button>
            @endif
        </div>
    </x-slot>

    {{-- Modal Konfirmasi Verifikasi Semua --}}
    @if ($daftarSiswaTerlambat->isNotEmpty())
        <div
            x-data="{
                open: false,
                agreed: false,
                get canSubmit() { return this.agreed; }
            }"
            x-on:open-verify-all-modal.window="open = true; agreed = false"
            x-show="open"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4"
            style="display: none;">

            {{-- Backdrop --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                @click="open = false"
                class="absolute inset-0 bg-gray-900/60 backdrop-blur-sm">
            </div>

            {{-- Modal Panel --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95 translate-y-4"
                x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                x-transition:leave-end="opacity-0 scale-95 translate-y-4"
                class="relative w-full max-w-md bg-white rounded-2xl shadow-2xl overflow-hidden">

                {{-- Danger Header Bar --}}
                <div class="h-1.5 bg-gradient-to-r from-rose-500 via-red-500 to-orange-400"></div>

                <div class="p-6">
                    {{-- Icon + Title --}}
                    <div class="flex items-start gap-4 mb-5">
                        <div class="flex-shrink-0 w-12 h-12 rounded-xl bg-rose-100 flex items-center justify-center">
                            <svg class="w-6 h-6 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Verifikasi Semua Sekaligus?</h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Tindakan ini akan memverifikasi
                                <span class="font-bold text-rose-600">{{ $daftarSiswaTerlambat->count() }} siswa</span>
                                terlambat secara massal dan mencatat poin pelanggaran untuk masing-masing siswa.
                            </p>
                        </div>
                    </div>

                    {{-- Info Box --}}
                    <div class="bg-amber-50 border border-amber-200 rounded-xl p-3 mb-5 flex gap-3">
                        <svg class="w-5 h-5 text-amber-500 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-xs text-amber-700">
                            Setiap siswa akan mendapat catatan <strong>"Diverifikasi massal oleh Guru Piket"</strong>
                            dan poin pelanggaran keterlambatan akan ditambahkan otomatis. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
                        </p>
                    </div>

                    {{-- Student list preview (max 5) --}}
                    <div class="bg-gray-50 rounded-xl border border-gray-200 divide-y divide-gray-100 mb-5 max-h-44 overflow-y-auto">
                        @foreach ($daftarSiswaTerlambat->take(5) as $item)
                            <div class="flex items-center gap-3 px-4 py-2.5">
                                <div class="w-7 h-7 rounded-full bg-rose-100 flex items-center justify-center text-rose-600 font-bold text-xs flex-shrink-0">
                                    {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->siswa->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-400">
                                        {{ $item->siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                        &middot;
                                        {{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->format('H:i') }} WIB
                                    </p>
                                </div>
                            </div>
                        @endforeach
                        @if ($daftarSiswaTerlambat->count() > 5)
                            <div class="px-4 py-2 text-center">
                                <span class="text-xs text-gray-400 italic">
                                    + {{ $daftarSiswaTerlambat->count() - 5 }} siswa lainnya...
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Confirmation Checkbox --}}
                    <label class="flex items-start gap-3 cursor-pointer group mb-6 select-none">
                        <div class="relative flex-shrink-0 mt-0.5">
                            <input
                                type="checkbox"
                                x-model="agreed"
                                class="peer sr-only">
                            <div class="w-5 h-5 rounded-md border-2 border-gray-300 bg-white peer-checked:bg-rose-600 peer-checked:border-rose-600 transition-colors duration-150 flex items-center justify-center">
                                <svg class="w-3 h-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>
                        <span class="text-sm text-gray-600 group-hover:text-gray-800 transition-colors leading-relaxed">
                            Saya memahami konsekuensinya dan menyetujui verifikasi
                            <strong class="text-gray-900">{{ $daftarSiswaTerlambat->count() }} siswa terlambat</strong>
                            secara massal.
                        </span>
                    </label>

                    {{-- Actions --}}
                    <form method="POST" action="{{ route('piket.verifikasi-terlambat.verify-all') }}">
                        @csrf
                        <div class="flex gap-3">
                            <button
                                type="button"
                                @click="open = false"
                                class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 active:bg-gray-100 transition-colors text-sm">
                                Batal
                            </button>
                            <button
                                type="submit"
                                :disabled="!canSubmit"
                                :class="canSubmit
                                    ? 'bg-rose-600 hover:bg-rose-500 active:bg-rose-700 cursor-pointer shadow-md hover:-translate-y-0.5'
                                    : 'bg-gray-200 text-gray-400 cursor-not-allowed'"
                                class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2.5 text-white font-bold rounded-xl transition-all duration-200 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Ya, Verifikasi Semua
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
