<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HappinessMetric extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_fingerprint',
        'ip_address',
        'mood_level',
        'mood_score',
        'user_agent',
        'submitted_date',
    ];

    protected $casts = [
        'submitted_date' => 'date',
        'mood_score' => 'integer',
    ];

    /**
     * Get mood label in Indonesian
     */
    public function getMoodLabelAttribute(): string
    {
        return match ($this->mood_level) {
            'sangat_bahagia' => 'Sangat Bahagia',
            'bahagia' => 'Bahagia',
            'netral' => 'Netral',
            'sedih' => 'Sedih',
            'sangat_sedih' => 'Sangat Sedih',
            default => 'Tidak Diketahui',
        };
    }

    /**
     * Get mood emoji
     */
    public function getMoodEmojiAttribute(): string
    {
        return match ($this->mood_level) {
            'sangat_bahagia' => '😄',
            'bahagia' => '🙂',
            'netral' => '😐',
            'sedih' => '😢',
            'sangat_sedih' => '😭',
            default => '❓',
        };
    }
}
