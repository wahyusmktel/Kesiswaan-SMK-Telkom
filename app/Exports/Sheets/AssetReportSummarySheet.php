<?php

namespace App\Exports\Sheets;

use App\Models\AssetReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AssetReportSummarySheet implements FromArray, WithColumnWidths, WithStyles, WithTitle
{
    public function __construct(
        private Collection $reports,
        private array $filterLabels,
    ) {}

    public function array(): array
    {
        $openReports = $this->reports->whereNotIn('status', ['selesai', 'ditolak']);
        $completed = $this->reports->where('status', 'selesai')->filter->completed_at;
        $averageResolutionHours = $completed->isEmpty()
            ? 0
            : round($completed->avg(fn (AssetReport $report) => $report->created_at->diffInMinutes($report->completed_at) / 60), 1);

        $rows = [
            ['REKAP LAPORAN ASET DAN SARANA PRASARANA'],
            ['Filter: '.implode(' | ', $this->filterLabels).' | Diekspor: '.now()->format('d/m/Y H:i').' WIB'],
            ['RINGKASAN UTAMA', 'JUMLAH', '', 'STATUS', 'JUMLAH', '', 'URGENSI', 'JUMLAH', '', 'KATEGORI', 'JUMLAH'],
            ['Total laporan', $this->reports->count(), '', 'Baru', $this->reports->where('status', 'baru')->count(), '', 'Darurat', $this->reports->where('urgency', 'darurat')->count(), '', 'Rusak/Tidak Berfungsi', $this->reports->where('category', 'rusak')->count()],
            ['Belum selesai', $openReports->count(), '', 'Diverifikasi', $this->reports->where('status', 'diverifikasi')->count(), '', 'Tinggi', $this->reports->where('urgency', 'tinggi')->count(), '', 'Hilang', $this->reports->where('category', 'hilang')->count()],
            ['Prioritas tinggi/darurat', $this->reports->whereIn('urgency', ['tinggi', 'darurat'])->count(), '', 'Sedang Diproses', $this->reports->where('status', 'diproses')->count(), '', 'Normal', $this->reports->where('urgency', 'normal')->count(), '', 'Kebersihan', $this->reports->where('category', 'kebersihan')->count()],
            ['Selesai', $completed->count(), '', 'Selesai', $completed->count(), '', 'Rendah', $this->reports->where('urgency', 'rendah')->count(), '', 'Keselamatan', $this->reports->where('category', 'keselamatan')->count()],
            ['Rata-rata penyelesaian', $averageResolutionHours.' jam', '', 'Ditolak', $this->reports->where('status', 'ditolak')->count(), '', '', '', '', 'Listrik', $this->reports->where('category', 'listrik')->count()],
            ['', '', '', '', '', '', '', '', '', 'Air dan Sanitasi', $this->reports->where('category', 'air_sanitasi')->count()],
            ['', '', '', '', '', '', '', '', '', 'Jaringan/Internet', $this->reports->where('category', 'jaringan')->count()],
            ['', '', '', '', '', '', '', '', '', 'Lainnya', $this->reports->where('category', 'lainnya')->count()],
            ['LAPORAN PRIORITAS YANG BELUM SELESAI'],
            ['No', 'Nomor Tiket', 'Tanggal', 'Gedung', 'Ruangan', 'Aset/Peralatan', 'Kategori', 'Urgensi', 'Status', 'Pelapor', 'Ringkasan Masalah'],
        ];

        $priorityReports = $openReports
            ->whereIn('urgency', ['tinggi', 'darurat'])
            ->sortByDesc(fn (AssetReport $report) => [$report->urgency === 'darurat', $report->created_at])
            ->take(15)
            ->values();

        foreach ($priorityReports as $index => $report) {
            $rows[] = [
                $index + 1,
                $report->ticket_number,
                $report->created_at->format('d/m/Y H:i'),
                $report->location?->building?->name ?? '-',
                $report->location?->name ?? '-',
                $report->asset_name,
                AssetReport::CATEGORIES[$report->category] ?? ucfirst($report->category),
                ucfirst($report->urgency),
                AssetReport::STATUSES[$report->status] ?? ucfirst($report->status),
                $report->reporter_name,
                $report->description,
            ];
        }

        if ($priorityReports->isEmpty()) {
            $rows[] = ['Tidak ada laporan berprioritas tinggi atau darurat yang masih terbuka.'];
        }

        return $rows;
    }

    public function title(): string
    {
        return 'Ringkasan';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 24,
            'B' => 19,
            'C' => 18,
            'D' => 22,
            'E' => 15,
            'F' => 24,
            'G' => 22,
            'H' => 14,
            'I' => 19,
            'J' => 24,
            'K' => 48,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $lastRow = $sheet->getHighestRow();
        $sheet->setShowGridlines(false);
        $sheet->mergeCells('A1:K1');
        $sheet->mergeCells('A2:K2');
        $sheet->mergeCells('A12:K12');
        $sheet->getStyle('A1:K'.$lastRow)->getFont()->setName('Arial')->setSize(10);
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '991B1B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        foreach (['A3:B3', 'D3:E3', 'G3:H3', 'J3:K3', 'A13:K13'] as $range) {
            $sheet->getStyle($range)->applyFromArray([
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DC2626']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '991B1B']]],
            ]);
        }

        $sheet->getStyle('A4:B8')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFF7ED']]]);
        $sheet->getStyle('D4:E8')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']]]);
        $sheet->getStyle('G4:H7')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']]]);
        $sheet->getStyle('J4:K11')->applyFromArray(['fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDFA']]]);
        $sheet->getStyle('B4:B8')->getFont()->setBold(true)->setSize(12)->getColor()->setRGB('991B1B');
        $sheet->getStyle('E4:E8')->getFont()->setBold(true);
        $sheet->getStyle('H4:H7')->getFont()->setBold(true);
        $sheet->getStyle('K4:K11')->getFont()->setBold(true);
        $sheet->getStyle('B4:B8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('E4:E8')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('H4:H7')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
        $sheet->getStyle('K4:K11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);

        $sheet->getStyle('A12')->applyFromArray([
            'font' => ['bold' => true, 'size' => 12, 'color' => ['rgb' => '991B1B']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEE2E2']],
        ]);

        if ($lastRow > 13) {
            $sheet->getStyle("A14:K{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_TOP],
                'borders' => ['bottom' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'E2E8F0']]],
            ]);
            $sheet->getStyle("K14:K{$lastRow}")->getAlignment()->setWrapText(true);
            $sheet->getStyle("A14:J{$lastRow}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        }

        $sheet->freezePane('A13');
        $sheet->getPageSetup()->setOrientation('landscape')->setFitToWidth(1)->setFitToHeight(0);
        $sheet->getPageMargins()->setTop(0.4)->setRight(0.3)->setBottom(0.4)->setLeft(0.3);

        return [];
    }
}
