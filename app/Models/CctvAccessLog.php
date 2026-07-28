<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CctvAccessLog extends Model
{
    public const UPDATED_AT = null;

    protected $fillable = [
        'cctv_camera_id',
        'user_id',
        'action',
        'ip_hash',
        'user_agent',
    ];

    public function camera()
    {
        return $this->belongsTo(CctvCamera::class, 'cctv_camera_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
