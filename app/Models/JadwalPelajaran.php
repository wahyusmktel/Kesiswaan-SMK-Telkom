<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

class JadwalPelajaran extends Model
{
    use HasFactory;
    protected $fillable = ['rombel_id', 'mata_pelajaran_id', 'master_guru_id', 'hari', 'jam_ke', 'jam_mulai', 'jam_selesai'];

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
    public function mataPelajaran()
    {
        return $this->belongsTo(MataPelajaran::class);
    }
    public function guru()
    {
        return $this->belongsTo(MasterGuru::class, 'master_guru_id');
    }
    public function izins()
    {
        return $this->belongsToMany(GuruIzin::class, 'guru_izin_jadwal')
                    ->using(GuruIzinJadwal::class)
                    ->withPivot(['lms_material_id', 'lms_assignment_id'])
                    ->withTimestamps();
    }
}
