<?php

namespace App\Services\Erapor;

use App\Models\Erapor\EraporRefCurriculum;
use App\Models\Erapor\EraporReferenceImport;
use App\Models\Erapor\EraporRefSubject;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class EraporReferenceImportService
{
    public const DATASETS = [
        'mata_pelajaran',
        'kurikulum',
        'mata_pelajaran_kurikulum',
    ];

    /**
     * @return array<int, array<string, mixed>>
     */
    public function import(
        string $sourcePath,
        string $sourceVersion = '8.0.3',
        array $datasets = self::DATASETS,
        ?int $userId = null
    ): array {
        $directory = $this->resolveDataDirectory($sourcePath);
        $datasets = array_values(array_intersect(self::DATASETS, $datasets ?: self::DATASETS));

        if ($datasets === []) {
            throw new RuntimeException('Dataset e-Rapor yang dipilih tidak valid.');
        }

        $results = [];

        foreach ($datasets as $dataset) {
            $files = $this->datasetFiles($directory, $dataset);

            if ($files === []) {
                throw new RuntimeException("Berkas dataset {$dataset} tidak ditemukan di {$directory}.");
            }

            $checksum = $this->checksum($files);
            $existing = EraporReferenceImport::query()
                ->where('dataset', $dataset)
                ->where('source_version', $sourceVersion)
                ->where('checksum', $checksum)
                ->where('status', 'completed')
                ->first();

            if ($existing) {
                $results[] = $this->result($existing, true);

                continue;
            }

            $manifest = EraporReferenceImport::query()->updateOrCreate(
                [
                    'dataset' => $dataset,
                    'source_version' => $sourceVersion,
                    'checksum' => $checksum,
                ],
                [
                    'source' => 'e-Rapor SMK',
                    'files_count' => count($files),
                    'records_total' => 0,
                    'records_imported' => 0,
                    'records_skipped' => 0,
                    'records_conflicted' => 0,
                    'status' => 'processing',
                    'metadata' => [
                        'source_directory' => $directory,
                        'files' => array_map('basename', $files),
                    ],
                    'error_message' => null,
                    'imported_by' => $userId,
                    'imported_at' => null,
                ]
            );

            try {
                $counts = DB::transaction(
                    fn () => $this->importDataset($dataset, $files, $manifest->id)
                );

                $manifest->update([
                    'records_total' => $counts['total'],
                    'records_imported' => $counts['imported'],
                    'records_skipped' => $counts['skipped'],
                    'records_conflicted' => $counts['conflicted'],
                    'status' => 'completed',
                    'imported_at' => now(),
                ]);
            } catch (Throwable $exception) {
                $manifest->update([
                    'status' => 'failed',
                    'error_message' => Str::limit($exception->getMessage(), 4000, ''),
                ]);

                throw $exception;
            }

            $results[] = $this->result($manifest->fresh(), false);
        }

        return $results;
    }

    private function resolveDataDirectory(string $path): string
    {
        $resolved = realpath($path);

        if ($resolved === false || ! is_dir($resolved)) {
            throw new RuntimeException("Folder sumber e-Rapor tidak ditemukan: {$path}");
        }

        $nested = $resolved.DIRECTORY_SEPARATOR.'database'.DIRECTORY_SEPARATOR.'data';

        return is_dir($nested) ? $nested : $resolved;
    }

    /**
     * @return array<int, string>
     */
    private function datasetFiles(string $directory, string $dataset): array
    {
        $pattern = match ($dataset) {
            'mata_pelajaran' => '/^mata_pelajaran\.json$/i',
            'kurikulum' => '/^kurikulum\.json$/i',
            'mata_pelajaran_kurikulum' => '/^mata_pelajaran_kurikulum(?:-\d+)?\.json$/i',
        };

        $files = [];

        foreach (scandir($directory) ?: [] as $filename) {
            if (preg_match($pattern, $filename) === 1) {
                $files[] = $directory.DIRECTORY_SEPARATOR.$filename;
            }
        }

        natsort($files);

        return array_values($files);
    }

    /**
     * @param  array<int, string>  $files
     */
    private function checksum(array $files): string
    {
        $context = hash_init('sha256');

        foreach ($files as $file) {
            hash_update($context, basename($file).':');
            hash_update_file($context, $file);
        }

        return hash_final($context);
    }

    /**
     * @param  array<int, string>  $files
     * @return array{total: int, imported: int, skipped: int, conflicted: int}
     */
    private function importDataset(string $dataset, array $files, int $manifestId): array
    {
        $counts = ['total' => 0, 'imported' => 0, 'skipped' => 0, 'conflicted' => 0];

        foreach ($files as $file) {
            $rows = json_decode(file_get_contents($file), true, 512, JSON_THROW_ON_ERROR);

            if (! is_array($rows)) {
                throw new RuntimeException('Isi JSON bukan array: '.basename($file));
            }

            $counts['total'] += count($rows);

            foreach (array_chunk($rows, 500) as $chunk) {
                $chunkCounts = match ($dataset) {
                    'mata_pelajaran' => $this->importSubjects($chunk, $manifestId),
                    'kurikulum' => $this->importCurricula($chunk, $manifestId),
                    'mata_pelajaran_kurikulum' => $this->importCurriculumSubjects($chunk, $manifestId),
                };

                $counts['imported'] += $chunkCounts['imported'];
                $counts['skipped'] += $chunkCounts['skipped'];
                $counts['conflicted'] += $chunkCounts['conflicted'];
            }
        }

        return $counts;
    }

    /**
     * @return array{imported: int, skipped: int, conflicted: int}
     */
    private function importSubjects(array $rows, int $manifestId): array
    {
        $now = now();
        $values = [];
        $skipped = 0;

        foreach ($rows as $row) {
            if (empty($row['mata_pelajaran_id']) || blank($row['nama'] ?? null)) {
                $skipped++;

                continue;
            }

            $values[] = [
                'external_id' => $row['mata_pelajaran_id'],
                'name' => trim($row['nama']),
                'major_external_id' => $row['jurusan_id'] ?: null,
                'valid_from' => $this->date($row['create_date'] ?? null),
                'valid_until' => $this->date($row['expired_date'] ?? null),
                'is_active' => empty($row['expired_date']),
                'source_updated_at' => $this->dateTime($row['last_update'] ?? null),
                'reference_import_id' => $manifestId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($values !== []) {
            DB::table('erapor_ref_subjects')->upsert(
                $values,
                ['external_id'],
                [
                    'name',
                    'major_external_id',
                    'valid_from',
                    'valid_until',
                    'is_active',
                    'source_updated_at',
                    'reference_import_id',
                    'updated_at',
                ]
            );
        }

        return ['imported' => count($values), 'skipped' => $skipped, 'conflicted' => 0];
    }

    /**
     * @return array{imported: int, skipped: int, conflicted: int}
     */
    private function importCurricula(array $rows, int $manifestId): array
    {
        $now = now();
        $values = [];
        $skipped = 0;

        foreach ($rows as $row) {
            if (empty($row['kurikulum_id']) || blank($row['nama_kurikulum'] ?? null)) {
                $skipped++;

                continue;
            }

            $values[] = [
                'external_id' => $row['kurikulum_id'],
                'name' => trim($row['nama_kurikulum']),
                'education_level_id' => $row['jenjang_pendidikan_id'] ?: null,
                'major_external_id' => $row['jurusan_id'] ?: null,
                'valid_from' => $this->date($row['mulai_berlaku'] ?? null),
                'valid_until' => $this->date($row['expired_date'] ?? null),
                'is_active' => empty($row['expired_date']),
                'source_updated_at' => $this->dateTime($row['last_update'] ?? null),
                'reference_import_id' => $manifestId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($values !== []) {
            DB::table('erapor_ref_curricula')->upsert(
                $values,
                ['external_id'],
                [
                    'name',
                    'education_level_id',
                    'major_external_id',
                    'valid_from',
                    'valid_until',
                    'is_active',
                    'source_updated_at',
                    'reference_import_id',
                    'updated_at',
                ]
            );
        }

        return ['imported' => count($values), 'skipped' => $skipped, 'conflicted' => 0];
    }

    /**
     * @return array{imported: int, skipped: int, conflicted: int}
     */
    private function importCurriculumSubjects(array $rows, int $manifestId): array
    {
        $curriculumIds = EraporRefCurriculum::query()
            ->whereIn('external_id', array_column($rows, 'kurikulum_id'))
            ->pluck('id', 'external_id');
        $subjectIds = EraporRefSubject::query()
            ->whereIn('external_id', array_column($rows, 'mata_pelajaran_id'))
            ->pluck('id', 'external_id');
        $now = now();
        $values = [];
        $skipped = 0;

        foreach ($rows as $row) {
            $curriculumId = $curriculumIds->get($row['kurikulum_id'] ?? null);
            $subjectId = $subjectIds->get($row['mata_pelajaran_id'] ?? null);
            $educationLevelId = $row['tingkat_pendidikan_id'] ?? null;

            if (! $curriculumId || ! $subjectId || ! is_numeric($educationLevelId)) {
                $skipped++;

                continue;
            }

            $values[] = [
                'erapor_ref_curriculum_id' => $curriculumId,
                'erapor_ref_subject_id' => $subjectId,
                'education_level_id' => (int) $educationLevelId,
                'hours' => max(0, (int) ($row['jumlah_jam'] ?? 0)),
                'maximum_hours' => max(0, (int) ($row['jumlah_jam_maksimum'] ?? 0)),
                'curriculum_status' => is_numeric($row['status_di_kurikulum'] ?? null)
                    ? (int) $row['status_di_kurikulum']
                    : null,
                'is_required' => filter_var($row['wajib'] ?? false, FILTER_VALIDATE_BOOLEAN),
                'is_active' => empty($row['expired_date']),
                'reference_import_id' => $manifestId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($values !== []) {
            DB::table('erapor_ref_curriculum_subjects')->upsert(
                $values,
                ['erapor_ref_curriculum_id', 'erapor_ref_subject_id', 'education_level_id'],
                [
                    'hours',
                    'maximum_hours',
                    'curriculum_status',
                    'is_required',
                    'is_active',
                    'reference_import_id',
                    'updated_at',
                ]
            );
        }

        return ['imported' => count($values), 'skipped' => $skipped, 'conflicted' => 0];
    }

    private function date(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value)->toDateString();
    }

    private function dateTime(mixed $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d H:i:s');
    }

    /**
     * @return array<string, mixed>
     */
    private function result(EraporReferenceImport $manifest, bool $unchanged): array
    {
        return [
            'dataset' => $manifest->dataset,
            'manifest_id' => $manifest->id,
            'status' => $manifest->status,
            'files' => $manifest->files_count,
            'records' => $manifest->records_imported,
            'skipped' => $manifest->records_skipped,
            'checksum' => $manifest->checksum,
            'unchanged' => $unchanged,
        ];
    }
}
