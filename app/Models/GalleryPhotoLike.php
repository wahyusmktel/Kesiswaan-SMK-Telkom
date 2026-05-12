<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPhotoLike extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_photo_id',
        'user_id',
    ];

    public function photo()
    {
        return $this->belongsTo(GalleryPhoto::class, 'gallery_photo_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
