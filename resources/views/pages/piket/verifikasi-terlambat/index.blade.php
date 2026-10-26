<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Verifikasi Keterlambatan</h2>
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
                        <p class="text-xs text-amber-700 leading-relaxed">
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

            {{-- Informational Banner --}}
            <div class="bg-blue-50/70 border border-blue-200/80 rounded-xl p-4 mb-6 flex items-start gap-3 shadow-sm">
                <div class="w-8 h-8 rounded-lg bg-blue-500 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold text-blue-900 leading-tight">Alur Verifikasi Keterlambatan Siswa</h4>
                    <p class="text-xs text-blue-700 mt-1 leading-relaxed">
                        Daftar di bawah ini berisi siswa yang dicatat keterlambatannya oleh <strong>Security</strong> di gerbang sekolah. Jika terdapat siswa terlambat yang belum sempat tercatat oleh Security, Guru Piket dapat langsung melakukan pencatatan melalui tombol <strong>Catat Terlambat Manual</strong>.
                    </p>
                </div>
            </div>

            {{-- Table Container Card --}}
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
                {{-- Header / Action Toolbar in Content Area --}}
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h3 class="font-bold text-gray-800 text-base leading-none">Menunggu Verifikasi Piket</h3>
                            <span class="px-2.5 py-0.5 bg-rose-100 text-rose-700 border border-rose-200 rounded-full text-xs font-black">
                                {{ $daftarSiswaTerlambat->count() }} Siswa
                            </span>
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Daftar siswa terlambat yang siap diverifikasi dan diberikan tindak lanjut oleh Guru Piket</p>
                    </div>

                    {{-- Action Buttons: Catat Manual (Navigasi) & Verifikasi Semua --}}
                    <div class="flex flex-wrap items-center gap-2.5">
                        {{-- Tombol Navigasi Catat Manual --}}
                        <a href="{{ route('piket.penanganan-terlambat.index') }}"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 bg-white hover:bg-gray-50 active:bg-gray-100 text-gray-700 hover:text-red-600 border border-gray-300 rounded-md font-bold text-xs shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-red-500">
                            <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            <span>Catat Terlambat Manual</span>
                        </a>

                        {{-- Tombol Verifikasi Semua --}}
                        @if ($daftarSiswaTerlambat->isNotEmpty())
                            <button
                                x-data
                                @click="$dispatch('open-verify-all-modal')"
                                class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 active:bg-rose-800 text-white rounded-md font-bold text-xs shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-rose-500">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                <span>Verifikasi Semua</span>
                                <span class="bg-white/25 text-white rounded-full px-1.5 py-0.5 text-[10px] font-black">
                                    {{ $daftarSiswaTerlambat->count() }}
                                </span>
                            </button>
                        @endif
                    </div>
                </div>

                {{-- Table Daftar Keterlambatan --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-gray-50/75 text-gray-700 uppercase text-[11px] font-bold tracking-wider border-b border-gray-100">
                            <tr>
                                <th class="px-4 py-3.5 text-center w-12">No</th>
                                <th class="px-6 py-3.5">Siswa</th>
                                <th class="px-6 py-3.5">Kelas</th>
                                <th class="px-6 py-3.5">Waktu Datang</th>
                                <th class="px-6 py-3.5">Alasan (Dicatat Security)</th>
                                <th class="px-6 py-3.5">Dicatat Oleh</th>
                                <th class="px-6 py-3.5 text-center w-40">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($daftarSiswaTerlambat as $item)
                                <tr class="hover:bg-gray-50/60 transition-colors">
                                    <td class="px-4 py-4 whitespace-nowrap text-center font-mono text-xs font-semibold text-gray-500">
                                        {{ $loop->iteration }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-red-100 text-red-700 font-bold text-xs flex items-center justify-center flex-shrink-0">
                                                {{ substr($item->siswa->nama_lengkap, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-gray-900 leading-tight">
                                                    {{ $item->siswa->nama_lengkap }}
                                                </div>
                                                <div class="text-xs text-gray-400 font-mono mt-0.5">
                                                    NIS: {{ $item->siswa->nis }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-semibold bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $item->siswa->rombels->first()?->kelas->nama_kelas ?? 'Tanpa Kelas' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-mono font-bold text-xs text-gray-800">
                                            {{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->format('H:i') }} WIB
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            {{ \Carbon\Carbon::parse($item->waktu_dicatat_security)->diffForHumans() }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs text-gray-600 italic line-clamp-2 max-w-xs" title="{{ $item->alasan_siswa }}">
                                            "{{ $item->alasan_siswa ?? '-' }}"
                                        </p>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600">
                                        <div class="flex items-center gap-1.5 font-medium">
                                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                            </svg>
                                            <span>{{ $item->security->name ?? 'Security' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center">
                                        <a href="{{ route('piket.verifikasi-terlambat.show', $item->id) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-md font-bold text-xs shadow-sm transition-all">
                                            <span>Proses Verifikasi</span>
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500 bg-gray-50/40">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                            <h4 class="font-bold text-base text-gray-800">Semua Beres!</h4>
                                            <p class="text-xs text-gray-500 mt-1 mb-4">Tidak ada antrean keterlambatan siswa dari Security yang perlu diverifikasi saat ini.</p>
                                            <a href="{{ route('piket.penanganan-terlambat.index') }}"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white hover:bg-gray-50 text-gray-700 hover:text-red-600 border border-gray-300 rounded-md font-bold text-xs shadow-sm transition-colors">
                                                <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                <span>Catat Keterlambatan Manual</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
