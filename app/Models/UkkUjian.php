<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkUjian extends Model
{
    protected $fillable = [
        'nama_ujian',
        'tahun_pelajaran_id',
        'jurusan',
        'nama_project',
        'tanggal_pelaksanaan',
        'is_bilingual',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
        'is_bilingual'        => 'boolean',
    ];

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tahun_pelajaran_id');
    }

    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'ukk_rombel_mappings')
            ->withPivot(['id', 'tanggal_pelaksanaan', 'jam_mulai', 'jam_selesai', 'ruangan'])
            ->withTimestamps();
    }

    public function rombelMappings()
    {
        return $this->hasMany(UkkRombelMapping::class, 'ukk_ujian_id');
    }

    public function instrumens()
    {
        return $this->hasMany(UkkInstrumen::class, 'ukk_ujian_id');
    }

    public function nilaiSikap()
    {
        return $this->hasMany(UkkNilaiSikap::class, 'ukk_ujian_id');
    }

    public function penguji()
    {
        return $this->belongsToMany(User::class, 'ukk_penguji')
            ->withPivot(['id', 'rombel_id', 'peran'])
            ->withTimestamps();
    }

    public function ukkPengujis()
    {
        return $this->hasMany(UkkPenguji::class, 'ukk_ujian_id');
    }

    public function pengujiPengetahuan()
    {
        return $this->belongsToMany(User::class, 'ukk_penguji')
            ->wherePivotIn('peran', ['semua', 'pengetahuan'])
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function pengujiKeterampilan()
    {
        return $this->belongsToMany(User::class, 'ukk_penguji')
            ->wherePivotIn('peran', ['semua', 'keterampilan'])
            ->withPivot('peran')
            ->withTimestamps();
    }

    public function hasSoalEsai(): bool
    {
        return $this->instrumens
            ->flatMap(fn ($i) => $i->soalPengetahuan)
            ->filter(fn ($s) => empty($s->jenis_soal) || $s->jenis_soal === 'esai')
            ->isNotEmpty();
    }

    public function hasSoalBilingual(): bool
    {
        if ($this->is_bilingual) {
            return true;
        }

        return $this->instrumens
            ->flatMap(fn ($i) => $i->soalPengetahuan)
            ->where('jenis_soal', 'bilingual')
            ->isNotEmpty();
    }

    public function hasSoalObservasi(): bool
    {
        return $this->instrumens
            ->flatMap(fn ($i) => $i->soalPengetahuan)
            ->where('jenis_soal', 'observasi')
            ->isNotEmpty();
    }
}
