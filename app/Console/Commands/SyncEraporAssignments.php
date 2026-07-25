<?php

namespace App\Console\Commands;

use App\Models\TahunPelajaran;
use App\Services\Erapor\EraporAssignmentSyncService;
use Illuminate\Console\Command;

class SyncEraporAssignments extends Command
{
    protected $signature = 'erapor:sync-assignments
                            {--period= : ID tahun pelajaran; default memakai periode aktif}';

    protected $description = 'Membentuk penugasan e-Rapor permanen dari jadwal SISFO';

    public function handle(EraporAssignmentSyncService $syncService): int
    {
        $period = TahunPelajaran::query()
            ->when(
                $this->option('period'),
                fn ($query, $periodId) => $query->whereKey($periodId),
                fn ($query) => $query->where('is_active', true)
            )
            ->first();

        if (! $period) {
            $this->error('Tahun pelajaran yang akan disinkronkan tidak ditemukan.');

            return self::FAILURE;
        }

        $stats = $syncService->sync($period);

        $this->table(
            ['Periode', 'Total', 'Baru', 'Diperbarui', 'Aktif kembali', 'Dinonaktifkan'],
            [[
                "{$period->tahun} / {$period->semester}",
                $stats['total'],
                $stats['created'],
                $stats['updated'],
                $stats['reactivated'],
                $stats['deactivated'],
            ]]
        );

        return self::SUCCESS;
    }
}
