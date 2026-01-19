<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Izin Guru</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl border border-gray-200 p-5 shadow-sm">
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Total Izin</p>
                    <p class="text-3xl font-black text-gray-900 mt-1">{{ $stats['total'] }}</p>
                </div>
                <div class="bg-yellow-50 rounded-2xl border border-yellow-200 p-5 shadow-sm">
                    <p class="text-xs font-bold text-yellow-600 uppercase tracking-widest">Menunggu</p>
                    <p class="text-3xl font-black text-yellow-700 mt-1">{{ $stats['menunggu'] }}</p>
                </div>
                <div class="bg-green-50 rounded-2xl border border-green-200 p-5 shadow-sm">
                    <p class="text-xs font-bold text-green-600 uppercase tracking-widest">Disetujui</p>
                    <p class="text-3xl font-black text-green-700 mt-1">{{ $stats['disetujui'] }}</p>
                </div>
                <div class="bg-red-50 rounded-2xl border border-red-200 p-5 shadow-sm">
                    <p class="text-xs font-bold text-red-600 uppercase tracking-widest">Ditolak</p>
                    <p class="text-3xl font-black text-red-700 mt-1">{{ $stats['ditolak'] }}</p>
                </div>
            </div>

            {{-- Filter & Table --}}
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                {{-- Filter Section --}}
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <form action="{{ route('piket.monitoring-izin-guru.index') }}" method="GET">
                        <div class="flex flex-col lg:flex-row gap-4 items-end">
                            <div class="w-full lg:w-1/5">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Guru</label>
                                <select name="guru_id" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua Guru</option>
                                    @foreach($gurus as $guru)
                                        <option value="{{ $guru->id }}" {{ request('guru_id') == $guru->id ? 'selected' : '' }}>{{ $guru->nama_lengkap }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="w-full lg:w-1/5 flex gap-2">
                                <div class="flex-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Dari</label>
                                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="flex-1">
                                    <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Sampai</label>
                                    <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="w-full lg:w-1/6">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Status</label>
                                <select name="status" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua</option>
                                    <option value="menunggu" {{ request('status') == 'menunggu' ? 'selected' : '' }}>Menunggu</option>
                                    <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                    <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                </select>
                            </div>
                            <div class="w-full lg:w-1/6">
                                <label class="text-xs font-bold text-gray-500 uppercase mb-1 block">Kategori</label>
                                <select name="kategori" class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="">Semua</option>
                                    <option value="sekolah" {{ request('kategori') == 'sekolah' ? 'selected' : '' }}>Sekolah</option>
                                    <option value="luar" {{ request('kategori') == 'luar' ? 'selected' : '' }}>Luar Sekolah</option>
                                    <option value="terlambat" {{ request('kategori') == 'terlambat' ? 'selected' : '' }}>Terlambat</option>
                                </select>
                            </div>
                            <div class="w-full lg:w-auto flex gap-2">
                                <button type="submit" class="h-10 px-4 bg-indigo-600 hover:bg-indigo-500 text-white rounded-xl shadow-sm transition-colors text-sm font-bold flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                                    Filter
                                </button>
                                <a href="{{ route('piket.monitoring-izin-guru.index') }}" class="h-10 px-4 bg-white border border-gray-200 text-gray-600 hover:bg-gray-50 rounded-xl shadow-sm transition-colors text-sm font-bold flex items-center">
                                    Reset
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Table --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 text-gray-500 font-bold uppercase text-xs">
                            <tr>
                                <th class="px-6 py-4">Guru</th>
                                <th class="px-6 py-4">Waktu & Kategori</th>
                                <th class="px-6 py-4">Status Approval</th>
                                <th class="px-6 py-4">Jadwal & Materi</th>
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
                                                <p class="text-xs text-gray-500">{{ $izin->guru->nip ?? '-' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-bold text-gray-700 text-xs">
                                            @if($izin->tanggal_mulai->isSameDay($izin->tanggal_selesai))
                                                {{ $izin->tanggal_mulai->translatedFormat('d M Y') }}
                                                <span class="block text-[10px] text-indigo-600 font-normal">{{ $izin->tanggal_mulai->format('H:i') }} - {{ $izin->tanggal_selesai->format('H:i') }}</span>
                                            @else
                                                <span class="text-[10px] text-gray-600 font-normal">{{ $izin->tanggal_mulai->translatedFormat('d M, H:i') }} → {{ $izin->tanggal_selesai->translatedFormat('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                        <div class="flex gap-1 mt-1">
                                            @if($izin->kategori_penyetujuan === 'sekolah')
                                                <span class="px-2 py-0.5 rounded-full bg-purple-50 text-purple-700 border border-purple-100 text-[10px] font-black uppercase">Sekolah</span>
                                            @elseif($izin->kategori_penyetujuan === 'luar')
                                                <span class="px-2 py-0.5 rounded-full bg-orange-50 text-orange-700 border border-orange-100 text-[10px] font-black uppercase">Luar</span>
                                            @else
                                                <span class="px-2 py-0.5 rounded-full bg-red-50 text-red-700 border border-red-100 text-[10px] font-black uppercase">Terlambat</span>
                                            @endif
                                            <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-100 text-[10px] font-black uppercase">{{ $izin->jenis_izin }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] font-bold text-gray-400">Piket</span>
                                                <x-status-badge-izin :status="$izin->status_piket" />
                                            </div>
                                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] font-bold text-gray-400">Kurikulum</span>
                                                <x-status-badge-izin :status="$izin->status_kurikulum" />
                                            </div>
                                            <svg class="w-3 h-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                            <div class="flex flex-col items-center">
                                                <span class="text-[9px] font-bold text-gray-400">SDM</span>
                                                <x-status-badge-izin :status="$izin->status_sdm" />
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($izin->jadwals->isNotEmpty())
                                            <div class="space-y-2">
                                                @foreach($izin->jadwals as $jadwal)
                                                    <div class="bg-gray-50 p-2 rounded-lg border border-gray-100">
                                                        <div class="flex justify-between items-center mb-1">
                                                            <span class="font-bold text-gray-900 text-xs">{{ $jadwal->rombel->kelas->nama_kelas }}</span>
                                                            <span class="text-[9px] text-indigo-600 uppercase font-bold">Jam {{ $jadwal->jam_ke }}</span>
                                                        </div>
                                                        <p class="text-[10px] text-gray-500 mb-1">{{ $jadwal->mataPelajaran->nama_mapel }}</p>
                                                        @if($jadwal->pivot->loadedMaterial)
                                                            <div class="flex items-center gap-1.5">
                                                                <div class="w-1 h-1 rounded-full bg-blue-500"></div>
                                                                <button type="button"
                                                                    class="text-[10px] font-bold text-blue-600 hover:underline"
                                                                    data-type="Materi"
                                                                    data-title="{{ $jadwal->pivot->loadedMaterial->title }}"
                                                                    data-content="{{ $jadwal->pivot->loadedMaterial->content ?? '' }}"
                                                                    data-file-url="{{ $jadwal->pivot->loadedMaterial->file_path ? Storage::url($jadwal->pivot->loadedMaterial->file_path) : '' }}"
                                                                    data-mapel="{{ $jadwal->mataPelajaran->nama_mapel }}"
                                                                    data-rombel="{{ $jadwal->rombel->kelas->nama_kelas }}"
                                                                    data-jam="{{ $jadwal->jam_ke }}"
                                                                    onclick="openLmsDetail(this)">
                                                                    {{ $jadwal->pivot->loadedMaterial->title }}
                                                                </button>
                                                            </div>
                                                        @endif
                                                        @if($jadwal->pivot->loadedAssignment)
                                                            <div class="flex items-center gap-1.5">
                                                                <div class="w-1 h-1 rounded-full bg-green-500"></div>
                                                                <button type="button"
                                                                    class="text-[10px] font-bold text-green-600 hover:underline"
                                                                    data-type="Tugas"
                                                                    data-title="{{ $jadwal->pivot->loadedAssignment->title }}"
                                                                    data-description="{{ $jadwal->pivot->loadedAssignment->description ?? '' }}"
                                                                    data-due-date="{{ $jadwal->pivot->loadedAssignment->due_date ? $jadwal->pivot->loadedAssignment->due_date->translatedFormat('d M Y H:i') : '' }}"
                                                                    data-points="{{ $jadwal->pivot->loadedAssignment->points ?? '' }}"
                                                                    data-file-url="{{ $jadwal->pivot->loadedAssignment->file_path ? Storage::url($jadwal->pivot->loadedAssignment->file_path) : '' }}"
                                                                    data-mapel="{{ $jadwal->mataPelajaran->nama_mapel }}"
                                                                    data-rombel="{{ $jadwal->rombel->kelas->nama_kelas }}"
                                                                    data-jam="{{ $jadwal->jam_ke }}"
                                                                    onclick="openLmsDetail(this)">
                                                                    {{ $jadwal->pivot->loadedAssignment->title }}
                                                                </button>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="text-xs text-gray-400 italic">Tidak ada jadwal</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-400 italic">
                                        <svg class="w-12 h-12 mx-auto text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        Tidak ada data izin guru ditemukan.
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
    </div>

    <div id="lms-detail-modal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" onclick="closeLmsDetail()"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl relative overflow-hidden">
                <div class="p-6 border-b border-gray-100">
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest" id="lms-detail-type">Detail</p>
                    <h3 class="text-lg font-bold text-gray-900 mt-1" id="lms-detail-title"></h3>
                    <p class="text-xs text-gray-500 mt-2" id="lms-detail-meta"></p>
                </div>
                <div class="p-6 space-y-4">
                    <div id="lms-detail-content-wrapper">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Ringkasan</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line" id="lms-detail-content"></p>
                    </div>
                    <div id="lms-detail-extra" class="hidden">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Detail Tugas</p>
                        <p class="text-sm text-gray-700 whitespace-pre-line" id="lms-detail-description"></p>
                        <div class="text-xs text-gray-600 mt-2 space-y-1">
                            <p id="lms-detail-due-date"></p>
                            <p id="lms-detail-points"></p>
                        </div>
                    </div>
                    <div id="lms-detail-file" class="hidden">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Lampiran</p>
                        <a id="lms-detail-file-link" href="#" target="_blank" class="text-sm text-blue-600 hover:underline">Lihat File</a>
                    </div>
                </div>
                <div class="p-4 border-t border-gray-100 bg-gray-50/50 flex justify-end">
                    <button type="button" onclick="closeLmsDetail()" class="px-4 py-2 rounded-xl bg-gray-900 text-white text-xs font-bold uppercase tracking-widest">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        window.openLmsDetail = function (el) {
            const type = el.dataset.type || 'Detail';
            const title = el.dataset.title || '-';
            const content = el.dataset.content || '';
            const description = el.dataset.description || '';
            const dueDate = el.dataset.dueDate || '';
            const points = el.dataset.points || '';
            const fileUrl = el.dataset.fileUrl || '';
            const mapel = el.dataset.mapel || '-';
            const rombel = el.dataset.rombel || '-';
            const jam = el.dataset.jam || '-';

            document.getElementById('lms-detail-type').textContent = type;
            document.getElementById('lms-detail-title').textContent = title;
            document.getElementById('lms-detail-meta').textContent = `${mapel} | ${rombel} | Jam ${jam}`;

            const contentWrapper = document.getElementById('lms-detail-content-wrapper');
            const contentEl = document.getElementById('lms-detail-content');
            contentEl.textContent = content || '-';
            contentWrapper.classList.toggle('hidden', !content && type !== 'Materi');

            const extra = document.getElementById('lms-detail-extra');
            if (type === 'Tugas') {
                document.getElementById('lms-detail-description').textContent = description || '-';
                document.getElementById('lms-detail-due-date').textContent = dueDate ? `Batas waktu: ${dueDate}` : '';
                document.getElementById('lms-detail-points').textContent = points ? `Poin: ${points}` : '';
                extra.classList.remove('hidden');
            } else {
                extra.classList.add('hidden');
            }

            const fileWrap = document.getElementById('lms-detail-file');
            const fileLink = document.getElementById('lms-detail-file-link');
            if (fileUrl) {
                fileLink.href = fileUrl;
                fileWrap.classList.remove('hidden');
            } else {
                fileWrap.classList.add('hidden');
            }

            document.getElementById('lms-detail-modal').classList.remove('hidden');
        }

        window.closeLmsDetail = function () {
            document.getElementById('lms-detail-modal').classList.add('hidden');
        }
    </script>
    @endpush
</x-app-layout>


