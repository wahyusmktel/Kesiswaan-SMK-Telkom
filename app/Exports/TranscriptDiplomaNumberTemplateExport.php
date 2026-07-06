<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TranscriptDiplomaNumberTemplateExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function __construct(private Collection $students)
    {
    }

    public function collection(): Collection
    {
        return $this->students->values();
    }

    public function headings(): array
    {
        return ['no', 'nama siswa', 'kelas', 'nomor ijazah'];
    }

    public function map($student): array
    {
        static $number = 0;
        $number++;

        return [
            $number,
            $student->nama_lengkap,
            $student->rombels->first()?->kelas?->nama_kelas ?? '-',
            $student->transcriptDiplomaNumber?->diploma_number ?? '',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('D2:D' . max(2, $this->students->count() + 1))->getFill()
            ->setFillType('solid')
            ->getStartColor()->setARGB('FFFFF2CC');

        return [];
    }
}
