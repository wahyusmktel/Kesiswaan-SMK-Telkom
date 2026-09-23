<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinPenempatan extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_siswa_id',
        'prakerin_rombel_id',
        'prakerin_industri_id',
        'master_guru_id',
        'nama_pembimbing_industri',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function getTanggalMulaiFormattedAttribute(): string
    {
        if (! $this->tanggal_mulai) return '-';
        return $this->tanggal_mulai instanceof \Carbon\Carbon
            ? $this->tanggal_mulai->translatedFormat('d M Y')
            : \Carbon\Carbon::parse($this->tanggal_mulai)->translatedFormat('d M Y');
    }

    public function getTanggalSelesaiFormattedAttribute(): string
    {
        if (! $this->tanggal_selesai) return '-';
        return $this->tanggal_selesai instanceof \Carbon\Carbon
            ? $this->tanggal_selesai->translatedFormat('d M Y')
            : \Carbon\Carbon::parse($this->tanggal_selesai)->translatedFormat('d M Y');
    }

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }
    public function industri()
    {
        return $this->belongsTo(PrakerinIndustri::class, 'prakerin_industri_id');
    }
    public function guruPembimbing()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function rombelPkl()
    {
        return $this->belongsTo(PrakerinRombel::class, 'prakerin_rombel_id');
    }

    public function jurnals()
    {
        return $this->hasMany(PrakerinJurnal::class);
    }

    public function absensis()
    {
        return $this->hasMany(PrakerinAbsensi::class);
    }

    public function bimbinganLaporan()
    {
        return $this->hasOne(PrakerinBimbinganLaporan::class, 'prakerin_penempatan_id');
    }
}
