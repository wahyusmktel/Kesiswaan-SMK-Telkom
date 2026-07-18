<?php

namespace App\Services;

use App\Exceptions\OkrAiException;
use App\Models\AppSetting;
use App\Models\OkrKeyResult;
use App\Models\OkrUnit;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class OkrAiService
{
    public function suggest(
        OkrKeyResult $keyResult,
        OkrUnit $unit,
        string $level,
        ?string $context = null
    ): array {
        $setting = AppSetting::first();
        if (! $this->isReady($setting)) {
            throw new OkrAiException('Stella AI belum aktif atau konfigurasi model belum lengkap.', 422);
        }

        $payload = [
            'model' => $setting->stella_ai_chat_model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah Stella AI, konsultan manajemen sekolah dan ahli OKR. Buat target yang spesifik, terukur, realistis, relevan dengan tugas unit, memiliki batas waktu, dan langsung dapat dilaksanakan. Jawaban wajib hanya JSON valid tanpa markdown.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->prompt($keyResult, $unit, $level, $context),
                ],
            ],
            'stream' => false,
            'max_tokens' => 2500,
        ];

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($setting->stella_ai_api_key)
                ->timeout(180)
                ->post(rtrim($setting->stella_ai_base_url, '/').'/chat/completions', $payload);

            if ($response->status() === 400 && str_contains(Str::lower($response->body()), 'max_tokens')) {
                unset($payload['max_tokens']);
                $response = Http::acceptJson()
                    ->asJson()
                    ->withToken($setting->stella_ai_api_key)
                    ->timeout(180)
                    ->post(rtrim($setting->stella_ai_base_url, '/').'/chat/completions', $payload);
            }
        } catch (\Throwable $exception) {
            Log::warning('Stella AI OKR provider connection failed.', [
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'key_result_id' => $keyResult->id,
                'unit_id' => $unit->id,
                'exception' => $exception,
            ]);

            throw new OkrAiException('Koneksi ke Stella AI gagal. Silakan coba kembali.', 502, $exception);
        }

        if ($response->failed()) {
            $message = data_get($response->json(), 'error.message')
                ?? data_get($response->json(), 'message')
                ?? 'Provider tidak memberikan detail error.';

            throw new OkrAiException(
                'Provider Stella AI menolak permintaan (HTTP '.$response->status().'): '.Str::limit(strip_tags((string) $message), 250)
            );
        }

        $raw = data_get($response->json(), 'choices.0.message.content')
            ?: data_get($response->json(), 'choices.0.message.reasoning_content');
        if (! is_string($raw) || trim($raw) === '') {
            throw new OkrAiException('Stella AI tidak mengembalikan rekomendasi OKR yang dapat dibaca.');
        }

        $decoded = $this->decodeJson($raw);
        $suggestions = $decoded['suggestions'] ?? $decoded;
        if (! is_array($suggestions)) {
            throw new OkrAiException('Struktur rekomendasi Stella AI tidak sesuai.');
        }

        return collect($suggestions)
            ->filter(fn ($item) => is_array($item) && filled($item['title'] ?? null))
            ->take(5)
            ->map(fn ($item) => [
                'title' => Str::limit(strip_tags((string) ($item['title'] ?? '')), 255, ''),
                'description' => Str::limit(strip_tags((string) ($item['description'] ?? '')), 2000, ''),
                'success_indicator' => Str::limit(strip_tags((string) ($item['success_indicator'] ?? '')), 2000, ''),
                'target_value' => is_numeric($item['target_value'] ?? null) ? (float) $item['target_value'] : 100,
                'metric_unit' => Str::limit(strip_tags((string) ($item['metric_unit'] ?? '%')), 40, ''),
            ])
            ->values()
            ->all();
    }

    private function prompt(OkrKeyResult $keyResult, OkrUnit $unit, string $level, ?string $context): string
    {
        return 'Susun 3 alternatif rencana kerja tingkat '.$level.' untuk unit '.$unit->name.".\n"
            .'Objektif sekolah: '.$keyResult->objective->title."\n"
            .'Key Result: '.$keyResult->code.' '.$keyResult->title."\n"
            .'Target utama: '.$keyResult->description."\n"
            .'Target angka: '.$keyResult->target_value.' '.$keyResult->metric_unit."\n"
            .'Konteks tambahan pengguna: '.($context ?: 'Tidak ada')."\n\n"
            .'Kembalikan JSON dengan format persis: {"suggestions":[{"title":"","description":"","success_indicator":"","target_value":100,"metric_unit":"%"}]}.';
    }

    private function decodeJson(string $content): array
    {
        $json = trim($content);
        $json = preg_replace('/^```(?:json)?\s*/i', '', $json) ?? $json;
        $json = preg_replace('/\s*```$/', '', $json) ?? $json;
        $firstBrace = strpos($json, '{');
        $lastBrace = strrpos($json, '}');
        if ($firstBrace !== false && $lastBrace !== false) {
            $json = substr($json, $firstBrace, $lastBrace - $firstBrace + 1);
        }

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new OkrAiException('Format rekomendasi Stella AI belum valid. Silakan hasilkan ulang.', 502, $exception);
        }

        if (! is_array($decoded)) {
            throw new OkrAiException('Struktur rekomendasi Stella AI tidak sesuai.');
        }

        return $decoded;
    }

    private function isReady(?AppSetting $setting): bool
    {
        return (bool) (
            $setting?->stella_ai_enabled
            && $setting->stella_ai_base_url
            && $setting->stella_ai_api_key
            && $setting->stella_ai_chat_model
        );
    }
}
