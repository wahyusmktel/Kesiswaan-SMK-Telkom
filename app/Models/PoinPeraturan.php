<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinPeraturan extends Model
{
    protected $fillable = ['poin_category_id', 'pasal', 'ayat', 'deskripsi', 'bobot_poin'];

    public function category()
    {
        return $this->belongsTo(PoinCategory::class, 'poin_category_id');
    }

    public function pelanggarans()
    {
        return $this->hasMany(SiswaPelanggaran::class);
    }
}
