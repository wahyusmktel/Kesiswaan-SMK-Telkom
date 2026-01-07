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
                                            <div class="flex flex-col">
                                                <span class="text-[9px] font-bold text-gray-400">KAUR SDM</span>
                                                <x-status-badge-izin :status="$izin->status_sdm" />
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
                                        <div class="flex items-center gap-2">
                                            @if($izin->status_sdm == 'menunggu')
                                                <button @click="openDetailModal({{ json_encode($izin) }})" 
                                                        class="px-4 py-2 bg-indigo-600 text-white rounded-xl font-bold text-xs uppercase hover:bg-indigo-500 shadow-sm transition-all">
                                                    Selesaikan
                                                </button>
                                            @else
                                                <button @click="openDetailModal({{ json_encode($izin) }})" 
                                                        class="p-2 text-gray-400 hover:bg-gray-100 rounded-xl transition-all border border-transparent shadow-sm"
                                                        title="Lihat Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                                    </svg>
                                                </button>
                                                @if($izin->status_sdm == 'disetujui')
                                                    <a href="{{ route('sdm.persetujuan-izin-guru.print', $izin->id) }}" target="_blank" class="p-1.5 text-indigo-600 hover:bg-indigo-50 rounded-lg transition-colors">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                                    </a>
                                                @endif
                                            @endif
                                        </div>
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

        {{-- Modal Detail Izin --}}
        <template x-teleport="body">
            <div x-show="isDetailOpen" 
                 x-cloak
                 class="fixed inset-0 z-[100] overflow-y-auto" 
                 aria-labelledby="modal-title" 
                 role="dialog" 
                 aria-modal="true">
                <div x-show="isDetailOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 bg-gray-900/60 backdrop-blur-sm transition-opacity" 
                     @click="isDetailOpen = false"
                     aria-hidden="true"></div>

                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                    <div x-show="isDetailOpen"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                         x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                         class="relative transform overflow-hidden rounded-[2.5rem] bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-2xl">
                        
                        {{-- Header --}}
                        <div class="bg-gray-50 px-8 py-6 border-b border-gray-100 flex items-center justify-between">
                            <div>
                                <h3 class="text-2xl font-black text-gray-900 leading-tight">Detail Pengajuan Izin</h3>
                                <p class="text-xs text-gray-500 font-bold uppercase tracking-widest mt-1">Informasi Lengkap Guru & Izin</p>
                            </div>
                            <button @click="isDetailOpen = false" class="p-2 hover:bg-gray-200 rounded-full transition-colors text-gray-400">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                            </button>
                        </div>

                        <div class="p-8 space-y-8 max-h-[70vh] overflow-y-auto custom-scrollbar text-sm">
                            {{-- Guru Info --}}
                            <div class="flex items-center gap-6 p-6 bg-indigo-50/50 rounded-3xl border border-indigo-100">
                                <div class="w-20 h-20 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 shadow-inner">
                                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                </div>
                                <div class="space-y-1">
                                    <h4 class="text-2xl font-black text-gray-900 leading-none" x-text="selectedItem.guru.nama_lengkap"></h4>
                                    <p class="text-gray-500 font-bold uppercase tracking-widest text-[10px]" x-text="'NIP: ' + (selectedItem.guru.nip || '-')"></p>
                                    <span class="inline-block px-3 py-1 bg-white text-indigo-600 text-[10px] font-black uppercase rounded-full border border-indigo-200 shadow-sm" x-text="selectedItem.jenis_izin"></span>
                                </div>
                            </div>

                            <div class="grid grid-cols-2 gap-6">
                                {{-- Waktu --}}
                                <div class="space-y-4">
                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Waktu & Durasi</h5>
                                    <div class="space-y-3">
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400">Mulai</p>
                                            <p class="font-black text-gray-800" x-text="formatDateTime(selectedItem.tanggal_mulai)"></p>
                                        </div>
                                        <div>
                                            <p class="text-[10px] font-bold text-gray-400">Selesai</p>
                                            <p class="font-black text-gray-800" x-text="formatDateTime(selectedItem.tanggal_selesai)"></p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="space-y-4">
                                    <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Status Approval</h5>
                                    <div class="space-y-4">
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                            <span class="text-xs font-bold text-gray-500">Piket</span>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm" 
                                                  :class="selectedItem.status_piket === 'disetujui' ? 'bg-green-100 text-green-700 border border-green-200' : (selectedItem.status_piket === 'ditolak' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200')"
                                                  x-text="selectedItem.status_piket"></span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                            <span class="text-xs font-bold text-gray-500">Kurikulum</span>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm" 
                                                  :class="selectedItem.status_kurikulum === 'disetujui' ? 'bg-green-100 text-green-700 border border-green-200' : (selectedItem.status_kurikulum === 'ditolak' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200')"
                                                  x-text="selectedItem.status_kurikulum"></span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-2xl border border-gray-100">
                                            <span class="text-xs font-bold text-gray-500">KAUR SDM</span>
                                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase shadow-sm" 
                                                  :class="selectedItem.status_sdm === 'disetujui' ? 'bg-green-100 text-green-700 border border-green-200' : (selectedItem.status_sdm === 'ditolak' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-yellow-100 text-yellow-700 border border-yellow-200')"
                                                  x-text="selectedItem.status_sdm"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <div class="space-y-3">
                                <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Alasan / Deskripsi</h5>
                                <div class="p-5 bg-slate-50 rounded-3xl border-2 border-slate-100 italic text-gray-600 text-lg leading-relaxed shadow-inner">
                                    "<span x-text="selectedItem.deskripsi"></span>"
                                </div>
                            </div>

                            {{-- Jadwal Terkena Dampak --}}
                            <div class="space-y-3">
                                <h5 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Jadwal Terkena Dampak</h5>
                                <div class="flex flex-wrap gap-2">
                                    <template x-for="j in selectedItem.jadwals" :key="j.id">
                                        <div class="px-4 py-2 bg-white border border-gray-200 rounded-xl shadow-sm">
                                            <p class="text-[10px] font-bold text-gray-400 leading-none">JAM KE</p>
                                            <p class="font-black text-indigo-600 text-lg" x-text="j.jam_ke"></p>
                                            <p class="text-[11px] font-bold text-gray-500 uppercase mt-1" x-text="j.rombel.kelas.nama_kelas"></p>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Action --}}
                        <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex justify-between items-center">
                            <button @click="isDetailOpen = false" 
                                    class="px-6 py-3 bg-white border border-gray-200 text-gray-500 rounded-2xl font-bold uppercase text-xs hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                                Tutup
                            </button>

                            <div x-show="selectedItem.status_sdm === 'menunggu'" class="flex gap-3">
                                <button @click="triggerRejectFromDetail()" 
                                        class="px-6 py-3 bg-white border border-red-200 text-red-600 rounded-2xl font-bold uppercase text-xs hover:bg-red-50 transition-all active:scale-95 shadow-sm">
                                    Tolak Izin
                                </button>
                                <button @click="triggerApprove()" 
                                        class="px-8 py-3 bg-indigo-600 text-white rounded-2xl font-black uppercase text-xs tracking-widest hover:bg-indigo-700 transition-all active:scale-95 shadow-lg shadow-indigo-200/50">
                                    Selesaikan & Validasi
                                </button>
                            </div>
                        </div>

                        {{-- Hidden Approval Form --}}
                        <form x-ref="approveForm" :action="approveUrl" method="POST" style="display: none;">
                            @csrf
                            @method('PATCH')
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
                isDetailOpen: false,
                selectedItem: { guru: {} },
                rejectUrl: '',
                approveUrl: '',
                openRejectModal(item) {
                    this.isOpen = true;
                    this.selectedItem = item;
                    this.rejectUrl = `{{ url('sdm/persetujuan-izin-guru') }}/${item.id}/reject`;
                },
                openDetailModal(item) {
                    this.selectedItem = item;
                    this.isDetailOpen = true;
                    this.approveUrl = `{{ url('sdm/persetujuan-izin-guru') }}/${item.id}/approve`;
                    this.rejectUrl = `{{ url('sdm/persetujuan-izin-guru') }}/${item.id}/reject`;
                },
                triggerApprove() {
                    if(confirm('Apakah Anda yakin ingin menyelesaikan dan memvalidasi izin ini?')) {
                        this.$refs.approveForm.submit();
                    }
                },
                triggerRejectFromDetail() {
                    this.isDetailOpen = false;
                    setTimeout(() => {
                        this.isOpen = true;
                    }, 300);
                },
                formatDateTime(dateStr) {
                    if (!dateStr) return '-';
                    const date = new Date(dateStr);
                    return date.toLocaleString('id-ID', { 
                        day: 'numeric', 
                        month: 'long', 
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    });
                }
            }
        }
    </script>
    @endpush
</x-app-layout>
