<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPiketTanggal extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_tanggals';

    protected $fillable = [
        'spmb_piket_kelompok_id',
        'tanggal',
        'hari',
        'waktu',
        'status',
        'keterangan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketKelompok::class, 'spmb_piket_kelompok_id');
    }

    public function getFormattedTanggalAttribute(): string
    {
        if (! $this->tanggal) {
            return '-';
        }

        return Carbon::parse($this->tanggal)->locale('id')->translatedFormat('d M Y');
    }
}
