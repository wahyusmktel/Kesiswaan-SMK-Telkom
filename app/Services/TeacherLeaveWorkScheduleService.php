<?php

namespace App\Services;

use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\WorkCalendarEvent;
use App\Support\EmploymentStatus;
use Carbon\Carbon;

class TeacherLeaveWorkScheduleService
{
    private const DAY_MAP = [
        'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu',
        'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu', 'Sunday' => 'Minggu',
    ];

    public function warnings(MasterGuru $guru, Carbon $start, Carbon $end): array
    {
        $guru->loadMissing('dapodikGuru');
        $status = EmploymentStatus::normalize($guru->dapodikGuru?->status_kepegawaian);
        $warnings = [];

        for ($day = $start->copy()->startOfDay(); $day->lte($end->copy()->startOfDay()); $day->addDay()) {
            $rangeStart = $day->isSameDay($start) ? $start : $day->copy()->startOfDay();
            $rangeEnd = $day->isSameDay($end) ? $end : $day->copy()->endOfDay();
            $label = $day->translatedFormat('l, d F Y');
            $holiday = WorkCalendarEvent::eventFor($day);
            if ($holiday) {
                $warnings[] = "{$label} tercatat sebagai hari libur: {$holiday->title}.";

                continue;
            }
            if ($day->isWeekend()) {
                $warnings[] = "{$label} berada di luar hari kerja Senin–Jumat.";

                continue;
            }

            if ($status === EmploymentStatus::PART_TIME) {
                $schedule = JadwalPelajaran::query()->inActiveAcademicPeriod()
                    ->where('master_guru_id', $guru->id)
                    ->where('hari', self::DAY_MAP[$day->format('l')])
                    ->selectRaw('MIN(jam_mulai) as starts_at, MAX(jam_selesai) as ends_at')
                    ->first();
                if (! $schedule?->starts_at) {
                    $warnings[] = "{$label} tidak memiliki jadwal mengajar aktif untuk Pegawai Part Time.";

                    continue;
                }
                $workStart = Carbon::parse($day->toDateString().' '.$schedule->starts_at);
                $workEnd = Carbon::parse($day->toDateString().' '.$schedule->ends_at);
                if ($rangeStart->lt($workStart) || $rangeEnd->gt($workEnd)) {
                    $warnings[] = "Rentang izin {$label} berada di luar jadwal kerja Part Time ".$workStart->format('H:i').'–'.$workEnd->format('H:i').'.';
                }

                continue;
            }

            if (in_array($status, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME], true)) {
                $workStart = $day->copy()->setTime(7, 0);
                $workEnd = $day->copy()->setTime(16, 0);
                if ($rangeStart->lt($workStart) || $rangeEnd->gt($workEnd)) {
                    $warnings[] = "Rentang izin {$label} berada di luar jam kerja 07:00–16:00.";
                }
            }
        }

        return array_values(array_unique($warnings));
    }
}
