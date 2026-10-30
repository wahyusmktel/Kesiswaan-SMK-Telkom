<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbPesertaSeleksi extends Model
{
    use HasFactory;

    protected $table = 'spmb_peserta_seleksis';

    protected $fillable = [
        'user_id',
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'nomor_pendaftaran',
        'nama_lengkap',
        'nisn',
        'nik',
        'jenis_kelamin',
        'asal_sekolah',
        'jurusan_pilihan',
        'jurusan_pilihan_2',
        'no_wa_siswa',
        'nama_orang_tua',
        'hubungan_wali',
        'no_wa_orang_tua',
        'raw_password',
        'status_kehadiran_siswa',
        'status_kehadiran_ortu',
        'waktu_kehadiran_siswa',
        'waktu_kehadiran_ortu',
        'status_seleksi',
        'catatan',
        'created_by',
    ];

    protected $casts = [
        'waktu_kehadiran_siswa' => 'datetime',
        'waktu_kehadiran_ortu' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedTanggalAttribute(): string
    {
        if (! $this->tanggal?->tanggal) {
            return '-';
        }

        return Carbon::parse($this->tanggal->tanggal)->locale('id')->isoFormat('dddd, D MMMM Y');
    }
}
