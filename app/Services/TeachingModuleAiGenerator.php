<?php

namespace App\Services;

use App\Exceptions\TeachingModuleAiException;
use App\Models\AppSetting;
use App\Models\TeachingModule;
use App\Support\TeachingModuleSchema;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use JsonException;

class TeachingModuleAiGenerator
{
    private const SECTIONS = [
        'identification' => 'identification',
        'design' => 'design',
        'experience' => 'experiences',
        'assessment' => 'assessment',
        'supporting' => 'supporting',
        'attachments' => 'attachments',
    ];

    public function generateSection(
        TeachingModule $module,
        string $topic,
        string $section,
        array $currentContent
    ): array
    {
        $setting = AppSetting::first();

        if (! $this->isReady($setting)) {
            throw new TeachingModuleAiException('Stella AI belum aktif atau konfigurasi model belum lengkap.', 422);
        }

        $module->loadMissing('teacher.masterGuru');
        $context = $this->schemaContext($module);
        $template = TeachingModuleSchema::defaults($context);
        $schemaKey = self::SECTIONS[$section] ?? null;

        if ($schemaKey === null) {
            throw new TeachingModuleAiException('Bagian modul yang diminta tidak valid.', 422);
        }

        $payload = [
            'model' => $setting->stella_ai_chat_model,
            'messages' => [
                [
                    'role' => 'system',
                    'content' => $this->systemPrompt(),
                ],
                [
                    'role' => 'user',
                    'content' => $this->userPrompt(
                        $module,
                        $topic,
                        $section,
                        [$schemaKey => $template[$schemaKey]]
                    ),
                ],
            ],
            'stream' => false,
            'max_tokens' => $section === 'experience' ? 6000 : 3500,
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
            Log::warning('Stella AI teaching module provider connection failed.', [
                'user_id' => $module->teacher_id,
                'teaching_module_id' => $module->id,
                'section' => $section,
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'exception' => $exception,
            ]);

            throw new TeachingModuleAiException(
                'Koneksi ke Stella AI gagal. Periksa koneksi server lalu coba kembali.',
                502,
                $exception
            );
        }

        if ($response->failed()) {
            $providerMessage = $this->providerErrorMessage($response->json());
            Log::warning('Stella AI teaching module generation was rejected.', [
                'user_id' => $module->teacher_id,
                'teaching_module_id' => $module->id,
                'section' => $section,
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'model' => $setting->stella_ai_chat_model,
                'status' => $response->status(),
                'response' => Str::limit($response->body(), 1500),
            ]);

            throw new TeachingModuleAiException(
                'Provider Stella AI menolak permintaan (HTTP '.$response->status().'): '.$providerMessage
            );
        }

        $responseData = $response->json();
        $rawContent = data_get($responseData, 'choices.0.message.content');
        if (is_array($rawContent)) {
            $rawContent = data_get($rawContent, '0.text');
        }
        if (! is_string($rawContent) || trim($rawContent) === '') {
            $rawContent = data_get($responseData, 'choices.0.message.reasoning_content');
        }

        if (! is_string($rawContent) || trim($rawContent) === '') {
            throw new TeachingModuleAiException('Stella AI tidak mengembalikan isi modul yang dapat dibaca.');
        }

        $generated = $this->decodeJson($rawContent);
        if (isset($generated['content']) && is_array($generated['content'])) {
            $generated = $generated['content'];
        }

        $generatedSection = $generated[$schemaKey] ?? $generated;
        if (! is_array($generatedSection)) {
            throw new TeachingModuleAiException('Struktur bagian hasil Stella AI tidak sesuai format modul ajar.');
        }

        $merged = TeachingModuleSchema::normalize($currentContent, $context);
        $merged[$schemaKey] = $generatedSection;
        $normalized = TeachingModuleSchema::sanitize($merged, $context);
        $normalized['approval'] = $this->trustedApproval(
            $normalized['approval'],
            $merged['approval'],
            $context
        );

        $this->ensureSectionComplete($normalized, $section);

        return $normalized;
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

    private function schemaContext(TeachingModule $module): array
    {
        return [
            'allocation' => $module->alokasi_waktu,
            'teacher_name' => $module->nama_penyusun,
            'teacher_nip' => $module->teacher?->masterGuru?->nik
                ?? $module->teacher?->masterGuru?->nuptk
                ?? '',
            'location' => 'Pringsewu',
            'date' => now()->format('Y-m-d'),
        ];
    }

    private function systemPrompt(): string
    {
        return <<<'PROMPT'
Kamu adalah Stella AI, ahli penyusunan modul ajar pembelajaran mendalam SMK Indonesia. Susun konten dalam Bahasa Indonesia formal, konkret, relevan dengan dunia kerja, berpusat pada peserta didik, dan selaras dengan pembelajaran mendalam. Jawaban WAJIB hanya satu objek JSON valid tanpa markdown, komentar, atau teks pembuka/penutup. Pertahankan seluruh key, tipe data, dan bentuk array dari template. Jangan membuat nama orang, NIP, tautan, atau referensi fiktif.
PROMPT;
    }

    private function userPrompt(
        TeachingModule $module,
        string $topic,
        string $section,
        array $template
    ): string
    {
        $metadata = [
            'topik_utama' => $topic,
            'nama_modul' => $module->nama_modul,
            'mata_pelajaran' => $module->mata_pelajaran,
            'program_keahlian' => $module->program_keahlian,
            'fase' => $module->fase,
            'kelas' => $module->kelas,
            'jenjang' => $module->jenjang,
            'jumlah_murid' => $module->jumlah_murid,
            'lingkup_materi' => $module->lingkup_materi,
            'alokasi_waktu' => $module->alokasi_waktu,
            'semester' => $module->semester,
            'tahun_pelajaran' => $module->tahun_pelajaran,
        ];

        $sectionInstructions = match ($section) {
            'identification' => 'Isi identifikasi peserta didik, identifikasi materi, dan pilih 3-5 dimensi profil lulusan yang paling relevan. selected wajib boolean dan dimensi terpilih wajib memiliki note.',
            'design' => 'Isi capaian, tujuan, topik, praktik pedagogis, mitra, lingkungan belajar, dan pemanfaatan digital secara saling selaras.',
            'experience' => 'Buat pengalaman belajar lengkap dan realistis terhadap alokasi waktu. Setiap pertemuan wajib memiliki pembukaan, fase inti, aktivitas guru, aktivitas peserta didik, output, dan penutup.',
            'assessment' => 'Isi asesmen awal, proses, akhir, serta kriteria penilaian yang terukur dan selaras dengan tujuan pembelajaran.',
            'supporting' => 'Isi pertanyaan pemantik, diferensiasi, pengayaan, dan remedial yang operasional.',
            'attachments' => 'Isi deskripsi bahan ajar, lembar kerja, dan lampiran instrumen asesmen yang siap disiapkan guru.',
        };

        return 'Buat bagian '.$section.' untuk modul ajar berdasarkan metadata berikut:'."\n"
            .json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n"
            .'Ketentuan isi:'."\n"
            .'- '.$sectionInstructions."\n"
            .'- Isi setiap daftar dengan 2-4 butir yang spesifik, ringkas, dan siap digunakan.'."\n"
            .'- Jangan menambah atau menghapus key pada template.'."\n\n"
            .'Kembalikan objek JSON dengan struktur persis seperti template berikut:'."\n"
            .json_encode($template, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function decodeJson(string $rawContent): array
    {
        $json = trim($rawContent);
        $json = preg_replace('/^```(?:json)?\s*/i', '', $json) ?? $json;
        $json = preg_replace('/\s*```$/', '', $json) ?? $json;

        $firstBrace = strpos($json, '{');
        $lastBrace = strrpos($json, '}');
        if ($firstBrace !== false && $lastBrace !== false && $lastBrace >= $firstBrace) {
            $json = substr($json, $firstBrace, $lastBrace - $firstBrace + 1);
        }

        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new TeachingModuleAiException('Format hasil Stella AI belum valid. Silakan hasilkan ulang.', 502, $exception);
        }

        if (! is_array($decoded)) {
            throw new TeachingModuleAiException('Struktur hasil Stella AI tidak sesuai format modul ajar.');
        }

        return $decoded;
    }

    private function providerErrorMessage(mixed $response): string
    {
        $message = data_get($response, 'error.message')
            ?? data_get($response, 'message')
            ?? data_get($response, 'detail');

        if (! is_string($message) || trim($message) === '') {
            return 'tidak ada detail error dari provider.';
        }

        return Str::limit(strip_tags(trim($message)), 300);
    }

    private function trustedApproval(array $generated, array $current, array $context): array
    {
        foreach (['location', 'date', 'validator_title', 'validator_name', 'validator_nip', 'teacher_title', 'teacher_name', 'teacher_nip'] as $key) {
            if (filled($current[$key] ?? null)) {
                $generated[$key] = $current[$key];
            }
        }

        $generated['teacher_name'] = $context['teacher_name'];
        $generated['teacher_nip'] = $context['teacher_nip'];

        return $generated;
    }

    private function ensureSectionComplete(array $content, string $section): void
    {
        $requiredBySection = [
            'identification' => [
                'identification.students',
                'identification.materials',
            ],
            'design' => [
                'design.learning_outcomes',
                'design.learning_objectives',
                'design.learning_topics',
                'design.pedagogical_practices',
                'design.learning_partners',
                'design.learning_environment',
                'design.digital_use',
            ],
            'assessment' => [
                'assessment.initial',
                'assessment.process',
                'assessment.final',
                'assessment.criteria',
            ],
            'supporting' => [
                'supporting.trigger_questions',
                'supporting.differentiation',
                'supporting.enrichment',
                'supporting.remedial',
            ],
            'attachments' => [
                'attachments.teaching_materials',
                'attachments.worksheets',
                'attachments.assessments',
            ],
        ];

        if ($section === 'experience') {
            if (! $this->hasLearningActivities(data_get($content, 'experiences'))) {
                throw new TeachingModuleAiException('Hasil Stella AI belum memiliki pengalaman belajar yang lengkap.');
            }

            return;
        }

        foreach ($requiredBySection[$section] ?? [] as $path) {
            if (! $this->hasText(data_get($content, $path))) {
                throw new TeachingModuleAiException('Hasil Stella AI belum lengkap pada bagian ini. Proses akan dicoba kembali saat Anda menekan tombol hasilkan.');
            }
        }
    }

    private function hasLearningActivities(mixed $experiences): bool
    {
        if (! is_array($experiences)) {
            return false;
        }

        foreach ($experiences as $meeting) {
            if (! is_array($meeting)) {
                continue;
            }

            if ($this->hasText($meeting['opening'] ?? null) && $this->hasText($meeting['closing'] ?? null)) {
                foreach (($meeting['core_phases'] ?? []) as $phase) {
                    if (is_array($phase)
                        && $this->hasText($phase['teacher_activities'] ?? null)
                        && $this->hasText($phase['student_activities'] ?? null)
                        && $this->hasText($phase['outputs'] ?? null)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    private function hasText(mixed $value): bool
    {
        if (is_string($value)) {
            return trim($value) !== '';
        }

        if (! is_array($value)) {
            return false;
        }

        foreach ($value as $item) {
            if ($this->hasText($item)) {
                return true;
            }
        }

        return false;
    }
}
