<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterGuru extends Model
{
    use HasFactory;

    protected $table = 'master_gurus';

    protected $fillable = [
        'nuptk',
        'nama_lengkap',
        'jenis_kelamin',
        'user_id',
    ];

    /**
     * Mendefinisikan relasi ke model User (akun login guru).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jadwalPelajaran()
    {
        return $this->hasMany(JadwalPelajaran::class);
    }

    public function penempatan()
    {
        return $this->hasMany(PrakerinPenempatan::class, 'master_guru_id');
    }
}
