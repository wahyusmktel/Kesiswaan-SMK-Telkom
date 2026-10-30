<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpmbBeritaAcara extends Model
{
    use HasFactory;

    protected $table = 'spmb_berita_acaras';

    protected $fillable = [
        'spmb_piket_tanggal_id',
        'tahun_ajaran',
        'nomor_surat',
        'judul',
        'tanggal_kegiatan',
        'waktu_mulai',
        'waktu_selesai',
        'tempat',
        'ringkasan_kegiatan',
        'jumlah_casis_hadir',
        'jumlah_tamu',
        'catatan_kendala',
        'kesimpulan',
        'kaur_spmb_user_id',
        'nama_penandatangan',
        'nip_penandatangan',
        'jabatan_penandatangan',
        'tempat_ttd',
        'tanggal_ttd',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal_kegiatan' => 'date',
        'jumlah_casis_hadir' => 'integer',
        'jumlah_tamu' => 'integer',
    ];

    public function tanggal(): BelongsTo
    {
        return $this->belongsTo(SpmbPiketTanggal::class, 'spmb_piket_tanggal_id');
    }

    public function kaurUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'kaur_spmb_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function getFormattedTanggalKegiatanAttribute(): string
    {
        if (! $this->tanggal_kegiatan) {
            return '-';
        }

        return Carbon::parse($this->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM Y');
    }

    public function getHariTanggalAttribute(): string
    {
        if (! $this->tanggal_kegiatan) {
            return '-';
        }

        return Carbon::parse($this->tanggal_kegiatan)->locale('id')->isoFormat('dddd, D MMMM Y');
    }
}
