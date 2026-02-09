<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Laragear\WebAuthn\WebauthnAuthenticatable;
use Laragear\WebAuthn\Contracts\WebauthnAuthenticatable as WebauthnAuthenticatableContract;

class User extends Authenticatable implements WebauthnAuthenticatableContract
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, WebauthnAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'avatar',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // Relasi untuk siswa: satu siswa hanya punya satu wali kelas
    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    // Relasi untuk wali kelas: satu wali kelas punya banyak siswa
    public function siswa()
    {
        return $this->hasMany(User::class, 'wali_kelas_id');
    }

    // Relasi dari User ke data induk siswanya
    public function masterSiswa()
    {
        return $this->hasOne(MasterSiswa::class);
    }
    // Relasi dari User (Wali Kelas) ke rombel yang diampunya
    public function rombels()
    {
        return $this->hasMany(Rombel::class, 'wali_kelas_id');
    }

    /**
     * Mendefinisikan relasi dari User ke Perizinan.
     * Satu user (siswa) bisa memiliki banyak perizinan.
     */
    public function perizinan()
    {
        return $this->hasMany(Perizinan::class);
    }

    /**
     * Mendefinisikan relasi dari User ke Perizinan.
     * Satu user (siswa) bisa memiliki banyak perizinan.
     */
    public function perizinanPiket()
    {
        return $this->hasMany(Perizinan::class, 'piket_id');
    }

    public function chatRoomsSiswa()
    {
        return $this->hasMany(BKChatRoom::class, 'siswa_user_id');
    }

    public function chatRoomsBK()
    {
        return $this->hasMany(BKChatRoom::class, 'guru_bk_user_id');
    }

    /**
     * Mendefinisikan relasi dari User ke Izin Meninggalkan Kelas.
     */
    public function izinMeninggalkanKelas()
    {
        return $this->hasMany(IzinMeninggalkanKelas::class);
    }

    public function masterGuru()
    {
        return $this->hasOne(MasterGuru::class);
    }

    public function notaDinasMasuk()
    {
        return $this->belongsToMany(NotaDinas::class, 'nota_dinas_penerima', 'penerima_user_id', 'nota_dinas_id')
            ->withPivot('is_read', 'read_at')
            ->withTimestamps();
    }

    public function notaDinasKeluar()
    {
        return $this->hasMany(NotaDinas::class, 'user_id');
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new \App\Notifications\CustomResetPasswordNotification($token));
    }

    public function surveysAsTarget()
    {
        return $this->belongsToMany(Survey::class, 'survey_targets');
    }
}
