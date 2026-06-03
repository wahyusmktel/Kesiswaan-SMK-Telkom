<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LegerSiswaDetailExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly mixed $ujian,
        private readonly mixed $siswa,
        private readonly array $detail
    ) {}

    public function collection()
    {
        return $this->detail['subjects'];
    }

    public function headings(): array
    {
        return ['No', 'Mata Pelajaran', 'Jumlah Benar', 'Jumlah Soal', 'Nilai'];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['mapel'],
            $row['jumlah_benar'] ?? '-',
            $row['jumlah_soal'] ?? '-',
            $row['nilai'] !== null ? $row['nilai'] : '-',
        ];
    }

    public function title(): string
    {
        return 'Detail Nilai Siswa';
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->insertNewRowBefore(1, 4);
        $lastRow = $sheet->getHighestRow();

        $sheet->setCellValue('A1', 'DETAIL NILAI SISWA');
        $sheet->setCellValue('A2', $this->siswa->nama_lengkap . ' | NIS: ' . $this->siswa->nis);
        $sheet->setCellValue('A3', $this->ujian->nama_ujian . ' | Rata-rata: ' . ($this->detail['average'] !== null ? number_format($this->detail['average'], 2) : '-'));
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $sheet->getStyle('A5:E5')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        if ($lastRow > 5) {
            $sheet->getStyle("A6:E{$lastRow}")->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);
            $sheet->getStyle("A6:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C6:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        return [];
    }
}
