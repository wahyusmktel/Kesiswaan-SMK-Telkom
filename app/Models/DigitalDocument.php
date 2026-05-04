<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DigitalDocument extends Model
{
    protected $fillable = [
        'token',
        'document_type',
        'document_title',
        'reference_id',
        'document_hash',
        'hmac_signature',
        'signed_by',
        'signer_name',
        'signer_nip',
        'signer_role',
        'signed_at',
        'is_valid',
        'revoked_at',
        'revoke_reason',
    ];

    protected $casts = [
        'signed_at'  => 'datetime',
        'revoked_at' => 'datetime',
        'is_valid'   => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function ($model) {
            if (empty($model->token)) {
                $model->token = (string) Str::uuid();
            }
        });
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signed_by');
    }

    public function verifyIntegrity(string $hash): bool
    {
        return hash_equals($this->document_hash, $hash);
    }

    public static function generateHash(array $content): string
    {
        return hash('sha256', implode('|', $content));
    }

    public static function generateHmac(string $hash): string
    {
        $key = base64_decode(str_replace('base64:', '', config('app.key')));
        return hash_hmac('sha256', $hash, $key);
    }

    public function verifyHmac(): bool
    {
        $expected = self::generateHmac($this->document_hash);
        return hash_equals($expected, $this->hmac_signature);
    }
}
