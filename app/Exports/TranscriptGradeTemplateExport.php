<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TranscriptGradeTemplateExport implements FromCollection, ShouldAutoSize, WithHeadings, WithStyles
{
    public function __construct(private Collection $students, private Collection $subjects)
    {
    }

    public function headings(): array
    {
        return array_merge(['NO', 'NAMA SISWA', 'KELAS', 'NISN'], $this->subjects->pluck('name')->all());
    }

    public function collection(): Collection
    {
        return $this->students->values()->map(function ($student, $index) {
            $grades = $student->transcriptGrades->keyBy('transcript_subject_id');
            $kelas = $student->rombels->first()?->kelas?->nama_kelas ?? '-';

            $row = [
                $index + 1,
                $student->nama_lengkap,
                $kelas,
                $student->dapodik?->nisn,
            ];

            foreach ($this->subjects as $subject) {
                $score = $grades->get($subject->id)?->score;
                $row[] = $score !== null ? number_format((float) $score, 2, '.', '') : '';
            }

            return $row;
        });
    }

    public function styles(Worksheet $sheet)
    {
        $lastColumn = $sheet->getHighestColumn();
        $sheet->getStyle("A1:{$lastColumn}1")->getFont()->setBold(true);
        $sheet->getStyle("E1:{$lastColumn}1")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('FFFFF3CD');
        $sheet->getStyle("A1:{$lastColumn}1")->getAlignment()->setWrapText(true);

        return [];
    }
}
