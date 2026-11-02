<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbTpaSoal extends Model
{
    use HasFactory;

    protected $table = 'spmb_tpa_soals';

    protected $fillable = [
        'spmb_tpa_kategori_id',
        'nomor_urut',
        'pertanyaan',
        'gambar',
        'tipe_soal',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'kunci_jawaban',
        'pembahasan',
        'tingkat_kesulitan',
        'bobot',
        'is_active',
    ];

    protected $casts = [
        'nomor_urut' => 'integer',
        'bobot' => 'integer',
        'is_active' => 'boolean',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(SpmbTpaKategori::class, 'spmb_tpa_kategori_id');
    }

    public function getPertanyaanAttribute($value): string
    {
        return str_replace(['\r\n', '\n', '\r'], "\n", $value ?? '');
    }

    public function setPertanyaanAttribute($value): void
    {
        $this->attributes['pertanyaan'] = str_replace(['\r\n', '\n', '\r'], "\n", $value ?? '');
    }

    public function getPembahasanAttribute($value): ?string
    {
        return $value !== null ? str_replace(['\r\n', '\n', '\r'], "\n", $value) : null;
    }

    public function setPembahasanAttribute($value): void
    {
        $this->attributes['pembahasan'] = $value !== null ? str_replace(['\r\n', '\n', '\r'], "\n", $value) : null;
    }

    public function getGambarUrlAttribute(): ?string
    {
        if (! $this->gambar) {
            return null;
        }

        if (str_starts_with($this->gambar, 'http://') || str_starts_with($this->gambar, 'https://')) {
            return $this->gambar;
        }

        if (str_starts_with($this->gambar, 'images/') || str_starts_with($this->gambar, '/images/')) {
            return asset(ltrim($this->gambar, '/'));
        }

        return asset('storage/' . ltrim($this->gambar, '/'));
    }

    public function getOpsiListAttribute(): array
    {
        $list = [];
        foreach (['A' => $this->opsi_a, 'B' => $this->opsi_b, 'C' => $this->opsi_c, 'D' => $this->opsi_d, 'E' => $this->opsi_e] as $k => $v) {
            if ($v !== null && trim($v) !== '') {
                $list[$k] = $v;
            }
        }
        return $list;
    }
}
