<?php

namespace App\Support;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintUser;
use App\Models\User;
use Carbon\Carbon;

class MyFingerprintAttendance
{
    public static function today(User $user): array
    {
        $logs = FingerprintAttendance::with('device')
            ->where('app_user_id', $user->id)
            ->whereDate('timestamp', today())
            ->orderBy('timestamp')
            ->get();

        return [
            'is_mapped' => FingerprintUser::where('app_user_id', $user->id)->exists(),
            'first_scan' => $logs->first()?->timestamp,
            'last_scan' => $logs->count() > 1 ? $logs->last()?->timestamp : null,
            'total_scan' => $logs->count(),
            'device' => $logs->last()?->device,
        ];
    }

    public static function dailyRecaps(User $user, ?Carbon $dateFrom = null, ?Carbon $dateTo = null)
    {
        return FingerprintAttendance::query()
            ->selectRaw('DATE(timestamp) as tanggal, MIN(timestamp) as scan_masuk, MAX(timestamp) as scan_keluar, COUNT(*) as total_scan')
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->groupByRaw('DATE(timestamp)')
            ->orderByDesc('tanggal')
            ->get();
    }

    public static function chartData($dailyRecaps): array
    {
        $items = collect($dailyRecaps)->sortBy('tanggal')->values();

        return [
            'labels' => $items
                ->map(fn ($recap) => Carbon::parse($recap->tanggal)->format('d M'))
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
        $checkinDeadline = self::minutesFromTime($setting->checkin_end);
        $checkoutMinimum = self::minutesFromTime($setting->checkout_start);
        $presentDays = $items->count();
        $lateDays = $items
            ->filter(fn ($recap) => self::minutesFromTimestamp($recap->scan_masuk) > $checkinDeadline)
            ->count();
        $earlyCheckoutDays = $items
            ->filter(fn ($recap) => ((int) $recap->total_scan > 1) && self::minutesFromTimestamp($recap->scan_keluar) < $checkoutMinimum)
            ->count();
        $incompleteDays = $items
            ->filter(fn ($recap) => (int) $recap->total_scan <= 1)
            ->count();
        $cleanDays = $items->filter(function ($recap) use ($checkinDeadline, $checkoutMinimum) {
            $checkin = self::minutesFromTimestamp($recap->scan_masuk);
            $checkout = self::minutesFromTimestamp($recap->scan_keluar);

            return $checkin !== null
                && $checkout !== null
                && (int) $recap->total_scan > 1
                && $checkin <= $checkinDeadline
                && $checkout >= $checkoutMinimum;
        })->count();
        $disciplineRate = $presentDays > 0 ? (int) round(($cleanDays / $presentDays) * 100) : 0;

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
