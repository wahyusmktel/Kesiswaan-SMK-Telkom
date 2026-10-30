<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPiketPetugas extends Model
{
    use HasFactory;

    protected $table = 'spmb_piket_petugas';

    protected $fillable = [
        'spmb_piket_kelompok_id',
        'master_guru_id',
        'user_id',
        'nama_petugas',
        'jabatan',
        'urutan',
        'keterangan',
    ];

    public function kelompok(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketKelompok::class, 'spmb_piket_kelompok_id');
    }

    public function masterGuru(): BelongsTo
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
