<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiswaCertificate extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'issuer',
        'issue_date',
        'expiration_date',
        'credential_id',
        'credential_url',
        'file_path',
        'description',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiration_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
