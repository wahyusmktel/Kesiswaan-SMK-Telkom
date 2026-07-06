<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptSubject extends Model
{
    protected $fillable = ['name', 'sort_order', 'group', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public static function groups(): array
    {
        return [
            'umum' => 'Kelompok Mata Pelajaran Umum',
            'muatan_lokal' => 'Muatan Lokal',
            'kejuruan' => 'Kelompok Mata Pelajaran Kejuruan',
        ];
    }

    public function grades()
    {
        return $this->hasMany(TranscriptGrade::class);
    }
}
