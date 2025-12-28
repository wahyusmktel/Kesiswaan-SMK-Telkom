<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotaDinasPenerima extends Model
{
    use HasFactory;

    protected $table = 'nota_dinas_penerima';

    protected $fillable = [
        'nota_dinas_id',
        'penerima_user_id',
        'is_read',
        'read_at',
    ];

    public function notaDinas()
    {
        return $this->belongsTo(NotaDinas::class, 'nota_dinas_id');
    }

    public function penerima()
    {
        return $this->belongsTo(User::class, 'penerima_user_id');
    }
}
