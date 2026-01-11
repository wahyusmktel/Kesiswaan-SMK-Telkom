<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\MasterGuru;
use App\Models\JadwalPelajaran;
use App\Models\User;

class GuruIzin extends Model
{
    protected $fillable = [
        'master_guru_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'kategori_penyetujuan',
        'deskripsi',
        'dokumen_pdf',
        'status_piket',
        'status_kurikulum',
        'status_sdm',
        'piket_id',
        'kurikulum_id',
        'sdm_id',
        'piket_at',
        'kurikulum_at',
        'sdm_at',
        'catatan_piket',
        'catatan_kurikulum',
        'catatan_sdm',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'piket_at' => 'datetime',
        'kurikulum_at' => 'datetime',
        'sdm_at' => 'datetime',
    ];

    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function piket()
    {
        return $this->belongsTo(User::class, 'piket_id');
    }

    public function kurikulum()
    {
        return $this->belongsTo(User::class, 'kurikulum_id');
    }

    public function sdm()
    {
        return $this->belongsTo(User::class, 'sdm_id');
    }

    public function jadwals()
    {
        return $this->belongsToMany(JadwalPelajaran::class, 'guru_izin_jadwal')
                    ->using(GuruIzinJadwal::class)
                    ->withPivot(['lms_material_id', 'lms_assignment_id'])
                    ->withTimestamps();
    }
}
