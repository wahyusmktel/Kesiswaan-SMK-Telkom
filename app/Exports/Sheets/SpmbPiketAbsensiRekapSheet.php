<?php

namespace App\Exports\Sheets;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SpmbPiketAbsensiRekapSheet implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Collection $rekapData,
        private string $tahunAjaran
    ) {}

    public function collection(): Collection
    {
        return $this->rekapData;
    }

    public function title(): string
    {
        return 'Rekap Per Pegawai';
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Guru / Pegawai',
            'Kelompok',
            'Total Jadwal',
            'Hadir',
            'Izin',
            'Sakit',
            'Alpa / Tanpa Keterangan',
            'Persentase Kehadiran',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row['nama_petugas'],
            $row['nama_kelompok'],
            $row['total_jadwal'],
            $row['hadir'],
            $row['izin'],
            $row['sakit'],
            $row['alpa'],
            $row['persentase'] . '%',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 35,  // Nama
            'C' => 16,  // Kelompok
            'D' => 15,  // Total
            'E' => 12,  // Hadir
            'F' => 12,  // Izin
            'G' => 12,  // Sakit
            'H' => 22,  // Alpa
            'I' => 22,  // %
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // 1. Insert header rows at top
                $sheet->insertNewRowBefore(1, 4);

                $sheet->setCellValue('A1', 'REKAPITULASI KEHADIRAN PIKET SPMB PER PEGAWAI');
                $sheet->setCellValue('A2', "TAHUN AJARAN {$this->tahunAjaran} | SMK TELKOM LAMPUNG");

                $sheet->mergeCells('A1:I1');
                $sheet->mergeCells('A2:I2');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 13, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Table Header Row 5
                $headerRow = 5;
                $sheet->getRowDimension($headerRow)->setRowHeight(25);
                $sheet->getStyle("A{$headerRow}:I{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']], // Dark Slate
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
                ]);

                $lastRow = $headerRow + $this->rowNumber;

                if ($this->rowNumber > 0) {
                    for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(20);

                        // Alternate row shading
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                            ]);
                        }

                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("C{$row}:I{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Highlight percentage
                        $sheet->getStyle("I{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => '0F766E']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDFA']],
                        ]);
                    }

                    // Soft thin borders for all data
                    $sheet->getStyle("A{$headerRow}:I{$lastRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => ['rgb' => 'E2E8F0'],
                            ],
                        ],
                    ]);
                }
            },
        ];
    }
}
