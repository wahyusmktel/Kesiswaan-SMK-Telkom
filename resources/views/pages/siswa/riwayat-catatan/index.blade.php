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
            </div>
        </div>
    </div>
</x-app-layout>
