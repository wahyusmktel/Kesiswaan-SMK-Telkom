<?php

namespace App\Services;

use App\Exceptions\OkrAiException;
use App\Models\AppSetting;
use App\Models\OkrWeeklyReport;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class OkrWeeklyPresentationAiService
{
    public function ready(): bool
    {
        return $this->isReady(AppSetting::first());
    }

    public function generate(OkrWeeklyReport $report): array
    {
        $setting = AppSetting::first();
        if (! $this->isReady($setting)) {
            throw new OkrAiException('Stella AI belum aktif atau konfigurasi model belum lengkap.', 422);
        }

        $payload = [
            'model' => $setting->stella_ai_chat_model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Kamu adalah Stella AI, editor presentasi evaluasi OKR sekolah. Susun narasi Bahasa Indonesia yang ringkas, profesional, mudah dipresentasikan, dan hanya berdasarkan data yang diberikan. Jangan mengarang angka, hasil, kendala, nama, atau tindak lanjut. Jawaban wajib satu objek JSON valid tanpa markdown.',
                ],
                [
                    'role' => 'user',
                    'content' => $this->prompt($report),
                ],
            ],
            'stream' => false,
            'max_tokens' => 3500,
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
            Log::warning('Stella AI weekly OKR presentation connection failed.', [
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'weekly_report_id' => $report->id,
                'exception' => $exception,
            ]);

            throw new OkrAiException('Koneksi ke Stella AI gagal saat menyusun presentasi. Silakan coba kembali.', 502, $exception);
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
            throw new OkrAiException('Stella AI tidak mengembalikan narasi presentasi yang dapat dibaca.');
        }

        return $this->normalize($report, $this->decodeJson($raw));
    }

    private function prompt(OkrWeeklyReport $report): string
    {
        $source = [
            'unit' => $report->unit->name,
            'pekan' => $report->week_start->format('Y-m-d').' sampai '.$report->week_end->format('Y-m-d'),
            'fokus' => $report->weekly_focus,
            'dukungan' => $report->support_needed,
            'status_laporan' => $report->status,
            'catatan_kepala_sekolah' => $report->review_notes,
            'komitmen' => $report->items->map(function ($item) {
                $latest = $item->progressUpdates->first();

                return [
                    'item_id' => $item->id,
                    'komitmen' => $item->commitment,
                    'target' => $item->measurable_target,
                    'progres_persen' => (float) $item->completion_percent,
                    'status' => $item->final_status,
                    'pembaruan_terakhir' => $latest?->note,
                    'hasil_aktual' => $item->actual_result,
                    'kendala' => $item->blockers ?: $latest?->blockers,
                    'tindak_lanjut' => $item->next_follow_up,
                ];
            })->values()->all(),
        ];

        return "Susun narasi slide evaluasi Jumat dari data berikut:\n"
            .json_encode($source, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
            ."\n\nKembalikan JSON persis dengan struktur: "
            .'{'
            .'"judul_deck":"maksimal 80 karakter",'
            .'"ringkasan_pembuka":"maksimal 220 karakter",'
            .'"kesimpulan_unit":"maksimal 220 karakter",'
            .'"komitmen":[{'
            .'"item_id":1,'
            .'"judul_slide":"maksimal 65 karakter",'
            .'"headline":"maksimal 120 karakter",'
            .'"ringkasan_progres":"maksimal 220 karakter",'
            .'"ringkasan_hasil":"maksimal 220 karakter",'
            .'"ringkasan_kendala":"maksimal 180 karakter atau kosong",'
            .'"tindak_lanjut":"maksimal 180 karakter atau kosong"'
            .'}]}.';
    }

    private function normalize(OkrWeeklyReport $report, array $decoded): array
    {
        $aiItems = collect($decoded['komitmen'] ?? [])->keyBy(fn ($item) => (string) ($item['item_id'] ?? ''));

        return [
            'title' => $this->clean($decoded['judul_deck'] ?? null, 'Evaluasi Pekanan OKR '.$report->unit->name, 80),
            'opening_summary' => $this->clean($decoded['ringkasan_pembuka'] ?? null, $report->weekly_focus, 220),
            'unit_conclusion' => $this->clean($decoded['kesimpulan_unit'] ?? null, $report->review_notes ?: 'Evaluasi menjadi dasar tindak lanjut pekan berikutnya.', 220),
            'items' => $report->items->map(function ($item) use ($aiItems) {
                $ai = $aiItems->get((string) $item->id, []);
                $latest = $item->progressUpdates->first();

                return [
                    'item_id' => $item->id,
                    'title' => $this->clean($ai['judul_slide'] ?? null, $item->commitment, 65),
                    'headline' => $this->clean($ai['headline'] ?? null, $item->commitment, 120),
                    'progress_summary' => $this->clean($ai['ringkasan_progres'] ?? null, $latest?->note ?: $item->actual_result ?: 'Belum ada pembaruan progres.', 220),
                    'result_summary' => $this->clean($ai['ringkasan_hasil'] ?? null, $item->actual_result ?: 'Evaluasi akhir belum diisi.', 220),
                    'blocker_summary' => $this->clean($ai['ringkasan_kendala'] ?? null, $item->blockers ?: $latest?->blockers, 180, true),
                    'next_step' => $this->clean($ai['tindak_lanjut'] ?? null, $item->next_follow_up, 180, true),
                ];
            })->values()->all(),
        ];
    }

    private function clean(mixed $value, ?string $fallback, int $limit, bool $nullable = false): ?string
    {
        $text = trim(strip_tags(is_string($value) ? $value : ''));
        if ($text === '') {
            $text = trim((string) $fallback);
        }
        if ($nullable && $text === '') {
            return null;
        }

        return Str::limit($text, $limit, '…');
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
            throw new OkrAiException('Format narasi presentasi Stella AI belum valid. Silakan hasilkan ulang.', 502, $exception);
        }

        if (! is_array($decoded)) {
            throw new OkrAiException('Struktur narasi presentasi Stella AI tidak sesuai.', 502);
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
