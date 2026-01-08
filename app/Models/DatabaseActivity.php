<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DatabaseActivity extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'filename',
        'file_size',
        'tables_count',
        'details',
        'status',
        'error_message',
    ];

    protected $casts = [
        'details' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
