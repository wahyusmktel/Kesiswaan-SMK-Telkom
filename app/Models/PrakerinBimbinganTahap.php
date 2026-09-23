<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class PrakerinBimbinganTahap extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'prakerin_bimbingan_tahaps';

    protected $fillable = [
        'prakerin_bimbingan_laporan_id',
        'judul_tahap',
        'deskripsi',
        'urutan',
        'status',
        'file_pdf_path',
        'file_pdf_nama_asli',
        'file_pdf_size',
        'catatan_siswa',
        'diajukan_at',
        'catatan_pembimbing',
        'reviewed_at',
        'reviewed_by',
        'disetujui_at',
        'nomor_berita_acara',
        'berita_acara_at',
    ];

    protected $casts = [
        'diajukan_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'disetujui_at' => 'datetime',
        'berita_acara_at' => 'datetime',
        'urutan' => 'integer',
        'file_pdf_size' => 'integer',
    ];

    public function laporan()
    {
        return $this->belongsTo(PrakerinBimbinganLaporan::class, 'prakerin_bimbingan_laporan_id');
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function anotasis()
    {
        return $this->hasMany(PrakerinBimbinganAnotasi::class, 'prakerin_bimbingan_tahap_id')->orderBy('halaman')->orderBy('id');
    }

    public function aktivitasLogs()
    {
        return $this->hasMany(PrakerinBimbinganAktivitasLog::class, 'prakerin_bimbingan_tahap_id')->latest();
    }

    public function getFilePdfUrlAttribute(): ?string
    {
        if (! $this->file_pdf_path) {
            return null;
        }

        $cleanPath = str_replace('public/', '', $this->file_pdf_path);
        return Storage::disk('public')->url($cleanPath);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        if (! $this->file_pdf_size) return '-';
        $bytes = $this->file_pdf_size;
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        return number_format($bytes / 1024, 1) . ' KB';
    }

    public function getNamaTahapAttribute(): string
    {
        return $this->judul_tahap;
    }

    public function getFileUrlAttribute(): ?string
    {
        return $this->file_pdf_url;
    }

    public function getFilePathAttribute(): ?string
    {
        return $this->file_pdf_path;
    }

    public function getFileNamaAsliAttribute(): ?string
    {
        return $this->file_pdf_nama_asli;
    }

    public function getFileSizeFormattedAttribute(): string
    {
        return $this->formatted_file_size;
    }

    public function getDiunggahAtAttribute()
    {
        return $this->diajukan_at;
    }

    public function getDireviewAtAttribute()
    {
        return $this->reviewed_at;
    }

    public function getAnotasisCountAttribute(): int
    {
        return $this->anotasis()->count();
    }
}
