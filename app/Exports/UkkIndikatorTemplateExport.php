<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class UkkIndikatorTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function __construct(private readonly string $namaKategori = '') {}

    public function array(): array
    {
        return [
            [1, 'Menyiapkan alat dan bahan sesuai standar operasional prosedur'],
            [2, 'Menggunakan alat dengan teknik dan cara yang benar'],
            [3, 'Memeriksa kondisi dan kelayakan alat sebelum digunakan'],
            [4, 'Menjaga keselamatan kerja selama proses pelaksanaan'],
            [5, '(Tambahkan indikator Anda di baris berikutnya…)'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Nama Indikator'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 75,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Data rows
        $lastRow = count($this->array()) + 1;
        $sheet->getStyle('A2:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'D1FAE5']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]);

        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':B' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('ECFDF5');
            }
            $sheet->getRowDimension($i)->setRowHeight(28);
        }

        // Kategori note in top-right (if provided)
        if ($this->namaKategori) {
            $sheet->setCellValue('D1', 'Kategori: ' . $this->namaKategori);
            $sheet->getStyle('D1')->getFont()->setItalic(true)->setColor(
                (new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('6B7280')
            );
        }

        return [];
    }
}
