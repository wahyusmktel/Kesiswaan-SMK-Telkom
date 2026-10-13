<?php

namespace App\Services;

use App\Models\AppSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class RabAiService
{
    /**
     * Generate draft RAB from user prompt.
     */
    public function generateRabDraft(string $userPrompt): array
    {
        $setting = AppSetting::first();

        if ($this->isStellaAiReady($setting)) {
            try {
                return $this->callStellaAi($setting, $userPrompt);
            } catch (\Throwable $e) {
                Log::warning('Stella AI RAB generation fallback to heuristic: ' . $e->getMessage());
            }
        }

        // Heuristic fallback if AI service is not available or failed
        return $this->heuristicFallback($userPrompt);
    }

    private function isStellaAiReady(?AppSetting $setting): bool
    {
        return (bool) (
            $setting?->stella_ai_enabled
            && !empty($setting->stella_ai_base_url)
            && !empty($setting->stella_ai_api_key)
            && !empty($setting->stella_ai_chat_model)
        );
    }

    private function callStellaAi(AppSetting $setting, string $userPrompt): array
    {
        $systemPrompt = <<<PROMPT
Anda adalah asisten AI resmi SMK Telkom Lampung / Yayasan Pendidikan Telkom untuk menyusun dokumen Rencana Anggaran Biaya (RAB) pengadaan sekolah.
Tugas Anda adalah menganalisis kebutuhan pengadaan yang diberikan pengguna dan mengubahnya menjadi struktur JSON lengkap untuk formulir RAB.

Format output WAJIB HANYA berupa objek JSON yang valid tanpa tanda petik pembuka (markdown code block) dan tanpa teks tambahan di luar JSON.

Format JSON yang dihasilkan harus memiliki key berikut:
{
  "judul_pengajuan": "NAMA PENGADAAN SINGKAT HURUF KAPITAL",
  "tahun_anggaran": "2026",
  "nomor_akun": "5611104",
  "nama_akun": "BPP Alat Pengolah Data",
  "drk": "Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan",
  "kebutuhan_waktu": "Bulan Tahun (contoh: April 2026)",
  "catatan_anggaran": "-",
  "items": [
    {
      "uraian_kegiatan": "Nama barang atau layanan",
      "spesifikasi": "Rincian spesifikasi teknis / durasi / tipe",
      "satuan": "unit / bulan / rim / buah / paket / dsb",
      "volume": 1,
      "harga_satuan": 500000
    }
  ]
}

Aturan:
1. "judul_pengajuan" gunakan huruf kapital ringkas dan jelas.
2. "nomor_akun" pilih yang sesuai (contoh: 5611104 untuk IT/Pengolah Data, 5611101 untuk Operasional Kantor, 5611102 untuk Kegiatan Kesiswaan/Kurikulum, 5611103 untuk Sarana Prasarana).
3. "nama_akun" sesuaikan dengan nomor akun (misal: "BPP Alat Pengolah Data", "BPP Bahan Praktikum Kejuruan", "BPP Operasional Sekolah").
4. "drk" gunakan kalimat program Yayasan Pendidikan Telkom yang profesional (misal: "Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan", "Peningkatan Mutu Pembelajaran dan Praktik Vokasi").
5. "items" berisi daftar barang/kegiatan yang diminta lengkap dengan estimasi harga pasar wajar dalam angka murni integer Rupiah.
PROMPT;

        $payload = [
            'model' => $setting->stella_ai_chat_model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => 'Buatkan draft RAB untuk kebutuhan berikut: ' . $userPrompt],
            ],
            'temperature' => 0.4,
            'stream' => false,
        ];

        $response = Http::acceptJson()
            ->asJson()
            ->withToken($setting->stella_ai_api_key)
            ->timeout(60)
            ->post(rtrim($setting->stella_ai_base_url, '/') . '/chat/completions', $payload);

        if ($response->failed()) {
            throw new \Exception('API response error ' . $response->status());
        }

        $responseData = $response->json();
        $rawContent = data_get($responseData, 'choices.0.message.content');
        if (is_array($rawContent)) {
            $rawContent = data_get($rawContent, '0.text');
        }
        if (!is_string($rawContent) || trim($rawContent) === '') {
            $rawContent = data_get($responseData, 'choices.0.message.reasoning_content');
        }

        $cleanJson = $this->cleanJsonString($rawContent);
        $decoded = json_decode($cleanJson, true);

        if (!is_array($decoded) || empty($decoded['items'])) {
            throw new \Exception('Failed to decode valid JSON from AI response');
        }

        return $this->normalizeResult($decoded);
    }

    private function cleanJsonString(?string $text): string
    {
        if (!$text) return '{}';
        // Remove markdown backticks
        $text = preg_replace('/^```(?:json)?\s*/i', '', trim($text));
        $text = preg_replace('/\s*```$/', '', $text);
        
        $start = strpos($text, '{');
        $end = strrpos($text, '}');
        if ($start !== false && $end !== false && $end > $start) {
            $text = substr($text, $start, $end - $start + 1);
        }
        return trim($text);
    }

    private function normalizeResult(array $data): array
    {
        $items = [];
        foreach ($data['items'] ?? [] as $item) {
            $vol = (float) ($item['volume'] ?? 1);
            $harga = (float) ($item['harga_satuan'] ?? $item['harga'] ?? 0);
            $items[] = [
                'uraian_kegiatan' => (string) ($item['uraian_kegiatan'] ?? 'Pengadaan Barang'),
                'spesifikasi' => (string) ($item['spesifikasi'] ?? '-'),
                'satuan' => (string) ($item['satuan'] ?? 'unit'),
                'volume' => $vol > 0 ? $vol : 1,
                'harga_satuan' => $harga,
                'jumlah' => $vol * $harga,
            ];
        }

        return [
            'judul_pengajuan' => strtoupper((string) ($data['judul_pengajuan'] ?? 'PENGAJUAN RENCANA ANGGARAN')),
            'tahun_anggaran' => (string) ($data['tahun_anggaran'] ?? date('Y')),
            'nomor_akun' => (string) ($data['nomor_akun'] ?? '5611104'),
            'nama_akun' => (string) ($data['nama_akun'] ?? 'BPP Alat Pengolah Data'),
            'drk' => (string) ($data['drk'] ?? 'Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan'),
            'kebutuhan_waktu' => (string) ($data['kebutuhan_waktu'] ?? now()->translatedFormat('F Y')),
            'catatan_anggaran' => (string) ($data['catatan_anggaran'] ?? '-'),
            'items' => $items,
        ];
    }

    /**
     * Heuristic fallback generator when AI is offline.
     */
    private function heuristicFallback(string $prompt): array
    {
        $currentYear = date('Y');
        $currentMonth = now()->translatedFormat('F') . ' ' . $currentYear;
        $cleanPrompt = trim($prompt);

        // Extract possible title
        $title = 'PENGAJUAN RENCANA ANGGARAN BIAYA';
        if (mb_strlen($cleanPrompt) > 0) {
            $firstSentence = explode('.', $cleanPrompt)[0];
            $title = Str::upper(Str::limit($firstSentence, 60, ''));
        }

        // Default item based on prompt
        $items = [
            [
                'uraian_kegiatan' => Str::title(Str::limit($cleanPrompt, 50, '')),
                'spesifikasi' => 'Sesuai kebutuhan operasional dan spesifikasi standar',
                'satuan' => 'unit',
                'volume' => 1,
                'harga_satuan' => 500000,
                'jumlah' => 500000,
            ]
        ];

        return [
            'judul_pengajuan' => $title,
            'tahun_anggaran' => $currentYear,
            'nomor_akun' => '5611104',
            'nama_akun' => 'BPP Alat Pengolah Data',
            'drk' => 'Peningkatan Kapasitas dan Modernisasi Infrastruktur Pendidikan',
            'kebutuhan_waktu' => $currentMonth,
            'catatan_anggaran' => '-',
            'items' => $items,
        ];
    }
}
