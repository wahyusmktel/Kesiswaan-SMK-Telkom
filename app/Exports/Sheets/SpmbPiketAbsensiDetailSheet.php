<?php

namespace App\Exports\Sheets;

use Carbon\Carbon;
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

class SpmbPiketAbsensiDetailSheet implements FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithMapping, WithStyles, WithTitle
{
    private int $rowNumber = 0;

    public function __construct(
        private Collection $records,
        private string $tahunAjaran,
        private array $stats
    ) {}

    public function collection(): Collection
    {
        return $this->records;
    }

    public function title(): string
    {
        return 'Detail Presensi Piket';
    }

    public function headings(): array
    {
        return [
            'No',
            'Hari & Tanggal',
            'Kelompok',
            'Nama Guru / Pegawai',
            'Jabatan',
            'Waktu Piket',
            'Status Kehadiran',
            'Jam Masuk / Absen',
            'Bukti Eviden',
            'Metode Input',
            'Keterangan / Catatan',
        ];
    }

    public function map($record): array
    {
        $this->rowNumber++;

        $tgl = $record->tanggal;
        $petugas = $record->petugas;
        $kelompok = $petugas?->kelompok ?? $tgl?->kelompok;

        $tglStr = $tgl ? Carbon::parse($tgl->tanggal)->locale('id')->translatedFormat('d F Y') : '-';
        $hariStr = $tgl ? $tgl->hari : '-';

        return [
            $this->rowNumber,
            "{$hariStr}, {$tglStr}",
            $kelompok?->nama_kelompok ?? '-',
            $petugas?->nama_petugas ?? '-',
            $petugas?->jabatan ?? 'Petugas Piket SPMB',
            $kelompok?->waktu ?? '07.30 - 12.00',
            $record->status ?? 'Belum Absen',
            $record->jam_absen ? Carbon::parse($record->jam_absen)->format('H:i') . ' WIB' : '-',
            $record->foto_eviden ? 'Ada Bukti Foto' : 'Tidak Ada',
            $record->metode_input === 'manual_kaur' ? 'Manual (Kaur SPMB)' : 'Mandiri (Guru)',
            $record->keterangan ?? '-',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // No
            'B' => 22,  // Hari & Tanggal
            'C' => 16,  // Kelompok
            'D' => 32,  // Nama
            'E' => 22,  // Jabatan
            'F' => 18,  // Waktu
            'G' => 18,  // Status
            'H' => 20,  // Jam
            'I' => 16,  // Eviden
            'J' => 20,  // Metode
            'K' => 35,  // Keterangan
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
                $sheet->insertNewRowBefore(1, 6);

                // Title Banner
                $sheet->setCellValue('A1', 'REKAPITULASI PRESENSI PIKET SPMB SMK TELKOM LAMPUNG');
                $sheet->setCellValue('A2', "TAHUN AJARAN {$this->tahunAjaran} | Dicetak pada: " . now()->locale('id')->translatedFormat('d F Y H:i'));

                $sheet->mergeCells('A1:K1');
                $sheet->mergeCells('A2:K2');

                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // KPI Summary Cards on Row 4
                $sheet->setCellValue('A4', 'TOTAL JADWAL');
                $sheet->setCellValue('B4', $this->stats['total_jadwal'] ?? 0);
                $sheet->setCellValue('C4', 'HADIR');
                $sheet->setCellValue('D4', $this->stats['hadir'] ?? 0);
                $sheet->setCellValue('E4', 'IZIN');
                $sheet->setCellValue('F4', $this->stats['izin'] ?? 0);
                $sheet->setCellValue('G4', 'SAKIT');
                $sheet->setCellValue('H4', $this->stats['sakit'] ?? 0);
                $sheet->setCellValue('I4', 'ALPA / BELUM');
                $sheet->setCellValue('J4', $this->stats['alpa'] ?? 0);
                $sheet->setCellValue('K4', 'TINGKAT KEHADIRAN: ' . ($this->stats['persentase'] ?? '0') . '%');

                $sheet->getStyle('A4:K4')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '334155']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);

                // Table Header Row 7
                $headerRow = 7;
                $sheet->getRowDimension($headerRow)->setRowHeight(26);
                $sheet->getStyle("A{$headerRow}:K{$headerRow}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']], // Soft Slate Dark
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '475569']]],
                ]);

                $lastRow = $headerRow + $this->rowNumber;

                // Data Rows Formatting
                if ($this->rowNumber > 0) {
                    for ($row = $headerRow + 1; $row <= $lastRow; $row++) {
                        $sheet->getRowDimension($row)->setRowHeight(20);

                        // Zebra shading
                        if ($row % 2 === 0) {
                            $sheet->getStyle("A{$row}:K{$row}")->applyFromArray([
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                            ]);
                        }

                        // Alignments
                        $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
                        $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("F{$row}:H{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle("I{$row}:J{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                        // Status Color Styling
                        $statusVal = $sheet->getCell("G{$row}")->getValue();
                        $statusColor = match ($statusVal) {
                            'Hadir' => ['bg' => 'DCFCE7', 'text' => '166534'], // Soft Emerald
                            'Izin' => ['bg' => 'FEF3C7', 'text' => '92400E'],  // Soft Amber
                            'Sakit' => ['bg' => 'FEE2E2', 'text' => '991B1B'], // Soft Rose
                            default => ['bg' => 'F1F5F9', 'text' => '475569'], // Soft Slate
                        };

                        $sheet->getStyle("G{$row}")->applyFromArray([
                            'font' => ['bold' => true, 'color' => ['rgb' => $statusColor['text']]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $statusColor['bg']]],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }

                    // Soft thin borders for all data
                    $sheet->getStyle("A{$headerRow}:K{$lastRow}")->applyFromArray([
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
