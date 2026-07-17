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
    public function generate(TeachingModule $module, string $topic, array $currentContent): array
    {
        $setting = AppSetting::first();

        if (! $this->isReady($setting)) {
            throw new TeachingModuleAiException('Stella AI belum aktif atau konfigurasi model belum lengkap.', 422);
        }

        $module->loadMissing('teacher.masterGuru');
        $context = $this->schemaContext($module);
        $template = TeachingModuleSchema::defaults($context);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->withToken($setting->stella_ai_api_key)
                ->timeout(180)
                ->post(rtrim($setting->stella_ai_base_url, '/').'/chat/completions', [
                    'model' => $setting->stella_ai_chat_model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $this->systemPrompt(),
                        ],
                        [
                            'role' => 'user',
                            'content' => $this->userPrompt($module, $topic, $template),
                        ],
                    ],
                    'stream' => false,
                    'max_tokens' => 12000,
                ]);
        } catch (\Throwable $exception) {
            Log::warning('Stella AI teaching module provider connection failed.', [
                'user_id' => $module->teacher_id,
                'teaching_module_id' => $module->id,
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
            Log::warning('Stella AI teaching module generation was rejected.', [
                'user_id' => $module->teacher_id,
                'teaching_module_id' => $module->id,
                'provider_host' => parse_url($setting->stella_ai_base_url, PHP_URL_HOST),
                'model' => $setting->stella_ai_chat_model,
                'status' => $response->status(),
                'response' => Str::limit($response->body(), 1500),
            ]);

            throw new TeachingModuleAiException('Provider Stella AI menolak permintaan pembuatan modul. Silakan coba kembali.');
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

        $normalized = TeachingModuleSchema::sanitize($generated, $context);
        $normalized['approval'] = $this->trustedApproval(
            $normalized['approval'],
            TeachingModuleSchema::normalize($currentContent, $context)['approval'],
            $context
        );

        $this->ensureComplete($normalized);

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

    private function userPrompt(TeachingModule $module, string $topic, array $template): string
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

        return 'Buat isi modul ajar yang lengkap berdasarkan metadata berikut:'."\n"
            .json_encode($metadata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)."\n\n"
            .'Ketentuan isi:'."\n"
            .'- Isi setiap daftar dengan 2-5 butir yang spesifik dan siap digunakan.'."\n"
            .'- Pilih 3-5 dimensi profil lulusan yang paling relevan; selected harus boolean dan dimensi terpilih wajib memiliki note.'."\n"
            .'- Buat pengalaman belajar lengkap dengan pembukaan, fase inti, aktivitas guru, aktivitas peserta didik, output, dan penutup.'."\n"
            .'- Gunakan jumlah pertemuan yang realistis terhadap alokasi waktu dan beri allocation pada setiap pertemuan.'."\n"
            .'- Lengkapi asesmen awal, proses, akhir, kriteria, pertanyaan pemantik, diferensiasi, pengayaan, remedial, dan deskripsi lampiran.'."\n"
            .'- Pada approval, jangan mengarang nama atau NIP; biarkan nilai identitas pada template apa adanya.'."\n\n"
            .'Kembalikan objek dengan struktur persis seperti template berikut dan ganti seluruh isi akademiknya:'."\n"
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

    private function ensureComplete(array $content): void
    {
        $requiredLists = [
            'identification.students',
            'identification.materials',
            'design.learning_outcomes',
            'design.learning_objectives',
            'design.learning_topics',
            'design.pedagogical_practices',
            'design.learning_partners',
            'design.learning_environment',
            'design.digital_use',
            'assessment.initial',
            'assessment.process',
            'assessment.final',
            'assessment.criteria',
            'supporting.trigger_questions',
            'supporting.differentiation',
            'supporting.enrichment',
            'supporting.remedial',
            'attachments.teaching_materials',
            'attachments.worksheets',
            'attachments.assessments',
        ];

        foreach ($requiredLists as $path) {
            if (! $this->hasText(data_get($content, $path))) {
                throw new TeachingModuleAiException('Hasil Stella AI belum lengkap pada seluruh bagian. Silakan hasilkan ulang.');
            }
        }

        if (! $this->hasLearningActivities(data_get($content, 'experiences'))) {
            throw new TeachingModuleAiException('Hasil Stella AI belum memiliki pengalaman belajar yang lengkap.');
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
