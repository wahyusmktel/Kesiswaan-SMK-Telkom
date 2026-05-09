<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InfoTicker extends Model
{
    protected $fillable = ['konten', 'is_active', 'created_by'];

    protected $casts = ['is_active' => 'boolean'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
