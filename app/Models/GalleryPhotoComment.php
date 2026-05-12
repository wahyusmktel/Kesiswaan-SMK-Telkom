<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GalleryPhotoComment extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_photo_id',
        'user_id',
        'parent_id',
        'body',
    ];

    public function photo()
    {
        return $this->belongsTo(GalleryPhoto::class, 'gallery_photo_id');
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function replies()
    {
        return $this->hasMany(self::class, 'parent_id')->oldest();
    }
}
