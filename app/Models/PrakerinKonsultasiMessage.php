<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrakerinKonsultasiMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'prakerin_rombel_id',
        'sender_id',
        'receiver_id',
        'type',
        'message',
    ];

    public function rombel()
    {
        return $this->belongsTo(PrakerinRombel::class, 'prakerin_rombel_id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }
}
