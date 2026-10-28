<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'perizinan'; // Eksplisit mendefinisikan nama tabel

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'tanggal_izin',
        'jenis_izin',
        'keterangan',
        'dokumen_pendukung',
        'status',
        'alasan_penolakan',
        'disetujui_oleh',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'dokumen_url',
    ];

    /**
     * Accessor untuk URL dokumen pendukung yang valid.
     */
    public function getDokumenUrlAttribute(): ?string
    {
        if (! $this->dokumen_pendukung) {
            return null;
        }

        if (str_starts_with($this->dokumen_pendukung, 'http')) {
            return $this->dokumen_pendukung;
        }

        if ($this->id) {
            try {
                return route('izin.dokumen', $this->id);
            } catch (\Throwable $e) {
                return '/izin/' . $this->id . '/dokumen';
            }
        }

        return \Illuminate\Support\Facades\Storage::url($this->dokumen_pendukung);
    }

    /**
     * Mendefinisikan relasi ke model User (siswa yang mengajukan).
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Mendefinisikan relasi ke model User (pihak yang menyetujui/menolak).
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'disetujui_oleh');
    }
}
