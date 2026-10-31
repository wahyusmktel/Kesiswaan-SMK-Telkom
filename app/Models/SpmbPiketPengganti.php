<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPiketPengganti extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_penggantis';

    protected $fillable = [
        'spmb_piket_tanggal_id',
        'spmb_piket_petugas_id',
        'master_guru_id',
        'user_id',
        'nama_pengganti',
        'jabatan_pengganti',
        'alasan',
        'created_by',
    ];

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function petugasAsli(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketPetugas::class, 'spmb_piket_petugas_id');
    }

    public function masterGuru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
