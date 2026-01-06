<?php

namespace App\Exports;

use App\Models\AbsensiGuru;
use App\Models\MasterGuru;
use App\Models\TahunPelajaran;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\Exportable;

class SemesterAuditExport implements WithMultipleSheets
{
    use Exportable;

    protected $tahunPelajaranId;

    public function __construct($tahunPelajaranId)
    {
        $this->tahunPelajaranId = $tahunPelajaranId;
    }

    public function sheets(): array
    {
        return [
            new \App\Exports\Sheets\SemesterSummarySheet($this->tahunPelajaranId),
            new \App\Exports\Sheets\TeacherAnalysisSheet($this->tahunPelajaranId),
        ];
    }
}
