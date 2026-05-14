<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumen extends Model
{
    protected $fillable = [
        'ukk_ujian_id',
        'nama_instrumen',
        'bobot_pengetahuan',
    ];

    public function ujian()
    {
        return $this->belongsTo(UkkUjian::class, 'ukk_ujian_id');
    }

    public function soalPengetahuan()
    {
        return $this->hasMany(UkkInstrumenSoal::class, 'instrumen_id')->orderBy('urutan');
    }

    public function kategoriKeterampilan()
    {
        return $this->hasMany(UkkInstrumenKategori::class, 'instrumen_id')->orderBy('urutan');
    }

    public function getBobotKeterampilanAttribute(): int
    {
        return 100 - (int) $this->bobot_pengetahuan;
    }
}
