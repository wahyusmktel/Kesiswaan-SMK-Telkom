<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Detail Leger Nilai</h2>
    </x-slot>

    @php
        $scores = $detail['subjects']->pluck('nilai')->filter(fn ($v) => $v !== null)->values();
        $labels = $detail['subjects']->pluck('mapel')->values();
        $pointCount = max(3, $detail['subjects']->count());
        $center = 120;
        $radius = 92;
        $points = [];
        foreach ($detail['subjects']->values() as $i => $subject) {
            $angle = (2 * pi() * $i / $pointCount) - (pi() / 2);
            $valueRadius = (($subject['nilai'] ?? 0) / 100) * $radius;
            $points[] = ($center + cos($angle) * $valueRadius) . ',' . ($center + sin($angle) * $valueRadius);
        }
    @endphp

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <a href="{{ route('kesiswaan.leger-nilai.index', ['ujian_id' => $ujian->id, 'kelas' => $detail['kelas']]) }}" class="text-sm font-semibold text-red-600 hover:text-red-700">Kembali ke leger</a>
                    <h1 class="text-2xl font-bold text-gray-900 mt-1">{{ $siswa->nama_lengkap }}</h1>
                    <p class="text-sm text-gray-500">{{ $siswa->nis }} | {{ $detail['kelas'] }} | {{ $ujian->nama_ujian }}</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('kesiswaan.leger-nilai.siswa.export', ['siswa' => $siswa->id, 'ujian_id' => $ujian->id]) }}" class="px-4 py-2 rounded-lg bg-green-600 hover:bg-green-500 text-white font-semibold text-sm">Export Excel</a>
                    <a href="{{ route('kesiswaan.leger-nilai.siswa.pdf', ['siswa' => $siswa->id, 'ujian_id' => $ujian->id]) }}" target="_blank" class="px-4 py-2 rounded-lg bg-gray-900 hover:bg-gray-800 text-white font-semibold text-sm">Print PDF</a>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Rata-rata</div><div class="text-2xl font-bold text-gray-900 mt-1">{{ $detail['average'] !== null ? number_format($detail['average'], 2, ',', '.') : '-' }}</div></div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Tertinggi</div><div class="text-2xl font-bold text-emerald-700 mt-1">{{ $detail['highest'] !== null ? number_format($detail['highest'], 2, ',', '.') : '-' }}</div></div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Terendah</div><div class="text-2xl font-bold text-red-700 mt-1">{{ $detail['lowest'] !== null ? number_format($detail['lowest'], 2, ',', '.') : '-' }}</div></div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Kelengkapan</div><div class="text-2xl font-bold text-indigo-700 mt-1">{{ $detail['complete_count'] }}/{{ $detail['subject_count'] }}</div></div>
                <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm"><div class="text-xs font-bold uppercase text-gray-500">Status</div><div class="text-lg font-bold text-gray-900 mt-2">{{ $detail['average'] >= 75 ? 'Baik' : 'Perlu Fokus' }}</div></div>
            </div>

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-5">Grafik Nilai Mata Pelajaran</h3>
                    <div class="space-y-4">
                        @foreach ($detail['subjects'] as $subject)
                            @php $value = $subject['nilai'] ?? 0; @endphp
                            <div>
                                <div class="flex items-center justify-between text-sm mb-1">
                                    <span class="font-semibold text-gray-700">{{ $subject['mapel'] }}</span>
                                    <span class="font-bold text-gray-900">{{ $subject['nilai'] !== null ? number_format($subject['nilai'], 2, ',', '.') : '-' }}</span>
                                </div>
                                <div class="h-4 rounded-full bg-gray-100 overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-sky-300 via-indigo-300 to-rose-300" style="width: {{ min(100, $value) }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h3 class="font-bold text-gray-900 mb-4">Profil Akademik</h3>
                    <svg viewBox="0 0 240 240" class="w-full max-w-[280px] mx-auto">
                        <circle cx="120" cy="120" r="92" fill="#F8FAFC" stroke="#E2E8F0" />
                        <circle cx="120" cy="120" r="62" fill="none" stroke="#E2E8F0" />
                        <circle cx="120" cy="120" r="31" fill="none" stroke="#E2E8F0" />
                        <polygon points="{{ implode(' ', $points) }}" fill="rgba(244, 63, 94, .22)" stroke="#F43F5E" stroke-width="3" />
                        @foreach ($detail['subjects']->values() as $i => $subject)
                            @php
                                $angle = (2 * pi() * $i / $pointCount) - (pi() / 2);
                                $x = $center + cos($angle) * 104;
                                $y = $center + sin($angle) * 104;
                            @endphp
                            <text x="{{ $x }}" y="{{ $y }}" text-anchor="middle" dominant-baseline="middle" font-size="9" fill="#475569">{{ \Illuminate\Support\Str::limit($subject['mapel'], 10) }}</text>
                        @endforeach
                    </svg>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                <h3 class="font-bold text-gray-900 mb-2">Analisa</h3>
                <p class="text-sm text-gray-600 leading-relaxed">{{ $detail['recommendation'] }}</p>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="font-bold text-gray-900">Rincian Nilai</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                            <tr>
                                <th class="px-6 py-3 text-left font-bold">Mata Pelajaran</th>
                                <th class="px-6 py-3 text-center font-bold">Benar</th>
                                <th class="px-6 py-3 text-center font-bold">Jumlah Soal</th>
                                <th class="px-6 py-3 text-right font-bold">Nilai</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($detail['subjects'] as $subject)
                                <tr>
                                    <td class="px-6 py-3 font-semibold text-gray-900">{{ $subject['mapel'] }}</td>
                                    <td class="px-6 py-3 text-center">{{ $subject['jumlah_benar'] ?? '-' }}</td>
                                    <td class="px-6 py-3 text-center">{{ $subject['jumlah_soal'] ?? '-' }}</td>
                                    <td class="px-6 py-3 text-right font-bold">{{ $subject['nilai'] !== null ? number_format($subject['nilai'], 2, ',', '.') : '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
