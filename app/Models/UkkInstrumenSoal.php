<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumenSoal extends Model
{
    protected $fillable = [
        'instrumen_id',
        'pertanyaan',
        'kunci_jawaban',
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
}
