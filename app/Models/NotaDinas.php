<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class NotaDinas extends Model
{
    use HasFactory;

    protected $table = 'nota_dinas';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'nomor_nota',
        'jenis_id',
        'perihal',
        'isi',
        'tanggal',
        'lampiran',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function pengirim()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function jenis()
    {
        return $this->belongsTo(NdeRefJenis::class, 'jenis_id');
    }

    public function penerimas()
    {
        return $this->belongsToMany(User::class, 'nota_dinas_penerima', 'nota_dinas_id', 'penerima_user_id')
                    ->withPivot('is_read', 'read_at')
                    ->withTimestamps();
    }
    
    public function notaDinasPenerima()
    {
        return $this->hasMany(NotaDinasPenerima::class, 'nota_dinas_id');
    }
}
