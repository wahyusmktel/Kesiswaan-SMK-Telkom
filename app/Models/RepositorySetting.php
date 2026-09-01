<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RepositorySetting extends Model
{
    protected $fillable = ['local_base_url', 'public_base_url'];

    public static function current(): self
    {
        return static::query()->first() ?? new static([
            'local_base_url' => config('repository.local_base_url'),
            'public_base_url' => config('repository.public_base_url'),
        ]);
    }
}
