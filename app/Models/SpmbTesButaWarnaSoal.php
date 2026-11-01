<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SpmbTesButaWarnaSoal extends Model
{
    use HasFactory;

    protected $table = 'spmb_tes_buta_warna_soals';

    protected $fillable = [
        'nomor_urut',
        'judul',
        'petunjuk',
        'gambar',
        'pilihan_opsi',
        'kunci_jawaban',
        'bobot',
        'is_active',
    ];

    protected $casts = [
        'pilihan_opsi' => 'array',
        'is_active' => 'boolean',
        'nomor_urut' => 'integer',
        'bobot' => 'integer',
    ];

    /**
     * Get image URL accessor.
     */
    public function getGambarUrlAttribute(): string
    {
        if (empty($this->gambar)) {
            return asset('images/logo.png');
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        // If stored in public path directly (e.g. images/soal-tes-buta-warna/1.png)
        if (file_exists(public_path($this->gambar))) {
            return asset($this->gambar);
        }

        // If stored in storage/app/public
        if (Storage::disk('public')->exists($this->gambar)) {
            return Storage::disk('public')->url($this->gambar);
        }

        return asset($this->gambar);
    }
}
