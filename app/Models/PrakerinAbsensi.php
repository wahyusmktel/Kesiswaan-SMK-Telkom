<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinAbsensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'prakerin_penempatan_id',
        'tanggal',
        'check_in_at',
        'check_in_latitude',
        'check_in_longitude',
        'check_in_photo',
        'check_out_at',
        'check_out_latitude',
        'check_out_longitude',
        'check_out_photo',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function penempatan()
    {
        return $this->belongsTo(PrakerinPenempatan::class, 'prakerin_penempatan_id');
    }
}
