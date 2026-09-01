<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RepositoryFile extends Model
{
    protected $fillable = [
        'public_token',
        'title',
        'description',
        'original_name',
        'path',
        'extension',
        'mime_type',
        'size',
        'is_active',
        'uploaded_by',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'size' => 'integer',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'public_token';
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getHumanSizeAttribute(): string
    {
        $bytes = max(0, $this->size);
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $unit = 0;

        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }

        return number_format($bytes, $unit === 0 ? 0 : 2, ',', '.').' '.$units[$unit];
    }
}
