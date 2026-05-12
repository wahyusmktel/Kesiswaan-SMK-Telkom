<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class GalleryPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'gallery_album_id',
        'user_id',
        'title',
        'caption',
        'image_path',
        'original_name',
        'mime_type',
        'size',
        'taken_at',
    ];

    protected $casts = [
        'taken_at' => 'datetime',
    ];

    protected $appends = [
        'image_url',
        'size_for_humans',
    ];

    public function getImageUrlAttribute(): string
    {
        return Storage::url($this->image_path);
    }

    public function getSizeForHumansAttribute(): string
    {
        $size = (int) $this->size;

        if ($size >= 1048576) {
            return number_format($size / 1048576, 1) . ' MB';
        }

        return number_format(max($size, 1) / 1024, 0) . ' KB';
    }

    public function album()
    {
        return $this->belongsTo(GalleryAlbum::class, 'gallery_album_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likes()
    {
        return $this->hasMany(GalleryPhotoLike::class);
    }

    public function comments()
    {
        return $this->hasMany(GalleryPhotoComment::class);
    }
}
