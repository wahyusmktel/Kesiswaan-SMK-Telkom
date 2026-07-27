<?php

namespace App\Services;

use App\Exceptions\NewsArticleAiException;
use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class NewsArticleAiGenerator
{
    public function generate(
        string $category,
        bool $useAiRecommendation,
        ?int $paragraphCount = null,
        ?int $sentencesPerParagraph = null
    ): array {
        $setting = AppSetting::first();
        if (! $this->isReady($setting)) {
            throw new NewsArticleAiException('Stella AI belum aktif atau konfigurasi model belum lengkap.', 422);
        }

        $payload = [
            'model' => $setting->stella_ai_chat_model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah Stella AI, redaktur berita resmi sekolah Indonesia. Gunakan Bahasa Indonesia baku, hangat, informatif, dan mudah dipahami. Jangan mengarang nama orang, tanggal, angka prestasi, kutipan, atau fakta spesifik yang tidak diberikan. Jika detail faktual tidak tersedia, tulis artikel institusional yang tetap relevan tanpa klaim palsu. Jawaban wajib hanya JSON valid tanpa markdown.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->prompt(
                        $setting->school_name ?: 'SMK Telkom Lampung',
                        $category,
                        $useAiRecommendation,
                        $paragraphCount,
                        $sentencesPerParagraph
                    ),
                ],
            ],
            'stream' => false,
            'max_tokens' => 5000,
        ];

        try {
            $response = $this->sendRequest($setting, $payload);

            if ($response->status() === 400 && str_contains(Str::lower($response->body()), 'max_tokens')) {
                unset($payload['max_tokens']);
                $response = $this->sendRequest($setting, $payload);
            }
        } catch (\Throwable $exception) {
            Log::warning('Stella AI news article generation connection failed.', [
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'category' => $category,
                'exception' => $exception,
            ]);

            throw new NewsArticleAiException('Koneksi ke Stella AI gagal. Silakan coba kembali.', 502, $exception);
        }

        if ($response->failed()) {
            $message = data_get($response->json(), 'error.message')
                ?? data_get($response->json(), 'message')
                ?? 'Provider tidak memberikan detail error.';

            throw new NewsArticleAiException(
                'Provider Stella AI menolak permintaan (HTTP '.$response->status().'): '
                .Str::limit(strip_tags((string) $message), 250),
                502
            );
        }

        $raw = data_get($response->json(), 'choices.0.message.content')
            ?: data_get($response->json(), 'choices.0.message.reasoning_content');
        if (! is_string($raw) || trim($raw) === '') {
            throw new NewsArticleAiException('Stella AI tidak mengembalikan artikel yang dapat dibaca.', 502);
        }

        $decoded = $this->decodeJson($raw);
        $title = trim(strip_tags((string) ($decoded['title'] ?? '')));
        $summary = trim(strip_tags((string) ($decoded['summary'] ?? '')));
        $content = $this->normalizeContent((string) ($decoded['content'] ?? ''));

        if ($title === '' || $content === '') {
            throw new NewsArticleAiException('Struktur artikel Stella AI belum lengkap. Silakan hasilkan ulang.', 502);
        }

        $paragraphs = preg_split('/\R{2,}/', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return [
            'title' => Str::limit($title, 255, ''),
            'summary' => Str::limit($summary, 500, ''),
            'content' => $content,
            'paragraph_count' => count($paragraphs),
            'sentence_count' => $this->countSentences($content),
            'recommended_by_ai' => $useAiRecommendation,
        ];
    }

    private function sendRequest(AppSetting $setting, array $payload)
    {
        return Http::acceptJson()
            ->asJson()
            ->withToken($setting->stella_ai_api_key)
            ->timeout(180)
            ->post(rtrim($setting->stella_ai_base_url, '/').'/chat/completions', $payload);
    }

    private function prompt(
        string $schoolName,
        string $category,
        bool $useAiRecommendation,
        ?int $paragraphCount,
        ?int $sentencesPerParagraph
    ): string {
        $lengthInstruction = $useAiRecommendation
            ? 'Tentukan panjang terbaik berdasarkan kategori. Gunakan 3-7 paragraf dan 2-5 kalimat per paragraf.'
            : "Buat tepat {$paragraphCount} paragraf dengan sekitar {$sentencesPerParagraph} kalimat pada setiap paragraf.";

        return "Buat satu draf artikel berita untuk situs resmi {$schoolName}.\n"
            ."Kategori: {$category}.\n"
            ."{$lengthInstruction}\n"
            ."Buat judul yang menarik namun tidak sensasional. Ringkasan maksimal 2 kalimat. Konten berupa teks polos dengan pemisah dua baris baru antarparagraf. Jangan gunakan markdown, heading, bullet, salam pembuka, atau penutup redaksional.\n\n"
            .'Kembalikan format persis: {"title":"","summary":"","content":"","paragraph_count":4,"sentences_per_paragraph":3}.';
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
            throw new NewsArticleAiException('Format artikel Stella AI belum valid. Silakan hasilkan ulang.', 502, $exception);
        }

        if (! is_array($decoded)) {
            throw new NewsArticleAiException('Struktur artikel Stella AI tidak sesuai.', 502);
        }

        return $decoded;
    }

    private function normalizeContent(string $content): string
    {
        $plain = strip_tags($content);
        $plain = preg_replace("/[ \t]+\n/", "\n", $plain) ?? $plain;
        $plain = preg_replace('/\R{3,}/', "\n\n", $plain) ?? $plain;

        return trim($plain);
    }

    private function countSentences(string $content): int
    {
        $sentences = preg_split('/(?<=[.!?])\s+/u', trim($content), -1, PREG_SPLIT_NO_EMPTY);

        return count($sentences ?: []);
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
