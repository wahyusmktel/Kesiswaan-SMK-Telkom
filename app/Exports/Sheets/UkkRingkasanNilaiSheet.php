<?php

namespace App\Exports\Sheets;

use App\Models\UkkUjian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UkkRingkasanNilaiSheet implements WithEvents, WithTitle
{
    public function __construct(
        private UkkUjian $ujian,
        private Collection $rekap,
        private string $rombelLabel,
        private string $sheetTitle = 'Ringkasan Nilai Akhir',
        private ?Collection $allNilaiBilingual = null
    ) {}

    public function title(): string
    {
        return mb_substr($this->sheetTitle, 0, 31);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $ins = $this->ujian->instrumens->first();
                $bp  = (float) ($ins?->bobot_pengetahuan ?? $this->ujian->bobot_pengetahuan ?? 30);
                $bk  = (float) ($ins?->bobot_keterampilan ?? $this->ujian->bobot_keterampilan ?? 70);

                // ══ 1. DOCUMENT HEADER ══
                $sheet->setCellValue('A1', 'RINGKASAN NILAI AKHIR UJI KOMPETENSI KEAHLIAN (UKK)');
                $sheet->setCellValue('A2', 'SMK TELKOM LAMPUNG');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));

                // Metadata block
                $sheet->setCellValue('A4', 'Nama Ujian');
                $sheet->setCellValue('C4', ': ' . $this->ujian->nama_ujian);
                $sheet->setCellValue('A5', 'Judul Project');
                $sheet->setCellValue('C5', ': ' . ($this->ujian->nama_project ?: ($ins?->nama_instrumen ?? '-')));
                $sheet->setCellValue('A6', 'Kompetensi Keahlian');
                $sheet->setCellValue('C6', ': ' . $this->ujian->jurusan);

                $sheet->setCellValue('E4', 'Kelas / Rombel');
                $sheet->setCellValue('F4', ': ' . $this->rombelLabel);
                $sheet->setCellValue('E5', 'Tahun Pelajaran');
                $sheet->setCellValue('F5', ': ' . ($this->ujian->tahunPelajaran?->tahun ?? '-'));
                $sheet->setCellValue('E6', 'Bobot Penilaian');
                $sheet->setCellValue('F6', ": Pengetahuan ({$bp}%) · Keterampilan ({$bk}%)");

                $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('E4:E6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('C4:C6')->getFont()->setSize(9.5);
                $sheet->getStyle('F4:F6')->getFont()->setSize(9.5);

                $isBilingual = (bool) $this->ujian->is_bilingual;
                $lastColLetter = $isBilingual ? 'J' : 'I';

                // ══ 2. KPI SUMMARY BANNER (ROW 8) ══
                $totalSiswa   = $this->rekap->count();
                $selesai      = $this->rekap->filter(fn ($r) => $r['is_complete'])->count();
                $belumSelesai = $totalSiswa - $selesai;
                $rataRata     = $this->rekap->filter(fn ($r) => $r['nilai_akhir'] !== null)->avg('nilai_akhir');
                $kompeten     = $this->rekap->filter(fn ($r) => $r['nilai_akhir'] !== null && $r['nilai_akhir'] >= 75)->count();
                $persenLulus  = $totalSiswa > 0 ? round(($kompeten / $totalSiswa) * 100, 1) : 0;

                $sheet->setCellValue('A8', 'TOTAL SISWA: ' . $totalSiswa);
                $sheet->setCellValue('C8', 'SUDAH DINILAI: ' . $selesai);
                $sheet->setCellValue('E8', 'BELUM SELESAI: ' . $belumSelesai);
                $sheet->setCellValue('G8', 'RATA-RATA: ' . ($rataRata !== null ? number_format($rataRata, 1) : '-'));
                $sheet->setCellValue('H8', 'KELULUSAN: ' . $persenLulus . '%');

                $sheet->mergeCells('A8:B8');
                $sheet->mergeCells('C8:D8');
                $sheet->mergeCells('E8:F8');
                $sheet->mergeCells("H8:{$lastColLetter}8");

                $sheet->getStyle("A8:{$lastColLetter}8")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => '1E293B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);
                $sheet->getRowDimension(8)->setRowHeight(24);

                // ══ 3. TABLE HEADERS (ROW 10) ══
                $headers = [
                    'A' => ['title' => 'NO', 'width' => 6],
                    'B' => ['title' => 'NIS', 'width' => 14],
                    'C' => ['title' => 'NAMA PESERTA DIDIK', 'width' => 35],
                    'D' => ['title' => 'KELAS', 'width' => 15],
                    'E' => ['title' => "SKOR TEORI / PENGETAHUAN\n(Skala 100)", 'width' => 20],
                    'F' => ['title' => "SKOR PRAKTIK / KETERAMPILAN\n(Skala 100)", 'width' => 22],
                    'G' => ['title' => "NILAI AKHIR UKK\n({$bp}% P + {$bk}% K)", 'width' => 18],
                    'H' => ['title' => 'PREDIKAT', 'width' => 18],
                    'I' => ['title' => 'STATUS KELULUSAN', 'width' => 20],
                ];

                if ($isBilingual) {
                    $headers['J'] = ['title' => "KECAKAPAN BILINGUAL (EN)\n(Speaking Proficiency)", 'width' => 25];
                }

                foreach ($headers as $col => $info) {
                    $sheet->setCellValue($col . '10', $info['title']);
                    $sheet->getColumnDimension($col)->setWidth($info['width']);
                }

                $sheet->getRowDimension(10)->setRowHeight(32);
                $sheet->getStyle("A10:{$lastColLetter}10")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0F172A']], // Slate 900
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
                ]);

                // ══ 4. DATA ROWS (ROW 11 ONWARDS) ══
                $r = 11;
                $dataStartRow = $r;

                foreach ($this->rekap as $idx => $item) {
                    $siswa = $item['siswa'];
                    $sheet->getRowDimension($r)->setRowHeight(22);
                    $isOdd = ($idx % 2 === 1);
                    $bgHex = $isOdd ? 'F8FAFC' : 'FFFFFF';

                    $sheet->setCellValue('A' . $r, $idx + 1);
                    $sheet->setCellValue('B' . $r, $siswa->nis ?: '-');
                    $sheet->setCellValue('C' . $r, $siswa->nama_lengkap);
                    $sheet->setCellValue('D' . $r, $item['rombel_label'] ?? $this->rombelLabel);

                    $sheet->getStyle("A{$r}:B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Scores from first instrumen (or aggregated)
                    $firstScore = $item['instrumen_scores'][0] ?? null;
                    $skorP = $firstScore['skor_p'] ?? null;
                    $skorK = $firstScore['skor_k'] ?? null;
                    $nilaiAkhir = $item['nilai_akhir'];

                    $sheet->setCellValue('E' . $r, $skorP !== null ? number_format($skorP, 1) : '-');
                    $sheet->setCellValue('F' . $r, $skorK !== null ? number_format($skorK, 1) : '-');
                    $sheet->setCellValue('G' . $r, $nilaiAkhir !== null ? number_format($nilaiAkhir, 1) : '-');

                    $sheet->getStyle("E{$r}:G{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("G{$r}")->getFont()->setBold(true);

                    // Predikat & Status
                    $predikat = 'Belum Selesai';
                    $status = 'BELUM LENGKAP';

                    if ($nilaiAkhir !== null) {
                        if ($nilaiAkhir >= 90) {
                            $predikat = 'Sangat Kompeten';
                        } elseif ($nilaiAkhir >= 75) {
                            $predikat = 'Kompeten';
                        } elseif ($nilaiAkhir >= 70) {
                            $predikat = 'Cukup Kompeten';
                        } else {
                            $predikat = 'Belum Kompeten';
                        }

                        $status = $nilaiAkhir >= 75 ? 'KOMPETEN' : 'BELUM KOMPETEN';
                    }

                    $sheet->setCellValue('H' . $r, $predikat);
                    $sheet->setCellValue('I' . $r, $status);

                    $sheet->getStyle("H{$r}:I{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("H{$r}:I{$r}")->getFont()->setBold(true);

                    if ($status === 'KOMPETEN') {
                        $sheet->getStyle('I' . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => '047857']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
                        ]);
                    } elseif ($status === 'BELUM KOMPETEN') {
                        $sheet->getStyle('I' . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'B91C1C']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                        ]);
                    }

                    // Bilingual proficiency column
                    if ($isBilingual) {
                        $bil = $item['nilai_bilingual'] ?? null;
                        $bilText = $bil?->kesimpulan ?: '-';
                        $sheet->setCellValue('J' . $r, $bilText);
                        $sheet->getStyle('J' . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle('J' . $r)->getFont()->setBold(true);
                        if ($bil?->kesimpulan) {
                            $sheet->getStyle('J' . $r)->applyFromArray([
                                'font' => ['color' => ['rgb' => '4338CA']],
                                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                            ]);
                        }
                    }

                    $sheet->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHex]],
                    ]);

                    $r++;
                }

                $lastDataRow = $r - 1;

                // ══ 5. SUMMARY STATISTICS ══
                if ($this->rekap->isNotEmpty()) {
                    $statRows = [
                        ['label' => 'RATA-RATA KELAS', 'func' => 'AVERAGE'],
                        ['label' => 'NILAI TERTINGGI', 'func' => 'MAX'],
                        ['label' => 'NILAI TERENDAH', 'func' => 'MIN'],
                    ];

                    foreach ($statRows as $st) {
                        $sheet->getRowDimension($r)->setRowHeight(22);
                        $sheet->mergeCells("A{$r}:D{$r}")->setCellValue("A{$r}", $st['label']);
                        $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '334155']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        foreach (['E', 'F', 'G'] as $col) {
                            $sheet->setCellValue($col . $r, "={$st['func']}({$col}{$dataStartRow}:{$col}{$lastDataRow})");
                            $sheet->getStyle($col . $r)->getNumberFormat()->setFormatCode('0.0');
                            $sheet->getStyle($col . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle($col . $r)->getFont()->setBold(true);
                        }

                        $sheet->getStyle("A{$r}:{$lastColLetter}{$r}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                        ]);

                        $r++;
                    }
                }

                // ══ 6. SIGNATURES ══
                $r += 2;
                $todayStr = now()->translatedFormat('d F Y');
                $sigColRight = $isBilingual ? 'H' : 'G';

                $sheet->setCellValue("C{$r}", "Mengetahui,");
                $sheet->setCellValue("{$sigColRight}{$r}", "Lampung, " . $todayStr);

                $sheet->setCellValue("C" . ($r + 1), "Kepala Program Keahlian,");
                $sheet->setCellValue("{$sigColRight}" . ($r + 1), "Guru Penguji UKK,");

                $sheet->setCellValue("C" . ($r + 5), "( ........................................................... )");
                $sheet->setCellValue("{$sigColRight}" . ($r + 5), "( ........................................................... )");

                $sheet->setCellValue("C" . ($r + 6), "NIP. ");
                $sheet->setCellValue("{$sigColRight}" . ($r + 6), "NIP. ");

                $sheet->getStyle("C{$r}:C" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("{$sigColRight}{$r}:{$sigColRight}" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("C{$r}:C" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$sigColRight}{$r}:{$sigColRight}" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                $sheet->freezePane('E11');
            },
        ];
    }
}
