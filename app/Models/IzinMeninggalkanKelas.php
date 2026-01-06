<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class IzinMeninggalkanKelas extends Model
{
    use HasFactory;

    protected $table = 'izin_meninggalkan_kelas';

    protected $fillable = [
        'uuid',
        'user_id',
        'rombel_id',
        'jadwal_pelajaran_id',
        'jenis_izin',
        'tujuan',
        'keterangan',
        'estimasi_kembali',
        'waktu_keluar_sebenarnya',
        'waktu_kembali_sebenarnya',
        'status',
        'guru_kelas_approval_id',
        'guru_kelas_approved_at',
        'guru_piket_approval_id',
        'guru_piket_approved_at',
        'security_verification_id',
        'security_verified_at',
        'alasan_penolakan',
        'ditolak_oleh',
    ];

    // Otomatis membuat UUID saat data baru akan disimpan
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }

    // Relasi ke siswa yang mengajukan
    public function siswa()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    // Relasi ke rombel
    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
    // Relasi ke guru kelas yang menyetujui
    public function guruKelasApprover()
    {
        return $this->belongsTo(User::class, 'guru_kelas_approval_id');
    }
    // Relasi ke guru piket yang menyetujui
    public function guruPiketApprover()
    {
        return $this->belongsTo(User::class, 'guru_piket_approval_id');
    }
    // Relasi ke security yang memverifikasi
    public function securityVerifier()
    {
        return $this->belongsTo(User::class, 'security_verification_id');
    }
    // Relasi ke user yang menolak
    public function penolak()
    {
        return $this->belongsTo(User::class, 'ditolak_oleh');
    }

    // Relasi ke jadwal pelajaran saat izin dibuat
    public function jadwalPelajaran()
    {
        return $this->belongsTo(JadwalPelajaran::class);
    }
}
