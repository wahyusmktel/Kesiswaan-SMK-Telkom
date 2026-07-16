<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StellaAiMessage extends Model
{
    protected $table = 'stella_ai_messages';

    protected $fillable = [
        'conversation_id',
        'role',
        'content',
        'image_path',
        'type',
    ];

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(StellaAiConversation::class, 'conversation_id');
    }
}
