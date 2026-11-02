<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SpmbTesBacaQuranJawaban extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_baca_quran_jawabans';

    protected $fillable = [
        'spmb_tes_baca_quran_sesi_id',
        'spmb_tes_baca_quran_rubrik_id',
        'jawaban_pilihan',
        'jawaban_teks',
        'file_path',
        'skor_diperoleh',
    ];

    protected $casts = [
        'skor_diperoleh' => 'decimal:2',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SpmbTesBacaQuranSesi::class, 'spmb_tes_baca_quran_sesi_id');
    }

    public function rubrik(): BelongsTo
    {
        return $this->belongsTo(SpmbTesBacaQuranRubrik::class, 'spmb_tes_baca_quran_rubrik_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        if (Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }

        return asset('storage/' . $this->file_path);
    }
}
