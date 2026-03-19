<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IqTestResult extends Model
{
    protected $fillable = [
        'user_id',
        'total_correct',
        'iq_score',
        'certificate_code',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
