<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class RekapNilaiUkkExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    use Exportable;

    private int $rowNumber = 0;

    public function __construct(
        private readonly mixed $ujian,
        private readonly mixed $rekap
    ) {}

    public function collection()
    {
        return $this->rekap;
    }

    public function title(): string
    {
        return 'Rekap UKK';
    }

    public function headings(): array
    {
        $heads = ['No', 'NIS', 'Nama Siswa', 'Kelas'];

        foreach ($this->ujian->instrumens as $ins) {
            $label = $ins->nama_instrumen;
            $heads[] = $label . ' - Skor P';
            $heads[] = $label . ' - Skor K';
            $heads[] = $label . ' - Nilai';
        }

        $heads[] = 'Nilai Akhir';
        $heads[] = 'Status';

        return $heads;
    }

    public function map($row): array
    {
        $this->rowNumber++;

        $cols = [
            $this->rowNumber,
            $row['siswa']->nis ?? '-',
            $row['siswa']->nama_lengkap,
            $row['rombel_label'],
        ];

        foreach ($row['instrumen_scores'] as $score) {
            $cols[] = $score['skor_p'] !== null ? $score['skor_p'] : '-';
            $cols[] = $score['skor_k'] !== null ? $score['skor_k'] : '-';
            $cols[] = $score['nilai']  !== null ? $score['nilai']  : '-';
        }

        $cols[] = $row['nilai_akhir'] !== null ? $row['nilai_akhir'] : '-';
        $cols[] = $row['is_complete'] ? 'Selesai' : 'Belum Selesai';

        return $cols;
    }

    public function styles(Worksheet $sheet): array
    {
        $lastCol = $sheet->getHighestColumn();
        $lastRow = $sheet->getHighestRow();

        // Header row style
        $sheet->getStyle('A1:' . $lastCol . '1')->applyFromArray([
            'font' => [
                'bold'  => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size'  => 10,
            ],
            'fill' => [
                'fillType'   => Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'C05621'],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'FFFFFF'],
                ],
            ],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(36);

        // Data rows
        if ($lastRow > 1) {
            $sheet->getStyle('A2:' . $lastCol . $lastRow)->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color'       => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
            ]);

            // Alternate row shading
            for ($row = 2; $row <= $lastRow; $row++) {
                if ($row % 2 === 0) {
                    $sheet->getStyle('A' . $row . ':' . $lastCol . $row)->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FFF8F0');
                }
            }

            // Center No, NIS, Nilai columns
            $sheet->getStyle('A2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Style nilai akhir column (second to last)
            $lastColIndex = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($lastCol);
            $nilaiAkhirCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($lastColIndex - 1);
            $sheet->getStyle($nilaiAkhirCol . '2:' . $nilaiAkhirCol . $lastRow)->applyFromArray([
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ]);

            // Status column (last)
            $sheet->getStyle($lastCol . '2:' . $lastCol . $lastRow)->applyFromArray([
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'font' => ['bold' => true],
            ]);
        }

        // Auto-width all columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
