<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinPembimbing extends Model
{
    use HasFactory;

    protected $fillable = [
        'tipe',
        'master_guru_id',
        'prakerin_industri_id',
        'nama',
        'jabatan',
        'telepon',
        'email',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function industri()
    {
        return $this->belongsTo(PrakerinIndustri::class, 'prakerin_industri_id');
    }
}
