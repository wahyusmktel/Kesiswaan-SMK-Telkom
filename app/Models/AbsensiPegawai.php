<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class AbsensiPegawai extends Model
{
    use HasFactory;

    protected $table = 'absensi_pegawais';

    protected $fillable = [
        'user_id',
        'tanggal',
        'waktu_checkin',
        'waktu_checkout',
        'lat_checkin',
        'lng_checkin',
        'lat_checkout',
        'lng_checkout',
        'status',
        'dalam_radius_checkin',
        'dalam_radius_checkout',
        'keterangan',
    ];

    protected $casts = [
        'tanggal'             => 'date',
        'waktu_checkin'       => 'datetime',
        'waktu_checkout'      => 'datetime',
        'dalam_radius_checkin'  => 'boolean',
        'dalam_radius_checkout' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'tepat_waktu'   => 'Tepat Waktu',
            'terlambat'     => 'Terlambat',
            'tidak_hadir'   => 'Tidak Hadir',
            default         => '-',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'tepat_waktu' => 'green',
            'terlambat'   => 'amber',
            'tidak_hadir' => 'red',
            default       => 'gray',
        };
    }

    public function getDurasiKerjaAttribute(): ?string
    {
        if (!$this->waktu_checkin || !$this->waktu_checkout) {
            return null;
        }
        $diff = $this->waktu_checkin->diff($this->waktu_checkout);
        return sprintf('%02d:%02d', $diff->h + ($diff->days * 24), $diff->i);
    }

    // Scopes
    public function scopeForDate($query, $date)
    {
        return $query->whereDate('tanggal', $date);
    }

    public function scopeForMonth($query, $month, $year)
    {
        return $query->whereMonth('tanggal', $month)->whereYear('tanggal', $year);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Calculate Haversine distance in meters between two coordinates
     */
    public static function haversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lng2 - $lng1);
        $a = sin($dLat / 2) ** 2 +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
