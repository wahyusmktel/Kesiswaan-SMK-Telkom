<?php

namespace App\Exports;

use App\Exports\Sheets\AssetReportDetailSheet;
use App\Exports\Sheets\AssetReportSummarySheet;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AssetReportExport implements WithMultipleSheets
{
    public function __construct(
        private Collection $reports,
        private array $filterLabels,
    ) {}

    public function sheets(): array
    {
        return [
            new AssetReportSummarySheet($this->reports, $this->filterLabels),
            new AssetReportDetailSheet($this->reports, $this->filterLabels),
        ];
    }
}
