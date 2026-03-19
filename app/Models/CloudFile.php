<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CloudFile extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'file_name',
        'file_path',
        'mime_type',
        'extension',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getSizeForHumansAttribute()
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
