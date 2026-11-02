<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTpaJawaban extends Model
{
    use HasFactory;

    protected $table = 'spmb_tpa_jawabans';

    protected $fillable = [
        'spmb_tpa_sesi_id',
        'spmb_tpa_soal_id',
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
        return $this->belongsTo(SpmbTpaSesi::class, 'spmb_tpa_sesi_id');
    }

    public function soal(): BelongsTo
    {
        return $this->belongsTo(SpmbTpaSoal::class, 'spmb_tpa_soal_id');
    }
}
