<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkInstrumenIndikator extends Model
{
    protected $fillable = ['kategori_id', 'nama_indikator', 'urutan'];

    public function kategori()
    {
        return $this->belongsTo(UkkInstrumenKategori::class, 'kategori_id');
    }
}
