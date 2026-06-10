<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinRombel extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_rombel',
        'prakerin_industri_id',
        'pembimbing_internal_id',
        'pembimbing_external_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'status',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
    ];

    public function industri()
    {
        return $this->belongsTo(PrakerinIndustri::class, 'prakerin_industri_id');
    }

    public function pembimbingInternal()
    {
        return $this->belongsTo(PrakerinPembimbing::class, 'pembimbing_internal_id');
    }

    public function pembimbingExternal()
    {
        return $this->belongsTo(PrakerinPembimbing::class, 'pembimbing_external_id');
    }

    public function penempatans()
    {
        return $this->hasMany(PrakerinPenempatan::class, 'prakerin_rombel_id');
    }
}
