<?php

namespace App\Support;

class EmploymentStatus
{
    public const PERMANENT = 'Pegawai Tetap';
    public const FULL_TIME = 'Pegawai Full Time';
    public const PART_TIME = 'Pegawai Part Time';
    public const SECURITY = 'Security';
    public const CLEANING = 'Tenaga Kebersihan';
    public const ACADEMIC_SUPPORT = 'Tenaga Penunjang Akademik';

    public static function options(): array
    {
        return [
            self::PERMANENT,
            self::FULL_TIME,
            self::PART_TIME,
            self::SECURITY,
            self::CLEANING,
            self::ACADEMIC_SUPPORT,
        ];
    }

    public static function normalize(?string $status): ?string
    {
        $status = trim((string) $status);

        if ($status === '') {
            return null;
        }

        foreach (self::options() as $option) {
            if (strcasecmp($status, $option) === 0) {
                return $option;
            }
        }

        return $status;
    }

    public static function isFullDay(?string $status): bool
    {
        return in_array(self::normalize($status), [
            self::PERMANENT,
            self::FULL_TIME,
            self::CLEANING,
            self::ACADEMIC_SUPPORT,
        ], true);
    }
}
