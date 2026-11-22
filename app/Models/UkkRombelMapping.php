<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkRombelMapping extends Model
{
    protected $table = 'ukk_rombel_mappings';

    protected $fillable = [
        'ukk_ujian_id',
        'rombel_id',
        'tanggal_pelaksanaan',
        'jam_mulai',
        'jam_selesai',
        'ruangan',
    ];

    protected $casts = [
        'tanggal_pelaksanaan' => 'date',
    ];

    public function ujian()
    {
        return $this->belongsTo(UkkUjian::class, 'ukk_ujian_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }
}
