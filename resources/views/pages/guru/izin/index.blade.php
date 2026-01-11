<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Riwayat Pengajuan Izin Guru</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-bold text-gray-800">Daftar Izin Anda</h3>
                <a href="{{ route('guru.izin.create') }}" 
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-xl font-bold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 shadow-sm transition-all">
                   Buat Pengajuan Baru
                </a>
            </div>

            <!-- Panduan Pengajuan Izin -->
            <div class="bg-blue-50 border border-blue-200 rounded-2xl p-6">
                <div class="flex items-start gap-4">
                    <div class="p-2 bg-blue-100 rounded-xl text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h4 class="text-blue-900 font-bold mb-2 text-sm uppercase tracking-wider">Panduan Pengajuan Izin Guru</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-white/50 p-3 rounded-xl border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 mb-1">1. Izin Lingkungan Sekolah</p>
                                <p class="text-[11px] text-blue-600 leading-relaxed font-medium">Gunakan kategori ini jika Anda izin meninggalkan kelas untuk kegiatan operasional sekolah (Rapat, dsb) namun tetap berada di lingkungan sekolah. Cukup memerlukan persetujuan dari <strong>Guru Piket</strong>.</p>
                            </div>
                            <div class="bg-white/50 p-3 rounded-xl border border-blue-100">
                                <p class="text-xs font-bold text-blue-800 mb-1">2. Izin Luar Sekolah / Absen</p>
                                <p class="text-[11px] text-blue-600 leading-relaxed font-medium">Gunakan kategori ini jika Anda izin keluar lingkungan sekolah atau tidak masuk di hari berikutnya. Memerlukan persetujuan berjenjang: <strong>Guru Piket &rarr; Waka Kurikulum &rarr; KAUR SDM</strong>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4">Jenis</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Status Approval</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($izins as $izin)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="font-bold text-gray-900 text-xs">
                                            @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                                {{ $izin->tanggal_mulai->translatedFormat('d F Y') }}
                                                <div class="text-[10px] text-indigo-600">{{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }}</div>
                                            @else
                                                <div class="flex flex-col">
                                                    <span>{{ $izin->tanggal_mulai->translatedFormat('d M Y, H:i') }}</span>
                                                    <span class="text-gray-400">s/d</span>
                                                    <span>{{ $izin->tanggal_selesai->translatedFormat('d M Y, H:i') }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-100 text-blue-800 border border-blue-200">
                                            {{ $izin->jenis_izin }}
                                        </span>
                                        @if($izin->kategori_penyetujuan === 'sekolah')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 text-purple-800 border border-purple-200 uppercase">
                                                Sekolah
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 max-w-xs truncate text-gray-600">
                                        {{ $izin->deskripsi }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            {{-- Status Piket --}}
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Piket</span>
                                                <x-status-badge-izin :status="$izin->status_piket" />
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            {{-- Status Kurikulum --}}
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">Kurikulum</span>
                                                <x-status-badge-izin :status="$izin->status_kurikulum" />
                                            </div>
                                            <svg class="w-4 h-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            {{-- Status SDM --}}
                                            <div class="flex flex-col items-center">
                                                <span class="text-[10px] font-bold text-gray-400 uppercase">SDM</span>
                                                <x-status-badge-izin :status="$izin->status_sdm" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if ($izin->status_sdm === 'disetujui')
                                            <a href="{{ route('sdm.persetujuan-izin-guru.print', $izin->id) }}" target="_blank"
                                               class="text-indigo-600 hover:text-indigo-900 font-bold inline-flex items-center gap-1">
                                               <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"/></svg>
                                               Unduh PDF
                                            </a>
                                        @else
                                            <span class="text-gray-400 text-xs italic">Belum tersedia</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                        Belum ada riwayat pengajuan izin.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($izins->hasPages())
                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                        {{ $izins->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
