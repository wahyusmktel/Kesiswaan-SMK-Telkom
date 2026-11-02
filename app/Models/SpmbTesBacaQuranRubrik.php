<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbTesBacaQuranRubrik extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_baca_quran_rubriks';

    protected $fillable = [
        'nomor_urut',
        'kategori',
        'aspek',
        'petunjuk',
        'tipe_input',
        'pilihan_opsi',
        'skor_bobot',
        'is_required',
        'is_active',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
        'pilihan_opsi' => 'array',
        'skor_bobot' => 'array',
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function jawabans(): HasMany
    {
        return $this->hasMany(SpmbTesBacaQuranJawaban::class, 'spmb_tes_baca_quran_rubrik_id');
    }
}
