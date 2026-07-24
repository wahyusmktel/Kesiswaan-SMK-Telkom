<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-bold leading-tight text-gray-800">Analisis Pekan Efektif</h2>
            <p class="mt-0.5 text-sm text-gray-500">Hitung alokasi pekan dan jam tatap muka dari jadwal mengajar serta kalender sekolah.</p>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-lg border border-red-200 bg-red-50 px-5 py-4 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="rounded-lg border border-blue-200 bg-blue-50 p-5">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-5 w-5 flex-none text-blue-600" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10" />
                    <path stroke-linecap="round" d="M12 16v-4m0-4h.01" />
                </svg>
                <div>
                    <h3 class="font-bold text-blue-900">Perhitungan dilakukan otomatis</h3>
                    <p class="mt-1 text-sm leading-6 text-blue-800">
                        Sistem membaca hari dan jumlah JP dari jadwal guru pada tahun pelajaran aktif. Pekan yang
                        beririsan dengan agenda tidak efektif dari Kalender SDM akan dikurangi dari pekan tatap muka.
                        Semester ganjil dihitung Juli-Desember dan semester genap Januari-Juni.
                    </p>
                </div>
            </div>
        </div>

        @if (! $academicYear)
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-8 text-center">
                <h3 class="font-bold text-amber-900">Tahun pelajaran aktif belum tersedia</h3>
                <p class="mt-2 text-sm text-amber-700">Aktifkan tahun pelajaran dan semester terlebih dahulu.</p>
            </div>
        @elseif ($schedules->isEmpty())
            <div class="rounded-lg border border-amber-200 bg-amber-50 p-8 text-center">
                <h3 class="font-bold text-amber-900">Jadwal mengajar belum ditemukan</h3>
                <p class="mt-2 text-sm text-amber-700">
                    Belum ada jadwal guru pada {{ $academicYear->tahun }} semester {{ $academicYear->semester }}.
                </p>
            </div>
        @else
            @php
                $selectedKey = $selected['rombel_id'].'-'.$selected['mata_pelajaran_id'];
                $pdfParameters = [
                    'rombel_id' => $selected['rombel_id'],
                    'mata_pelajaran_id' => $selected['mata_pelajaran_id'],
                    'p5_weeks' => request('p5_weeks', 0),
                    'reserve_weeks' => request('reserve_weeks', 0),
                ];
            @endphp

            <form method="GET" action="{{ route('guru-kelas.teaching-module.effective-week.index') }}"
                class="rounded-lg border border-gray-200 bg-white shadow-sm"
                x-data="{
                    schedule: @js($selectedKey),
                    rombelId: @js((string) $selected['rombel_id']),
                    subjectId: @js((string) $selected['mata_pelajaran_id']),
                    syncSchedule() {
                        const values = this.schedule.split('-');
                        this.rombelId = values[0];
                        this.subjectId = values[1];
                    }
                }">
                <div class="grid grid-cols-1 gap-4 p-5 lg:grid-cols-12 lg:items-end">
                    <div class="lg:col-span-6">
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Jadwal Mata Pelajaran</label>
                        <select x-model="schedule" @change="syncSchedule()"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                            @foreach ($schedules as $schedule)
                                <option value="{{ $schedule['rombel_id'] }}-{{ $schedule['mata_pelajaran_id'] }}">
                                    {{ $schedule['label'] }} · {{ implode(' & ', $schedule['days']) }} · {{ $schedule['jp_per_week'] }} JP
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="rombel_id" x-model="rombelId">
                        <input type="hidden" name="mata_pelajaran_id" x-model="subjectId">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Pekan P5</label>
                        <input type="number" name="p5_weeks" min="0" max="20"
                            value="{{ request('p5_weeks', 0) }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="mb-2 block text-xs font-black uppercase text-gray-500">Pekan Cadangan</label>
                        <input type="number" name="reserve_weeks" min="0" max="20"
                            value="{{ request('reserve_weeks', 0) }}"
                            class="w-full rounded-lg border-gray-300 text-sm focus:border-red-500 focus:ring-red-500">
                    </div>
                    <div class="lg:col-span-2">
                        <button class="h-10 w-full rounded-lg bg-gray-900 px-4 text-sm font-bold text-white hover:bg-red-600">
                            Hitung Ulang
                        </button>
                    </div>
                </div>
            </form>

            @if ($analysis)
                <section class="rounded-lg border border-gray-200 bg-white shadow-sm">
                    <div class="flex flex-col gap-4 border-b border-gray-100 px-5 py-4 lg:flex-row lg:items-center lg:justify-between">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-lg font-black text-gray-900">{{ $analysis['subject'] }}</h3>
                                <span class="rounded-full bg-red-50 px-2.5 py-1 text-xs font-bold text-red-700">
                                    {{ $analysis['class'] }}
                                </span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500">
                                {{ $analysis['teacher_name'] }} · {{ $analysis['schedule_label'] }} ·
                                TP {{ $analysis['academic_year']->tahun }}
                            </p>
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('guru-kelas.teaching-module.effective-week.pdf', $pdfParameters) }}"
                                target="_blank"
                                class="inline-flex h-10 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-bold text-gray-700 hover:bg-gray-50">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z" />
                                    <circle cx="12" cy="12" r="3" />
                                </svg>
                                Preview PDF
                            </a>
                            <a href="{{ route('guru-kelas.teaching-module.effective-week.pdf', $pdfParameters + ['download' => 1]) }}"
                                class="inline-flex h-10 items-center gap-2 rounded-lg bg-red-600 px-4 text-sm font-bold text-white hover:bg-red-700">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14" />
                                </svg>
                                Unduh PDF
                            </a>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-px bg-gray-200 sm:grid-cols-4">
                        <div class="bg-white p-4">
                            <div class="text-xs font-bold uppercase text-gray-400">Tahun Pelajaran</div>
                            <div class="mt-1 font-black text-gray-900">{{ $analysis['academic_year']->tahun }}</div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="text-xs font-bold uppercase text-gray-400">Semester Aktif</div>
                            <div class="mt-1 font-black text-gray-900">{{ $analysis['active_semester'] }}</div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="text-xs font-bold uppercase text-gray-400">Fase</div>
                            <div class="mt-1 font-black text-gray-900">{{ $analysis['phase'] }}</div>
                        </div>
                        <div class="bg-white p-4">
                            <div class="text-xs font-bold uppercase text-gray-400">Agenda Terbaca</div>
                            <div class="mt-1 font-black text-gray-900">{{ $analysis['calendar_event_count'] }}</div>
                        </div>
                    </div>
                </section>

                @foreach ($analysis['semesters'] as $semester)
                    <section class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
                        <div class="flex flex-col gap-3 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <h3 class="font-black text-gray-900">Semester {{ $semester['name'] }}</h3>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ $semester['start']->translatedFormat('d F Y') }} -
                                    {{ $semester['end']->translatedFormat('d F Y') }}
                                </p>
                            </div>
                            <div class="flex gap-2 text-xs font-bold">
                                <span class="rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-700">{{ $semester['effective_weeks'] }} efektif</span>
                                <span class="rounded-full bg-red-50 px-3 py-1.5 text-red-700">{{ $semester['ineffective_weeks'] }} tidak efektif</span>
                                <span class="rounded-full bg-blue-50 px-3 py-1.5 text-blue-700">{{ $semester['total_jp'] }} JP</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-center text-xs font-black uppercase text-gray-500">No.</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-gray-500">Bulan</th>
                                        <th class="px-4 py-3 text-center text-xs font-black uppercase text-gray-500">Banyak Pekan</th>
                                        <th class="px-4 py-3 text-center text-xs font-black uppercase text-gray-500">Tidak Efektif</th>
                                        <th class="px-4 py-3 text-center text-xs font-black uppercase text-gray-500">Efektif</th>
                                        <th class="px-4 py-3 text-left text-xs font-black uppercase text-gray-500">Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @foreach ($semester['months'] as $row)
                                        <tr>
                                            <td class="px-4 py-3 text-center text-sm text-gray-600">{{ $row['number'] }}</td>
                                            <td class="whitespace-nowrap px-4 py-3 text-sm font-bold text-gray-900">
                                                {{ $row['month']->translatedFormat('M Y') }}
                                            </td>
                                            <td class="px-4 py-3 text-center text-sm font-bold text-gray-700">{{ $row['total_weeks'] }}</td>
                                            <td class="px-4 py-3 text-center text-sm font-bold text-red-600">{{ $row['ineffective_weeks'] }}</td>
                                            <td class="px-4 py-3 text-center text-sm font-bold text-emerald-600">{{ $row['effective_weeks'] }}</td>
                                            <td class="min-w-80 px-4 py-3 text-sm leading-5 text-gray-600">{{ $row['notes'] ?: '-' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-gray-50 font-black text-gray-900">
                                    <tr>
                                        <td colspan="2" class="px-4 py-3 text-center">Jumlah</td>
                                        <td class="px-4 py-3 text-center">{{ $semester['total_weeks'] }}</td>
                                        <td class="px-4 py-3 text-center">{{ $semester['ineffective_weeks'] }}</td>
                                        <td class="px-4 py-3 text-center">{{ $semester['effective_weeks'] }}</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <div class="grid grid-cols-2 gap-px border-t border-gray-200 bg-gray-200 sm:grid-cols-3 lg:grid-cols-6">
                            @foreach ([
                                ['Pekan Efektif', $semester['effective_weeks']],
                                ['Pekan P5', $semester['p5_weeks']],
                                ['Pekan Cadangan', $semester['reserve_weeks']],
                                ['Pekan Tatap Muka', $semester['contact_weeks']],
                                ['JP per Pekan', $semester['jp_per_week']],
                                ['Total JP', $semester['total_jp']],
                            ] as [$label, $value])
                                <div class="bg-white p-4 text-center">
                                    <div class="text-xs font-bold uppercase text-gray-400">{{ $label }}</div>
                                    <div class="mt-1 text-xl font-black text-gray-900">{{ $value }}</div>
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            @endif
        @endif
    </div>
</x-app-layout>
