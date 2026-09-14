<?php

namespace App\Exports\Sheets;

use App\Models\AssetReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetReportDetailSheet implements FromCollection, WithColumnFormatting, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Collection $reports,
        private array $filterLabels,
    ) {}

    public function collection(): Collection
    {
        return $this->reports;
    }

    public function headings(): array
    {
        return [
            'No', 'Nomor Tiket', 'Waktu Laporan', 'Gedung', 'Ruangan', 'Lantai',
            'Aset/Peralatan', 'Kategori', 'Urgensi', 'Status', 'Nama Pelapor',
            'Tipe Pelapor', 'Identitas', 'Kontak', 'Deskripsi Masalah',
            'Tindak Lanjut', 'Ditangani Oleh', 'Mulai Ditangani', 'Selesai',
            'Durasi Penyelesaian (Jam)', 'Foto',
        ];
    }

    public function map($report): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $report->ticket_number,
            ExcelDate::dateTimeToExcel($report->created_at),
            $report->location?->building?->name ?? '-',
            $report->location?->name ?? '-',
            $report->location?->floor ?? '-',
            $report->asset_name,
            AssetReport::CATEGORIES[$report->category] ?? ucfirst($report->category),
            ucfirst($report->urgency),
            AssetReport::STATUSES[$report->status] ?? ucfirst($report->status),
            $report->reporter_name,
            str_replace('_', '/', ucfirst($report->reporter_type)),
            $report->reporter_identifier ?? '-',
            $report->contact ?? '-',
            $report->description,
            $report->admin_notes ?? '-',
            $report->handler?->name ?? '-',
            $report->handled_at ? ExcelDate::dateTimeToExcel($report->handled_at) : null,
            $report->completed_at ? ExcelDate::dateTimeToExcel($report->completed_at) : null,
            $report->completed_at ? round($report->created_at->diffInMinutes($report->completed_at) / 60, 1) : null,
            $report->photo_path ? 'Buka foto' : '-',
        ];
    }

    public function title(): string
    {
        return 'Detail Laporan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6, 'B' => 20, 'C' => 19, 'D' => 18, 'E' => 24, 'F' => 14,
            'G' => 28, 'H' => 24, 'I' => 12, 'J' => 19, 'K' => 24, 'L' => 16,
            'M' => 18, 'N' => 18, 'O' => 48, 'P' => 48, 'Q' => 24, 'R' => 19,
            'S' => 19, 'T' => 22, 'U' => 14,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => 'dd/mm/yyyy hh:mm',
            'M' => NumberFormat::FORMAT_TEXT,
            'N' => NumberFormat::FORMAT_TEXT,
            'R' => 'dd/mm/yyyy hh:mm',
            'S' => 'dd/mm/yyyy hh:mm',
            'T' => '0.0',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->insertNewRowBefore(1, 3);
        $sheet->setCellValue('A1', 'DETAIL LAPORAN ASET DAN SARANA PRASARANA');
        $sheet->setCellValue('A2', 'Filter: '.implode(' | ', $this->filterLabels));
        $sheet->setCellValue('A3', 'Diekspor: '.now()->format('d/m/Y H:i').' WIB | Total: '.$this->reports->count().' laporan');
        $sheet->mergeCells('A1:U1');
        $sheet->mergeCells('A2:U2');
        $sheet->mergeCells('A3:U3');
        $sheet->setShowGridlines(false);
        $sheet->getStyle('A1:U'.$sheet->getHighestRow())->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getStyle('A2:A3')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
        ]);
        $sheet->getStyle('A4:U4')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '991B1B']]],
        ]);
        $sheet->getRowDimension(4)->setRowHeight(32);

        $lastRow = $sheet->getHighestRow();
        if ($lastRow > 4) {
            $sheet->getStyle("A5:U{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("O5:P{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A5:N{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("Q5:U{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C5:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("H5:J{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("R5:U{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }

        $sheet->freezePane('D5');
        $sheet->setAutoFilter("A4:U{$lastRow}");
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setRight(0.25)->setBottom(0.4)->setLeft(0.25);

        return [];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                foreach ($this->reports->values() as $index => $report) {
                    $row = $index + 5;
                    $sheet->getCell("B{$row}")->setValueExplicit((string) $report->ticket_number, DataType::TYPE_STRING);
                    $sheet->getCell("M{$row}")->setValueExplicit((string) ($report->reporter_identifier ?? '-'), DataType::TYPE_STRING);
                    $sheet->getCell("N{$row}")->setValueExplicit((string) ($report->contact ?? '-'), DataType::TYPE_STRING);
                    $fill = match ($report->status) {
                        'selesai' => 'ECFDF5',
                        'ditolak' => 'F1F5F9',
                        'diproses' => 'FFFBEB',
                        'diverifikasi' => 'ECFEFF',
                        default => in_array($report->urgency, ['tinggi', 'darurat'], true) ? 'FEF2F2' : ($row % 2 === 0 ? 'F8FAFC' : 'FFFFFF'),
                    };
                    $sheet->getStyle("A{$row}:U{$row}")->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fill);

                    if ($report->photo_path) {
                        $sheet->getCell("U{$row}")->getHyperlink()->setUrl(route('super-admin.asset-reports.photo', $report));
                        $sheet->getStyle("U{$row}")->getFont()->getColor()->setRGB('2563EB');
                        $sheet->getStyle("U{$row}")->getFont()->setUnderline(true);
                    }
                }
            },
        ];
    }
}
