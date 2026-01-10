<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KeterlambatanCoaching extends Model
{
    protected $fillable = [
        'keterlambatan_id',
        'tanggal_coaching',
        'lokasi',
        'goal_response',
        'reality_response',
        'options_response',
        'will_response',
        'rencana_aksi',
        'konsekuensi_logis',
    ];

    public function keterlambatan()
    {
        return $this->belongsTo(Keterlambatan::class);
    }
}
