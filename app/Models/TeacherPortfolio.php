<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_profile_id',
        'judul',
        'slug',
        'kategori',
        'deskripsi',
        'gambar',
        'tautan_proyek',
        'tahun',
        'teknologi',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'teknologi' => 'array',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted()
    {
        static::creating(function (TeacherPortfolio $portfolio) {
            if (empty($portfolio->slug)) {
                $portfolio->slug = Str::slug($portfolio->judul) . '-' . Str::random(5);
            }
        });
    }

    public function teacherProfile(): BelongsTo
    {
        return $this->belongsTo(TeacherProfile::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->gambar ? Storage::url($this->gambar) : null;
    }
}
