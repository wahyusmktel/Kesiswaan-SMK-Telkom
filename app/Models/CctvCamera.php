<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CctvCamera extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'location',
        'description',
        'rtsp_url',
        'stream_path',
        'is_active',
        'sort_order',
        'last_sync_status',
        'last_sync_message',
        'last_synced_at',
    ];

    protected $hidden = [
        'rtsp_url',
    ];

    protected function casts(): array
    {
        return [
            'rtsp_url' => 'encrypted',
            'is_active' => 'boolean',
            'last_synced_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'cctv_camera_user')->withTimestamps();
    }

    public function accessLogs()
    {
        return $this->hasMany(CctvAccessLog::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getMaskedRtspUrlAttribute(): string
    {
        return preg_replace('#^(rtsps?://)(?:[^/@]+@)?([^/]+)(.*)$#i', '$1***:***@$2$3', $this->rtsp_url)
            ?: 'RTSP tersimpan aman';
    }
}
