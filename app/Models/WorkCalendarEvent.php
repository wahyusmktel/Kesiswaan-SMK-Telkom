<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class WorkCalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'type',
        'is_non_working',
        'date_from',
        'date_to',
        'description',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
        'is_non_working' => 'boolean',
    ];

    public static function typeOptions(): array
    {
        return [
            'holiday' => 'Hari Libur',
            'collective_leave' => 'Cuti Bersama',
            'national_holiday' => 'Hari Libur Nasional',
            'academic_activity' => 'Kegiatan Akademik',
            'assessment' => 'Asesmen/Ujian',
            'report_distribution' => 'Pembagian Rapor',
            'school_break' => 'Libur',
            'religious_holiday' => 'Libur/Hari Besar',
            'other' => 'Kegiatan Lainnya',
        ];
    }

    public static function normalizeType(?string $type): string
    {
        $normalized = Str::lower(trim((string) $type));

        return match ($normalized) {
            'holiday', 'hari libur' => 'holiday',
            'collective_leave', 'cuti bersama' => 'collective_leave',
            'hari libur nasional' => 'national_holiday',
            'kegiatan akademik' => 'academic_activity',
            'asesmen/ujian', 'asesmen ujian', 'ujian' => 'assessment',
            'pembagian rapor' => 'report_distribution',
            'libur' => 'school_break',
            'libur/hari besar', 'libur hari besar' => 'religious_holiday',
            'kegiatan lainnya', 'lainnya' => 'other',
            default => Str::slug($normalized, '_') ?: 'other',
        };
    }

    public static function typeIsNonWorking(string $type): bool
    {
        return in_array($type, [
            'holiday',
            'collective_leave',
            'national_holiday',
            'school_break',
            'religious_holiday',
        ], true);
    }

    public static function eventFor(string|Carbon $date): ?self
    {
        $date = Carbon::parse($date)->toDateString();

        return self::whereDate('date_from', '<=', $date)
            ->whereDate('date_to', '>=', $date)
            ->where('is_non_working', true)
            ->orderBy('date_from')
            ->first();
    }

    public function getTypeLabelAttribute(): string
    {
        return self::typeOptions()[$this->type] ?? Str::headline($this->type);
    }
}
