<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-gray-800 leading-tight">Leger Nilai Siswa</h2>
    </x-slot>

    <div class="py-6">
        <div class="w-full px-4 sm:px-6 lg:px-8 space-y-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Rekapitulasi Leger Nilai</h1>
                    <p class="text-sm text-gray-500 mt-1">Analisa nilai per kelas berdasarkan hasil import ujian semester.</p>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                <form method="GET" action="{{ route('kesiswaan.leger-nilai.index') }}" class="grid grid-cols-1 md:grid-cols-[1fr_220px_110px] gap-3">
                    <select name="ujian_id" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        @foreach ($ujians as $ujian)
                            <option value="{{ $ujian->id }}" {{ $selectedUjian?->id === $ujian->id ? 'selected' : '' }}>
                                {{ $ujian->nama_ujian }} - {{ $ujian->tahunPelajaran?->tahun }} {{ $ujian->semester }}
                            </option>
                        @endforeach
                    </select>
                    <select name="kelas" class="rounded-lg border-gray-300 focus:border-red-500 focus:ring-red-500">
                        @foreach ($kelasOptions as $item)
                            <option value="{{ $item }}" {{ $kelas === $item ? 'selected' : '' }}>{{ $item }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg bg-red-600 hover:bg-red-500 text-white font-semibold">Terapkan</button>
                </form>
            </div>

            @if ($selectedUjian && $kelas)
                <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Siswa</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $analysis['student_count'] }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Rata-rata Kelas</div>
                        <div class="text-2xl font-bold text-gray-900 mt-1">{{ $analysis['class_average'] !== null ? number_format($analysis['class_average'], 2, ',', '.') : '-' }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Tertinggi</div>
                        <div class="text-2xl font-bold text-emerald-700 mt-1">{{ $analysis['highest'] !== null ? number_format($analysis['highest'], 2, ',', '.') : '-' }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Terendah</div>
                        <div class="text-2xl font-bold text-red-700 mt-1">{{ $analysis['lowest'] !== null ? number_format($analysis['lowest'], 2, ',', '.') : '-' }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Kelengkapan</div>
                        <div class="text-2xl font-bold text-indigo-700 mt-1">{{ $analysis['complete_rate'] }}%</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 p-4 shadow-sm">
                        <div class="text-xs font-bold uppercase text-gray-500">Perlu Perhatian</div>
                        <div class="text-2xl font-bold text-amber-700 mt-1">{{ $analysis['risk_count'] }}</div>
                    </div>
                </div>

                <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                    <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-5">
                            <div>
                                <h3 class="font-bold text-gray-900">Rata-rata per Mata Pelajaran</h3>
                                <p class="text-xs text-gray-500">Peta kekuatan kelas {{ $kelas }}</p>
                            </div>
                        </div>
                        <div class="space-y-4">
                            @foreach ($analysis['mapel_averages'] as $item)
                                @php $value = $item['average'] ?? 0; @endphp
                                <div>
                                    <div class="flex items-center justify-between text-sm mb-1">
                                        <span class="font-semibold text-gray-700">{{ $item['nama'] }}</span>
                                        <span class="font-bold text-gray-900">{{ $item['average'] !== null ? number_format($item['average'], 2, ',', '.') : '-' }}</span>
                                    </div>
                                    <div class="h-3 rounded-full bg-gray-100 overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r from-rose-300 via-amber-300 to-emerald-300" style="width: {{ min(100, $value) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="font-bold text-gray-900 mb-5">Distribusi Rata-rata</h3>
                        <div class="grid grid-cols-4 gap-3 h-56 items-end">
                            @php $maxDist = max($analysis['distribution'] ?: [1]); @endphp
                            @foreach ($analysis['distribution'] as $label => $count)
                                <div class="h-full flex flex-col justify-end items-center gap-2">
                                    <div class="text-xs font-bold text-gray-700">{{ $count }}</div>
                                    <div class="w-full rounded-t-xl bg-gradient-to-t from-red-500 to-rose-200" style="height: {{ $maxDist > 0 ? max(8, ($count / $maxDist) * 180) : 8 }}px"></div>
                                    <div class="text-[11px] font-semibold text-gray-500">{{ $label }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="text-xs font-bold uppercase text-gray-500">Siswa Teratas</div>
                        <div class="font-bold text-gray-900 mt-2">{{ $analysis['top_student']['siswa']->nama_lengkap ?? '-' }}</div>
                        <div class="text-sm text-gray-500">Rata-rata {{ $analysis['top_student']['average'] ?? '-' }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="text-xs font-bold uppercase text-gray-500">Mapel Terkuat</div>
                        <div class="font-bold text-gray-900 mt-2">{{ $analysis['best_mapel']['nama'] ?? '-' }}</div>
                        <div class="text-sm text-gray-500">Rata-rata {{ $analysis['best_mapel']['average'] ?? '-' }}</div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                        <div class="text-xs font-bold uppercase text-gray-500">Mapel Perlu Fokus</div>
                        <div class="font-bold text-gray-900 mt-2">{{ $analysis['weakest_mapel']['nama'] ?? '-' }}</div>
                        <div class="text-sm text-gray-500">Rata-rata {{ $analysis['weakest_mapel']['average'] ?? '-' }}</div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="font-bold text-gray-900">Leger Kelas {{ $kelas }}</h3>
                        <p class="text-xs text-gray-500">{{ $selectedUjian->nama_ujian }}</p>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-xs uppercase text-gray-500">
                                <tr>
                                    <th class="px-5 py-3 text-left font-bold">Siswa</th>
                                    @foreach ($mapels as $mapel)
                                        <th class="px-5 py-3 text-center font-bold whitespace-nowrap">{{ $mapel->nama_mapel }}</th>
                                    @endforeach
                                    <th class="px-5 py-3 text-center font-bold">Rata-rata</th>
                                    <th class="px-5 py-3 text-center font-bold">Status</th>
                                    <th class="px-5 py-3 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse ($rows as $row)
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-gray-900">{{ $row['siswa']->nama_lengkap }}</div>
                                            <div class="text-xs text-gray-500">{{ $row['siswa']->nis }}</div>
                                        </td>
                                        @foreach ($mapels as $mapel)
                                            <td class="px-5 py-3 text-center font-semibold">{{ $row['scores'][$mapel->id] !== null ? number_format($row['scores'][$mapel->id], 2, ',', '.') : '-' }}</td>
                                        @endforeach
                                        <td class="px-5 py-3 text-center font-bold text-gray-900">{{ $row['average'] !== null ? number_format($row['average'], 2, ',', '.') : '-' }}</td>
                                        <td class="px-5 py-3 text-center">
                                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $row['complete'] ? 'bg-green-50 text-green-700 border border-green-100' : 'bg-amber-50 text-amber-700 border border-amber-100' }}">
                                                {{ $row['complete'] ? 'Lengkap' : $row['complete_count'] . '/' . $mapels->count() }}
                                            </span>
                                        </td>
                                        <td class="px-5 py-3 text-right">
                                            <a href="{{ $row['detail_url'] }}" class="px-3 py-1.5 rounded-lg bg-gray-900 hover:bg-gray-800 text-white text-xs font-semibold">Detail</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ 5 + $mapels->count() }}" class="px-6 py-10 text-center text-gray-500">Belum ada nilai yang cocok dengan master siswa untuk kelas ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @else
                <div class="bg-white rounded-xl border border-dashed border-gray-300 p-8 text-center text-gray-500">
                    Belum ada data nilai yang bisa direkap.
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
