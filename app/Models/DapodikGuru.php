<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DapodikGuru extends Model
{
    protected $table = 'dapodik_gurus';

    protected $fillable = [
        'master_guru_id',
        'nik', 'nama', 'nuptk', 'jenis_kelamin', 'tempat_lahir', 'tanggal_lahir', 'agama', 'kewarganegaraan',
        'nip', 'status_kepegawaian', 'jenis_ptk', 'tugas_tambahan',
        'sk_cpns', 'tanggal_cpns', 'sk_pengangkatan', 'tmt_pengangkatan',
        'lembaga_pengangkatan', 'pangkat_golongan', 'sumber_gaji', 'tmt_pns',
        'lisensi_kepala_sekolah', 'diklat_kepengawasan', 'keahlian_braille', 'keahlian_bahasa_isyarat', 'nuks',
        'alamat_jalan', 'rt', 'rw', 'nama_dusun', 'desa_kelurahan', 'kecamatan', 'kode_pos',
        'telepon', 'hp', 'email_dapodik',
        'nama_ibu_kandung', 'status_perkawinan', 'nama_pasangan', 'nip_pasangan', 'pekerjaan_pasangan', 'no_kk',
        'npwp', 'nama_wajib_pajak', 'bank', 'no_rekening', 'rekening_atas_nama', 'karpeg', 'karis_karsu',
        'lintang', 'bujur',
    ];

    protected $casts = [
        'tanggal_lahir'   => 'date',
        'tanggal_cpns'    => 'date',
        'tmt_pengangkatan'=> 'date',
        'tmt_pns'         => 'date',
    ];

    public function masterGuru()
    {
        return $this->belongsTo(MasterGuru::class);
    }

    public function getJenisKelaminLabelAttribute(): string
    {
        return match($this->jenis_kelamin) {
            'L' => 'Laki-laki',
            'P' => 'Perempuan',
            default => '-',
        };
    }

    public function getIsLinkedAttribute(): bool
    {
        return $this->master_guru_id !== null;
    }
}
