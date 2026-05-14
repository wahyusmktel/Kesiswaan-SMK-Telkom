<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumenKategori extends Model
{
    protected $fillable = ['instrumen_id', 'nama_kategori', 'bobot', 'urutan'];

    public function instrumen()
    {
        return $this->belongsTo(UkkInstrumen::class, 'instrumen_id');
    }

    public function indikator()
    {
        return $this->hasMany(UkkInstrumenIndikator::class, 'kategori_id')->orderBy('urutan');
    }
}
