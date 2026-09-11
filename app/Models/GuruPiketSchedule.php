<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruPiketSchedule extends Model
{
    public const WEEKDAYS = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    protected $fillable = ['weekday', 'slot', 'user_id'];

    protected $casts = ['slot' => 'integer'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
