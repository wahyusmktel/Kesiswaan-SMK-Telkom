<?php

namespace App\Console\Commands;

use App\Services\Erapor\EraporReferenceImportService;
use Illuminate\Console\Command;

class ImportEraporReferences extends Command
{
    protected $signature = 'erapor:import-references
                            {path : Folder root e-Rapor atau database/data}
                            {--source-version=8.0.3 : Versi sumber referensi}
                            {--dataset=* : Dataset tertentu yang akan diimpor}';

    protected $description = 'Mengimpor referensi kurikulum e-Rapor SMK secara idempoten';

    public function handle(EraporReferenceImportService $importer): int
    {
        $datasets = $this->option('dataset') ?: EraporReferenceImportService::DATASETS;

        try {
            $results = $importer->import(
                $this->argument('path'),
                $this->option('source-version'),
                $datasets
            );
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->table(
            ['Dataset', 'Status', 'Berkas', 'Rekaman', 'Dilewati', 'Checksum'],
            array_map(fn (array $result) => [
                $result['dataset'],
                $result['unchanged'] ? 'tidak berubah' : $result['status'],
                $result['files'],
                $result['records'],
                $result['skipped'],
                substr($result['checksum'], 0, 12),
            ], $results)
        );

        return self::SUCCESS;
    }
}
