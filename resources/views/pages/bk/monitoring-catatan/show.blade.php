<x-app-layout>
    <div class="py-12" x-data="{ activeTab: 'pelanggaran' }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header --}}
            <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex items-center gap-5">
                    <a href="{{ route('bk.monitoring-catatan.index') }}" class="w-12 h-12 rounded-2xl bg-white shadow-sm border border-gray-100 flex items-center justify-center text-gray-500 hover:text-indigo-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                    </a>
                    <div>
                        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">{{ $siswa->nama_lengkap }}</h2>
                        <p class="text-gray-500 font-medium">Monitoring Kedisiplinan Siswa</p>
                    </div>
                </div>

                {{-- Action Button --}}
                <div class="flex gap-3">
                    <button onclick="openModalKonsultasi()" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-95 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Buat Jadwal Konsultasi
                    </button>
                    <button onclick="openModalProposal()" class="inline-flex items-center px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl shadow-lg shadow-red-100 transition-all active:scale-95 gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Ajukan Panggilan Orang Tua
                    </button>
                </div>
            </div>

            {{-- Student Quick Info --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Kelas</p>
                        <p class="text-lg font-extrabold text-gray-900">{{ $siswa->rombels->first()->kelas->nama_kelas ?? '-' }}</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Total Poin</p>
                        <p class="text-lg font-extrabold text-gray-900">{{ $siswa->getCurrentPoints() }} Poin</p>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-xs font-bold text-gray-500 uppercase tracking-widest">Terlambat</p>
                        <p class="text-lg font-extrabold text-gray-900">{{ $keterlambatans->count() }} Kali</p>
                    </div>
                </div>
            </div>

            {{-- Tabs --}}
            <div class="mb-6 flex gap-2 p-1 bg-gray-100/50 rounded-2xl w-fit">
                <button @click="activeTab = 'pelanggaran'" 
                        :class="activeTab === 'pelanggaran' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all">
                    Data Pelanggaran
                </button>
                <button @click="activeTab = 'keterlambatan'" 
                        :class="activeTab === 'keterlambatan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all">
                    Data Keterlambatan
                </button>
                <button @click="activeTab = 'panggilan'" 
                        :class="activeTab === 'panggilan' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-700'"
                        class="px-6 py-2.5 rounded-xl font-bold text-sm transition-all">
                    Riwayat Panggilan
                </button>
            </div>

            {{-- Content --}}
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                {{-- Pelanggaran Table --}}
                <div x-show="activeTab === 'pelanggaran'" class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Tanggal</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Pelanggaran</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Poin</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Pelapor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($pelanggarans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 text-gray-600 font-medium">{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $p->peraturan->deskripsi ?? '-' }}</div>
                                    <div class="text-xs text-gray-400">Pasal {{ $p->peraturan->pasal ?? '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg font-bold text-xs">{{ $p->peraturan->bobot_poin ?? 0 }}</span>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $p->pelapor->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">Tidak ada riwayat pelanggaran.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Keterlambatan Table --}}
                <div x-show="activeTab === 'keterlambatan'" x-cloak class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Waktu</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Alasan</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Petugas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($keterlambatans as $k)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-bold">{{ $k->created_at->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ $k->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $k->alasan_siswa ?? '-' }}</td>
                                <td class="px-6 py-4 text-gray-600">{{ $k->security->name ?? '-' }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="3" class="px-6 py-12 text-center text-gray-400">Tidak ada riwayat keterlambatan.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Panggilan Table --}}
                <div x-show="activeTab === 'panggilan'" x-cloak class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-gray-50/50 border-b border-gray-100">
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Jadwal</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Nomor / Perihal</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Status</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs">Petugas</th>
                                <th class="px-6 py-4 font-bold text-gray-700 uppercase tracking-wider text-xs text-center">Cetak</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($panggilans as $p)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="text-gray-900 font-bold">{{ \Carbon\Carbon::parse($p->tanggal_panggilan)->format('d/m/Y') }}</div>
                                    <div class="text-xs text-gray-500">{{ date('H:i', strtotime($p->jam_panggilan)) }} WIB</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-800">{{ $p->nomor_surat }}</div>
                                    <div class="text-xs text-gray-500">{{ $p->perihal }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusStyles = [
                                            'diajukan' => 'bg-yellow-100 text-yellow-700',
                                            'disetujui' => 'bg-indigo-100 text-indigo-700',
                                            'terkirim' => 'bg-blue-100 text-blue-700',
                                            'hadir' => 'bg-green-100 text-green-700',
                                            'tidak_hadir' => 'bg-red-100 text-red-700',
                                            'ditolak' => 'bg-gray-100 text-gray-700',
                                        ];
                                    @endphp
                                    <span class="px-2 py-1 rounded-lg font-bold text-[10px] uppercase tracking-wider {{ $statusStyles[$p->status] ?? 'bg-gray-50 text-gray-600' }}">
                                        {{ $p->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-xs">
                                        <p class="text-gray-400 uppercase font-bold tracking-tighter" style="font-size: 8px">Diajukan:</p>
                                        <p class="text-gray-600 font-medium">{{ $p->creator->name ?? '-' }}</p>
                                        @if($p->approver)
                                            <p class="text-gray-400 uppercase font-bold tracking-tighter mt-1" style="font-size: 8px">Disetujui:</p>
                                            <p class="text-gray-600 font-medium">{{ $p->approver->name ?? '-' }}</p>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if(in_array($p->status, ['disetujui', 'terkirim', 'hadir', 'tidak_hadir']))
                                    <a href="{{ route('kesiswaan.panggilan-ortu.print', $p->id) }}" target="_blank" class="inline-flex items-center justify-center p-2 bg-indigo-50 text-indigo-600 rounded-xl hover:bg-indigo-100 transition-colors" title="Cetak Surat Panggilan">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </a>
                                    @else
                                    <span class="text-gray-300">
                                        <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                    </span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="px-6 py-12 text-center text-gray-400">Tidak ada riwayat panggilan orang tua.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Proposal --}}
    <div id="modalProposal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden animate-modal-up">
            <form action="{{ route('bk.panggilan-proposal.store') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="master_siswa_id" value="{{ $siswa->id }}">
                
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Ajukan Panggilan Orang Tua</h3>
                <p class="text-gray-500 text-sm mb-6">Lengkapi data surat panggilan untuk diajukan ke Waka Kesiswaan.</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Nomor Surat</label>
                        <input type="text" name="nomor_surat" required class="w-full rounded-2xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Contoh: 045/BK/2025">
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Tanggal</label>
                            <input type="date" name="tanggal_panggilan" required class="w-full rounded-2xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Jam</label>
                            <input type="time" name="jam_panggilan" required class="w-full rounded-2xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Tempat</label>
                        <input type="text" name="tempat_panggilan" required class="w-full rounded-2xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Ruang BK">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Perihal</label>
                        <textarea name="perihal" required rows="3" class="w-full rounded-2xl border-gray-200 focus:border-red-500 focus:ring-red-500 shadow-sm" placeholder="Konsultasi kedisiplinan..."></textarea>
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeModalProposal()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-extrabold rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-red-600 hover:bg-red-700 text-white font-extrabold rounded-2xl shadow-lg shadow-red-100 transition-all active:scale-95">Ajukan Panggilan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Konsultasi --}}
    <div id="modalKonsultasi" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-gray-900/60 backdrop-blur-sm hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full shadow-2xl overflow-hidden animate-modal-up">
            <form action="{{ route('bk.konsultasi.store-bk') }}" method="POST" class="p-8">
                @csrf
                <input type="hidden" name="master_siswa_id" value="{{ $siswa->id }}">
                
                <h3 class="text-2xl font-extrabold text-gray-900 mb-2">Buat Jadwal Pembinaan</h3>
                <p class="text-gray-500 text-sm mb-6">Jadwalkan sesi pembinaan atau konsultasi dengan siswa ini.</p>

                <div class="space-y-5">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Perihal Pembinaan</label>
                        <textarea name="perihal" required rows="3" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Contoh: Pembinaan kedisiplinan poin pelanggaran..."></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Tanggal</label>
                            <input type="date" name="tanggal_rencana" required class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" min="{{ date('Y-m-d') }}">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Jam</label>
                            <input type="time" name="jam_rencana" required class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-widest mb-2">Tempat</label>
                        <input type="text" name="tempat" class="w-full rounded-2xl border-gray-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" placeholder="Ruang BK" value="Ruang BK">
                    </div>
                </div>

                <div class="mt-8 flex gap-3">
                    <button type="button" onclick="closeModalKonsultasi()" class="flex-1 px-6 py-3 bg-gray-100 hover:bg-gray-200 text-gray-600 font-extrabold rounded-2xl transition-all">Batal</button>
                    <button type="submit" class="flex-1 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-extrabold rounded-2xl shadow-lg shadow-indigo-100 transition-all active:scale-95">Buat Jadwal</button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModalProposal() {
            document.getElementById('modalProposal').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModalProposal() {
            document.getElementById('modalProposal').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }

        function openModalKonsultasi() {
            document.getElementById('modalKonsultasi').classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        function closeModalKonsultasi() {
            document.getElementById('modalKonsultasi').classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    </script>
    <style>
        .animate-modal-up {
            animation: modal-up 0.3s ease-out forwards;
        }
        @keyframes modal-up {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
    </style>
    @endpush
</x-app-layout>
