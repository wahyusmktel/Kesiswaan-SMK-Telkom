<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Izin Guru (Piket)</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="approvalPiket()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Daftar Pengajuan Izin</h3>
                    <div class="flex gap-2">
                        <a href="?status=menunggu" class="text-xs font-bold px-3 py-1 rounded-full {{ request('status', 'menunggu') == 'menunggu' ? 'bg-yellow-100 text-yellow-700' : 'bg-white text-gray-500 border' }}">Menunggu</a>
                        <a href="?status=disetujui" class="text-xs font-bold px-3 py-1 rounded-full {{ request('status') == 'disetujui' ? 'bg-green-100 text-green-700' : 'bg-white text-gray-500 border' }}">Disetujui</a>
                        <a href="?status=ditolak" class="text-xs font-bold px-3 py-1 rounded-full {{ request('status') == 'ditolak' ? 'bg-red-100 text-red-700' : 'bg-white text-gray-500 border' }}">Ditolak</a>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Guru</th>
                                <th class="px-6 py-4">Waktu & Jenis</th>
                                <th class="px-6 py-4">Penugasan & Materi</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($izins as $izin)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold">
                                                {{ substr($izin->guru->nama_lengkap, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-900">{{ $izin->guru->nama_lengkap }}</p>
                                                <p class="text-xs text-gray-500">NIP: {{ $izin->guru->nip ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-700 text-xs">
                                            @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                                {{ $izin->tanggal_mulai->translatedFormat('d M Y') }}
                                                <span class="block text-[10px] text-indigo-600 font-normal">{{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }}</span>
                                            @else
                                                <span class="block text-[10px] text-gray-600 font-normal">{{ $izin->tanggal_mulai->translatedFormat('d M Y, H:i') }}</span>
                                                <span class="block text-[10px] text-indigo-600 font-normal">{{ $izin->tanggal_selesai->translatedFormat('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                        <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black uppercase">{{ $izin->jenis_izin }}</span>
                                        @if($izin->kategori_penyetujuan === 'sekolah')
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-black uppercase">Lingkungan Sekolah</span>
                                        @elseif($izin->kategori_penyetujuan === 'luar')
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-100 text-[10px] font-black uppercase">Luar Sekolah</span>
                                        @else
                                            <span class="inline-block mt-1 px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-100 text-[10px] font-black uppercase">Terlambat</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->jadwals->isNotEmpty())
                                            <div class="space-y-3">
                                                @foreach($izin->jadwals as $jadwal)
                                                    <div class="bg-white border border-gray-100 rounded-xl p-3 shadow-sm">
                                                        <div class="flex justify-between items-center mb-2">
                                                            <span class="text-xs font-black text-gray-900">{{ $jadwal->rombel->kelas->nama_kelas }}</span>
                                                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Jam {{ $jadwal->jam_ke }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-500 mb-2">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                                        
                                                        <div class="space-y-1.5">
                                                            @if($jadwal->pivot->loadedMaterial)
                                                                <div class="flex items-center gap-2 group cursor-pointer">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-blue-500"></div>
                                                                    <span class="text-[10px] font-bold text-blue-600 group-hover:underline">{{ $jadwal->pivot->loadedMaterial->title }} (Materi)</span>
                                                                </div>
                                                            @endif
                                                            @if($jadwal->pivot->loadedAssignment)
                                                                <div class="flex items-center gap-2 group cursor-pointer">
                                                                    <div class="w-1.5 h-1.5 rounded-full bg-green-500"></div>
                                                                    <span class="text-[10px] font-bold text-green-600 group-hover:underline">{{ $jadwal->pivot->loadedAssignment->title }} (Tugas)</span>
                                                                </div>
                                                            @endif
                                                            @if(!$jadwal->pivot->loadedMaterial && !$jadwal->pivot->loadedAssignment)
                                                                <span class="text-[10px] italic text-red-500">Materi/Tugas belum dilampirkan</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 italic">"{{ $izin->deskripsi }}"</p>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if($izin->status_piket == 'menunggu')
                                            <div class="flex gap-2">
                                                <form action="{{ route('piket.persetujuan-izin-guru.approve', $izin->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-green-500 transition-all">Setuju</button>
                                                </form>
                                                <button @click="openRejectModal({{ json_encode($izin) }})" class="px-3 py-1.5 bg-white border border-red-200 text-red-600 rounded-lg font-bold text-xs uppercase tracking-wider hover:bg-red-50 transition-all">Tolak</button>
                                            </div>
                                        @else
                                            <x-status-badge-izin :status="$izin->status_piket" />
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                        Tidak ada data izin ditemukan.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($izins->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100">
                        {{ $izins->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- Modal Reject (Conceptual simplified) --}}
        <div x-show="isOpen" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="isOpen = false"></div>
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl relative overflow-hidden">
                    <form :action="rejectUrl" method="POST">
                        @csrf
                        @method('PATCH')
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4">Tolak Izin Guru</h3>
                            <div class="space-y-4">
                                <div class="bg-gray-50 p-3 rounded-lg border">
                                    <p class="text-xs text-gray-400 font-bold uppercase">Guru</p>
                                    <p class="font-bold text-gray-800" x-text="selectedItem.guru?.nama_lengkap"></p>
                                </div>
                                <textarea name="catatan_piket" required class="w-full rounded-xl border-gray-200" rows="3" placeholder="Alasan penolakan..."></textarea>
                            </div>
                            <div class="mt-6 flex gap-3">
                                <button type="button" @click="isOpen = false" class="flex-1 py-2 font-bold text-gray-500 border rounded-xl">Batal</button>
                                <button type="submit" class="flex-1 py-2 font-bold bg-red-600 text-white rounded-xl">Tolak Izin</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function approvalPiket() {
            return {
                isOpen: false,
                selectedItem: {},
                rejectUrl: '',
                openRejectModal(item) {
                    this.isOpen = true;
                    this.selectedItem = item;
                    this.rejectUrl = `{{ url('piket/persetujuan-izin-guru') }}/${item.id}/reject`;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
