<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiswaPanggilan extends Model
{
    use HasFactory;

    protected $fillable = [
        'master_siswa_id',
        'nomor_surat',
        'tanggal_panggilan',
        'jam_panggilan',
        'tempat_panggilan',
        'perihal',
        'status',
        'created_by',
        'disetujui_oleh',
        'catatan_waka',
    ];

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
