<?php

namespace App\Services;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\WorkCalendarEvent;
use App\Support\EmploymentStatus;
use Carbon\Carbon;

class TelegramFingerprintRecapService
{
    public function text(MasterGuru $guru): string
    {
        $guru->loadMissing('dapodikGuru');
        $end = today();
        $start = $end->copy()->subDays(6);
        $setting = FingerprintAttendanceSetting::getSetting();
        $scans = FingerprintAttendance::query()
            ->where('app_user_id', $guru->user_id)
            ->whereBetween('timestamp', [$start, $end->copy()->endOfDay()])
            ->get()->groupBy(fn ($scan) => $scan->timestamp->toDateString());
        $leaves = GuruIzin::fullyApproved()->where('master_guru_id', $guru->id)
            ->where('tanggal_mulai', '<=', $end->copy()->endOfDay())
            ->where('tanggal_selesai', '>=', $start)->get();
        $schedules = JadwalPelajaran::inActiveAcademicPeriod()->where('master_guru_id', $guru->id)
            ->selectRaw('hari, MIN(jam_mulai) as starts_at')->groupBy('hari')->get()->keyBy('hari');
        $employment = EmploymentStatus::normalize($guru->dapodikGuru?->status_kepegawaian);
        $totals = ['hadir' => 0, 'terlambat' => 0, 'tidak_hadir' => 0, 'izin' => 0];
        $lines = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $key = $date->toDateString();
            $dayScans = $scans->get($key, collect())->sortBy('timestamp');
            $first = $dayScans->first()?->timestamp;
            $last = $dayScans->last()?->timestamp;
            $holiday = $date->isWeekend() || WorkCalendarEvent::eventFor($date);
            $leave = $leaves->contains(fn ($item) => $item->tanggal_mulai->lte($date->copy()->endOfDay()) && $item->tanggal_selesai->gte($date));
            $dayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$date->dayOfWeekIso - 1];
            $schedule = $schedules->get($dayName);
            $required = ! $holiday && ($guru->is_tpa || EmploymentStatus::isFullDay($employment) || ($employment === EmploymentStatus::PART_TIME && $schedule));

            if ($first) {
                $deadline = Carbon::parse($key.' '.(($employment === EmploymentStatus::PART_TIME && ! $guru->is_tpa) ? $schedule?->starts_at : $setting->checkin_end));
                $late = $first->gt($deadline);
                $totals['hadir']++;
                $totals[$late ? 'terlambat' : 'hadir'] += $late ? 1 : 0;
                $status = $late ? 'Terlambat' : 'Hadir';
                $time = $first->format('H:i').'–'.(($last && ! $last->equalTo($first)) ? $last->format('H:i') : '—');
            } elseif ($leave) {
                $totals['izin']++;
                $status = 'Izin';
                $time = '—';
            } elseif ($required && now()->gt(Carbon::parse($key.' '.(($employment === EmploymentStatus::PART_TIME && ! $guru->is_tpa) ? $schedule?->starts_at : $setting->checkin_end)))) {
                $totals['tidak_hadir']++;
                $status = 'Tidak hadir';
                $time = '—';
            } else {
                $status = $holiday ? 'Libur' : 'Tidak wajib';
                $time = '—';
            }
            $lines[] = $date->format('d/m').' · '.$status.' · '.$time;
        }

        return "📊 REKAP FINGERPRINT 7 HARI TERAKHIR\n"
            .$start->format('d/m/Y').'–'.$end->format('d/m/Y')."\n\n"
            .implode("\n", $lines)."\n\n"
            .'Hadir (termasuk terlambat): '.$totals['hadir'].' | Terlambat: '.$totals['terlambat'].' | Tidak hadir: '.$totals['tidak_hadir'].' | Izin: '.$totals['izin'];
    }
}
