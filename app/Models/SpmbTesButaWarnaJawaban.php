<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTesButaWarnaJawaban extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_buta_warna_jawabans';

    protected $fillable = [
        'spmb_tes_buta_warna_sesi_id',
        'spmb_tes_buta_warna_soal_id',
        'jawaban_peserta',
        'is_correct',
        'skor_diperoleh',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'skor_diperoleh' => 'decimal:2',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SpmbTesButaWarnaSesi::class, 'spmb_tes_buta_warna_sesi_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SpmbTesButaWarnaSoal::class, 'spmb_tes_buta_warna_soal_id');
    }
}
