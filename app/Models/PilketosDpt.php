<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Picqer\Barcode\BarcodeGeneratorSVG;

class PilketosDpt extends Model
{
    use HasFactory;

    protected $table = 'pilketos_dpts';
    protected $guarded = ['id'];

    protected $casts = [
        'waktu_memilih' => 'datetime',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PilketosPeriode::class, 'pilketos_periode_id');
    }

    public function masterSiswa(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public static function generateUniqueBarcodeToken(): string
    {
        do {
            $token = 'VOTE-' . strtoupper(Str::random(8));
        } while (static::where('barcode_token', $token)->exists());

        return $token;
    }

    /**
     * Generate 1D Code 128 Barcode as inline SVG
     */
    public function getBarcodeSvgAttribute(): string
    {
        try {
            $generator = new BarcodeGeneratorSVG();
            return $generator->getBarcode($this->barcode_token, $generator::TYPE_CODE_128, 2, 60);
        } catch (\Throwable $e) {
            return '';
        }
    }
}
