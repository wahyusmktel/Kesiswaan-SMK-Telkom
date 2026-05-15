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

class UkkSoalTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    use Exportable;

    public function array(): array
    {
        return [
            [1, 'Sebutkan dan jelaskan jenis-jenis alat yang digunakan dalam kegiatan produksi!'],
            [2, 'Apa yang dimaksud dengan prosedur Keselamatan dan Kesehatan Kerja (K3)?'],
            [3, 'Jelaskan langkah-langkah pemeriksaan kualitas produk yang baik!'],
            [4, 'Tuliskan minimal 3 faktor yang mempengaruhi efisiensi kerja!'],
            [5, '(Tambahkan soal Anda di baris berikutnya…)'],
        ];
    }

    public function headings(): array
    {
        return ['No', 'Pertanyaan'];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 80,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        // Header row style
        $sheet->getStyle('A1:B1')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1D4ED8']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(22);

        // Data rows
        $lastRow = count($this->array()) + 1;
        $sheet->getStyle('A2:B' . $lastRow)->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DBEAFE']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_TOP, 'wrapText' => true],
        ]);

        // No column center
        $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Alternate row color
        for ($i = 2; $i <= $lastRow; $i++) {
            if ($i % 2 === 0) {
                $sheet->getStyle('A' . $i . ':B' . $i)->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('EFF6FF');
            }
        }

        // Row height for data rows
        for ($i = 2; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(28);
        }

        return [];
    }
}
