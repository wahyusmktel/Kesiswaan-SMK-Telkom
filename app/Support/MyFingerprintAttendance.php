<?php

namespace App\Support;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintUser;
use App\Models\JadwalPelajaran;
use App\Models\User;
use App\Models\WorkCalendarEvent;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class MyFingerprintAttendance
{
    public static function today(User $user): array
    {
        $today = today();
        $nonWorkingRule = self::nonWorkingDayRule($today->toDateString());
        $logs = FingerprintAttendance::with('device')
            ->where('app_user_id', $user->id)
            ->whereDate('timestamp', $today)
            ->orderBy('timestamp')
            ->get();

        return [
            'is_mapped' => FingerprintUser::where('app_user_id', $user->id)->exists(),
            'is_non_working' => (bool) $nonWorkingRule,
            'non_working_label' => $nonWorkingRule['label'] ?? null,
            'non_working_note' => $nonWorkingRule['note'] ?? null,
            'first_scan' => $logs->first()?->timestamp,
            'last_scan' => $logs->count() > 1 ? $logs->last()?->timestamp : null,
            'total_scan' => $logs->count(),
            'device' => $logs->last()?->device,
        ];
    }

    public static function dailyRecaps(User $user, ?Carbon $dateFrom = null, ?Carbon $dateTo = null)
    {
        $recaps = FingerprintAttendance::query()
            ->selectRaw('DATE(timestamp) as tanggal, MIN(timestamp) as scan_masuk, MAX(timestamp) as scan_keluar, COUNT(*) as total_scan')
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->groupByRaw('DATE(timestamp)')
            ->orderByDesc('tanggal')
            ->get();

        return self::applyMonitoringAttributes($user, $recaps);
    }

    public static function chartData($dailyRecaps): array
    {
        $items = collect($dailyRecaps)->sortBy('tanggal')->values();

        return [
            'labels' => $items
                ->map(fn ($recap) => self::indonesianDayName(Carbon::parse($recap->tanggal)) . ', ' . Carbon::parse($recap->tanggal)->format('d M'))
                ->all(),
            'checkinTimes' => $items
                ->map(fn ($recap) => self::minutesFromTimestamp($recap->scan_masuk))
                ->all(),
            'checkoutTimes' => $items
                ->map(fn ($recap) => ((int) $recap->total_scan > 1) ? self::minutesFromTimestamp($recap->scan_keluar) : null)
                ->all(),
        ];
    }

    public static function appreciation($dailyRecaps): array
    {
        $items = collect($dailyRecaps);
        $setting = FingerprintAttendanceSetting::getSetting();
        $presentDays = $items->count();
        $requiredItems = $items->filter(fn ($recap) => (bool) ($recap->monitoring_required ?? false));
        $lateDays = $requiredItems->filter(fn ($recap) => (int) ($recap->monitoring_late_minutes ?? 0) > 0)->count();
        $earlyCheckoutDays = $requiredItems->filter(fn ($recap) => (int) ($recap->monitoring_early_minutes ?? 0) > 0)->count();
        $incompleteDays = $requiredItems->filter(fn ($recap) => (int) $recap->total_scan <= 1)->count();
        $cleanDays = $requiredItems->filter(fn ($recap) => (int) ($recap->monitoring_late_minutes ?? 0) === 0
            && (int) ($recap->monitoring_early_minutes ?? 0) === 0
            && (int) $recap->total_scan > 1)->count();
        $ratedDays = $requiredItems->count();
        $disciplineRate = $ratedDays > 0 ? (int) round(($cleanDays / $ratedDays) * 100) : 0;

        return [
            'tone' => self::appreciationTone($disciplineRate, $lateDays, $earlyCheckoutDays, $incompleteDays, $presentDays),
            'title' => self::appreciationTitle($disciplineRate, $lateDays, $earlyCheckoutDays, $incompleteDays, $presentDays),
            'message' => self::appreciationMessage($disciplineRate, $lateDays, $earlyCheckoutDays, $incompleteDays, $presentDays),
            'discipline_rate' => $disciplineRate,
            'late_days' => $lateDays,
            'early_checkout_days' => $earlyCheckoutDays,
            'incomplete_days' => $incompleteDays,
            'present_days' => $presentDays,
            'checkin_deadline' => Carbon::parse($setting->checkin_end)->format('H:i'),
            'checkout_minimum' => Carbon::parse($setting->checkout_start)->format('H:i'),
        ];
    }

    private static function applyMonitoringAttributes(User $user, Collection $recaps): Collection
    {
        $user->loadMissing(['masterGuru.dapodikGuru', 'securityShiftAssignment.shift']);
        $mapping = FingerprintUser::with(['device', 'appUser.masterGuru.dapodikGuru', 'appUser.securityShiftAssignment.shift'])
            ->where('app_user_id', $user->id)
            ->first();
        $setting = FingerprintAttendanceSetting::getSetting();

        return $recaps->map(function ($recap) use ($user, $mapping, $setting) {
            $date = Carbon::parse($recap->tanggal)->toDateString();
            $rule = self::attendanceRuleFor($user, $date, $setting);
            $firstScan = $recap->scan_masuk ? Carbon::parse($recap->scan_masuk) : null;
            $lastScan = $recap->scan_keluar ? Carbon::parse($recap->scan_keluar) : null;
            $totalScan = (int) $recap->total_scan;

            if (($rule['use_shift_window'] ?? false) && $mapping?->fingerprint_device_id && $mapping?->user_id && $rule['start_at'] && $rule['end_at']) {
                $shiftLogs = FingerprintAttendance::where('fingerprint_device_id', $mapping->fingerprint_device_id)
                    ->where('user_id', $mapping->user_id)
                    ->whereBetween('timestamp', [$rule['start_at'], $rule['end_at']])
                    ->orderBy('timestamp')
                    ->get(['timestamp']);

                if ($shiftLogs->isNotEmpty()) {
                    $firstScan = $shiftLogs->first()->timestamp;
                    $lastScan = $shiftLogs->last()->timestamp;
                    $totalScan = $shiftLogs->count();
                    $recap->scan_masuk = $firstScan;
                    $recap->scan_keluar = $lastScan;
                    $recap->total_scan = $totalScan;
                }
            }

            $hasCheckout = $firstScan && $lastScan && !$firstScan->equalTo($lastScan);
            $lateMinutes = 0;
            $earlyMinutes = 0;
            $notes = [];

            if ($rule['required']) {
                if ($firstScan && $rule['checkin_deadline'] && $firstScan->greaterThan($rule['checkin_deadline'])) {
                    $lateMinutes = (int) ceil($rule['checkin_deadline']->diffInMinutes($firstScan));
                    $notes[] = 'Terlambat ' . AttendanceDuration::humanizeMinutes($lateMinutes);
                }

                if ($hasCheckout && $rule['checkout_minimum'] && $lastScan->lessThan($rule['checkout_minimum'])) {
                    $earlyMinutes = (int) ceil($lastScan->diffInMinutes($rule['checkout_minimum']));
                    $notes[] = 'Pulang cepat ' . AttendanceDuration::humanizeMinutes($earlyMinutes);
                }
            } elseif (!empty($rule['note'])) {
                $notes[] = $rule['note'];
            }

            $statusText = match (true) {
                !$rule['required'] && !$firstScan => 'Tidak Wajib Hadir',
                !$rule['required'] && (bool) $firstScan => 'Hadir Opsional',
                !$firstScan => 'Belum Ada Scan',
                $hasCheckout => 'Hadir Lengkap',
                default => 'Belum Scan Pulang',
            };

            $statusClass = match ($statusText) {
                'Hadir Lengkap' => 'bg-emerald-50 text-emerald-700',
                'Hadir Opsional' => 'bg-blue-50 text-blue-700',
                'Tidak Wajib Hadir' => 'bg-gray-100 text-gray-600',
                'Belum Scan Pulang' => 'bg-amber-50 text-amber-700',
                default => 'bg-red-50 text-red-700',
            };

            $recap->monitoring_status_text = $statusText;
            $recap->monitoring_status_class = $statusClass;
            $recap->monitoring_notes = $notes ?: ['Sesuai jadwal'];
            $recap->monitoring_late_minutes = $lateMinutes;
            $recap->monitoring_early_minutes = $earlyMinutes;
            $recap->monitoring_required = $rule['required'];
            $recap->monitoring_rule_label = $rule['label'];

            return $recap;
        });
    }

    private static function attendanceRuleFor(User $user, string $date, FingerprintAttendanceSetting $setting): array
    {
        $nonWorkingRule = self::nonWorkingDayRule($date);
        if ($nonWorkingRule) {
            return $nonWorkingRule;
        }

        $status = EmploymentStatus::normalize($user->masterGuru?->dapodikGuru?->status_kepegawaian);

        if ($status === EmploymentStatus::PART_TIME) {
            return self::partTimeAttendanceRule($user, $date);
        }

        if ($status === EmploymentStatus::SECURITY) {
            return self::securityAttendanceRule($user, $date);
        }

        return self::fullDayAttendanceRule($date, $setting);
    }

    private static function nonWorkingDayRule(string $date): ?array
    {
        $dateObject = Carbon::parse($date);
        $calendarEvent = WorkCalendarEvent::eventFor($dateObject);

        if ($calendarEvent) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => $calendarEvent->type_label,
                'note' => $calendarEvent->type_label . ': ' . $calendarEvent->title,
            ];
        }

        if ($dateObject->isWeekend()) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Akhir pekan',
                'note' => 'Tidak wajib hadir Sabtu/Minggu',
            ];
        }

        return null;
    }

    private static function fullDayAttendanceRule(string $date, FingerprintAttendanceSetting $setting): array
    {
        $day = Carbon::parse($date)->dayOfWeekIso;
        $required = $day >= 1 && $day <= 5;

        return [
            'required' => $required,
            'checkin_deadline' => $required ? Carbon::parse($date . ' ' . $setting->checkin_end) : null,
            'checkout_minimum' => $required ? Carbon::parse($date . ' ' . $setting->checkout_start) : null,
            'start_at' => $required ? Carbon::parse($date . ' ' . $setting->checkin_start) : null,
            'end_at' => $required ? Carbon::parse($date . ' ' . $setting->checkout_end) : null,
            'use_shift_window' => false,
            'label' => 'Full day',
            'note' => $required ? null : 'Tidak wajib hadir akhir pekan',
        ];
    }

    private static function partTimeAttendanceRule(User $user, string $date): array
    {
        $masterGuruId = $user->masterGuru?->id;
        $dayName = self::indonesianDayName(Carbon::parse($date));

        if (!$masterGuruId) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Part time',
                'note' => 'Data guru belum terhubung',
            ];
        }

        $schedule = JadwalPelajaran::where('master_guru_id', $masterGuruId)
            ->where('hari', $dayName)
            ->selectRaw('MIN(jam_mulai) as starts_at, MAX(jam_selesai) as ends_at, COUNT(*) as total')
            ->first();

        if (!$schedule || (int) $schedule->total === 0) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Part time',
                'note' => 'Tidak ada jadwal mengajar',
            ];
        }

        return [
            'required' => true,
            'checkin_deadline' => Carbon::parse($date . ' ' . $schedule->starts_at),
            'checkout_minimum' => Carbon::parse($date . ' ' . $schedule->ends_at),
            'start_at' => Carbon::parse($date . ' ' . $schedule->starts_at),
            'end_at' => Carbon::parse($date . ' ' . $schedule->ends_at),
            'use_shift_window' => false,
            'label' => 'Part time ' . substr($schedule->starts_at, 0, 5) . '-' . substr($schedule->ends_at, 0, 5),
            'note' => null,
        ];
    }

    private static function securityAttendanceRule(User $user, string $date): array
    {
        $shift = $user->securityShiftAssignment?->shift;

        if (!$shift) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Security',
                'note' => 'Shift security belum diset',
            ];
        }

        $startAt = Carbon::parse($date . ' ' . $shift->starts_at);
        $endAt = Carbon::parse($date . ' ' . $shift->ends_at);
        if ($shift->is_overnight || $endAt->lessThanOrEqualTo($startAt)) {
            $endAt->addDay();
        }

        return [
            'required' => true,
            'checkin_deadline' => $startAt,
            'checkout_minimum' => $endAt,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'use_shift_window' => true,
            'label' => $shift->name . ' ' . $startAt->format('H:i') . '-' . $endAt->format('H:i'),
            'note' => null,
        ];
    }

    private static function indonesianDayName(Carbon $date): string
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ][$date->dayOfWeekIso];
    }

    private static function minutesFromTimestamp($timestamp): ?int
    {
        if (!$timestamp) {
            return null;
        }

        $time = Carbon::parse($timestamp);

        return ($time->hour * 60) + $time->minute;
    }

    private static function minutesFromTime(string $time): int
    {
        $parsed = Carbon::parse($time);

        return ($parsed->hour * 60) + $parsed->minute;
    }

    private static function appreciationTone(int $disciplineRate, int $lateDays, int $earlyCheckoutDays, int $incompleteDays, int $presentDays): string
    {
        if ($presentDays === 0) {
            return 'gray';
        }

        if ($disciplineRate >= 95 && $lateDays === 0 && $earlyCheckoutDays === 0 && $incompleteDays === 0) {
            return 'emerald';
        }

        if ($disciplineRate >= 80) {
            return 'blue';
        }

        return 'amber';
    }

    private static function appreciationTitle(int $disciplineRate, int $lateDays, int $earlyCheckoutDays, int $incompleteDays, int $presentDays): string
    {
        if ($presentDays === 0) {
            return 'Data Masih Terbatas';
        }

        if ($disciplineRate >= 95 && $lateDays === 0 && $earlyCheckoutDays === 0 && $incompleteDays === 0) {
            return 'Kehadiran Sangat Konsisten';
        }

        if ($disciplineRate >= 80) {
            return 'Disiplin Sudah Stabil';
        }

        if ($lateDays >= max($earlyCheckoutDays, $incompleteDays)) {
            return 'Evaluasi Jam Datang';
        }

        if ($earlyCheckoutDays > 0) {
            return 'Evaluasi Jam Pulang';
        }

        return 'Lengkapi Pola Absensi';
    }

    private static function appreciationMessage(int $disciplineRate, int $lateDays, int $earlyCheckoutDays, int $incompleteDays, int $presentDays): string
    {
        if ($presentDays === 0) {
            return 'Belum ada riwayat fingerprint pada periode ini. Setelah data sinkron, sistem akan menampilkan apresiasi dan evaluasi personal.';
        }

        if ($disciplineRate >= 95 && $lateDays === 0 && $earlyCheckoutDays === 0 && $incompleteDays === 0) {
            return 'Mantap, pola absensi masuk dan pulang terlihat rapi. Pertahankan ritme hadir tepat waktu seperti ini.';
        }

        if ($disciplineRate >= 80) {
            return 'Kedisiplinan sudah baik. Perbaiki beberapa catatan kecil agar rekap kehadiran semakin bersih.';
        }

        if ($lateDays >= max($earlyCheckoutDays, $incompleteDays)) {
            return 'Masih ada beberapa hari datang melewati batas. Fokus menyiapkan keberangkatan lebih awal akan membantu menaikkan skor disiplin.';
        }

        if ($earlyCheckoutDays > 0) {
            return 'Ada hari dengan checkout sebelum rentang pulang. Pastikan scan pulang dilakukan setelah waktu checkout minimal.';
        }

        return 'Beberapa hari belum memiliki pasangan scan masuk dan pulang. Biasakan scan lengkap agar evaluasi kehadiran lebih akurat.';
    }
}
