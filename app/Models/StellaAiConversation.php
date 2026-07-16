<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StellaAiConversation extends Model
{
    protected $table = 'stella_ai_conversations';

    protected $fillable = ['user_id', 'title', 'model'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(StellaAiMessage::class, 'conversation_id');
    }

    protected static function booted(): void
    {
        static::deleting(function (StellaAiConversation $conversation): void {
            $conversation->messages()
                ->whereNotNull('image_path')
                ->pluck('image_path')
                ->each(function (string $path): void {
                    if (!Str::startsWith($path, ['http://', 'https://'])) {
                        Storage::disk('public')->delete($path);
                    }
                });
        });
    }
}
