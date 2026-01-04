<x-app-layout>
    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
            {{-- Header Section --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6 px-4 sm:px-0">
                <div class="space-y-1">
                    <h2 class="text-3xl font-black bg-clip-text text-transparent bg-gradient-to-r from-gray-900 to-gray-600">
                        Riwayat Keterlambatan
                    </h2>
                    <p class="text-sm text-gray-500 font-medium">
                        Monitor daftar keterlambatan Anda dan unduh surat izin masuk kelas secara digital.
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <div class="px-5 py-3 bg-white rounded-2xl border border-gray-100 shadow-sm flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600 ring-1 ring-orange-100">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">Total Terlambat</p>
                            <p class="text-xl font-black text-gray-900 mt-1">{{ $keterlambatans->total() }} <span class="text-xs font-bold text-gray-400 ml-1">KALI</span></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-[2rem] border border-gray-100">
                <div class="p-8 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 shadow-inner">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-gray-900 leading-tight">Daftar Keterlambatan</h3>
                            <p class="text-xs text-gray-500 font-medium">Data keterlambatan yang tercatat oleh sistem keamanan</p>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto min-w-full">
                    <table class="w-full text-sm text-left align-middle">
                        <thead class="bg-gray-50/80 text-gray-400 uppercase text-[11px] font-black tracking-widest border-b border-gray-100">
                            <tr>
                                <th class="px-8 py-5">Waktu Tercatat</th>
                                <th class="px-8 py-5">Alasan Siswa</th>
                                <th class="px-8 py-5 text-center">Status</th>
                                <th class="px-8 py-5 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50">
                            @forelse($keterlambatans as $k)
                                <tr class="group hover:bg-gray-50/50 transition-all duration-300">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl bg-gray-50 flex flex-col items-center justify-center border border-gray-100 leading-none">
                                                <span class="text-[10px] font-black text-gray-400 uppercase tracking-tighter">{{ $k->waktu_dicatat_security->translatedFormat('M') }}</span>
                                                <span class="text-base font-black text-gray-900">{{ $k->waktu_dicatat_security->format('d') }}</span>
                                            </div>
                                            <div>
                                                <div class="font-black text-gray-900">{{ $k->waktu_dicatat_security->translatedFormat('l, d F Y') }}</div>
                                                <div class="flex items-center gap-1.5 mt-0.5">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                                    <span class="text-[10px] text-gray-500 font-bold font-mono uppercase tracking-tight">Pukul {{ $k->waktu_dicatat_security->format('H:i') }} WIB</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="text-gray-600 font-medium max-w-xs leading-relaxed italic">
                                            "{{ $k->alasan_siswa ?? 'Tidak memberikan alasan' }}"
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-center">
                                        @php
                                            $statusConfig = [
                                                'menunggu_piket' => [
                                                    'label' => 'Menunggu Piket',
                                                    'class' => 'bg-amber-50 text-amber-600 ring-1 ring-amber-200'
                                                ],
                                                'menunggu_guru_kelas' => [
                                                    'label' => 'Diproses Guru',
                                                    'class' => 'bg-blue-50 text-blue-600 ring-1 ring-blue-200'
                                                ],
                                                'disetujui' => [
                                                    'label' => 'Disetujui',
                                                    'class' => 'bg-green-50 text-green-600 ring-1 ring-green-200'
                                                ],
                                                'ditolak' => [
                                                    'label' => 'Ditolak',
                                                    'class' => 'bg-red-50 text-red-600 ring-1 ring-red-200'
                                                ],
                                            ];
                                            $config = $statusConfig[$k->status] ?? [
                                                'label' => ucfirst(str_replace('_', ' ', $k->status)),
                                                'class' => 'bg-gray-50 text-gray-600 ring-1 ring-gray-200'
                                            ];
                                        @endphp
                                        <span class="px-3 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest {{ $config['class'] }}">
                                            {{ $config['label'] }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <a href="{{ route('siswa.riwayat-keterlambatan.print', $k->id) }}" 
                                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white rounded-2xl font-black text-xs hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-100 hover:shadow-indigo-200 active:scale-95 group-hover:translate-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                            </svg>
                                            CETAK SLIP
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center justify-center max-w-sm mx-auto">
                                            <div class="w-24 h-24 bg-gray-50 rounded-[2rem] flex items-center justify-center text-gray-300 mb-6">
                                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                            <h4 class="text-lg font-black text-gray-900 leading-tight">Tidak Ada Keterlambatan</h4>
                                            <p class="text-sm text-gray-500 font-medium mt-2 leading-relaxed">
                                                Luar biasa! Kamu selalu tepat waktu. Pertahankan kedisiplinan ini untuk masa depan yang gemilang.
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($keterlambatans->hasPages())
                    <div class="p-8 border-t border-gray-100 bg-gray-50/30">
                        {{ $keterlambatans->links() }}
                    </div>
                @endif
            </div>

            {{-- Info Section --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-indigo-600 rounded-[2rem] p-8 text-white relative overflow-hidden group shadow-xl shadow-indigo-100">
                    <div class="absolute -right-4 -bottom-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                        <svg class="w-48 h-48" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg>
                    </div>
                    <div class="relative z-10">
                        <h4 class="text-xl font-black leading-tight mb-3">Informasi Penting</h4>
                        <p class="text-indigo-100 text-sm font-medium leading-relaxed">
                            Setiap keterlambatan yang tercatat akan menambah <span class="text-white font-bold">+1 poin pelanggaran</span>. Pastikan Anda tiba di sekolah sebelum bel tanda masuk berbunyi.
                        </p>
                    </div>
                </div>

                <div class="bg-white rounded-[2rem] p-8 border border-gray-100 shadow-sm flex items-center gap-6">
                    <div class="w-16 h-16 rounded-3xl bg-gray-50 flex items-center justify-center text-indigo-600 ring-1 ring-gray-100 shrink-0 shadow-inner">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-black text-gray-900 leading-tight">Cetak Surat Izin</h4>
                        <p class="text-sm text-gray-500 font-medium mt-1 leading-relaxed">
                            Unduh dan cetak surat izin masuk kelas (Digital Slip) untuk ditunjukkan kepada Guru Mata Pelajaran saat Anda masuk kelas.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
