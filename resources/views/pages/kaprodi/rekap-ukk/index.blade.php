<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Rekapitulasi Nilai UKK</h2>
    </x-slot>

    <div class="py-6 w-full">
        <div class="w-full px-4 sm:px-6 lg:px-8">

            {{-- Page Header --}}
            <div class="bg-gradient-to-r from-orange-500 to-amber-600 rounded-2xl p-6 mb-6 text-white shadow-xl">
                <h3 class="text-xl font-black">📊 Rekapitulasi Nilai UKK</h3>
                <p class="text-orange-100 text-sm mt-1">Hasil penilaian UKK per siswa berdasarkan instrumen penilaian</p>
            </div>

            {{-- Filter Card --}}
            <form method="GET" action="{{ request()->url() }}" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-5 mb-6">
                <div class="flex flex-wrap gap-4 items-end">
                    {{-- Pilih UKK --}}
                    <div class="flex-1 min-w-48">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Pilih UKK</label>
                        <select name="ujian_id" id="ujian_select"
                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-orange-400 focus:border-orange-400"
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
                    @if($ujian && $rombels->isNotEmpty())
                    <div class="flex-1 min-w-44">
                        <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider mb-1.5">Filter Kelas</label>
                        <select name="rombel_id"
                            class="w-full rounded-xl border-gray-200 text-sm focus:ring-orange-400 focus:border-orange-400">
                            <option value="">Semua Kelas</option>
                            @foreach($rombels as $r)
                                <option value="{{ $r->id }}" {{ $selectedRombelId == $r->id ? 'selected' : '' }}>
                                    {{ $r->kelas->nama_kelas ?? ('Rombel ' . $r->id) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    {{-- Apply button --}}
                    <button type="submit"
                        class="px-5 py-2.5 bg-orange-500 hover:bg-orange-400 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                        Tampilkan
                    </button>

                    {{-- Export button --}}
                    @if($ujian && $rekap->isNotEmpty())
                    <a href="{{ request()->url() }}/export?{{ http_build_query(['ujian_id' => $selectedUjianId, 'rombel_id' => $selectedRombelId]) }}"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-sm transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Export Excel
                    </a>
                    @endif
                </div>
            </form>

            @if(!$ujian)
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-4xl mb-3">📋</p>
                    <p class="text-gray-500 font-semibold">Pilih UKK terlebih dahulu untuk melihat rekapitulasi.</p>
                </div>
            @elseif($rekap->isEmpty())
                <div class="bg-white rounded-2xl border-2 border-dashed border-gray-200 p-16 text-center">
                    <p class="text-4xl mb-3">🎓</p>
                    <p class="text-gray-500 font-semibold">Belum ada siswa yang terdaftar pada ujian ini.</p>
                </div>
            @else

            {{-- Stat Summary --}}
            @php
                $totalSiswa   = $rekap->count();
                $selesai      = $rekap->filter(fn($r) => $r['is_complete'])->count();
                $belumSelesai = $totalSiswa - $selesai;
                $nilaiRataAll = $rekap->filter(fn($r) => $r['nilai_akhir'] !== null)->avg('nilai_akhir');
            @endphp
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-black text-gray-900">{{ $totalSiswa }}</p>
                    <p class="text-xs font-semibold text-gray-400 mt-0.5">Total Siswa</p>
                </div>
                <div class="bg-emerald-50 rounded-2xl border border-emerald-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-black text-emerald-700">{{ $selesai }}</p>
                    <p class="text-xs font-semibold text-emerald-500 mt-0.5">Sudah Dinilai</p>
                </div>
                <div class="bg-orange-50 rounded-2xl border border-orange-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-black text-orange-700">{{ $belumSelesai }}</p>
                    <p class="text-xs font-semibold text-orange-500 mt-0.5">Belum Selesai</p>
                </div>
                <div class="bg-violet-50 rounded-2xl border border-violet-200 shadow-sm p-4 text-center">
                    <p class="text-2xl font-black text-violet-700">{{ $nilaiRataAll !== null ? number_format($nilaiRataAll, 1) : '—' }}</p>
                    <p class="text-xs font-semibold text-violet-500 mt-0.5">Rata-rata Nilai Akhir</p>
                </div>
            </div>

            {{-- Table --}}
            @php
                $instrumens = $ujian->instrumens;
                $hasMultiInstrumen = $instrumens->count() > 1;
            @endphp

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <div>
                        <h4 class="font-black text-gray-800">{{ $ujian->nama_ujian }}</h4>
                        <p class="text-xs text-gray-400 mt-0.5">
                            {{ $ujian->jurusan }}
                            @if($ujian->tahunPelajaran) &middot; {{ $ujian->tahunPelajaran->tahun }} @endif
                            @if($selectedRombelId)
                                &middot; Kelas: {{ $rombels->firstWhere('id', $selectedRombelId)?->kelas->nama_kelas ?? '-' }}
                            @endif
                        </p>
                    </div>
                    <span class="text-xs font-bold text-gray-400">{{ $totalSiswa }} siswa</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm min-w-[640px]">
                        <thead>
                            {{-- Multi-row header if multiple instrumens --}}
                            @if($hasMultiInstrumen)
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-2.5 text-left text-xs font-black text-gray-500 uppercase tracking-wide" rowspan="2">No</th>
                                <th class="px-4 py-2.5 text-left text-xs font-black text-gray-500 uppercase tracking-wide" rowspan="2">NIS</th>
                                <th class="px-4 py-2.5 text-left text-xs font-black text-gray-500 uppercase tracking-wide" rowspan="2">Nama Siswa</th>
                                <th class="px-4 py-2.5 text-left text-xs font-black text-gray-500 uppercase tracking-wide" rowspan="2">Kelas</th>
                                @foreach($instrumens as $ins)
                                <th class="px-3 py-2.5 text-center text-xs font-black text-violet-600 uppercase tracking-wide border-l border-gray-200"
                                    colspan="3">
                                    {{ $ins->nama_instrumen }}
                                </th>
                                @endforeach
                                <th class="px-4 py-2.5 text-center text-xs font-black text-orange-600 uppercase tracking-wide border-l border-gray-200" rowspan="2">
                                    Nilai Akhir
                                </th>
                                <th class="px-4 py-2.5 text-center text-xs font-black text-gray-500 uppercase tracking-wide" rowspan="2">Status</th>
                            </tr>
                            <tr class="bg-gray-50 border-b border-gray-200 text-[10px] font-bold text-gray-400 uppercase">
                                @foreach($instrumens as $ins)
                                <th class="px-3 py-1.5 text-center border-l border-gray-200 text-blue-500">Skor P</th>
                                <th class="px-3 py-1.5 text-center text-emerald-600">Skor K</th>
                                <th class="px-3 py-1.5 text-center text-violet-600">Nilai</th>
                                @endforeach
                            </tr>
                            @else
                            {{-- Single instrumen: flat header --}}
                            <tr class="bg-gray-50 border-b border-gray-200">
                                <th class="px-4 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wide">No</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wide">NIS</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wide">Nama Siswa</th>
                                <th class="px-4 py-3 text-left text-xs font-black text-gray-500 uppercase tracking-wide">Kelas</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-blue-600 uppercase tracking-wide">Skor Pengetahuan</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-emerald-600 uppercase tracking-wide">Skor Keterampilan</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-orange-600 uppercase tracking-wide">Nilai Akhir</th>
                                <th class="px-4 py-3 text-center text-xs font-black text-gray-500 uppercase tracking-wide">Status</th>
                            </tr>
                            @endif
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rekap as $i => $row)
                            <tr class="hover:bg-orange-50/30 transition-colors {{ $row['is_complete'] ? '' : 'opacity-75' }}">
                                <td class="px-4 py-3 text-xs text-gray-400 font-semibold">{{ $i + 1 }}</td>
                                <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ $row['siswa']->nis ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-black text-gray-900 text-sm">{{ $row['siswa']->nama_lengkap }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-0.5 bg-orange-50 text-orange-700 text-[10px] font-bold border border-orange-100 rounded-lg">
                                        {{ $row['rombel_label'] }}
                                    </span>
                                </td>

                                {{-- Instrumen scores --}}
                                @foreach($row['instrumen_scores'] as $score)
                                <td class="px-3 py-3 text-center border-l border-gray-100">
                                    @if($score['skor_p'] !== null)
                                        <span class="text-sm font-bold text-blue-700">{{ $score['skor_p'] }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($score['skor_k'] !== null)
                                        <span class="text-sm font-bold text-emerald-700">{{ $score['skor_k'] }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if($score['nilai'] !== null)
                                        <span class="text-sm font-black text-violet-700">{{ $score['nilai'] }}</span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>
                                @endforeach

                                {{-- Nilai akhir --}}
                                <td class="px-4 py-3 text-center border-l border-gray-100">
                                    @if($row['nilai_akhir'] !== null)
                                        @php
                                            $na = $row['nilai_akhir'];
                                            $naColor = $na >= 80 ? 'bg-emerald-100 text-emerald-800 border-emerald-200'
                                                     : ($na >= 65 ? 'bg-blue-100 text-blue-800 border-blue-200'
                                                     : 'bg-red-100 text-red-800 border-red-200');
                                        @endphp
                                        <span class="inline-block px-2.5 py-0.5 rounded-lg border font-black text-sm {{ $naColor }}">
                                            {{ $na }}
                                        </span>
                                    @else
                                        <span class="text-gray-300 text-xs">—</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-4 py-3 text-center">
                                    @if($row['is_complete'])
                                        <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-lg border border-emerald-200">✓ Selesai</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-orange-50 text-orange-600 text-[10px] font-black rounded-lg border border-orange-200">Belum</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>

                        {{-- Footer averages --}}
                        @php
                            $instrumenCount = $instrumens->count();
                            $colsBeforeScores = 4; // No, NIS, Nama, Kelas
                            $colsPerInstrumen = 3; // Skor P, Skor K, Nilai
                        @endphp
                        <tfoot>
                            <tr class="bg-orange-50 border-t-2 border-orange-200">
                                <td colspan="{{ $colsBeforeScores }}" class="px-4 py-3 text-xs font-black text-gray-600 text-right">
                                    Rata-rata Kelas:
                                </td>
                                @foreach($instrumens as $ins)
                                @php
                                    $idx = $loop->index;
                                    $avgSkorP = $rekap->filter(fn($r) => $r['instrumen_scores'][$idx]['skor_p'] !== null)
                                        ->avg(fn($r) => $r['instrumen_scores'][$idx]['skor_p']);
                                    $avgSkorK = $rekap->filter(fn($r) => $r['instrumen_scores'][$idx]['skor_k'] !== null)
                                        ->avg(fn($r) => $r['instrumen_scores'][$idx]['skor_k']);
                                    $avgNilai = $rekap->filter(fn($r) => $r['instrumen_scores'][$idx]['nilai'] !== null)
                                        ->avg(fn($r) => $r['instrumen_scores'][$idx]['nilai']);
                                @endphp
                                <td class="px-3 py-3 text-center border-l border-orange-200 text-xs font-black text-blue-700">
                                    {{ $avgSkorP !== null ? number_format($avgSkorP, 1) : '—' }}
                                </td>
                                <td class="px-3 py-3 text-center text-xs font-black text-emerald-700">
                                    {{ $avgSkorK !== null ? number_format($avgSkorK, 1) : '—' }}
                                </td>
                                <td class="px-3 py-3 text-center text-xs font-black text-violet-700">
                                    {{ $avgNilai !== null ? number_format($avgNilai, 1) : '—' }}
                                </td>
                                @endforeach
                                <td class="px-4 py-3 text-center border-l border-orange-200">
                                    <span class="text-sm font-black text-orange-700">
                                        {{ $nilaiRataAll !== null ? number_format($nilaiRataAll, 1) : '—' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center text-xs font-semibold text-gray-500">
                                    {{ $selesai }}/{{ $totalSiswa }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            @endif
        </div>
    </div>
</x-app-layout>
