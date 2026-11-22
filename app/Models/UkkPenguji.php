<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkPenguji extends Model
{
    protected $table = 'ukk_penguji';

    protected $fillable = [
        'ukk_ujian_id',
        'rombel_id',
        'user_id',
        'peran',
        'no_urut_mulai',
        'no_urut_selesai',
    ];

    public function ujian()
    {
        return $this->belongsTo(UkkUjian::class, 'ukk_ujian_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class, 'rombel_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
