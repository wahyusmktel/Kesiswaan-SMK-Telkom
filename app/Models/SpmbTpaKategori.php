<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTpaKategori extends Model
{
    use HasFactory;

    protected $table = 'spmb_tpa_kategoris';

    protected $fillable = [
        'kode',
        'nama',
        'deskripsi',
        'jumlah_soal_ditampilkan',
        'durasi_menit',
        'passing_grade',
        'nomor_urut',
        'warna_tema',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'jumlah_soal_ditampilkan' => 'integer',
        'durasi_menit' => 'integer',
        'passing_grade' => 'integer',
        'nomor_urut' => 'integer',
    ];

    public function soals(): HasMany
    {
        return $this->hasMany(SpmbTpaSoal::class, 'spmb_tpa_kategori_id')->orderBy('nomor_urut');
    }

    public function activeSoals(): HasMany
    {
        return $this->hasMany(SpmbTpaSoal::class, 'spmb_tpa_kategori_id')->where('is_active', true)->orderBy('nomor_urut');
    }

    public function getBadgeClassAttribute(): string
    {
        return match ($this->warna_tema) {
            'red' => 'bg-red-100 text-red-800 border-red-300',
            'blue' => 'bg-blue-100 text-blue-800 border-blue-300',
            'purple' => 'bg-purple-100 text-purple-800 border-purple-300',
            'emerald' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
            'amber' => 'bg-amber-100 text-amber-800 border-amber-300',
            default => 'bg-indigo-100 text-indigo-800 border-indigo-300',
        };
    }
}
