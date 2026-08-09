<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaProject extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image_path',
        'github_url',
        'project_url',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
