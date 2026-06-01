<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class UjianSemesterNilaiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize, WithColumnFormatting
{
    private int $rowNumber = 0;

    public function __construct(
        private readonly mixed $ujian,
        private readonly Collection $data,
        private readonly array $filters = []
    ) {}

    public function collection()
    {
        return $this->data;
    }

    public function title(): string
    {
        return 'Rekap Nilai';
    }

    public function headings(): array
    {
        return [
            'No',
            'NIS',
            'Nama Lengkap',
            'Kelas',
            'Mata Pelajaran',
            'Jumlah Benar',
            'Jumlah Soal',
            'Nilai Akhir',
            'Status Data',
            'File Import',
            'Waktu Import',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $row->kode_peserta,
            $row->nama_lengkap,
            $row->kelas,
            $row->mataPelajaran?->nama_mapel,
            $row->jumlah_benar,
            $row->jumlah_soal,
            $row->nilai_akhir !== null ? (float) $row->nilai_akhir : null,
            $row->master_siswa_id ? 'Cocok master siswa' : 'NIS belum cocok',
            $row->nama_file,
            $row->imported_at?->format('d/m/Y H:i'),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $sheet->insertNewRowBefore(1, 3);
        $lastRow = $sheet->getHighestRow();
        $sheet->setCellValue('A1', 'REKAP NILAI UJIAN SEMESTER');
        $sheet->setCellValue('A2', $this->ujian->nama_ujian . ' | ' . ($this->ujian->tahunPelajaran?->tahun ?? '-') . ' - ' . $this->ujian->semester);
        $sheet->setCellValue('A3', 'Filter kelas: ' . ($this->filters['kelas'] ?? 'Semua') . ' | Diekspor: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A3:K3');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '475569']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headerRow = 4;
        $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(26);

        if ($lastRow > $headerRow) {
            $sheet->getStyle("A5:K{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("F5:H{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H5:H{$lastRow}")->getFont()->setBold(true);

            for ($row = 5; $row <= $lastRow; $row++) {
                if ($row % 2 === 1) {
                    $sheet->getStyle("A{$row}:K{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()->setRGB('FEF2F2');
                }
            }
        }

        $sheet->setAutoFilter("A{$headerRow}:{$lastCol}{$lastRow}");
        $sheet->freezePane('A5');

        return [];
    }
}
