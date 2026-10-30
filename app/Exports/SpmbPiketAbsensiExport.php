<?php

namespace App\Exports;

use App\Exports\Sheets\SpmbPiketAbsensiDetailSheet;
use App\Exports\Sheets\SpmbPiketAbsensiRekapSheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SpmbPiketAbsensiExport implements WithMultipleSheets
{
    use Exportable;

    public function __construct(
        private Collection $detailRecords,
        private Collection $rekapRecords,
        private string $tahunAjaran,
        private array $stats
    ) {}

    public function sheets(): array
    {
        return [
            new SpmbPiketAbsensiDetailSheet($this->detailRecords, $this->tahunAjaran, $this->stats),
            new SpmbPiketAbsensiRekapSheet($this->rekapRecords, $this->tahunAjaran),
        ];
    }
}
