<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class RabRealisasiEvidence extends Model
{
    use HasFactory;

    protected $table = 'rab_realisasi_evidences';

    protected $fillable = [
        'rab_realisasi_id',
        'judul',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'keterangan',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function realisasi()
    {
        return $this->belongsTo(RabRealisasi::class, 'rab_realisasi_id');
    }

    public function getFileUrlAttribute(): ?string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }
        return null;
    }

    public function isImage(): bool
    {
        $ext = strtolower(pathinfo($this->file_name ?? $this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif']);
    }

    public function isPdf(): bool
    {
        $ext = strtolower(pathinfo($this->file_name ?? $this->file_path, PATHINFO_EXTENSION));
        return $ext === 'pdf';
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }
}
