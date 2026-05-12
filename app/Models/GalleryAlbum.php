<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class GalleryAlbum extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'description',
    ];

    protected static function booted(): void
    {
        static::creating(function (GalleryAlbum $album) {
            if (blank($album->slug)) {
                $album->slug = static::uniqueSlug($album->name);
            }
        });
    }

    public static function uniqueSlug(string $name): string
    {
        $base = Str::slug($name) ?: 'album';
        $slug = $base;
        $i = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    public function photos()
    {
        return $this->hasMany(GalleryPhoto::class);
    }

    public function latestPhotos()
    {
        return $this->hasMany(GalleryPhoto::class)->latest()->limit(4);
    }

    public function coverPhoto()
    {
        return $this->hasOne(GalleryPhoto::class)->latestOfMany();
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
