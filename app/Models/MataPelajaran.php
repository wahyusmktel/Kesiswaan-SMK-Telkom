<?php

namespace App\Models;

use App\Models\Erapor\EraporSubjectMapping;
use App\Models\Erapor\EraporTeachingAssignment;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataPelajaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode_mapel',
        'nama_mapel',
        'jumlah_jam',
        'kelas_id',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function eraporMapping()
    {
        return $this->hasOne(EraporSubjectMapping::class);
    }

    public function eraporAssignments()
    {
        return $this->hasMany(EraporTeachingAssignment::class);
    }
}
