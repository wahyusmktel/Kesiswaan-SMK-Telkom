<?php

namespace App\Exports;

use App\Models\FingerprintAttendanceSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class FingerprintAttendanceMonitoringExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, ShouldAutoSize
{
    private int $rowNumber = 0;

    public function __construct(
        private Collection $rows,
        private string $date,
        private array $filters = [],
        private ?FingerprintAttendanceSetting $setting = null,
    ) {}

    public function collection(): Collection
    {
        return $this->rows;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Pegawai',
            'Kode/NIP',
            'Email',
            'User ID Mesin',
            'Mesin',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Total Scan',
            'Status',
            'Keterangan',
        ];
    }

    public function map($row): array
    {
        $this->rowNumber++;
        $firstScan = $row->first_scan ? Carbon::parse($row->first_scan) : null;
        $lastScan = $row->last_scan ? Carbon::parse($row->last_scan) : null;
        $hasCheckout = $firstScan && $lastScan && !$firstScan->equalTo($lastScan);
        $setting = $this->setting ?? FingerprintAttendanceSetting::getSetting();
        $checkinEnd = Carbon::parse($this->date . ' ' . $setting->checkin_end);
        $checkoutStart = Carbon::parse($this->date . ' ' . $setting->checkout_start);
        $lateMinutes = $firstScan && $firstScan->greaterThan($checkinEnd) ? (int) ceil($checkinEnd->diffInMinutes($firstScan)) : 0;
        $earlyMinutes = $hasCheckout && $lastScan->lessThan($checkoutStart) ? (int) ceil($lastScan->diffInMinutes($checkoutStart)) : 0;
        $notes = [];

        if ($lateMinutes > 0) {
            $notes[] = "Terlambat {$lateMinutes} menit";
        }

        if ($earlyMinutes > 0) {
            $notes[] = "Pulang cepat {$earlyMinutes} menit";
        }

        return [
            $this->rowNumber,
            $row->appUser?->masterGuru?->nama_lengkap ?? $row->appUser?->name ?? $row->name,
            $row->appUser?->masterGuru?->kode_guru ?? '-',
            $row->appUser?->email ?? '-',
            $row->user_id,
            $row->device?->name ?? '-',
            Carbon::parse($this->date)->format('d/m/Y'),
            $firstScan?->format('H:i:s') ?? '-',
            $hasCheckout ? $lastScan->format('H:i:s') : '-',
            (int) ($row->total_scan ?? 0),
            $firstScan ? ($hasCheckout ? 'Hadir Lengkap' : 'Belum Scan Pulang') : 'Belum Ada Scan',
            $notes ? implode(', ', $notes) : ($firstScan ? 'Sesuai jadwal' : 'Tidak ada log pada tanggal ini'),
        ];
    }

    public function title(): string
    {
        return 'Monitoring Absensi';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 30,
            'C' => 14,
            'D' => 28,
            'E' => 16,
            'F' => 20,
            'G' => 14,
            'H' => 12,
            'I' => 12,
            'J' => 12,
            'K' => 20,
            'L' => 32,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();

        $sheet->insertNewRowBefore(1, 3);
        $sheet->setCellValue('A1', 'MONITORING ABSENSI FINGERPRINT');
        $sheet->setCellValue('A2', 'Tanggal: ' . Carbon::parse($this->date)->translatedFormat('d F Y'));
        $sheet->setCellValue('A3', 'Filter: ' . $this->filterLabel() . ' | Diekspor: ' . now()->format('d/m/Y H:i'));
        $sheet->mergeCells('A1:L1');
        $sheet->mergeCells('A2:L2');
        $sheet->mergeCells('A3:L3');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $headerRow = 4;
        $sheet->getStyle("A{$headerRow}:L{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '991B1B']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(24);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow > $headerRow) {
            $sheet->getStyle("A5:L{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E5E7EB']]],
            ]);
            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("G5:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            for ($row = 5; $row <= $lastRow; $row++) {
                $status = (string) $sheet->getCell("K{$row}")->getValue();
                $fill = match ($status) {
                    'Hadir Lengkap' => 'ECFDF5',
                    'Belum Scan Pulang' => 'FFFBEB',
                    default => 'FEF2F2',
                };

                $sheet->getStyle("A{$row}:L{$row}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fill]],
                ]);
            }
        }

        $sheet->freezePane('A5');
        $sheet->setAutoFilter("A{$headerRow}:L{$lastRow}");

        return [];
    }

    private function filterLabel(): string
    {
        $parts = [];

        if (!empty($this->filters['search'])) {
            $parts[] = 'Pencarian "' . $this->filters['search'] . '"';
        }

        if (!empty($this->filters['device_id'])) {
            $parts[] = 'Mesin ID ' . $this->filters['device_id'];
        }

        return $parts ? implode(', ', $parts) : 'Semua pegawai termapping';
    }
}
