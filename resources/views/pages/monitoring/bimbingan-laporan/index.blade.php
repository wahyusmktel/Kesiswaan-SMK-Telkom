<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="p-2.5 bg-rose-50 text-rose-600 rounded-2xl shadow-sm border border-rose-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l3.414 3.414A1 1 0 0117 7.414V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Bimbingan Laporan Prakerin</h2>
                    <p class="text-xs text-gray-500">
                        Pantau progres bimbingan laporan siswa, tahapan bab, dan periksa hasil koreksi review terakhir
                        @if ($isWaliKelas && $kelasWali)
                            <span class="font-bold text-rose-600">• Khusus Kelas Binaan: {{ $kelasWali->nama_kelas }}</span>
                        @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 text-xs font-bold bg-gray-100 text-gray-700 rounded-xl">
                    Hak Akses: {{ session('active_role') ?: auth()->user()?->getRoleNames()->first() }}
                </span>
            </div>
        </div>
    </x-slot>

    <div class="py-6 space-y-6">
        {{-- KPI Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Total Siswa Dimonitor</div>
                    <div class="text-2xl font-extrabold text-gray-900">{{ $penempatans->total() }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Tahap Review Judul</div>
                    <div class="text-2xl font-extrabold text-amber-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->judul_status === 'diajukan')->count() }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Sedang Bimbingan Bab</div>
                    <div class="text-2xl font-extrabold text-rose-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->status === 'proses')->count() }}
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-xs font-semibold text-gray-400">Laporan Selesai (ACC)</div>
                    <div class="text-2xl font-extrabold text-emerald-600">
                        {{ $penempatans->filter(fn($p) => $p->bimbinganLaporan?->status === 'selesai')->count() }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter & Search Form --}}
        <div class="bg-white rounded-2xl border border-gray-100 p-6 shadow-sm">
            <form method="GET" action="{{ route('monitoring.bimbingan-laporan.index') }}" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-4">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Cari Siswa / Judul</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Ketik nama siswa, NIS, atau judul..."
                            class="w-full text-xs rounded-xl border-gray-300 pl-10 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                        <svg class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </div>

                @if (!$isWaliKelas)
                    <div class="md:col-span-3">
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Kelas</label>
                        <select name="kelas_id" class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                            <option value="">Semua Kelas XII</option>
                            @foreach ($kelasList as $k)
                                <option value="{{ $k->id }}" {{ request('kelas_id') == $k->id ? 'selected' : '' }}>
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <div class="{{ $isWaliKelas ? 'md:col-span-5' : 'md:col-span-3' }}">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-2">Filter Status Laporan</label>
                    <select name="status" class="w-full text-xs rounded-xl border-gray-300 focus:border-rose-500 focus:ring-rose-500 shadow-sm transition">
                        <option value="">Semua Status</option>
                        <option value="belum_judul" {{ request('status') === 'belum_judul' ? 'selected' : '' }}>Belum / Sedang Pengajuan Judul</option>
                        <option value="proses" {{ request('status') === 'proses' ? 'selected' : '' }}>Sedang Bimbingan Bab</option>
                        <option value="selesai" {{ request('status') === 'selesai' ? 'selected' : '' }}>Selesai (ACC Final)</option>
                    </select>
                </div>

                <div class="{{ $isWaliKelas ? 'md:col-span-3' : 'md:col-span-2' }} flex items-center gap-2">
                    <button type="submit"
                        class="w-full py-2.5 px-4 rounded-xl bg-gray-900 text-white font-bold text-xs hover:bg-gray-800 transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                        Filter
                    </button>
                    @if (request()->hasAny(['search', 'kelas_id', 'status']))
                        <a href="{{ route('monitoring.bimbingan-laporan.index') }}"
                            class="p-2.5 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-50 transition" title="Reset Filter">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        {{-- Monitoring Table --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-gray-50 text-gray-500 uppercase tracking-wider text-[10px] font-bold border-b border-gray-100">
                        <tr>
                            <th class="py-4 px-6">Siswa & Kelas</th>
                            <th class="py-4 px-6">Industri & Pembimbing</th>
                            <th class="py-4 px-6">Judul Laporan</th>
                            <th class="py-4 px-6">Progres Bab</th>
                            <th class="py-4 px-6">Status Terkini</th>
                            <th class="py-4 px-6 text-right">Aksi Monitoring</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 text-gray-700">
                        @forelse ($penempatans as $penempatan)
                            @php
                                $lap = $penempatan->bimbinganLaporan;
                                $persen = $lap?->persentase_selesai ?? 0;
                                $tahapDisetujui = $lap ? $lap->tahaps->where('status', 'disetujui')->count() : 0;
                                $totalTahap = $lap ? $lap->tahaps->count() : 0;
                                $tahapTerakhir = $lap?->tahap_terakhir;
                            @endphp
                            <tr class="hover:bg-gray-50/60 transition">
                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-900 text-sm">{{ $penempatan->siswa?->nama_lengkap }}</div>
                                    <div class="text-[11px] text-gray-400 mt-0.5">NIS: {{ $penempatan->siswa?->nis ?? '-' }} • {{ $penempatan->siswa?->kelas_saat_ini ?? '-' }}</div>
                                </td>

                                <td class="py-4 px-6">
                                    <div class="font-bold text-gray-800">{{ $penempatan->industri?->nama_industri ?? '-' }}</div>
                                    <div class="text-[11px] text-gray-500 mt-0.5">
                                        Guru: {{ $penempatan->guruPembimbing?->nama_lengkap ?? '-' }}
                                    </div>
                                </td>

                                <td class="py-4 px-6 max-w-xs">
                                    @if ($lap && $lap->judul)
                                        <div class="font-semibold text-gray-800 line-clamp-2" title="{{ $lap->judul }}">
                                            "{{ $lap->judul }}"
                                        </div>
                                        <div class="text-[10px] mt-1">
                                            @if ($lap->judul_status === 'disetujui')
                                                <span class="text-emerald-600 font-bold">Judul Disetujui</span>
                                            @elseif ($lap->judul_status === 'diajukan')
                                                <span class="text-amber-600 font-bold">Judul Menunggu Review</span>
                                            @elseif ($lap->judul_status === 'ditolak')
                                                <span class="text-rose-600 font-bold">Judul Ditolak (Revisi)</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 italic">Belum mengajukan judul</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6">
                                    @if ($lap && $lap->judul_status === 'disetujui')
                                        <div class="w-32">
                                            <div class="flex items-center justify-between text-[10px] font-bold mb-1">
                                                <span class="text-gray-600">{{ $tahapDisetujui }}/{{ $totalTahap }}</span>
                                                <span class="text-rose-600">{{ $persen }}%</span>
                                            </div>
                                            <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                                <div class="bg-gradient-to-r from-rose-500 to-emerald-500 h-1.5 rounded-full" style="width: {{ $persen }}%"></div>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6">
                                    @if ($lap && $lap->status === 'selesai')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-black bg-emerald-100 text-emerald-800">
                                            ACC Final (Selesai)
                                        </span>
                                    @elseif ($tahapTerakhir)
                                        <div class="text-[11px]">
                                            <div class="font-bold text-gray-800">{{ $tahapTerakhir->nama_tahap }}</div>
                                            @if ($tahapTerakhir->status === 'disetujui')
                                                <span class="text-emerald-600 font-semibold">Telah Disetujui</span>
                                            @elseif ($tahapTerakhir->status === 'revisi')
                                                <span class="text-rose-600 font-semibold">Dalam Revisi</span>
                                            @elseif ($tahapTerakhir->status === 'diajukan')
                                                <span class="text-blue-600 font-semibold">Sedang Ditinjau</span>
                                            @else
                                                <span class="text-gray-400">Belum Unggah Dokumen</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-gray-400 text-xs">Belum ada tahap aktif</span>
                                    @endif
                                </td>

                                <td class="py-4 px-6 text-right space-x-1 whitespace-nowrap">
                                    @if ($lap && $tahapTerakhir && $tahapTerakhir->file_path)
                                        <a href="{{ route('monitoring.bimbingan-laporan.review-terakhir', $lap) }}"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-bold rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 transition"
                                            title="Lihat Review & Coretan Terakhir">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            Review Terakhir
                                        </a>
                                    @endif

                                    @if ($lap && $lap->judul_status === 'disetujui')
                                        <a href="{{ route('monitoring.bimbingan-laporan.riwayat-pdf', $lap) }}" target="_blank"
                                            class="inline-flex items-center gap-1 px-2.5 py-1.5 text-xs font-semibold rounded-xl bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 transition shadow-sm"
                                            title="Unduh Rekap Riwayat Bimbingan PDF">
                                            <svg class="w-3.5 h-3.5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0119 9.414V19a2 2 0 01-2 2z"/></svg>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-gray-400">
                                    Tidak ada data bimbingan laporan prakerin ditemukan sesuai filter pencarian.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-4 border-t border-gray-100">
                {{ $penempatans->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
