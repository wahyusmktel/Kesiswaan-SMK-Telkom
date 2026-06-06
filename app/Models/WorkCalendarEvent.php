<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class WorkCalendarEvent extends Model
{
    protected $fillable = [
        'title',
        'type',
        'date_from',
        'date_to',
        'description',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];

    public static function eventFor(string|Carbon $date): ?self
    {
        $date = Carbon::parse($date)->toDateString();

        return self::whereDate('date_from', '<=', $date)
            ->whereDate('date_to', '>=', $date)
            ->orderBy('date_from')
            ->first();
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'collective_leave' => 'Cuti Bersama',
            default => 'Hari Libur',
        };
    }
}
