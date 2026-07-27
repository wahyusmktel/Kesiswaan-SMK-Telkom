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
        ?int $sentencesPerParagraph = null,
        ?string $instructions = null,
        bool $includeCodeSnippets = false,
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
                    'content' => 'Kamu adalah Stella AI, redaktur dan editor SEO situs sekolah Indonesia. Tulis konten orisinal, mengutamakan manfaat pembaca, akurat, dan mudah dipahami. Jangan mengarang nama, tanggal, angka prestasi, kutipan, atau fakta spesifik yang tidak diberikan. Gunakan Markdown pada isi artikel. Setiap blok kode wajib memakai fenced code block dengan nama bahasa yang tepat, misalnya ```php atau ```python. Jawaban wajib hanya JSON valid tanpa pembungkus markdown.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->prompt(
                        $setting->school_name ?: 'SMK Telkom Lampung',
                        $category,
                        $useAiRecommendation,
                        $paragraphCount,
                        $sentencesPerParagraph,
                        $instructions,
                        $includeCodeSnippets,
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
        $seoTitle = trim(strip_tags((string) ($decoded['seo_title'] ?? $title)));
        $seoDescription = trim(strip_tags((string) ($decoded['seo_description'] ?? $summary)));
        $focusKeyword = trim(strip_tags((string) ($decoded['focus_keyword'] ?? '')));
        $seoKeywords = $decoded['seo_keywords'] ?? [];
        if (is_array($seoKeywords)) {
            $seoKeywords = implode(', ', array_filter(array_map(
                fn ($keyword) => trim(strip_tags((string) $keyword)),
                $seoKeywords
            )));
        } else {
            $seoKeywords = trim(strip_tags((string) $seoKeywords));
        }

        if ($title === '' || $content === '') {
            throw new NewsArticleAiException('Struktur artikel Stella AI belum lengkap. Silakan hasilkan ulang.', 502);
        }

        $paragraphs = preg_split('/\R{2,}/', $content, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return [
            'title' => Str::limit($title, 255, ''),
            'summary' => Str::limit($summary, 500, ''),
            'content' => $content,
            'seo_title' => Str::limit($seoTitle, 255, ''),
            'seo_description' => Str::limit($seoDescription, 320, ''),
            'focus_keyword' => Str::limit($focusKeyword, 255, ''),
            'seo_keywords' => Str::limit($seoKeywords, 2000, ''),
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
        ?int $sentencesPerParagraph,
        ?string $instructions,
        bool $includeCodeSnippets,
    ): string {
        $lengthInstruction = $useAiRecommendation
            ? 'Tentukan panjang terbaik berdasarkan kategori. Gunakan 3-7 paragraf dan 2-5 kalimat per paragraf.'
            : "Buat tepat {$paragraphCount} paragraf dengan sekitar {$sentencesPerParagraph} kalimat pada setiap paragraf.";

        $topicInstruction = filled($instructions)
            ? "Instruksi redaksi pengguna:\n".trim($instructions)
            : 'Tentukan topik yang relevan dengan kategori tanpa membuat klaim faktual yang tidak diberikan.';
        $codeInstruction = $includeCodeSnippets
            ? 'Sertakan contoh kode yang relevan dan siap dipelajari. Gunakan fenced code block Markdown dengan nama bahasa pada setiap blok, lalu jelaskan cara kerjanya dan praktik aman yang perlu diperhatikan.'
            : 'Jangan memaksakan contoh kode kecuali diminta secara eksplisit dalam instruksi redaksi.';

        return "Buat satu draf artikel untuk situs resmi {$schoolName}.\n"
            ."Kategori: {$category}.\n"
            ."{$topicInstruction}\n"
            ."{$lengthInstruction}\n"
            ."{$codeInstruction}\n"
            ."Optimalkan secara wajar untuk pencarian: judul deskriptif dan tidak sensasional, satu fokus keyword yang sesuai niat pembaca, struktur heading H2/H3 yang jelas, pembuka langsung menjawab kebutuhan, serta keyword yang digunakan alami tanpa pengulangan berlebihan. Ringkasan maksimal 2 kalimat. Konten harus orisinal, bermanfaat, dan memakai Markdown. Jangan menambahkan salam atau penutup redaksional.\n\n"
            .'Kembalikan format persis: {"title":"","summary":"","content":"","seo_title":"","seo_description":"","focus_keyword":"","seo_keywords":["",""],"paragraph_count":4,"sentences_per_paragraph":3}.';
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
        $plain = str_replace("\0", '', $content);
        $plain = preg_replace("/[ \t]+\n/", "\n", $plain) ?? $plain;
        $plain = preg_replace('/\R{4,}/', "\n\n\n", $plain) ?? $plain;

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
