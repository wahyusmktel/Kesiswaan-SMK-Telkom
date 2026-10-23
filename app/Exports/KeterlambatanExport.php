<?php

namespace App\Exports;

use App\Models\Keterlambatan;
use App\Models\Kelas;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\Exportable;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KeterlambatanExport implements FromQuery, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    use Exportable;

    protected $filters;
    private int $rowNumber = 0;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Keterlambatan::with(['siswa.rombels.kelas', 'security', 'guruPiket']);

        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $query->whereBetween('waktu_dicatat_security', [
                $this->filters['start_date'] . ' 00:00:00',
                $this->filters['end_date'] . ' 23:59:59'
            ]);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        if (!empty($this->filters['kelas_id'])) {
            $kelasId = $this->filters['kelas_id'];
            $query->whereHas('siswa.rombels', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        if (!empty($this->filters['wali_kelas_id'])) {
            $query->whereHas('siswa.rombels', function ($q) {
                $q->where('wali_kelas_id', $this->filters['wali_kelas_id']);
            });
        }

        return $query->latest('waktu_dicatat_security');
    }

    public function title(): string
    {
        return 'Rekap Keterlambatan';
    }

    public function headings(): array
    {
        return [
            'NO',
            'NAMA LENGKAP SISWA',
            'NIS',
            'KELAS',
            'WAKTU DATANG',
            'ALASAN SISWA',
            'STATUS PEMBINAAN',
            'PENCATAT (SECURITY)',
            'GURU PIKET (VERIFIKATOR)',
            'WAKTU VERIFIKASI',
        ];
    }

    public function map($late): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $late->siswa?->nama_lengkap ?? 'Siswa Tidak Ditemukan',
            $late->siswa?->nis ? (string)$late->siswa->nis : '-',
            $late->siswa?->rombels->first()?->kelas?->nama_kelas ?? 'N/A',
            $late->waktu_dicatat_security ? $late->waktu_dicatat_security->format('d/m/Y H:i') : '-',
            $late->alasan_siswa ?? '-',
            str_replace('_', ' ', strtoupper($late->status ?? '-')),
            $late->security?->name ?? 'N/A',
            $late->guruPiket?->name ?? '-',
            $late->waktu_verifikasi_piket ? $late->waktu_verifikasi_piket->format('d/m/Y H:i') : '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 32,  // Nama Siswa
            'C' => 14,  // NIS
            'D' => 14,  // Kelas
            'E' => 18,  // Waktu Datang
            'F' => 32,  // Alasan
            'G' => 22,  // Status
            'H' => 24,  // Security
            'I' => 24,  // Guru Piket
            'J' => 18,  // Waktu Verifikasi
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // 1. Insert 3 title rows before data headings
        $sheet->insertNewRowBefore(1, 3);

        $periodeText = 'Semua Periode';
        if (!empty($this->filters['start_date']) && !empty($this->filters['end_date'])) {
            $startDate = Carbon::parse($this->filters['start_date'])->translatedFormat('d F Y');
            $endDate = Carbon::parse($this->filters['end_date'])->translatedFormat('d F Y');
            $periodeText = "Periode: {$startDate} s/d {$endDate}";
        }

        $filterExtra = [];
        if (!empty($this->filters['kelas_id'])) {
            $kelas = Kelas::find($this->filters['kelas_id']);
            if ($kelas) {
                $filterExtra[] = "Kelas: {$kelas->nama_kelas}";
            }
        }
        if (!empty($this->filters['status'])) {
            $filterExtra[] = "Status: " . str_replace('_', ' ', strtoupper($this->filters['status']));
        }
        $subFilter = !empty($filterExtra) ? ' (' . implode(' | ', $filterExtra) . ')' : '';

        // Title content
        $sheet->setCellValue('A1', 'REKAPITULASI KETERLAMBATAN SISWA');
        $sheet->setCellValue('A2', 'SMK TELKOM LAMPUNG');
        $sheet->setCellValue('A3', "{$periodeText}{$subFilter}  •  Diekspor: " . now()->translatedFormat('d F Y, H:i') . ' WIB');

        $sheet->mergeCells('A1:J1');
        $sheet->mergeCells('A2:J2');
        $sheet->mergeCells('A3:J3');

        // Title styles
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('A3')->applyFromArray([
            'font' => ['italic' => true, 'size' => 9.5, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);

        $sheet->getRowDimension(1)->setRowHeight(24);
        $sheet->getRowDimension(2)->setRowHeight(18);
        $sheet->getRowDimension(3)->setRowHeight(18);

        // Header Row (Now at Row 4)
        $headerRow = 4;
        $sheet->getRowDimension($headerRow)->setRowHeight(28);
        $sheet->getStyle("A{$headerRow}:J{$headerRow}")->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 10,
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '991B1B'], // Telkom Red Accent
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true,
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '7F1D1D'],
                ],
            ],
        ]);

        // Data Rows Styling
        $lastRow = $sheet->getHighestRow();
        if ($lastRow > $headerRow) {
            // General row height and border
            $sheet->getStyle("A5:J{$lastRow}")->applyFromArray([
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'E2E8F0'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ]);

            // Specific Column Alignments
            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C5:E{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G5:G{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("J5:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Nama Siswa bold
            $sheet->getStyle("B5:B{$lastRow}")->getFont()->setBold(true)->setSize(10);
            // NIS explicit text format to prevent scientific notation
            $sheet->getStyle("C5:C{$lastRow}")->getNumberFormat()->setFormatCode('@');
            // Alasan wrap text
            $sheet->getStyle("F5:F{$lastRow}")->getAlignment()->setWrapText(true);

            // Modern Zebra Striping & Status Highlighting
            for ($row = 5; $row <= $lastRow; $row++) {
                $sheet->getRowDimension($row)->setRowHeight(22);

                // Alternating row background
                $isEven = ($row % 2 === 0);
                $rowBg = $isEven ? 'F8FAFC' : 'FFFFFF';
                $sheet->getStyle("A{$row}:J{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($rowBg);

                // Status Column Tag Styling
                $statusVal = strtoupper(trim((string)$sheet->getCell("G{$row}")->getValue()));
                $statusColor = match (true) {
                    str_contains($statusVal, 'SELESAI') || str_contains($statusVal, 'VERIFIKASI') => ['bg' => 'DCFCE7', 'text' => '166534'], // Soft Emerald
                    str_contains($statusVal, 'MENUNGGU') => ['bg' => 'FEF3C7', 'text' => '92400E'], // Soft Amber
                    str_contains($statusVal, 'TOLAK') => ['bg' => 'FEE2E2', 'text' => '991B1B'], // Soft Rose
                    str_contains($statusVal, 'BK') || str_contains($statusVal, 'PENDAMPINGAN') => ['bg' => 'F3E8FF', 'text' => '6B21A8'], // Soft Purple
                    default => ['bg' => 'F1F5F9', 'text' => '334155'],
                };

                $sheet->getStyle("G{$row}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $statusColor['text']]],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor['bg']]],
                ]);
            }

            // Summary Footer Row
            $footerRow = $lastRow + 1;
            $totalCount = $lastRow - $headerRow;
            $sheet->setCellValue("A{$footerRow}", "TOTAL KETERLAMBATAN : {$totalCount} CATATAN");
            $sheet->mergeCells("A{$footerRow}:J{$footerRow}");
            $sheet->getRowDimension($footerRow)->setRowHeight(24);
            $sheet->getStyle("A{$footerRow}:J{$footerRow}")->applyFromArray([
                'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '0F172A']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => [
                    'top' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '94A3B8']],
                    'bottom' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '94A3B8']],
                ],
            ]);
            $sheet->getStyle("A{$footerRow}")->getAlignment()->setIndent(1);
        }

        // Freeze top panes so headers are sticky when scrolling
        $sheet->freezePane('A5');

        // Enable Excel native AutoFilter dropdowns
        $sheet->setAutoFilter("A{$headerRow}:J{$lastRow}");

        return [];
    }
}
