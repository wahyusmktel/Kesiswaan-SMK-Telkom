<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h2 class="font-bold text-2xl text-gray-800 leading-tight">Rekapitulasi Hasil Pemilihan OSIS</h2>
                <p class="text-xs text-gray-500 mt-1">Laporan resmi perolehan suara paslon, audit bilik, dan statistik partisipasi</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('public.osis.live-count') }}" target="_blank" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-xl text-xs font-bold flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4 text-rose-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <span>Live Count Publik</span>
                </a>
                <a href="{{ route('osis.pilketos.rekapitulasi.berita-acara') }}" target="_blank" class="px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm flex items-center gap-1.5 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <span>Cetak Berita Acara</span>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">

            @if(session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-semibold flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Key Indicators -->
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-gray-400">Total DPT</span>
                    <div class="text-3xl font-black text-gray-900 mt-2 font-mono">{{ number_format($totalDpt) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Pemilih terdaftar sah</div>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-rose-600">Suara Masuk</span>
                    <div class="text-3xl font-black text-rose-600 mt-2 font-mono">{{ number_format($totalVotes) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Total kertas suara digital sah</div>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">Partisipasi (Turnout)</span>
                    <div class="text-3xl font-black text-indigo-600 mt-2 font-mono">{{ $turnout }}%</div>
                    <div class="text-[11px] text-gray-500 mt-1">Tingkat kehadiran pemilih</div>
                </div>
                <div class="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm">
                    <span class="text-xs font-bold uppercase tracking-wider text-amber-600">Belum Menggunakan Hak</span>
                    <div class="text-3xl font-black text-amber-600 mt-2 font-mono">{{ number_format($totalDpt - $totalVotes) }}</div>
                    <div class="text-[11px] text-gray-500 mt-1">Pemilih yang belum scan barcode</div>
                </div>
            </div>

            <!-- Candidate Standings Card -->
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="font-bold text-lg text-gray-900">Perolehan Suara Pasangan Calon</h3>
                        <p class="text-xs text-gray-500">Urutan paslon berdasarkan nomor urut resmi</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 text-rose-600 flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-rose-600 animate-pulse"></span>
                        Status: {{ $periode?->is_active ? 'Pemilu Sedang Berlangsung' : 'Selesai / Nonaktif' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @forelse($kandidats as $k)
                        <div class="p-6 rounded-2xl border-2 {{ $loop->first && $k->votes_count > 0 ? 'border-rose-400 bg-rose-50/20' : 'border-gray-100 bg-gray-50/50' }} flex flex-col justify-between space-y-4">
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <span class="w-10 h-10 rounded-xl bg-gray-900 text-white font-black text-lg flex items-center justify-center">
                                        0{{ $k->nomor_urut }}
                                    </span>
                                    <span class="text-2xl font-black text-rose-600 font-mono">
                                        {{ $k->percentage }}%
                                    </span>
                                </div>
                                <div class="flex items-center gap-3">
                                    <img src="{{ $k->foto_paslon_url }}" alt="{{ $k->nama_ketua }}" class="w-14 h-14 rounded-xl object-cover border">
                                    <div>
                                        <div class="font-bold text-sm text-gray-900">{{ $k->nama_ketua }}</div>
                                        @if($k->nama_wakil)
                                            <div class="text-xs text-gray-500 font-medium">&amp; {{ $k->nama_wakil }}</div>
                                        @endif
                                        <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $k->kelas_ketua }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="space-y-2">
                                <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                                    <div class="bg-rose-600 h-3 rounded-full transition-all duration-700" style="width: {{ $k->percentage }}%"></div>
                                </div>
                                <div class="flex items-center justify-between text-xs font-mono font-bold text-gray-600">
                                    <span>{{ number_format($k->votes_count) }} Suara</span>
                                    <span>{{ $k->percentage }}% dari total</span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-span-3 text-center py-8 text-gray-400 text-xs">
                            Belum ada kandidat paslon terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Partisipasi per Kelas & Bilik Suara -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                <!-- Breakdown Partisipasi Kelas -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
                    <h3 class="font-bold text-base text-gray-900">Partisipasi Berdasarkan Kelas</h3>
                    <p class="text-xs text-gray-500">Tingkat kehadiran siswa per rombongan belajar</p>

                    <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                        @forelse($classBreakdown as $c)
                            <div class="p-3 rounded-2xl bg-gray-50 border border-gray-100 space-y-2">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-bold text-gray-800">{{ $c->kelas }}</span>
                                    <span class="font-mono font-bold text-gray-600">{{ $c->voted }} / {{ $c->total }} ({{ $c->percentage }}%)</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                    <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $c->percentage }}%"></div>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-gray-400 py-4 text-center">Belum ada data pemilih per kelas.</p>
                        @endforelse
                    </div>
                </div>

                <!-- Breakdown Suara Masuk per Bilik & Log Suara Terbaru -->
                <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-6">
                    <div>
                        <h3 class="font-bold text-base text-gray-900">Aktivitas Bilik Suara</h3>
                        <p class="text-xs text-gray-500">Distribusi kertas suara digital per bilik scanner</p>

                        <div class="grid grid-cols-2 gap-3 mt-4">
                            @forelse($bilikBreakdown as $b)
                                <div class="p-3 rounded-2xl bg-rose-50/50 border border-rose-100 text-center">
                                    <div class="text-xs font-bold text-gray-700">{{ $b->bilik_id ?? 'Bilik Digital' }}</div>
                                    <div class="text-xl font-black text-rose-600 font-mono mt-1">{{ number_format($b->count) }}</div>
                                    <div class="text-[10px] text-gray-400">Surat Suara Terpindai</div>
                                </div>
                            @empty
                                <div class="col-span-2 text-center py-4 text-xs text-gray-400">
                                    Belum ada pemilih yang menyelesaikan bilik suara.
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="border-t border-gray-100 pt-4">
                        <h4 class="font-bold text-xs uppercase tracking-wider text-gray-400 mb-3">Audit Suara Masuk Terakhir (Anonim)</h4>
                        <div class="space-y-2 max-h-48 overflow-y-auto text-xs">
                            @forelse($recentVotes as $rv)
                                <div class="flex items-center justify-between p-2 rounded-xl bg-gray-50 border border-gray-100">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        <span class="font-mono text-gray-500">{{ $rv->voted_at?->format('H:i:s') }} WIB</span>
                                        <span class="text-gray-400">&bull;</span>
                                        <span class="font-bold text-gray-700">{{ $rv->bilik_id }}</span>
                                    </div>
                                    <span class="px-2 py-0.5 rounded bg-gray-200 text-gray-700 font-bold font-mono text-[10px]">
                                        Tersimpan Aman
                                    </span>
                                </div>
                            @empty
                                <p class="text-[11px] text-gray-400 text-center py-2">Belum ada suara terekam.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

            </div>

            <!-- Danger Zone: Reset Pemilu -->
            <div class="bg-rose-50/70 border border-rose-200 rounded-3xl p-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h4 class="font-bold text-sm text-rose-900">Reset Semua Suara Masuk (Pemilihan Ulang)</h4>
                    <p class="text-xs text-rose-700 mt-1">Tindakan ini akan mengosongkan seluruh surat suara yang telah masuk dan mereset status DPT ke "Belum Memilih".</p>
                </div>
                <form action="{{ route('osis.pilketos.rekapitulasi.reset-votes') }}" method="POST" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS SEMUA SUARA MASUK dan mereset DPT ke belum memilih?')">
                    @csrf
                    <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white rounded-xl text-xs font-bold shadow-sm transition-colors whitespace-nowrap">
                        Reset Suara Pemilu
                    </button>
                </form>
            </div>

        </div>
    </div>
</x-app-layout>
