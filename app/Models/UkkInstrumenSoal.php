<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumenSoal extends Model
{
    protected $fillable = [
        'instrumen_id',
        'pertanyaan',
        'kunci_jawaban',
        'jenis_soal',
        'urutan',
    ];

    public function instrumen()
    {
        return $this->belongsTo(UkkInstrumen::class, 'instrumen_id');
    }

    public function nilaiPengetahuan()
    {
        return $this->hasMany(UkkNilaiPengetahuan::class, 'soal_id');
    }

    public function isBilingual(): bool
    {
        return $this->jenis_soal === 'bilingual';
    }

    public function isObservasi(): bool
    {
        return $this->jenis_soal === 'observasi';
    }

    public function isEsai(): bool
    {
        return empty($this->jenis_soal) || $this->jenis_soal === 'esai';
    }

    public function scopeEsai($query)
    {
        return $query->where(function ($q) {
            $q->where('jenis_soal', 'esai')
              ->orWhereNull('jenis_soal');
        });
    }

    public function scopeBilingual($query)
    {
        return $query->where('jenis_soal', 'bilingual');
    }

    public function scopeObservasi($query)
    {
        return $query->where('jenis_soal', 'observasi');
    }
}
