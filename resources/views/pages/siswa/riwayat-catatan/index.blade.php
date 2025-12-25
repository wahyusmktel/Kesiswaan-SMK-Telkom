<x-app-layout>
    <div class="py-6" x-data="{ activeTab: 'pelanggaran' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-gray-800 to-gray-600">
                        Riwayat Catatan Siswa
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Pantau riwayat kedisiplinan dan kehadiran Anda di sini.
                    </p>
                </div>

                {{-- Tab Navigation --}}
                <div class="bg-white p-1 rounded-xl shadow-sm border border-gray-100 flex items-center gap-1">
                    <button @click="activeTab = 'pelanggaran'"
                        :class="activeTab === 'pelanggaran' ? 'bg-red-50 text-red-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                        Pelanggaran
                    </button>
                    <button @click="activeTab = 'keterlambatan'"
                        :class="activeTab === 'keterlambatan' ? 'bg-blue-50 text-blue-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                        Keterlambatan
                    </button>
                    <button @click="activeTab = 'prestasi'"
                        :class="activeTab === 'prestasi' ? 'bg-green-50 text-green-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                        Prestasi
                    </button>
                    <button @click="activeTab = 'pemutihan'"
                        :class="activeTab === 'pemutihan' ? 'bg-amber-50 text-amber-600 shadow-sm' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200">
                        Pemutihan
                    </button>
                </div>
            </div>

            <div class="space-y-6">
                {{-- Content: Pelanggaran --}}
                <div x-show="activeTab === 'pelanggaran'"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Daftar Pelanggaran</h3>
                                <p class="text-xs text-gray-500">Catatan pelanggaran tata tertib sekolah</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50/50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Tanggal</th>
                                        <th class="px-6 py-4">Pelanggaran</th>
                                        <th class="px-6 py-4 text-center">Poin</th>
                                        <th class="px-6 py-4">Pelapor</th>
                                        <th class="px-6 py-4">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($pelanggarans as $pelanggaran)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                {{ \Carbon\Carbon::parse($pelanggaran->tanggal)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="px-6 py-4">
                                                <div class="font-medium text-gray-800">{{ $pelanggaran->peraturan->deskripsi ?? '-' }}</div>
                                                <div class="text-xs text-gray-400 mt-0.5">Pasal {{ $pelanggaran->peraturan->pasal ?? '-' }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                    -{{ $pelanggaran->peraturan->bobot_poin ?? 0 }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $pelanggaran->pelapor->name ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 italic">
                                                {{ $pelanggaran->catatan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p class="font-medium">Tidak ada riwayat pelanggaran.</p>
                                                    <p class="text-xs mt-1">Pertahankan sikap disiplin Anda!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($pelanggarans->hasPages())
                            <div class="p-4 border-t border-gray-100">
                                {{ $pelanggarans->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Content: Keterlambatan --}}
                <div x-show="activeTab === 'keterlambatan'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Daftar Keterlambatan</h3>
                                <p class="text-xs text-gray-500">Catatan keterlambatan kehadiran di sekolah</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50/50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Tanggal & Waktu</th>
                                        <th class="px-6 py-4">Alasan</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4">Tindak Lanjut</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($keterlambatans as $keterlambatan)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="font-medium text-gray-800">
                                                    {{ $keterlambatan->created_at->translatedFormat('d F Y') }}
                                                </div>
                                                <div class="text-xs text-gray-500 mt-0.5">
                                                    Pukul {{ $keterlambatan->created_at->format('H:i') }} WIB
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 text-gray-600">
                                                {{ $keterlambatan->alasan_siswa ?? '-' }}
                                            </td>
                                            <td class="px-6 py-4">
                                                @php
                                                    $statusClasses = [
                                                        'dicatat_security' => 'bg-yellow-100 text-yellow-800',
                                                        'diverifikasi_piket' => 'bg-green-100 text-green-800',
                                                        'ditolak' => 'bg-red-100 text-red-800', // Asumsi ada status ini
                                                    ];
                                                    $statusLabel = [
                                                        'dicatat_security' => 'Proses',
                                                        'diverifikasi_piket' => 'Selesai',
                                                        'ditolak' => 'Ditolak',
                                                    ];
                                                @endphp
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClasses[$keterlambatan->status] ?? 'bg-gray-100 text-gray-800' }}">
                                                    {{ $statusLabel[$keterlambatan->status] ?? ucfirst(str_replace('_', ' ', $keterlambatan->status)) }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500 italic">
                                                {{ $keterlambatan->tindak_lanjut_piket ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <p class="font-medium">Tidak ada riwayat keterlambatan.</p>
                                                    <p class="text-xs mt-1">Terima kasih sudah selalu tepat waktu!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($keterlambatans->hasPages())
                            <div class="p-4 border-t border-gray-100">
                                {{ $keterlambatans->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Content: Prestasi --}}
                <div x-show="activeTab === 'prestasi'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Daftar Prestasi</h3>
                                <p class="text-xs text-gray-500">Catatan prestasi dan penghargaan siswa</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50/50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Tanggal</th>
                                        <th class="px-6 py-4">Nama Prestasi</th>
                                        <th class="px-6 py-4 text-center">Poin Bonus</th>
                                        <th class="px-6 py-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($prestasis as $prestasi)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                {{ \Carbon\Carbon::parse($prestasi->tanggal)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="px-6 py-4 font-medium text-gray-800">
                                                {{ $prestasi->nama_prestasi }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                    +{{ $prestasi->poin_bonus }}
                                                </span>
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $prestasi->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-7.714 2.143L11 21l-2.286-6.857L1 12l7.714-2.143L11 3z"/>
                                                    </svg>
                                                    <p class="font-medium">Belum ada riwayat prestasi.</p>
                                                    <p class="text-xs mt-1">Teruslah berkarya dan raih prestasi!</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($prestasis->hasPages())
                            <div class="p-4 border-t border-gray-100">
                                {{ $prestasis->links() }}
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Content: Pemutihan --}}
                <div x-show="activeTab === 'pemutihan'"
                     x-cloak
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    
                    <div class="p-6 border-b border-gray-100 bg-gray-50/50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800">Daftar Pemutihan Poin</h3>
                                <p class="text-xs text-gray-500">Catatan pengurangan poin pelanggaran (Pemutihan)</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm">
                                <thead class="bg-gray-50/50 text-gray-500 uppercase tracking-wider text-xs font-semibold">
                                    <tr>
                                        <th class="px-6 py-4">Tanggal</th>
                                        <th class="px-6 py-4">Poin Dikurangi</th>
                                        <th class="px-6 py-4">Status</th>
                                        <th class="px-6 py-4 text-right">Dokumen</th>
                                        <th class="px-6 py-4">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse ($pemutihans as $pemutihan)
                                        <tr class="hover:bg-gray-50/50 transition-colors">
                                            <td class="px-6 py-4 whitespace-nowrap text-gray-600">
                                                {{ \Carbon\Carbon::parse($pemutihan->tanggal)->translatedFormat('d F Y') }}
                                            </td>
                                            <td class="px-6 py-4 text-center">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800 font-bold">
                                                    {{ $pemutihan->poin_dikurangi }} Poin
                                                </span>
                                            </td>
                                            <td class="px-6 py-4">
                                                @if($pemutihan->status == 'diajukan')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-600 ring-1 ring-amber-100 uppercase tracking-tighter">Diajukan</span>
                                                @elseif($pemutihan->status == 'disetujui')
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-600 ring-1 ring-green-100 uppercase tracking-tighter">Disetujui</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-600 ring-1 ring-red-100 uppercase tracking-tighter">Ditolak</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-right">
                                                @if($pemutihan->status != 'diajukan')
                                                    <a href="{{ route('kesiswaan.input-pemutihan.print', $pemutihan->id) }}" target="_blank" class="inline-flex items-center gap-1 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-lg transition-all text-[10px] font-bold uppercase tracking-tight shadow-sm">
                                                        <svg class="w-3.5 h-3.5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17h6M9 13h6"/></svg>
                                                        Berita Acara
                                                    </a>
                                                @else
                                                    <span class="text-[10px] text-gray-400 italic">Menunggu...</span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 text-gray-500">
                                                {{ $pemutihan->keterangan ?? '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-12 text-center">
                                                <div class="flex flex-col items-center justify-center text-gray-400">
                                                    <svg class="w-12 h-12 mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                    <p class="font-medium">Tidak ada riwayat pemutihan poin.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if ($pemutihans->hasPages())
                            <div class="p-4 border-t border-gray-100">
                                {{ $pemutihans->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
