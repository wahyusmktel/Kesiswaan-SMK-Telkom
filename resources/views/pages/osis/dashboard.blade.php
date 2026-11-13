<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Dashboard Pengurus OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Pusat Pengelolaan Halaman Khusus OSIS &amp; E-Voting Pemilihan Ketua OSIS</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('public.osis.index') }}" target="_blank" class="px-4 py-2 bg-white border border-gray-300 rounded-xl text-xs font-bold text-gray-700 hover:bg-gray-50 shadow-sm flex items-center gap-1.5 transition-colors">
                    <span>Lihat Web Publik OSIS</span>
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                </a>
                <a href="{{ route('pilketos.booth') }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 rounded-xl text-xs font-bold text-white shadow-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/></svg>
                    <span>Buka Bilik Suara</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            <!-- Banner Sambutan OSIS -->
            <div class="relative overflow-hidden bg-gradient-to-r from-red-600 via-rose-600 to-slate-900 rounded-3xl p-8 text-white shadow-xl">
                <div class="absolute right-0 top-0 w-80 h-80 bg-white/10 rounded-full -mr-20 -mt-20 blur-3xl pointer-events-none"></div>
                <div class="relative z-10 grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                    <div class="md:col-span-8 space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/20 text-white text-xs font-bold uppercase tracking-wider backdrop-blur-sm">
                            <span>🏛️</span> OSIS SMK Telkom Lampung
                        </div>
                        <h3 class="text-3xl font-black">Selamat Datang, Pengurus OSIS!</h3>
                        <p class="text-white/80 text-sm max-w-xl font-normal leading-relaxed">
                            Kelola profil organisasi, data pengurus, agenda kegiatan, hingga sistem pemilihan ketua OSIS berbasis barcode sekali pakai dengan akurat dan anti-kecurangan.
                        </p>
                    </div>
                    <div class="md:col-span-4 flex flex-col items-start md:items-end gap-3">
                        <div class="bg-black/30 backdrop-blur-md px-5 py-3 rounded-2xl border border-white/15 text-left md:text-right">
                            <span class="text-[10px] text-white/70 uppercase tracking-widest font-bold block">Status Pemilu Saat Ini</span>
                            <span class="font-black text-lg text-amber-300 uppercase">
                                {{ $activePeriode ? str_replace('_', ' ', $activePeriode->status) : 'DRAFT' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats 4 Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Pengurus Aktif</span>
                        <div class="text-2xl font-black text-gray-800 mt-1">{{ $totalPengurus }}</div>
                        <span class="text-[11px] text-gray-400">Periode {{ $setting->periode_aktif }}</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Agenda Kegiatan</span>
                        <div class="text-2xl font-black text-gray-800 mt-1">{{ $totalAgenda }}</div>
                        <span class="text-[11px] text-emerald-600 font-semibold">Tampil di web publik</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">DPT Terdaftar</span>
                        <div class="text-2xl font-black text-gray-800 mt-1">{{ $totalDpt }}</div>
                        <span class="text-[11px] text-gray-400">{{ $totalVotes }} Suara Masuk</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/></svg>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">Partisipasi Suara</span>
                        <div class="text-2xl font-black text-rose-600 mt-1">{{ $turnout }}%</div>
                        <span class="text-[11px] text-gray-400">{{ $kandidats->count() }} Paslon Bertarung</span>
                    </div>
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                    </div>
                </div>
            </div>

            <!-- E-Voting Section Preview -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-gray-100 pb-5">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-rose-500 animate-pulse"></span>
                            <h3 class="font-black text-xl text-gray-800">E-Voting Pilketos {{ $activePeriode?->tahun_pelajaran ?? '2026/2027' }}</h3>
                        </div>
                        <p class="text-xs text-gray-500 mt-0.5">Pemantauan rekapitulasi suara calon ketua OSIS secara langsung</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('osis.pilketos.dpt.print-cards') }}" target="_blank" class="px-3.5 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold transition-colors">
                            Cetak Kartu Barcode
                        </a>
                        <a href="{{ route('osis.pilketos.rekapitulasi.index') }}" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold transition-colors">
                            Rekapitulasi Lengkap
                        </a>
                    </div>
                </div>

                <!-- Paslon Live Mini Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($kandidats as $k)
                        @php
                            $pct = $totalVotes > 0 ? round(($k->votes_count / $totalVotes) * 100, 1) : 0;
                        @endphp
                        <div class="p-5 rounded-2xl border border-gray-200 bg-gray-50 flex flex-col justify-between space-y-4">
                            <div class="flex items-start justify-between">
                                <span class="w-10 h-10 rounded-xl bg-rose-600 text-white font-black text-lg flex items-center justify-center shadow-md">
                                    0{{ $k->nomor_urut }}
                                </span>
                                <div class="text-right">
                                    <div class="text-2xl font-black text-gray-800">{{ $pct }}%</div>
                                    <span class="text-xs text-gray-500 font-bold">{{ $k->votes_count }} suara</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-3">
                                <img src="{{ $k->foto_paslon_url }}" alt="{{ $k->nama_ketua }}" class="w-14 h-14 rounded-2xl object-cover border border-gray-300 shrink-0">
                                <div class="overflow-hidden">
                                    <h4 class="font-bold text-sm text-gray-800 truncate">{{ $k->nama_ketua }}</h4>
                                    @if($k->nama_wakil)
                                        <div class="text-xs text-gray-600 truncate">&amp; {{ $k->nama_wakil }}</div>
                                    @endif
                                    <span class="text-[10px] text-gray-400 font-mono">{{ $k->kelas_ketua }}</span>
                                </div>
                            </div>

                            <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                <div class="bg-rose-600 h-2 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-gray-400 text-xs">
                            Belum ada kandidat paslon pada periode ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Quick Action Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Agenda Terbaru -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="font-bold text-gray-800 text-base">Agenda &amp; Proker Terkini</h4>
                        <a href="{{ route('osis.agenda.index') }}" class="text-xs font-bold text-rose-600 hover:text-rose-700">Kelola Semua &rarr;</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentAgendas as $ag)
                            <div class="p-3.5 rounded-xl bg-gray-50 border border-gray-100 flex items-center justify-between">
                                <div>
                                    <div class="font-bold text-xs text-gray-800">{{ $ag->judul }}</div>
                                    <span class="text-[10px] text-gray-400">{{ $ag->tanggal_mulai->format('d M Y') }} &bull; {{ $ag->lokasi ?? 'SMK Telkom' }}</span>
                                </div>
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $ag->status === 'berlangsung' ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-200 text-gray-600' }}">
                                    {{ $ag->status }}
                                </span>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 text-center py-4">Belum ada agenda terdaftar.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Modul Manajemen Shortcut -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h4 class="font-bold text-gray-800 text-base">Pusat Navigasi Fitur OSIS</h4>
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('osis.profile.edit') }}" class="p-4 rounded-2xl bg-gray-50 hover:bg-rose-50 border border-gray-100 hover:border-rose-200 transition-colors group">
                            <span class="text-lg">⚙️</span>
                            <div class="font-bold text-xs text-gray-800 group-hover:text-rose-600 mt-1">Profil &amp; Visi Misi</div>
                            <span class="text-[10px] text-gray-400">Atur info web publik</span>
                        </a>

                        <a href="{{ route('osis.pengurus.index') }}" class="p-4 rounded-2xl bg-gray-50 hover:bg-rose-50 border border-gray-100 hover:border-rose-200 transition-colors group">
                            <span class="text-lg">👥</span>
                            <div class="font-bold text-xs text-gray-800 group-hover:text-rose-600 mt-1">Pengurus OSIS</div>
                            <span class="text-[10px] text-gray-400">Kelola BPH &amp; Sekbid</span>
                        </a>

                        <a href="{{ route('osis.pilketos.kandidat.index') }}" class="p-4 rounded-2xl bg-gray-50 hover:bg-rose-50 border border-gray-100 hover:border-rose-200 transition-colors group">
                            <span class="text-lg">🎯</span>
                            <div class="font-bold text-xs text-gray-800 group-hover:text-rose-600 mt-1">Kandidat Paslon</div>
                            <span class="text-[10px] text-gray-400">Pilih dari data siswa</span>
                        </a>

                        <a href="{{ route('osis.pilketos.dpt.index') }}" class="p-4 rounded-2xl bg-gray-50 hover:bg-rose-50 border border-gray-100 hover:border-rose-200 transition-colors group">
                            <span class="text-lg">🪪</span>
                            <div class="font-bold text-xs text-gray-800 group-hover:text-rose-600 mt-1">DPT &amp; Barcode</div>
                            <span class="text-[10px] text-gray-400">Generate &amp; cetak kartu</span>
                        </a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
