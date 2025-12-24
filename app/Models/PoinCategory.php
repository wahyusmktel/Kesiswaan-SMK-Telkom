<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PoinCategory extends Model
{
    protected $fillable = ['name', 'description'];

    public function peraturans()
    {
        return $this->hasMany(PoinPeraturan::class);
    }
}
