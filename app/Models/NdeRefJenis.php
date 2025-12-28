<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NdeRefJenis extends Model
{
    use HasFactory;

    protected $table = 'nde_ref_jenis';
    protected $fillable = ['nama', 'kode'];

    public function notaDinas()
    {
        return $this->hasMany(NotaDinas::class, 'jenis_id');
    }
}
