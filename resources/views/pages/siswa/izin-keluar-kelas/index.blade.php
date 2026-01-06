<x-app-layout>
    @push('styles')
        <style>
            @keyframes gradient-xy {
                0% {
                    background-position: 0% 50%;
                }

                50% {
                    background-position: 100% 50%;
                }

                100% {
                    background-position: 0% 50%;
                }
            }

            .animate-gradient {
                background-size: 200% 200%;
                animation: gradient-xy 6s ease infinite;
            }
        </style>
    @endpush

    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Izin Keluar Kelas</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="izinKeluarData()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if ($jadwalSaatIni)
                <div
                    class="relative rounded-2xl bg-gradient-to-r from-indigo-600 via-purple-600 to-blue-500 shadow-lg overflow-hidden p-6 sm:p-8 animate-gradient">
                    <div class="absolute right-0 top-0 h-full w-1/3 bg-white/10 transform skew-x-12 blur-xl"></div>

                    <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-6">
                        <div class="text-white text-center md:text-left">
                            <div
                                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 backdrop-blur-sm text-xs font-bold uppercase tracking-wider mb-2">
                                <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                                Sedang Berlangsung
                            </div>
                            <h3 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                                {{ $jadwalSaatIni->mataPelajaran->nama_mapel }}</h3>
                            <p class="mt-1 text-indigo-100 text-lg">Guru: {{ $jadwalSaatIni->guru->nama_lengkap }}</p>
                        </div>

                        <button @click="openModal()"
                            class="group relative inline-flex items-center justify-center px-8 py-4 bg-white text-indigo-700 font-bold rounded-xl shadow-xl hover:shadow-2xl hover:bg-indigo-50 transition-all transform hover:-translate-y-1">
                            <svg class="w-6 h-6 mr-2 text-indigo-600 group-hover:scale-110 transition-transform"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            Minta Izin Keluar
                        </button>
                    </div>
                </div>
            @else
                <div class="rounded-2xl bg-gray-100 border-2 border-dashed border-gray-300 p-8 text-center">
                    <div class="inline-flex p-4 rounded-full bg-gray-200 mb-4 text-gray-400">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-700">Tidak Ada Jam Pelajaran Aktif</h3>
                    <p class="text-gray-500">Anda hanya bisa mengajukan izin keluar saat jam pelajaran berlangsung.</p>
                </div>
            @endif

            <div class="bg-white border border-gray-200 shadow-sm rounded-xl overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Riwayat Izin Keluar</h3>
                    <span class="text-xs text-gray-500">Menampilkan 10 data terakhir</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-bold tracking-wider">Tujuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Jenis</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Waktu Pengajuan</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Status</th>
                                <th class="px-6 py-4 font-bold tracking-wider">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($riwayatIzin as $izin)
                                <tr class="bg-white hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4 font-semibold text-gray-900">
                                        {{ $izin->tujuan }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-xs">
                                        @if(($izin->jenis_izin ?? 'keluar_sekolah') === 'keluar_sekolah')
                                            <span class="text-red-500 font-bold">Keluar</span>
                                        @else
                                            <span class="text-blue-500 font-bold">Internal</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap font-mono text-xs text-gray-600">
                                        {{ \Carbon\Carbon::parse($izin->created_at)->isoFormat('D MMM Y, HH:mm') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $statusClass = match ($izin->status) {
                                                'disetujui_guru_piket'
                                                    => 'bg-indigo-100 text-indigo-700 border-indigo-200',
                                                'diverifikasi_security'
                                                    => 'bg-orange-100 text-orange-700 border-orange-200', // Sedang di luar
                                                'selesai'
                                                    => 'bg-green-100 text-green-700 border-green-200', // Sudah kembali
                                                'ditolak' => 'bg-red-100 text-red-700 border-red-200',
                                                default => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                            };
                                            $statusLabel = str_replace('_', ' ', $izin->status);
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border uppercase tracking-wide {{ $statusClass }}">
                                            {{ $statusLabel }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">
                                        @if ($izin->status == 'ditolak')
                                            <span class="text-red-600 italic flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                                </svg>
                                                {{ $izin->alasan_penolakan }}
                                            </span>
                                        @else
                                            {{ $izin->keterangan ?? '-' }}
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none"
                                                stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p>Belum ada riwayat izin keluar.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                    {{ $riwayatIzin->links() }}
                </div>
            </div>
        </div>

        @include('pages.siswa.izin-keluar-kelas.partials.form-modal')

    </div>

    @push('scripts')
        <script>
            function izinKeluarData() {
                return {
                    isOpen: false,
                    openModal() {
                        this.isOpen = true;
                    },
                    closeModal() {
                        this.isOpen = false;
                    }
                }
            }
        </script>
    @endpush
</x-app-layout>
