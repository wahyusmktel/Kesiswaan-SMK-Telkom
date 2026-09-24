<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PrakerinBimbinganAnotasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_anotasis';

    protected $fillable = [
        'prakerin_bimbingan_tahap_id',
        'user_id',
        'halaman',
        'posisi_x',
        'posisi_y',
        'lebar',
        'tinggi',
        'tipe_anotasi',
        'warna',
        'catatan',
        'drawing_data',
    ];

    protected $casts = [
        'halaman' => 'integer',
        'posisi_x' => 'float',
        'posisi_y' => 'float',
        'lebar' => 'float',
        'tinggi' => 'float',
        'drawing_data' => 'array',
    ];

    protected $appends = [
        'koordinat_x',
        'koordinat_y',
        'tipe',
    ];

    public function tahap()
    {
        return $this->belongsTo(PrakerinBimbinganTahap::class, 'prakerin_bimbingan_tahap_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getKoordinatXAttribute(): float
    {
        return (float) $this->posisi_x;
    }

    public function getKoordinatYAttribute(): float
    {
        return (float) $this->posisi_y;
    }

    public function getTipeAttribute(): string
    {
        return match ($this->tipe_anotasi) {
            'sorot_lingkaran' => 'circle',
            'coretan_bebas' => 'drawing',
            'pin_catatan' => 'pin',
            default => 'box',
        };
    }
}
