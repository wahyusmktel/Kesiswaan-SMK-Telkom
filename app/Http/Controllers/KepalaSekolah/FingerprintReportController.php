<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\WorkCalendarEvent;
use App\Support\EmploymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FingerprintReportController extends Controller
{
    public function index(Request $request)
    {
        $input = $request->validate(['period' => ['nullable', 'in:week,month'], 'date' => ['nullable', 'date_format:Y-m-d'], 'search' => ['nullable', 'string', 'max:100']]);
        $period = $input['period'] ?? 'week';
        $date = Carbon::parse($input['date'] ?? today()->toDateString());
        $start = $period === 'week' ? $date->copy()->startOfWeek(Carbon::MONDAY) : $date->copy()->startOfMonth();
        $end = $period === 'week' ? $start->copy()->addDays(6)->endOfDay() : $date->copy()->endOfMonth();
        $now = now();
        $setting = FingerprintAttendanceSetting::getSetting();
        $teachers = MasterGuru::with('dapodikGuru')->where('is_active', true)
            ->when($input['search'] ?? null, fn ($q, $search) => $q->where('nama_lengkap', 'like', '%'.$search.'%'))->orderBy('nama_lengkap')->get();
        $scans = FingerprintAttendance::query()->whereIn('app_user_id', $teachers->pluck('user_id')->filter())
            ->whereBetween('timestamp', [$start, $end])->where('timestamp', '<=', $now)
            ->selectRaw('app_user_id, DATE(timestamp) as day, MIN(timestamp) as first_scan, MAX(timestamp) as last_scan')
            ->groupBy('app_user_id')->groupByRaw('DATE(timestamp)')->get()->keyBy(fn ($s) => $s->app_user_id.'|'.$s->day);
        $schedules = JadwalPelajaran::inActiveAcademicPeriod()->whereIn('master_guru_id', $teachers->modelKeys())
            ->selectRaw('master_guru_id, hari, MIN(jam_mulai) as starts_at')->groupBy('master_guru_id', 'hari')->get()->keyBy(fn ($s) => $s->master_guru_id.'|'.$s->hari);
        $leaves = GuruIzin::fullyApproved()->whereIn('master_guru_id', $teachers->modelKeys())->where('tanggal_mulai', '<=', $end)->where('tanggal_selesai', '>=', $start)->get()->groupBy('master_guru_id');
        $days = collect();
        for ($cursor = $start->copy(); $cursor->lte($end) && $cursor->lte($now); $cursor->addDay()) {
            $days->push(['date' => $cursor->copy(), 'working' => ! $cursor->isWeekend() && ! WorkCalendarEvent::eventFor($cursor)]);
        }
        $trend = $days->mapWithKeys(fn ($d) => [$d['date']->toDateString() => ['label' => $d['date']->format('d/m'), 'present' => 0, 'late' => 0, 'absent' => 0]])->all();
        $rows = $teachers->map(function ($teacher) use ($days, $scans, $schedules, $leaves, $setting, $now, &$trend) {
            $employment = EmploymentStatus::normalize($teacher->dapodikGuru?->status_kepegawaian);
            $isTpa = $teacher->is_tpa;
            $recognized = $isTpa || in_array($employment, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME], true);
            $row = ['name' => $teacher->nama_lengkap, 'employment' => $employment ?: 'Belum diisi', 'present' => 0, 'late' => 0, 'absent' => 0, 'leave' => 0];
            $in = [];
            $out = [];
            foreach ($days as $day) {
                $key = $day['date']->toDateString();
                $scan = $scans->get($teacher->user_id.'|'.$key);
                if ($scan) {
                    $row['present']++;
                    $trend[$key]['present']++;
                    $in[] = $this->minutes($scan->first_scan);
                    if ($scan->last_scan > $scan->first_scan) {
                        $out[] = $this->minutes($scan->last_scan);
                    }
                }
                $leave = ($leaves->get($teacher->id) ?? collect())->contains(fn ($l) => $l->tanggal_mulai->lte($day['date']->copy()->endOfDay()) && $l->tanggal_selesai->gte($day['date']));
                if ($leave) {
                    $row['leave']++;

                    continue;
                }
                $dayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$day['date']->dayOfWeekIso - 1];
                $schedule = $schedules->get($teacher->id.'|'.$dayName);
                if (! $recognized || ! $day['working'] || (! $isTpa && $employment === EmploymentStatus::PART_TIME && ! $schedule)) {
                    continue;
                }
                $deadline = Carbon::parse($key.' '.(! $isTpa && $employment === EmploymentStatus::PART_TIME ? $schedule->starts_at : $setting->checkin_end));
                if ($scan && Carbon::parse($scan->first_scan)->gt($deadline)) {
                    $row['late']++;
                    $trend[$key]['late']++;
                } elseif (! $scan && $now->gt($deadline)) {
                    $row['absent']++;
                    $trend[$key]['absent']++;
                }
            }
            $row['average_in'] = $this->averageTime($in);
            $row['average_out'] = $this->averageTime($out);

            return $row;
        });
        $trend = array_values($trend);
        $chartMax = max(1, collect($trend)->max('present'), collect($trend)->max('late'), collect($trend)->max('absent'));
        $lines = collect(['present' => '#059669', 'late' => '#d97706', 'absent' => '#dc2626'])->map(fn ($color, $key) => ['color' => $color, 'points' => collect($trend)->map(fn ($d, $i) => (45 + $i * 680 / max(1, count($trend) - 1)).','.(180 - $d[$key] * 150 / $chartMax))->implode(' ')]);

        return view('pages.kepala-sekolah.fingerprint-report', compact('rows', 'trend', 'lines', 'chartMax', 'period', 'date', 'start', 'end'));
    }

    private function minutes(string $timestamp): int
    {
        $time = Carbon::parse($timestamp);

        return $time->hour * 60 + $time->minute;
    }

    private function averageTime(array $values): string
    {
        if (! $values) {
            return '—';
        }
        $minutes = (int) round(array_sum($values) / count($values));

        return sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
