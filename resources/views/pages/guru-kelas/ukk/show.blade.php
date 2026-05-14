<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Daftar Siswa — {{ $ujian->nama_ujian }}</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Header --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-1">
                            <a href="{{ route('guru-kelas.penilaian-ukk.index') }}"
                                class="w-8 h-8 bg-white/20 hover:bg-white/30 rounded-lg flex items-center justify-center transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                </svg>
                            </a>
                            <h3 class="text-xl font-black">👨‍🎓 Daftar Siswa</h3>
                        </div>
                        <p class="text-orange-100 text-sm">
                            <span class="font-bold">{{ $ujian->nama_ujian }}</span>
                            &middot; {{ $ujian->jurusan }}
                            @if($ujian->tahunPelajaran) &middot; {{ $ujian->tahunPelajaran->tahun }} @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-3 bg-white/20 rounded-xl px-4 py-2 shrink-0">
                        @php
                            $totalSelesai = $siswas->filter(fn($s) =>
                                ($gradedP[$s->id] ?? 0) >= $totalSoal &&
                                ($gradedK[$s->id] ?? 0) >= $totalIndikator
                            )->count();
                        @endphp
                        <div class="text-center">
                            <p class="text-2xl font-black">{{ $totalSelesai }}/{{ $siswas->count() }}</p>
                            <p class="text-orange-100 text-xs font-semibold">Sudah Dinilai</p>
                        </div>
                    </div>
                </div>

                @if($ujian->instrumens->isEmpty())
                    <div class="mt-4 bg-white/20 rounded-xl px-4 py-3 text-sm font-semibold text-orange-50">
                        ⚠️ Belum ada instrumen penilaian yang diset untuk ujian ini. Hubungi Kaprodi.
                    </div>
                @endif
            </div>

            {{-- Search --}}
            <div class="mb-4" x-data="{ search: '' }">
                <div class="relative max-w-sm">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/>
                    </svg>
                    <input x-model="search" type="text" placeholder="Cari nama atau NIS…"
                        class="w-full pl-9 pr-4 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-orange-400 bg-white">
                </div>

                {{-- Student list --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-3 mt-4">
                    @foreach($siswas as $siswa)
                        @php
                            $pDone = ($gradedP[$siswa->id] ?? 0) >= $totalSoal;
                            $kDone = ($gradedK[$siswa->id] ?? 0) >= $totalIndikator;
                            $isComplete = ($totalSoal === 0 || $pDone) && ($totalIndikator === 0 || $kDone);
                            $pPct = $totalSoal > 0 ? round(($gradedP[$siswa->id] ?? 0) / $totalSoal * 100) : 100;
                            $kPct = $totalIndikator > 0 ? round(($gradedK[$siswa->id] ?? 0) / $totalIndikator * 100) : 100;
                        @endphp
                        <div x-show="!search || '{{ strtolower($siswa->nama_lengkap) }}'.includes(search.toLowerCase()) || '{{ $siswa->nis }}'.includes(search)"
                            class="bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-md transition-all overflow-hidden">

                            {{-- Top strip --}}
                            <div class="h-1.5 {{ $isComplete ? 'bg-emerald-400' : 'bg-orange-300' }}"></div>

                            <div class="p-4">
                                <div class="flex items-start gap-3">
                                    {{-- Avatar --}}
                                    <div class="w-11 h-11 rounded-xl overflow-hidden bg-orange-100 flex items-center justify-center shrink-0">
                                        @if($siswa->user?->avatar)
                                            <img src="{{ $siswa->user->avatar }}" class="w-full h-full object-cover" alt="{{ $siswa->nama_lengkap }}">
                                        @else
                                            <span class="text-orange-600 font-black text-base">{{ strtoupper(substr($siswa->nama_lengkap, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="font-black text-gray-900 text-sm truncate">{{ $siswa->nama_lengkap }}</p>
                                        <p class="text-xs text-gray-400">NIS: {{ $siswa->nis }}</p>
                                    </div>
                                    @if($isComplete)
                                        <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-lg border border-emerald-200 shrink-0">✓ Selesai</span>
                                    @else
                                        <span class="px-2 py-1 bg-orange-50 text-orange-600 text-[10px] font-black rounded-lg border border-orange-200 shrink-0">Belum</span>
                                    @endif
                                </div>

                                {{-- Progress mini bars --}}
                                @if($totalSoal > 0 || $totalIndikator > 0)
                                <div class="mt-3 space-y-1.5">
                                    @if($totalSoal > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-gray-400 w-20 shrink-0">Pengetahuan</span>
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-blue-400 rounded-full" style="width:{{ $pPct }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-blue-600 w-8 text-right">{{ $pPct }}%</span>
                                    </div>
                                    @endif
                                    @if($totalIndikator > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] text-gray-400 w-20 shrink-0">Keterampilan</span>
                                        <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                            <div class="h-full bg-emerald-400 rounded-full" style="width:{{ $kPct }}%"></div>
                                        </div>
                                        <span class="text-[10px] font-bold text-emerald-600 w-8 text-right">{{ $kPct }}%</span>
                                    </div>
                                    @endif
                                </div>
                                @endif

                                {{-- Action --}}
                                @if(!$ujian->instrumens->isEmpty())
                                <a href="{{ route('guru-kelas.penilaian-ukk.penilaian', [$ujian->id, $siswa->id]) }}"
                                    class="mt-3 flex items-center justify-center gap-2 w-full py-2 rounded-xl text-xs font-black transition-all
                                    {{ $isComplete
                                        ? 'bg-emerald-50 text-emerald-700 hover:bg-emerald-600 hover:text-white border border-emerald-200'
                                        : 'bg-orange-500 text-white hover:bg-orange-400' }}">
                                    {{ $isComplete ? '✏️ Edit Penilaian' : '📝 Nilai Sekarang' }}
                                </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <div x-show="search && {{ json_encode($siswas->count()) }} === 0"
                    class="text-center py-12 text-gray-400 text-sm italic">
                    Tidak ada siswa yang cocok.
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
