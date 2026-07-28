<?php

namespace App\Services;

use App\Models\CctvCamera;
use App\Models\User;
use Illuminate\Support\Str;

class CctvPlaybackToken
{
    public function issue(User $user, CctvCamera $camera): array
    {
        $expiresAt = now()->addSeconds((int) config('services.cctv.playback_token_ttl', 900))->timestamp;
        $payload = [
            'uid' => $user->getKey(),
            'cid' => $camera->getKey(),
            'path' => $camera->stream_path,
            'exp' => $expiresAt,
            'nonce' => Str::random(16),
        ];

        $encodedPayload = $this->base64UrlEncode(json_encode($payload, JSON_THROW_ON_ERROR));
        $signature = $this->base64UrlEncode(hash_hmac('sha256', $encodedPayload, $this->secret(), true));

        return [
            'token' => $encodedPayload.'.'.$signature,
            'expires_at' => $expiresAt,
        ];
    }

    public function verify(?string $token): ?array
    {
        if (! $token || ! str_contains($token, '.')) {
            return null;
        }

        [$encodedPayload, $encodedSignature] = explode('.', $token, 2);
        $expectedSignature = hash_hmac('sha256', $encodedPayload, $this->secret(), true);
        $actualSignature = $this->base64UrlDecode($encodedSignature);

        if ($actualSignature === null || ! hash_equals($expectedSignature, $actualSignature)) {
            return null;
        }

        try {
            $payload = json_decode($this->base64UrlDecode($encodedPayload) ?: '', true, flags: JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return null;
        }

        if (! is_array($payload) || ($payload['exp'] ?? 0) < now()->timestamp) {
            return null;
        }

        return $payload;
    }

    private function secret(): string
    {
        $secret = (string) config('services.cctv.playback_token_secret');
        if ($secret === '') {
            $secret = (string) config('app.key');
        }

        return str_starts_with($secret, 'base64:')
            ? (base64_decode(substr($secret, 7), true) ?: $secret)
            : $secret;
    }

    private function base64UrlEncode(string $value): string
    {
        return rtrim(strtr(base64_encode($value), '+/', '-_'), '=');
    }

    private function base64UrlDecode(string $value): ?string
    {
        $decoded = base64_decode(strtr($value, '-_', '+/'), true);

        return $decoded === false ? null : $decoded;
    }
}
