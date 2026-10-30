<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbPiketKelompok extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_kelompoks';

    protected $fillable = [
        'tahun_ajaran',
        'nama_kelompok',
        'waktu',
        'keterangan',
        'urutan',
    ];

    public function petugas(): HasMany
    {
        return $this->hasMany(SpmbPiketPetugas::class, 'spmb_piket_kelompok_id')->orderBy('urutan');
    }

    public function tanggals(): HasMany
    {
        return $this->hasMany(SpmbPiketTanggal::class, 'spmb_piket_kelompok_id')->orderBy('tanggal');
    }
}
