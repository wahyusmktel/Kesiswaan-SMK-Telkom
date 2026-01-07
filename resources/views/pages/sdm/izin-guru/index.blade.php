<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Persetujuan Akhir Izin Guru (KAUR SDM)</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="approvalSDM()">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-gray-800">Review Akhir & Validasi Absensi</h3>
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
                                <th class="px-6 py-4">Status Approver</th>
                                <th class="px-6 py-4">Deskripsi</th>
                                <th class="px-6 py-4">Detail Jam</th>
                                <th class="px-6 py-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse ($izins as $izin)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <p class="font-black text-gray-900">{{ $izin->guru->nama_lengkap }}</p>
                                        <div class="text-[10px] text-gray-500 mt-0.5">
                                            @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                                {{ $izin->tanggal_mulai->translatedFormat('d M Y') }} ({{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }})
                                            @else
                                                {{ $izin->tanggal_mulai->translatedFormat('d M, H:i') }} s/d {{ $izin->tanggal_selesai->translatedFormat('d M Y, H:i') }}
                                            @endif
                                        </div>
                                        <p class="text-xs text-indigo-600 font-bold mt-1">{{ $izin->jenis_izin }}</p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-bold text-gray-400">Piket</span>
                                                <x-status-badge-izin :status="$izin->status_piket" />
                                            </div>
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-bold text-gray-400">Kurikulum</span>
                                                <x-status-badge-izin :status="$izin->status_kurikulum" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <p class="text-xs text-gray-500 italic truncate max-w-[200px]" title="{{ $izin->deskripsi }}">
                                            "{{ $izin->deskripsi }}"
                                        </p>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($izin->jadwals as $j)
                                                <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-[10px] border">Jam {{ $j->jam_ke }} ({{ $j->rombel->kelas->nama_kelas }})</span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->status_sdm == 'menunggu')
                                            <div class="flex gap-2">
                                                <form action="{{ route('sdm.persetujuan-izin-guru.approve', $izin->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-indigo-500 shadow-sm transition-all">Selesaikan</button>
                                                </form>
                                                <button @click="openRejectModal({{ json_encode($izin) }})" class="px-4 py-2 bg-white border border-red-200 text-red-600 rounded-xl font-bold text-xs uppercase hover:bg-red-50 transition-all">Tolak</button>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <x-status-badge-izin :status="$izin->status_sdm" />
                                                @if($izin->status_sdm == 'disetujui')
                                                    <a href="{{ route('sdm.persetujuan-izin-guru.print', $izin->id) }}" target="_blank" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    </a>
                                                @endif
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-gray-400 italic">
                                        Tidak ada data yang perlu divalidasi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Modal Reject SDM --}}
        <template x-teleport="body">
            <div x-show="isOpen" 
                 x-cloak
                 class="fixed inset-0 z-[100] overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Background backdrop -->
                <div x-show="isOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                     @click="isOpen = false"
                     aria-hidden="true"></div>

                <!-- Modal panel -->
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="isOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-2xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-md p-6">
                        
                        <form :action="rejectUrl" method="POST">
                            @csrf
                            @method('PATCH')
                            
                            <div class="mb-4">
                                <h3 class="text-lg font-bold text-gray-900" id="modal-title">Batalkan Izin (Final)</h3>
                                <p class="text-xs text-gray-500 mt-1">Berikan alasan mengapa permohonan izin ini ditolak.</p>
                            </div>

                            <textarea name="catatan_sdm" 
                                      required 
                                      class="w-full rounded-xl border-gray-200 focus:border-red-500 focus:ring-red-500 transition-all" 
                                      rows="4" 
                                      placeholder="Alasan pembatalan..."></textarea>
                            
                            <div class="mt-6 flex gap-3">
                                <button type="button" 
                                        @click="isOpen = false" 
                                        class="flex-1 py-2.5 font-bold text-gray-500 border border-gray-200 rounded-xl hover:bg-gray-50 transition-colors">
                                    Batal
                                </button>
                                <button type="submit" 
                                        class="flex-1 py-2.5 font-bold bg-red-600 text-white rounded-xl hover:bg-red-700 shadow-sm transition-colors">
                                    Tolak Izin
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </template>
    </div>

    @push('scripts')
    <script>
        function approvalSDM() {
            return {
                isOpen: false,
                selectedItem: {},
                rejectUrl: '',
                openRejectModal(item) {
                    this.isOpen = true;
                    this.selectedItem = item;
                    this.rejectUrl = `{{ url('sdm/persetujuan-izin-guru') }}/${item.id}/reject`;
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
