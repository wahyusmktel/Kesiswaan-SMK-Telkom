<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Monitoring Penguji UKK</h2>
    </x-slot>

    <div class="py-6 w-full" x-data="{ searchQuery: '', activeTab: 'all' }">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- ══ PAGE HEADER ══ --}}
            <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-slate-800 rounded-3xl p-6 text-white shadow-xl flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-white/10 backdrop-blur-md flex items-center justify-center text-2xl border border-white/20 shadow-sm shrink-0">
                        📡
                    </div>
                    <div>
                        <h3 class="text-xl font-black tracking-wide flex items-center gap-2">
                            Monitoring Progres Penguji UKK
                        </h3>
                        <p class="text-blue-100 text-xs sm:text-sm mt-0.5">
                            Pantau aktivitas dan kelengkapan penilaian penguji secara real-time untuk memastikan seluruh siswa tuntas diuji.
                        </p>
                    </div>
                </div>

                {{-- Export Button --}}
                @if($ujian && $pengujisData->isNotEmpty())
                @php
                    if (request()->routeIs('kepala-sekolah.*')) {
                        $exportRoute = route('kepala-sekolah.ukk.monitoring-penguji.export');
                    } elseif (request()->routeIs('kurikulum.*')) {
                        $exportRoute = route('kurikulum.ukk.monitoring-penguji.export');
                    } else {
                        $exportRoute = route('kaprodi.ukk.monitoring-penguji.export');
                    }
                @endphp
                <a href="{{ $exportRoute }}?{{ http_build_query(['ujian_id' => $selectedUjianId, 'rombel_id' => $selectedRombelId, 'status' => $selectedStatus]) }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-500 hover:bg-emerald-600 active:scale-95 text-white font-bold rounded-2xl text-xs sm:text-sm transition-all shadow-lg shadow-emerald-900/20 shrink-0">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span>Export Excel Monitoring</span>
                </a>
                @endif
            </div>

            {{-- ══ FILTER CARD ══ --}}
            <form method="GET" action="{{ request()->url() }}" class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-5 space-y-4">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
                    {{-- Pilih UKK --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Pilih Ujian UKK</label>
                        <select name="ujian_id" id="ujian_select"
                            class="w-full rounded-2xl border-gray-200 text-xs sm:text-sm focus:ring-indigo-400 focus:border-indigo-400"
                            onchange="this.form.submit()">
                            <option value="">-- Pilih UKK --</option>
                            @foreach($ujians as $u)
                                <option value="{{ $u->id }}" {{ $selectedUjianId == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama_ujian }}
                                    @if($u->tahunPelajaran) ({{ $u->tahunPelajaran->tahun }}) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Kelas/Rombel --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Filter Kelas / Rombel</label>
                        <select name="rombel_id"
                            class="w-full rounded-2xl border-gray-200 text-xs sm:text-sm focus:ring-indigo-400 focus:border-indigo-400"
                            onchange="this.form.submit()">
                            <option value="">Semua Kelas</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}" {{ $selectedRombelId == $r->id ? 'selected' : '' }}>
                                    {{ $r->kelas->nama_kelas ?? ('Rombel ' . $r->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Filter Status Penilaian --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Status Penguji</label>
                        <select name="status"
                            class="w-full rounded-2xl border-gray-200 text-xs sm:text-sm focus:ring-indigo-400 focus:border-indigo-400"
                            onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Selesai" {{ $selectedStatus === 'Selesai' ? 'selected' : '' }}>✓ Selesai (100%)</option>
                            <option value="Sedang Menilai" {{ $selectedStatus === 'Sedang Menilai' ? 'selected' : '' }}>⏳ Sedang Menilai</option>
                            <option value="Belum Mulai" {{ $selectedStatus === 'Belum Mulai' ? 'selected' : '' }}>⚠️ Belum Mulai</option>
                        </select>
                    </div>

                    {{-- Cari Cepat / Filter Live --}}
                    <div>
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Cari Cepat Nama Penguji</label>
                        <div class="relative">
                            <input type="text" x-model="searchQuery" placeholder="Ketik nama penguji atau ruang..."
                                class="w-full pl-9 pr-4 py-2.5 rounded-2xl border-gray-200 text-xs sm:text-sm focus:ring-indigo-400 focus:border-indigo-400">
                            <svg class="w-4 h-4 absolute left-3 top-3.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </form>

            @if(!$ujian)
                <div class="bg-white rounded-3xl border border-gray-200/80 p-12 text-center shadow-sm">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="text-gray-800 font-bold text-base">Silakan pilih Ujian UKK di atas.</p>
                    <p class="text-gray-400 text-xs mt-1">Pilih ujian untuk melihat dashboard monitoring aktivitas penguji.</p>
                </div>
            @elseif($pengujisData->isEmpty())
                <div class="bg-white rounded-3xl border border-gray-200/80 p-12 text-center shadow-sm">
                    <p class="text-4xl mb-3">👥</p>
                    <p class="text-gray-800 font-bold text-base">Belum ada penguji yang ditugaskan untuk filter ini.</p>
                    @if(auth()->user()->hasAnyRole(['Kaprodi', 'Kaprodi PPLGIM ANIMASI', 'Kaprodi TJKT']))
                        <p class="text-gray-400 text-xs mt-1">Kaprodi dapat mengatur penugasan penguji pada menu <a href="{{ route('kaprodi.ukk.penguji.index') }}" class="text-indigo-600 font-bold underline">Set Penguji</a>.</p>
                    @else
                        <p class="text-gray-400 text-xs mt-1">Penugasan penguji diatur oleh masing-masing Kaprodi kejuruan.</p>
                    @endif
                </div>
            @else

            {{-- ══ KPI SUMMARY CARDS ══ --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3.5">
                {{-- Total Penguji --}}
                <div class="bg-white rounded-3xl border border-gray-200/80 shadow-sm p-4 text-center">
                    <p class="text-3xl font-black text-slate-900">{{ $kpis['total_penguji'] }}</p>
                    <p class="text-xs font-bold text-slate-400 mt-1 uppercase tracking-wider">Total Penguji</p>
                </div>

                {{-- Selesai 100% --}}
                <div class="bg-emerald-50/70 rounded-3xl border border-emerald-200/80 shadow-sm p-4 text-center">
                    <p class="text-3xl font-black text-emerald-700">{{ $kpis['selesai_count'] }}</p>
                    <p class="text-xs font-bold text-emerald-600 mt-1 uppercase tracking-wider">Tuntas (100%)</p>
                </div>

                {{-- Sedang Menilai --}}
                <div class="bg-amber-50/70 rounded-3xl border border-amber-200/80 shadow-sm p-4 text-center">
                    <p class="text-3xl font-black text-amber-700">{{ $kpis['proses_count'] }}</p>
                    <p class="text-xs font-bold text-amber-600 mt-1 uppercase tracking-wider">Sedang Menilai</p>
                </div>

                {{-- Belum Mulai --}}
                <div class="bg-rose-50/70 rounded-3xl border border-rose-200/80 shadow-sm p-4 text-center">
                    <p class="text-3xl font-black text-rose-700">{{ $kpis['belum_count'] }}</p>
                    <p class="text-xs font-bold text-rose-600 mt-1 uppercase tracking-wider">Belum Mulai</p>
                </div>

                {{-- Rata-rata Progres --}}
                <div class="bg-indigo-50/70 rounded-3xl border border-indigo-200/80 shadow-sm p-4 text-center col-span-2 sm:col-span-1">
                    <p class="text-3xl font-black text-indigo-700">{{ $kpis['avg_progress'] }}%</p>
                    <p class="text-xs font-bold text-indigo-600 mt-1 uppercase tracking-wider">
                        Rerata Progres ({{ $kpis['total_selesai_siswa'] }}/{{ $kpis['total_target_siswa'] }} Siswa)
                    </p>
                </div>
            </div>

            {{-- ══ DAFTAR PENGUJI DENGAN PROGRES PENILAIAN ══ --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between px-1">
                    <h4 class="font-black text-slate-800 text-base">
                        Daftar Penugasan Penguji UKK &amp; Progres Real-Time
                    </h4>
                    <span class="text-xs font-bold text-slate-400">
                        {{ $pengujisData->count() }} Penugasan Terdaftar
                    </span>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    @foreach($pengujisData as $p)
                    <div x-data="{ openDetails: false }"
                        x-show="searchQuery === '' || '{{ strtolower($p['nama_penguji'] . ' ' . $p['rombel_nama'] . ' ' . $p['target_label'] . ' ' . $p['jadwal_label']) }}'.includes(searchQuery.toLowerCase())"
                        class="bg-white rounded-3xl border border-gray-200/80 shadow-sm overflow-hidden hover:border-indigo-300 transition-all">
                        
                        {{-- Card Header & Main Info --}}
                        <div class="p-5 sm:p-6 space-y-4">
                            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                                {{-- Profile --}}
                                <div class="flex items-start sm:items-center gap-3.5">
                                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 text-white flex items-center justify-center font-black text-base shadow-sm shrink-0">
                                        @if($p['avatar'])
                                            <img src="{{ $p['avatar'] }}" alt="{{ $p['nama_penguji'] }}" class="w-12 h-12 rounded-2xl object-cover">
                                        @else
                                            {{ strtoupper(substr($p['nama_penguji'], 0, 2)) }}
                                        @endif
                                    </div>
                                    <div>
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h5 class="font-black text-slate-900 text-base">{{ $p['nama_penguji'] }}</h5>
                                            @if($p['kode_guru'] && $p['kode_guru'] !== '-')
                                                <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 font-mono text-[10px] font-bold">
                                                    {{ $p['kode_guru'] }}
                                                </span>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-2 mt-1 flex-wrap">
                                            {{-- Role Badge --}}
                                            @if($p['peran'] === 'pengetahuan')
                                                <span class="px-2.5 py-0.5 rounded-lg font-bold text-xs bg-blue-50 text-blue-700 border border-blue-200">
                                                    {{ $p['peran_label'] }}
                                                </span>
                                            @elseif($p['peran'] === 'keterampilan')
                                                <span class="px-2.5 py-0.5 rounded-lg font-bold text-xs bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                    {{ $p['peran_label'] }}
                                                </span>
                                            @else
                                                <span class="px-2.5 py-0.5 rounded-lg font-bold text-xs bg-purple-50 text-purple-700 border border-purple-200">
                                                    {{ $p['peran_label'] }}
                                                </span>
                                            @endif

                                            <span class="text-slate-300">&bull;</span>
                                            <span class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                                                <span>🏫</span> {{ $p['rombel_nama'] }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Status Pill & Progress Stat --}}
                                <div class="flex items-center gap-3 self-start md:self-auto">
                                    <div class="text-right">
                                        <div class="text-sm font-black text-slate-900">
                                            {{ $p['selesai_count'] }} / {{ $p['target_count'] }} Siswa
                                        </div>
                                        <div class="text-[11px] font-bold text-slate-400">
                                            {{ $p['persentase'] }}% Selesai
                                        </div>
                                    </div>

                                    @if($p['status'] === 'Selesai')
                                        <span class="px-3 py-1.5 rounded-xl font-black text-xs bg-emerald-100 text-emerald-800 border border-emerald-200 flex items-center gap-1.5 shadow-sm">
                                            <span>✓</span> Selesai
                                        </span>
                                    @elseif($p['status'] === 'Sedang Menilai')
                                        <span class="px-3 py-1.5 rounded-xl font-black text-xs bg-amber-100 text-amber-800 border border-amber-200 flex items-center gap-1.5 shadow-sm">
                                            <span>⏳</span> Sedang Menilai
                                        </span>
                                    @else
                                        <span class="px-3 py-1.5 rounded-xl font-black text-xs bg-rose-100 text-rose-800 border border-rose-200 flex items-center gap-1.5 shadow-sm">
                                            <span>⚠️</span> Belum Mulai
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Progress Bar --}}
                            <div class="space-y-1.5 pt-1">
                                <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden p-0.5 border border-slate-200/60">
                                    <div class="h-full rounded-full transition-all duration-500 {{ $p['persentase'] >= 100 ? 'bg-emerald-500' : ($p['persentase'] > 0 ? 'bg-gradient-to-r from-amber-400 to-indigo-500' : 'bg-transparent') }}"
                                        style="width: {{ $p['persentase'] }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-[11px] font-bold text-slate-500">
                                    <div class="flex items-center gap-3">
                                        <span class="text-emerald-700">✓ {{ $p['selesai_count'] }} Selesai</span>
                                        @if($p['sebagian_count'] > 0)
                                            <span class="text-amber-700">&bull; {{ $p['sebagian_count'] }} Dalam Proses</span>
                                        @endif
                                        <span class="text-slate-400">&bull; {{ $p['belum_count'] }} Belum Dinilai</span>
                                    </div>
                                    <div class="text-slate-400">
                                        Target: {{ $p['target_label'] }}
                                    </div>
                                </div>
                            </div>

                            {{-- Session Metadata & Last Active --}}
                            <div class="pt-3 border-t border-slate-100 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2.5 text-xs text-slate-600">
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-400">🎯 Target:</span>
                                    <strong class="text-slate-800">{{ $p['target_label'] }}</strong>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-slate-400">📅 Jadwal:</span>
                                    <span class="text-slate-800 font-medium">{{ $p['jadwal_label'] }}</span>
                                </div>
                                <div class="flex items-center gap-2 sm:col-span-2 lg:col-span-1">
                                    <span class="text-slate-400">🕒 Terakhir Aktif:</span>
                                    @if($p['terakhir_aktif_diff'])
                                        <strong class="text-indigo-600">{{ $p['terakhir_aktif'] }}</strong>
                                        <span class="text-[10px] text-slate-400">({{ $p['terakhir_aktif_diff'] }})</span>
                                    @else
                                        <span class="text-slate-400 italic">{{ $p['terakhir_aktif'] }}</span>
                                    @endif
                                </div>
                            </div>

                            {{-- Toggle Student Details --}}
                            <div class="pt-2">
                                <button type="button" @click="openDetails = !openDetails"
                                    class="w-full py-2 px-4 rounded-xl bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-xs transition-all flex items-center justify-center gap-2 border border-slate-200/80">
                                    <span x-text="openDetails ? '▲ Sembunyikan Rincian Siswa' : '▼ Lihat Rincian Progres per Siswa ({{ $p['target_count'] }} Siswa)'"></span>
                                </button>
                            </div>
                        </div>

                        {{-- Expanded Student List Drawer --}}
                        <div x-show="openDetails" x-collapse class="bg-slate-50/70 border-t border-slate-200/80 p-5 space-y-3">
                            <div class="flex items-center justify-between text-xs font-bold text-slate-600">
                                <span>Daftar Siswa yang Ditugaskan ke Penguji Ini:</span>
                                <span>{{ $p['selesai_count'] }} dari {{ $p['target_count'] }} Selesai</span>
                            </div>

                            <div class="overflow-x-auto bg-white rounded-2xl border border-slate-200 shadow-sm">
                                <table class="w-full text-xs text-left">
                                    <thead>
                                        <tr class="bg-slate-100/80 border-b border-slate-200 text-slate-600 font-black uppercase text-[10px]">
                                            <th class="py-2.5 px-3 w-10 text-center">No</th>
                                            <th class="py-2.5 px-3 w-28">NIS</th>
                                            <th class="py-2.5 px-3">Nama Lengkap Siswa</th>
                                            <th class="py-2.5 px-3 text-center w-36">Kelengkapan Item</th>
                                            <th class="py-2.5 px-3 text-center w-32">Status Penilaian</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($p['siswa_details'] as $s)
                                        <tr class="hover:bg-slate-50/80 transition-colors">
                                            <td class="py-2.5 px-3 text-center font-bold text-slate-400">{{ $s['nomor_urut'] }}</td>
                                            <td class="py-2.5 px-3 font-mono text-slate-600">{{ $s['nis'] }}</td>
                                            <td class="py-2.5 px-3 font-bold text-slate-800">{{ $s['nama_lengkap'] }}</td>
                                            <td class="py-2.5 px-3 text-center font-semibold text-slate-500">{{ $s['items_graded'] }}</td>
                                            <td class="py-2.5 px-3 text-center">
                                                @if($s['status'] === 'Selesai')
                                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-emerald-100 text-emerald-800 border border-emerald-200">
                                                        ✓ Selesai
                                                    </span>
                                                @elseif($s['status'] === 'Sebagian')
                                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-amber-100 text-amber-800 border border-amber-200">
                                                        ⏳ Sebagian
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-md font-bold text-[10px] bg-slate-100 text-slate-500 border border-slate-200">
                                                        — Belum
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>
            </div>

            @endif

        </div>
    </div>
</x-app-layout>
