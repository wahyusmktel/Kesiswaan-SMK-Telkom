<?php

namespace App\Exports;

use App\Models\UkkUjian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class MonitoringPengujiUkkExport implements WithEvents, WithTitle
{
    use Exportable;

    public function __construct(
        private readonly UkkUjian $ujian,
        private readonly Collection $pengujiList,
        private readonly string $rombelLabel = 'Semua Kelas',
        private readonly array $kpis = []
    ) {}

    public function title(): string
    {
        return 'Monitoring Penguji UKK';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ══ 1. DOCUMENT HEADER ══
                $sheet->setCellValue('A1', 'LAPORAN MONITORING PROGRES PENILAIAN PENGUJI UKK');
                $sheet->setCellValue('A2', 'SMK TELKOM LAMPUNG');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));

                // Metadata block
                $sheet->setCellValue('A4', 'Nama Ujian');
                $sheet->setCellValue('C4', ': ' . $this->ujian->nama_ujian);
                $sheet->setCellValue('A5', 'Judul Project');
                $sheet->setCellValue('C5', ': ' . ($this->ujian->nama_project ?: '-'));
                $sheet->setCellValue('A6', 'Kompetensi Keahlian');
                $sheet->setCellValue('C6', ': ' . $this->ujian->jurusan);

                $sheet->setCellValue('E4', 'Filter Kelas');
                $sheet->setCellValue('F4', ': ' . $this->rombelLabel);
                $sheet->setCellValue('E5', 'Tahun Pelajaran');
                $sheet->setCellValue('F5', ': ' . ($this->ujian->tahunPelajaran?->tahun ?? '-'));
                $sheet->setCellValue('E6', 'Tipe Ujian');
                $sheet->setCellValue('F6', ': ' . ($this->ujian->is_bilingual ? 'Bilingual (English Assessment)' : 'Reguler'));

                $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('E4:E6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('C4:C6')->getFont()->setSize(9.5);
                $sheet->getStyle('F4:F6')->getFont()->setSize(9.5);

                // ══ 2. KPI SUMMARY BANNER (ROW 8) ══
                $totalPenguji  = $this->kpis['total_penguji'] ?? $this->pengujiList->count();
                $selesaiCount  = $this->kpis['selesai_count'] ?? $this->pengujiList->where('status', 'Selesai')->count();
                $prosesCount   = $this->kpis['proses_count'] ?? $this->pengujiList->where('status', 'Sedang Menilai')->count();
                $belumCount    = $this->kpis['belum_count'] ?? $this->pengujiList->where('status', 'Belum Mulai')->count();
                $avgProgress   = $this->kpis['avg_progress'] ?? ($totalPenguji > 0 ? round($this->pengujiList->avg('persentase'), 1) : 0);

                $sheet->setCellValue('A8', 'TOTAL PENGUJI: ' . $totalPenguji);
                $sheet->setCellValue('C8', 'SELESAI (100%): ' . $selesaiCount);
                $sheet->setCellValue('F8', 'SEDANG MENILAI: ' . $prosesCount);
                $sheet->setCellValue('I8', 'BELUM MULAI: ' . $belumCount);
                $sheet->setCellValue('K8', 'RATA-RATA PROGRES: ' . $avgProgress . '%');

                $sheet->mergeCells('A8:B8');
                $sheet->mergeCells('C8:E8');
                $sheet->mergeCells('F8:H8');
                $sheet->mergeCells('I8:J8');
                $sheet->mergeCells('K8:L8');

                $sheet->getStyle('A8:L8')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '1E293B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(24);

                // ══ 3. TABLE HEADERS (ROW 10) ══
                $headers = [
                    'A' => ['title' => 'NO', 'width' => 6],
                    'B' => ['title' => 'NAMA PENGUJI', 'width' => 32],
                    'C' => ['title' => 'PERAN PENUGASAN', 'width' => 24],
                    'D' => ['title' => 'KELAS / ROMBEL', 'width' => 16],
                    'E' => ['title' => 'TARGET PESERTA DIDIK', 'width' => 26],
                    'F' => ['title' => 'JADWAL & RUANGAN', 'width' => 30],
                    'G' => ['title' => "TARGET\nSISWA", 'width' => 12],
                    'H' => ['title' => "SUDAH\nLENGKAP", 'width' => 12],
                    'I' => ['title' => "BELUM\nSELESAI", 'width' => 12],
                    'J' => ['title' => "PROGRES\n(%)", 'width' => 12],
                    'K' => ['title' => 'STATUS', 'width' => 18],
                    'L' => ['title' => 'TERAKHIR AKTIF', 'width' => 24],
                ];

                foreach ($headers as $col => $info) {
                    $sheet->setCellValue($col . '10', $info['title']);
                    $sheet->getColumnDimension($col)->setWidth($info['width']);
                }

                $sheet->getRowDimension(10)->setRowHeight(32);
                $sheet->getStyle('A10:L10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']], // Slate 900
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
                ]);

                // ══ 4. DATA ROWS (ROW 11 ONWARDS) ══
                $r = 11;
                $dataStartRow = $r;

                foreach ($this->pengujiList as $idx => $p) {
                    $sheet->getRowDimension($r)->setRowHeight(24);
                    $isOdd = ($idx % 2 === 1);
                    $bgHex = $isOdd ? 'F8FAFC' : 'FFFFFF';

                    $sheet->setCellValue('A' . $r, $idx + 1);
                    $sheet->setCellValue('B' . $r, $p['nama_penguji']);
                    $sheet->setCellValue('C' . $r, $p['peran_label']);
                    $sheet->setCellValue('D' . $r, $p['rombel_nama']);
                    $sheet->setCellValue('E' . $r, $p['target_label']);
                    $sheet->setCellValue('F' . $r, $p['jadwal_label']);
                    $sheet->setCellValue('G' . $r, $p['target_count']);
                    $sheet->setCellValue('H' . $r, $p['selesai_count']);
                    $sheet->setCellValue('I' . $r, $p['belum_count']);
                    $sheet->setCellValue('J' . $r, $p['persentase'] . '%');
                    $sheet->setCellValue('K' . $r, $p['status']);
                    $sheet->setCellValue('L' . $r, $p['terakhir_aktif']);

                    $sheet->getStyle("A{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
                    $sheet->getStyle("C{$r}:E{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("F{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
                    $sheet->getStyle("G{$r}:J{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("K{$r}:L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    $sheet->getStyle("G{$r}:J{$r}")->getFont()->setBold(true);
                    $sheet->getStyle("K{$r}")->getFont()->setBold(true);

                    // Color code status badge
                    if ($p['status'] === 'Selesai') {
                        $sheet->getStyle('K' . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => '047857']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
                        ]);
                    } elseif ($p['status'] === 'Sedang Menilai') {
                        $sheet->getStyle('K' . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'B45309']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']],
                        ]);
                    } else {
                        $sheet->getStyle('K' . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'B91C1C']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                        ]);
                    }

                    $sheet->getStyle("A{$r}:L{$r}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHex]],
                    ]);

                    $r++;
                }

                $lastDataRow = $r - 1;

                // ══ 5. SUMMARY ROW ══
                if ($this->pengujiList->isNotEmpty()) {
                    $sheet->getRowDimension($r)->setRowHeight(24);
                    $sheet->mergeCells("A{$r}:F{$r}")->setCellValue("A{$r}", 'TOTAL KESELURUHAN');
                    $sheet->getStyle("A{$r}:F{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '0F172A']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $sheet->setCellValue("G{$r}", "=SUM(G{$dataStartRow}:G{$lastDataRow})");
                    $sheet->setCellValue("H{$r}", "=SUM(H{$dataStartRow}:H{$lastDataRow})");
                    $sheet->setCellValue("I{$r}", "=SUM(I{$dataStartRow}:I{$lastDataRow})");
                    $sheet->setCellValue("J{$r}", "=AVERAGE(J{$dataStartRow}:J{$lastDataRow})");
                    $sheet->getStyle("J{$r}")->getNumberFormat()->setFormatCode('0.0"%"');

                    $sheet->mergeCells("K{$r}:L{$r}")->setCellValue("K{$r}", $selesaiCount . ' dari ' . $totalPenguji . ' Penguji Tuntas');

                    $sheet->getStyle("G{$r}:L{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("A{$r}:L{$r}")->getFont()->setBold(true);

                    $sheet->getStyle("A{$r}:L{$r}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E2E8F0']],
                    ]);

                    $r++;
                }

                // ══ 6. SIGNATURES ══
                $r += 2;
                $todayStr = now()->translatedFormat('d F Y');
                $sheet->setCellValue("C{$r}", "Mengetahui,");
                $sheet->setCellValue("J{$r}", "Lampung, " . $todayStr);

                $sheet->setCellValue("C" . ($r + 1), "Kepala Program Keahlian,");
                $sheet->setCellValue("J" . ($r + 1), "Tim Monitoring UKK / Kurikulum,");

                $sheet->setCellValue("C" . ($r + 5), "( ........................................................... )");
                $sheet->setCellValue("J" . ($r + 5), "( ........................................................... )");

                $sheet->setCellValue("C" . ($r + 6), "NIP. ");
                $sheet->setCellValue("J" . ($r + 6), "NIP. ");

                $sheet->getStyle("C{$r}:C" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("J{$r}:J" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("C{$r}:C" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("J{$r}:J" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('E11');
            },
        ];
    }
}
