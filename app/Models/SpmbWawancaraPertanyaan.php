<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpmbWawancaraPertanyaan extends Model
{
    use HasFactory;

    protected $table = 'spmb_wawancara_pertanyaans';

    protected $fillable = [
        'kategori',
        'nomor_urut',
        'field_key',
        'pertanyaan',
        'petunjuk',
        'tipe_input',
        'pilihan_opsi',
        'has_other',
        'is_required',
        'is_readonly',
        'is_active',
        'bobot',
        'skor_opsi',
    ];

    protected $casts = [
        'pilihan_opsi' => 'array',
        'skor_opsi' => 'array',
        'has_other' => 'boolean',
        'is_required' => 'boolean',
        'is_readonly' => 'boolean',
        'is_active' => 'boolean',
        'nomor_urut' => 'integer',
        'bobot' => 'integer',
    ];

    public function jawabans(): HasMany
    {
        return $this->hasMany(SpmbWawancaraJawaban::class, 'spmb_wawancara_pertanyaan_id');
    }
}
