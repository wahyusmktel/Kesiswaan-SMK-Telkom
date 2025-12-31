<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterSiswa extends Model
{
    protected $table = "master_siswa";

    // Jangan lupa tambahkan $fillable
    protected $fillable = ['nis', 'nama_lengkap', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat', 'user_id', 'last_synced_at'];

    protected $casts = [
        'tanggal_lahir' => 'date',
        'last_synced_at' => 'datetime',
    ];

    // Relasi dari MasterSiswa ke akun loginnya
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // Relasi dari MasterSiswa ke banyak rombel (many-to-many)
    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_siswa');
    }

    /**
     * Mendefinisikan relasi "has many through" ke Perizinan melalui User.
     * Ini adalah "jalan pintas" dari MasterSiswa ke Perizinan.
     */
    public function perizinan()
    {
        return $this->hasManyThrough(
            Perizinan::class, // Model tujuan akhir
            User::class,      // Model perantara
            'id',             // Foreign key di tabel users (yang terhubung ke master_siswa)
            'user_id',        // Foreign key di tabel perizinan (yang terhubung ke users)
            'user_id',        // Local key di tabel master_siswa
            'id'              // Local key di tabel users
        );
    }

    public function dispensasi()
    {
        return $this->belongsToMany(Dispensasi::class, 'dispensasi_siswa');
    }

    public function penempatan()
    {
        return $this->hasOne(PrakerinPenempatan::class, 'master_siswa_id');
    }

    // Relationships for Poin System
    public function pelanggarans()
    {
        return $this->hasMany(SiswaPelanggaran::class, 'master_siswa_id');
    }

    public function prestasis()
    {
        return $this->hasMany(SiswaPrestasi::class, 'master_siswa_id');
    }

    public function pemutihans()
    {
        return $this->hasMany(SiswaPemutihan::class, 'master_siswa_id');
    }

    public function panggilans()
    {
        return $this->hasMany(SiswaPanggilan::class, 'master_siswa_id');
    }

    public function pembinaanRutins()
    {
        return $this->hasMany(BKPembinaanRutin::class, 'master_siswa_id');
    }

    public function konsultasiJadwals()
    {
        return $this->hasMany(BKKonsultasiJadwal::class, 'master_siswa_id');
    }

    public function keterlambatans()
    {
        return $this->hasMany(Keterlambatan::class, 'master_siswa_id');
    }

    public function dapodik()
    {
        return $this->hasOne(DapodikSiswa::class, 'master_siswa_id');
    }

    public function dapodikSubmissions()
    {
        return $this->hasMany(DapodikSubmission::class, 'master_siswa_id');
    }

    // Point Calculation Methods
    public function getTotalViolationPoints()
    {
        return $this->pelanggarans()
            ->join('poin_peraturans', 'siswa_pelanggarans.poin_peraturan_id', '=', 'poin_peraturans.id')
            ->sum('poin_peraturans.bobot_poin');
    }

    public function getTotalAchievementPoints()
    {
        return $this->prestasis()->sum('poin_bonus');
    }

    public function getTotalExpungementPoints()
    {
        return $this->pemutihans()->where('status', 'disetujui')->sum('poin_dikurangi');
    }

    public function getCurrentPoints()
    {
        $violations = $this->getTotalViolationPoints();
        $achievements = $this->getTotalAchievementPoints();
        $expungements = $this->getTotalExpungementPoints();

        // Current Points = Violations - Achievements - Expungements
        // Point cannot be negative (or can it? Usually 0 is the floor for bad points)
        $total = $violations - $achievements - $expungements;
        
        return max(0, $total);
    }

    public function getPointStatus()
    {
        $points = $this->getCurrentPoints();
        
        if ($points >= 150) return ['label' => 'Sangat Kritis', 'color' => 'red', 'class' => 'bg-red-600'];
        if ($points >= 100) return ['label' => 'Kritis', 'color' => 'orange', 'class' => 'bg-orange-600'];
        if ($points >= 75) return ['label' => 'Peringatan Keras', 'color' => 'yellow', 'class' => 'bg-yellow-600'];
        if ($points >= 50) return ['label' => 'Peringatan', 'color' => 'yellow', 'class' => 'bg-yellow-500'];
        if ($points >= 25) return ['label' => 'Waspada', 'color' => 'blue', 'class' => 'bg-blue-500'];
        
        return ['label' => 'Aman', 'color' => 'green', 'class' => 'bg-green-500'];
    }
}
