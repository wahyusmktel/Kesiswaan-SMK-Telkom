<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SmartTvMedia extends Model
{
    use HasFactory;

    protected $table = 'smart_tv_media';

    protected $fillable = [
        'judul',
        'tipe',
        'file_path',
        'file_type',
        'jam_mulai',
        'jam_selesai',
        'hari',
        'mode_tayang',
        'durasi_detik',
        'urutan',
        'is_active',
        'keterangan',
        'created_by',
    ];

    protected $casts = [
        'hari' => 'array',
        'is_active' => 'boolean',
        'durasi_detik' => 'integer',
        'urutan' => 'integer',
    ];

    protected $appends = [
        'file_url',
        'tipe_label',
        'mode_tayang_label',
        'jam_mulai_formatted',
        'jam_selesai_formatted',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFileUrlAttribute(): string
    {
        if (empty($this->file_path)) {
            return '';
        }

        if (str_starts_with($this->file_path, 'http://') || str_starts_with($this->file_path, 'https://')) {
            return $this->file_path;
        }

        $clean = str_replace('public/', '', $this->file_path);
        return asset('storage/' . $clean);
    }

    public function getJamMulaiFormattedAttribute(): string
    {
        return $this->jam_mulai ? substr($this->jam_mulai, 0, 5) : '00:00';
    }

    public function getJamSelesaiFormattedAttribute(): string
    {
        return $this->jam_selesai ? substr($this->jam_selesai, 0, 5) : '23:59';
    }

    public function getTipeLabelAttribute(): string
    {
        return match ($this->tipe) {
            'qr_hadir' => 'QR Presensi Hadir',
            'qr_pulang' => 'QR Presensi Pulang',
            'mading' => 'Mading Digital',
            'pengumuman' => 'Pengumuman Resmi',
            default => 'Media Display',
        };
    }

    public function getModeTayangLabelAttribute(): string
    {
        return match ($this->mode_tayang) {
            'fullscreen_total' => 'Full Screen Total (Iklan Penuh)',
            'eksklusif' => 'Eksklusif (Takeover Frame)',
            default => 'Kombinasi (Slide Jadwal)',
        };
    }

    /**
     * Cek apakah media aktif dan memenuhi kriteria hari & jam saat ini.
     */
    public function isScheduleActiveNow(?Carbon $now = null): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = $now ?: Carbon::now();
        $dayMap = [0 => 'Minggu', 1 => 'Senin', 2 => 'Selasa', 3 => 'Rabu', 4 => 'Kamis', 5 => 'Jumat', 6 => 'Sabtu'];
        $todayName = $dayMap[$now->dayOfWeek] ?? 'Senin';

        // Cek hari (jika hari diset dan tidak kosong)
        if (! empty($this->hari) && is_array($this->hari)) {
            if (! in_array($todayName, $this->hari, true)) {
                return false;
            }
        }

        // Cek rentang waktu (jam & menit)
        $currentTime = $now->format('H:i:s');
        $mulai = $this->jam_mulai ?: '00:00:00';
        $selesai = $this->jam_selesai ?: '23:59:59';

        if ($mulai <= $selesai) {
            // Rentang normal: contoh 06:00:00 s.d 08:00:00
            return ($currentTime >= $mulai && $currentTime <= $selesai);
        } else {
            // Rentang melewati tengah malam: contoh 22:00:00 s.d 02:00:00
            return ($currentTime >= $mulai || $currentTime <= $selesai);
        }
    }
}
