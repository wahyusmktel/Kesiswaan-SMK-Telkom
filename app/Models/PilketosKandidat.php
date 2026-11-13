<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class PilketosKandidat extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'nomor_urut' => 'integer',
    ];

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PilketosPeriode::class, 'pilketos_periode_id');
    }

    public function ketuaSiswa(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'ketua_siswa_id');
    }

    public function wakilSiswa(): BelongsTo
    {
        return $this->belongsTo(MasterSiswa::class, 'wakil_siswa_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(PilketosVote::class, 'pilketos_kandidat_id');
    }

    public function getFotoPaslonUrlAttribute(): string
    {
        if ($this->foto_paslon) {
            return Storage::url($this->foto_paslon);
        }

        if ($this->foto_ketua) {
            return Storage::url($this->foto_ketua);
        }

        if ($this->ketuaSiswa && $this->ketuaSiswa->foto) {
            return Storage::url($this->ketuaSiswa->foto);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_ketua) . '&background=dc2626&color=ffffff&size=256';
    }

    public function getFotoKetuaUrlAttribute(): string
    {
        if ($this->foto_ketua) {
            return Storage::url($this->foto_ketua);
        }

        if ($this->ketuaSiswa && $this->ketuaSiswa->foto) {
            return Storage::url($this->ketuaSiswa->foto);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_ketua) . '&background=ef4444&color=ffffff';
    }

    public function getFotoWakilUrlAttribute(): ?string
    {
        if ($this->foto_wakil) {
            return Storage::url($this->foto_wakil);
        }

        if ($this->wakilSiswa && $this->wakilSiswa->foto) {
            return Storage::url($this->wakilSiswa->foto);
        }

        if ($this->nama_wakil) {
            return 'https://ui-avatars.com/api/?name=' . urlencode($this->nama_wakil) . '&background=3b82f6&color=ffffff';
        }

        return null;
    }

    public function getVotesCountAttribute(): int
    {
        return $this->votes()->count();
    }
}
