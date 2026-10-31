<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class SpmbWawancaraJawaban extends Model
{
    use HasFactory;

    protected $table = 'spmb_wawancara_jawabans';

    protected $fillable = [
        'spmb_wawancara_sesi_id',
        'spmb_wawancara_pertanyaan_id',
        'kategori',
        'jawaban_teks',
        'jawaban_pilihan',
        'jawaban_other',
        'jawaban_skala',
        'file_path',
        'skor_diperoleh',
    ];

    protected $casts = [
        'jawaban_pilihan' => 'array',
        'jawaban_skala' => 'integer',
        'skor_diperoleh' => 'decimal:2',
    ];

    public function sesi(): BelongsTo
    {
        return $this->belongsTo(SpmbWawancaraSesi::class, 'spmb_wawancara_sesi_id');
    }

    public function pertanyaan(): BelongsTo
    {
        return $this->belongsTo(SpmbWawancaraPertanyaan::class, 'spmb_wawancara_pertanyaan_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if (! $this->file_path) {
            return null;
        }

        return Storage::disk('public')->url($this->file_path);
    }
}
