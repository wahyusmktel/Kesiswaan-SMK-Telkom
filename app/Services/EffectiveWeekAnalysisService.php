<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\JadwalPelajaran;
use App\Models\TahunPelajaran;
use App\Models\TranscriptConfig;
use App\Models\User;
use App\Models\WorkCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class EffectiveWeekAnalysisService
{
    private const DAY_NUMBERS = [
        'Senin' => Carbon::MONDAY,
        'Selasa' => Carbon::TUESDAY,
        'Rabu' => Carbon::WEDNESDAY,
        'Kamis' => Carbon::THURSDAY,
        'Jumat' => Carbon::FRIDAY,
        'Sabtu' => Carbon::SATURDAY,
    ];

    public function activeAcademicYear(): ?TahunPelajaran
    {
        return TahunPelajaran::where('is_active', true)->first();
    }

    public function scheduleOptions(User $user, TahunPelajaran $academicYear): Collection
    {
        $teacher = $user->loadMissing('masterGuru')->masterGuru;
        if (! $teacher) {
            return collect();
        }

        return JadwalPelajaran::query()
            ->with(['mataPelajaran', 'rombel.kelas'])
            ->where('master_guru_id', $teacher->id)
            ->whereHas('rombel', fn ($query) => $query->where('tahun_pelajaran_id', $academicYear->id))
            ->orderBy('hari')
            ->orderBy('jam_ke')
            ->get()
            ->groupBy(fn ($schedule) => $schedule->rombel_id.'-'.$schedule->mata_pelajaran_id)
            ->map(function (Collection $schedules) {
                $first = $schedules->first();
                $days = $schedules->pluck('hari')->unique()->sortBy(
                    fn ($day) => self::DAY_NUMBERS[$day] ?? 99
                )->values();

                return [
                    'rombel_id' => $first->rombel_id,
                    'mata_pelajaran_id' => $first->mata_pelajaran_id,
                    'subject' => $first->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran',
                    'class' => $first->rombel?->kelas?->nama_kelas ?? 'Kelas',
                    'program' => $first->rombel?->kelas?->jurusan,
                    'days' => $days->all(),
                    'jp_per_week' => $schedules->count(),
                    'label' => ($first->mataPelajaran?->nama_mapel ?? 'Mata Pelajaran')
                        .' - '.($first->rombel?->kelas?->nama_kelas ?? 'Kelas'),
                ];
            })
            ->sortBy('label')
            ->values();
    }

    public function analyze(
        User $user,
        TahunPelajaran $academicYear,
        int $rombelId,
        int $subjectId,
        int $p5Weeks = 0,
        int $reserveWeeks = 0,
    ): array {
        $option = $this->scheduleOptions($user, $academicYear)
            ->first(fn ($item) => $item['rombel_id'] === $rombelId
                && $item['mata_pelajaran_id'] === $subjectId);

        if (! $option) {
            throw ValidationException::withMessages([
                'schedule' => 'Jadwal mengajar tidak ditemukan atau bukan milik guru yang sedang masuk.',
            ]);
        }

        [$firstYear, $secondYear] = $this->academicYears($academicYear);
        $calendarStart = Carbon::create($firstYear, 7, 1)->startOfDay();
        $calendarEnd = Carbon::create($secondYear, 6, 30)->endOfDay();
        $events = WorkCalendarEvent::query()
            ->whereDate('date_from', '<=', $calendarEnd)
            ->whereDate('date_to', '>=', $calendarStart)
            ->orderBy('date_from')
            ->get();

        $oddStart = $this->semesterStart(
            $events,
            Carbon::create($firstYear, 7, 1),
            Carbon::create($firstYear, 12, 31),
        );
        $evenStart = $this->semesterStart(
            $events,
            Carbon::create($secondYear, 1, 1),
            Carbon::create($secondYear, 6, 30),
        );
        $evenEnd = $this->evenSemesterEnd($events, $secondYear);
        $dayNumbers = collect($option['days'])
            ->map(fn ($day) => self::DAY_NUMBERS[$day] ?? null)
            ->filter()
            ->values()
            ->all();

        $semesters = [
            $this->semesterAnalysis(
                'Ganjil',
                $oddStart,
                Carbon::create($firstYear, 12, 31),
                $dayNumbers,
                $events,
                $option['jp_per_week'],
                $p5Weeks,
                $reserveWeeks,
            ),
            $this->semesterAnalysis(
                'Genap',
                $evenStart,
                $evenEnd,
                $dayNumbers,
                $events,
                $option['jp_per_week'],
                $p5Weeks,
                $reserveWeeks,
            ),
        ];

        $teacher = $user->loadMissing('masterGuru.dapodikGuru')->masterGuru;
        $validator = User::whereHas('roles', fn ($query) => $query->where('name', 'Kurikulum'))
            ->with('masterGuru.dapodikGuru')
            ->orderBy('name')
            ->first();
        $transcriptConfig = TranscriptConfig::first();

        return [
            'academic_year' => $academicYear,
            'active_semester' => $academicYear->semester,
            'school_name' => AppSetting::first()?->school_name ?? config('app.name', 'Sekolah'),
            'subject' => $option['subject'],
            'class' => $option['class'],
            'phase' => $this->phaseForClass($option['class']),
            'program' => $option['program'],
            'teacher_name' => $teacher?->nama_lengkap ?? $user->name,
            'teacher_nip' => $teacher?->dapodikGuru?->nip
                ?: $teacher?->nik
                ?: $teacher?->nuptk
                ?: '-',
            'validator_name' => $validator?->masterGuru?->nama_lengkap ?? $validator?->name ?? '-',
            'validator_nip' => $validator?->masterGuru?->dapodikGuru?->nip
                ?: $validator?->masterGuru?->nik
                ?: $validator?->masterGuru?->nuptk
                ?: '-',
            'signature_city' => $transcriptConfig?->signature_city ?: 'Pringsewu',
            'signature_date' => now(),
            'schedule_label' => implode(' & ', $option['days']).', '.$option['jp_per_week'].' JP per minggu',
            'jp_per_week' => $option['jp_per_week'],
            'semesters' => $semesters,
            'calendar_event_count' => $events->count(),
        ];
    }

    private function semesterAnalysis(
        string $name,
        Carbon $start,
        Carbon $end,
        array $dayNumbers,
        Collection $events,
        int $jpPerWeek,
        int $p5Weeks,
        int $reserveWeeks,
    ): array {
        $months = collect();
        $cursor = $start->copy()->startOfMonth();
        $number = 1;
        $finalAssessment = $name === 'Genap'
            ? $events->first(fn (WorkCalendarEvent $event) => $event->date_from->betweenIncluded($start, $end)
                && $event->type === 'assessment'
                && Str::contains(Str::lower($event->title), 'sas genap'))
            : null;

        while ($cursor->lte($end)) {
            $monthStart = $cursor->copy()->startOfMonth()->max($start);
            $monthEnd = $cursor->copy()->endOfMonth()->min($end);
            $occurrences = $this->scheduledDates($monthStart, $monthEnd, $dayNumbers);
            $weeks = $occurrences->groupBy(fn (Carbon $date) => $date->copy()->startOfWeek()->toDateString());
            $ineffectiveWeeks = 0;
            $reasonEvents = collect();

            foreach ($weeks as $weekDates) {
                $weekStart = $weekDates->first()->copy()->startOfWeek();
                $weekEnd = $weekStart->copy()->endOfWeek();
                $weekReasons = $events->filter(
                    fn (WorkCalendarEvent $event) => $this->eventMakesWeekIneffective(
                        $event,
                        $weekStart,
                        $weekEnd,
                        $weekDates,
                    )
                );

                if ($finalAssessment && $weekEnd->gte($finalAssessment->date_from)) {
                    $weekReasons->push($finalAssessment);
                }

                if ($weekReasons->isNotEmpty()) {
                    $ineffectiveWeeks++;
                    $reasonEvents = $reasonEvents->merge($weekReasons);
                }
            }

            $totalWeeks = $weeks->count();
            $months->push([
                'number' => $number++,
                'month' => $cursor->copy(),
                'total_weeks' => $totalWeeks,
                'ineffective_weeks' => $ineffectiveWeeks,
                'effective_weeks' => max(0, $totalWeeks - $ineffectiveWeeks),
                'notes' => $reasonEvents
                    ->unique('id')
                    ->sortBy('date_from')
                    ->map(fn ($event) => $this->eventSummary($event))
                    ->implode('; '),
            ]);

            $cursor->addMonth();
        }

        $effectiveWeeks = (int) $months->sum('effective_weeks');
        $contactWeeks = max(0, $effectiveWeeks - $p5Weeks - $reserveWeeks);

        return [
            'name' => $name,
            'start' => $start,
            'end' => $end,
            'months' => $months,
            'total_weeks' => (int) $months->sum('total_weeks'),
            'ineffective_weeks' => (int) $months->sum('ineffective_weeks'),
            'effective_weeks' => $effectiveWeeks,
            'p5_weeks' => $p5Weeks,
            'reserve_weeks' => $reserveWeeks,
            'contact_weeks' => $contactWeeks,
            'jp_per_week' => $jpPerWeek,
            'total_jp' => $contactWeeks * $jpPerWeek,
        ];
    }

    private function scheduledDates(Carbon $start, Carbon $end, array $dayNumbers): Collection
    {
        $dates = collect();
        $date = $start->copy();

        while ($date->lte($end)) {
            if (in_array($date->dayOfWeekIso, $dayNumbers, true)) {
                $dates->push($date->copy());
            }
            $date->addDay();
        }

        return $dates;
    }

    private function eventMakesWeekIneffective(
        WorkCalendarEvent $event,
        Carbon $weekStart,
        Carbon $weekEnd,
        Collection $scheduledDates,
    ): bool {
        if ($event->date_from->gt($weekEnd) || $event->date_to->lt($weekStart)) {
            return false;
        }

        if (Str::contains(Str::lower($event->title), ['hari pertama kbm', 'hari pertama kmb'])) {
            return false;
        }

        if (in_array($event->type, ['academic_activity', 'assessment'], true)) {
            return true;
        }

        return $scheduledDates->contains(
            fn (Carbon $date) => $date->betweenIncluded($event->date_from, $event->date_to)
        );
    }

    private function semesterStart(Collection $events, Carbon $fallback, Carbon $rangeEnd): Carbon
    {
        $event = $events->first(function (WorkCalendarEvent $event) use ($fallback, $rangeEnd) {
            $title = Str::lower($event->title);

            return $event->date_from->betweenIncluded($fallback, $rangeEnd)
                && Str::contains($title, ['hari pertama kbm', 'hari pertama kmb']);
        });

        return $event?->date_from?->copy() ?? $fallback;
    }

    private function evenSemesterEnd(Collection $events, int $year): Carbon
    {
        $fallback = Carbon::create($year, 6, 30);
        $event = $events->first(function (WorkCalendarEvent $event) use ($year) {
            return $event->date_from->year === $year
                && Str::contains(Str::lower($event->title), 'libur akhir tahun pelajaran');
        });

        return $event ? $event->date_from->copy()->subDay() : $fallback;
    }

    private function academicYears(TahunPelajaran $academicYear): array
    {
        $parts = array_map('intval', explode('/', $academicYear->tahun));
        $firstYear = $parts[0] ?: now()->year;
        $secondYear = $parts[1] ?? ($firstYear + 1);

        return [$firstYear, $secondYear];
    }

    private function phaseForClass(string $className): string
    {
        $className = Str::upper($className);

        return Str::contains($className, ['XI', 'XII', '11', '12']) ? 'F' : 'E';
    }

    private function eventSummary(WorkCalendarEvent $event): string
    {
        $start = $event->date_from->locale('id');
        $end = $event->date_to->locale('id');

        if ($start->isSameDay($end)) {
            return $event->title.' '.$start->translatedFormat('d M');
        }

        if ($start->isSameMonth($end)) {
            return $event->title.' '.$start->format('d').'-'.$end->translatedFormat('d M');
        }

        return $event->title.' '.$start->translatedFormat('d M').'-'.$end->translatedFormat('d M');
    }
}
