<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    use HasFactory;

    protected $fillable = [
        'judul',
        'slug',
        'ringkasan',
        'seo_title',
        'seo_description',
        'focus_keyword',
        'seo_keywords',
        'konten',
        'gambar',
        'kategori',
        'status',
        'published_at',
        'user_id',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * Generate slug otomatis dari judul.
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul).'-'.Str::random(5);
            }
        });
    }

    /**
     * Relasi ke user (penulis).
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function comments()
    {
        return $this->hasMany(BeritaComment::class);
    }

    /**
     * Scope: hanya berita yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Accessor untuk thumbnail URL.
     */
    public function getGambarUrlAttribute()
    {
        if ($this->gambar) {
            return \Storage::url($this->gambar);
        }

        return null;
    }
}
