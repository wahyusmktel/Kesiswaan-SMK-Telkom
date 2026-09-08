<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'status_kepala_sekolah',
        'piket_id',
        'kurikulum_id',
        'sdm_id',
        'kepala_sekolah_id',
        'piket_at',
        'kurikulum_at',
        'sdm_at',
        'kepala_sekolah_at',
        'catatan_piket',
        'catatan_kurikulum',
        'catatan_sdm',
        'catatan_kepala_sekolah',
    ];

    protected $casts = [
        'tanggal_mulai' => 'datetime',
        'tanggal_selesai' => 'datetime',
        'piket_at' => 'datetime',
        'kurikulum_at' => 'datetime',
        'sdm_at' => 'datetime',
        'kepala_sekolah_at' => 'datetime',
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

    public function kepalaSekolah()
    {
        return $this->belongsTo(User::class, 'kepala_sekolah_id');
    }

    public function isFullyApproved(): bool
    {
        return $this->status_sdm === 'disetujui'
            && in_array($this->status_kepala_sekolah, ['disetujui', 'tidak_diperlukan'], true);
    }

    public function scopeFullyApproved($query)
    {
        return $query->where('status_sdm', 'disetujui')
            ->whereIn('status_kepala_sekolah', ['disetujui', 'tidak_diperlukan']);
    }

    public function jadwals()
    {
        return $this->belongsToMany(JadwalPelajaran::class, 'guru_izin_jadwal')
            ->using(GuruIzinJadwal::class)
            ->withPivot(['lms_material_id', 'lms_assignment_id'])
            ->withTimestamps();
    }
}
