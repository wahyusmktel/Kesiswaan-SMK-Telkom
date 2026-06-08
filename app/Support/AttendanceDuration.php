<?php

namespace App\Support;

class AttendanceDuration
{
    public static function humanizeMinutes(int $minutes): string
    {
        if ($minutes <= 0) {
            return '0 menit';
        }

        $hours = intdiv($minutes, 60);
        $remainingMinutes = $minutes % 60;
        $parts = [];

        if ($hours > 0) {
            $parts[] = $hours . ' jam';
        }

        if ($remainingMinutes > 0) {
            $parts[] = $remainingMinutes . ' menit';
        }

        return implode(' ', $parts);
    }
}
