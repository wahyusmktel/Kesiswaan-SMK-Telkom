<?php

namespace App\Exports\Sheets;

use App\Models\UkkInstrumen;
use App\Models\UkkUjian;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class UkkDetailInstrumenSheet implements WithEvents, WithTitle
{
    public function __construct(
        private UkkUjian $ujian,
        private UkkInstrumen $instrumen,
        private Collection $siswas,
        private Collection $allNilaiP,
        private Collection $allNilaiK,
        private string $rombelLabel,
        private string $sheetTitle = 'Detail Penilaian & Rekap'
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

                $ins = $this->instrumen;
                $bp  = (float) ($ins->bobot_pengetahuan ?? $this->ujian->bobot_pengetahuan ?? 30);
                $bk  = (float) ($ins->bobot_keterampilan ?? $this->ujian->bobot_keterampilan ?? 70);

                // ══ 1. DOCUMENT HEADER & METADATA ══
                $sheet->setCellValue('A1', 'LEGER & REKAPITULASI PENILAIAN UJI KOMPETENSI KEAHLIAN (UKK)');
                $sheet->setCellValue('A2', 'SMK TELKOM MAKASSAR');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('1E293B'));
                $sheet->getStyle('A2')->getFont()->setBold(true)->setSize(11)->setColor(new \PhpOffice\PhpSpreadsheet\Style\Color('475569'));

                $sheet->setCellValue('A4', 'Nama Ujian');
                $sheet->setCellValue('C4', ': ' . $this->ujian->nama_ujian);
                $sheet->setCellValue('A5', 'Judul Project');
                $sheet->setCellValue('C5', ': ' . ($this->ujian->nama_project ?: $ins->nama_instrumen));
                $sheet->setCellValue('A6', 'Kompetensi Keahlian');
                $sheet->setCellValue('C6', ': ' . $this->ujian->jurusan);

                $sheet->setCellValue('E4', 'Kelas / Rombel');
                $sheet->setCellValue('G4', ': ' . $this->rombelLabel);
                $sheet->setCellValue('E5', 'Tahun Pelajaran');
                $sheet->setCellValue('G5', ': ' . ($this->ujian->tahunPelajaran?->tahun ?? '-'));
                $sheet->setCellValue('E6', 'Bobot Penilaian');
                $sheet->setCellValue('G6', ": Pengetahuan ({$bp}%) · Keterampilan ({$bk}%)");

                $sheet->getStyle('A4:A6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('E4:E6')->getFont()->setBold(true)->setSize(9.5);
                $sheet->getStyle('C4:C6')->getFont()->setSize(9.5);
                $sheet->getStyle('G4:G6')->getFont()->setSize(9.5);

                // ══ 2. COLUMN MAPPING ══
                $curCol = 1;
                $colMap = [
                    'no'    => $curCol++,
                    'nis'   => $curCol++,
                    'nama'  => $curCol++,
                    'kelas' => $curCol++,
                ];

                // Pengetahuan items
                $soalList = $ins->soalPengetahuan;
                $pStartCol = $curCol;
                $pSoalCols = [];
                foreach ($soalList as $idx => $soal) {
                    $pSoalCols[$soal->id] = [
                        'col' => $curCol++,
                        'no'  => $idx + 1,
                        'pertanyaan' => $soal->pertanyaan,
                    ];
                }
                $pEndSoalCol = $curCol - 1;

                // Pengetahuan Recap
                $colMap['p_benar'] = $curCol++;
                $colMap['p_skor']  = $curCol++;
                $colMap['p_nilai'] = $curCol++;
                $pEndCol = $curCol - 1;

                // Keterampilan items
                $kStartCol = $curCol;
                $kKatCols = [];
                foreach ($ins->kategoriKeterampilan as $kat) {
                    $katData = [
                        'kat' => $kat,
                        'start_col' => $curCol,
                        'ind_cols' => [],
                    ];
                    foreach ($kat->indikator as $i => $ind) {
                        $katData['ind_cols'][$ind->id] = [
                            'col' => $curCol++,
                            'no'  => $i + 1,
                            'indikator' => $ind->indikator_penilaian,
                        ];
                    }
                    $katData['subtotal_col'] = $curCol++;
                    $katData['end_col'] = $curCol - 1;
                    $kKatCols[$kat->id] = $katData;
                }
                $colMap['k_skor']  = $curCol++;
                $colMap['k_nilai'] = $curCol++;
                $kEndCol = $curCol - 1;

                // Rekapitulasi Akhir
                $rStartCol = $curCol;
                $colMap['nilai_akhir'] = $curCol++;
                $colMap['predikat']    = $curCol++;
                $colMap['status']      = $curCol++;
                $lastCol = $curCol - 1;

                $lastColStr = Coordinate::stringFromColumnIndex($lastCol);

                // ══ 3. COLUMN WIDTHS & ROW HEIGHTS ══
                $sheet->getColumnDimension('A')->setWidth(5.5); // No
                $sheet->getColumnDimension('B')->setWidth(13);  // NIS
                $sheet->getColumnDimension('C')->setWidth(32);  // Nama
                $sheet->getColumnDimension('D')->setWidth(13);  // Kelas

                foreach ($pSoalCols as $pInfo) {
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($pInfo['col']))->setWidth(5.5);
                }
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['p_benar']))->setWidth(8.5);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['p_skor']))->setWidth(9);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['p_nilai']))->setWidth(9);

                foreach ($kKatCols as $katData) {
                    foreach ($katData['ind_cols'] as $indInfo) {
                        $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($indInfo['col']))->setWidth(5.5);
                    }
                    $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($katData['subtotal_col']))->setWidth(9.5);
                }
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['k_skor']))->setWidth(9);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['k_nilai']))->setWidth(9);

                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['nilai_akhir']))->setWidth(12);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['predikat']))->setWidth(16);
                $sheet->getColumnDimension(Coordinate::stringFromColumnIndex($colMap['status']))->setWidth(18);

                $sheet->getRowDimension(8)->setRowHeight(25);
                $sheet->getRowDimension(9)->setRowHeight(24);
                $sheet->getRowDimension(10)->setRowHeight(145);

                // ══ 4. LEVEL 1 HEADERS (ROW 8) ══
                $sheet->mergeCells('A8:A10')->setCellValue('A8', 'NO');
                $sheet->mergeCells('B8:B10')->setCellValue('B8', 'NIS');
                $sheet->mergeCells('C8:C10')->setCellValue('C8', 'NAMA PESERTA DIDIK');
                $sheet->mergeCells('D8:D10')->setCellValue('D8', 'KELAS');

                $sheet->getStyle('A8:D10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9.5, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '334155']]],
                ]);

                $pStartStr = Coordinate::stringFromColumnIndex($pStartCol);
                $pEndStr   = Coordinate::stringFromColumnIndex($pEndCol);
                if ($pStartCol <= $pEndCol) {
                    $sheet->mergeCells("{$pStartStr}8:{$pEndStr}8")->setCellValue($pStartStr . '8', "PENILAIAN PENGETAHUAN (BOBOT: {$bp}%)");
                    $sheet->getStyle("{$pStartStr}8:{$pEndStr}8")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E40AF']], // Blue 800
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '1D4ED8']]],
                    ]);
                }

                $kStartStr = Coordinate::stringFromColumnIndex($kStartCol);
                $kEndStr   = Coordinate::stringFromColumnIndex($kEndCol);
                if ($kStartCol <= $kEndCol) {
                    $sheet->mergeCells("{$kStartStr}8:{$kEndStr}8")->setCellValue($kStartStr . '8', "PENILAIAN KETERAMPILAN (BOBOT: {$bk}%)");
                    $sheet->getStyle("{$kStartStr}8:{$kEndStr}8")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '065F46']], // Emerald 800
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '047857']]],
                    ]);
                }

                $rStartStr = Coordinate::stringFromColumnIndex($rStartCol);
                $rEndStr   = Coordinate::stringFromColumnIndex($lastCol);
                $sheet->mergeCells("{$rStartStr}8:{$rEndStr}8")->setCellValue($rStartStr . '8', "REKAPITULASI HASIL AKHIR");
                $sheet->getStyle("{$rStartStr}8:{$rEndStr}8")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9A3412']], // Orange 800
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C2410C']]],
                ]);

                // ══ 5. LEVEL 2 HEADERS (ROW 9) ══
                if (!empty($pSoalCols)) {
                    $pEndSoalStr = Coordinate::stringFromColumnIndex($pEndSoalCol);
                    $sheet->mergeCells("{$pStartStr}9:{$pEndSoalStr}9")->setCellValue($pStartStr . '9', 'BUTIR SOAL / WAWANCARA');
                    $sheet->getStyle("{$pStartStr}9:{$pEndSoalStr}9")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E3A8A']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
                    ]);
                }

                $pRecapStartStr = Coordinate::stringFromColumnIndex($colMap['p_benar']);
                $sheet->mergeCells("{$pRecapStartStr}9:{$pEndStr}9")->setCellValue($pRecapStartStr . '9', 'REKAP PENGETAHUAN');
                $sheet->getStyle("{$pRecapStartStr}9:{$pEndStr}9")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '1E3A8A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'BFDBFE']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '93C5FD']]],
                ]);

                $katColors = [
                    ['bg' => 'FEF3C7', 'text' => '92400E', 'border' => 'FDE68A'], // Amber
                    ['bg' => 'D1FAE5', 'text' => '065F46', 'border' => 'A7F3D0'], // Emerald
                    ['bg' => 'EDE9FE', 'text' => '5B21B6', 'border' => 'DDD6FE'], // Purple
                    ['bg' => 'E0F2FE', 'text' => '0369A1', 'border' => 'BAE6FD'], // Sky
                ];
                $colorIdx = 0;
                foreach ($kKatCols as $katData) {
                    $c = $katColors[$colorIdx % count($katColors)];
                    $colorIdx++;
                    $ksStr = Coordinate::stringFromColumnIndex($katData['start_col']);
                    $keStr = Coordinate::stringFromColumnIndex($katData['end_col']);
                    $sheet->mergeCells("{$ksStr}9:{$keStr}9")->setCellValue($ksStr . '9', "{$katData['kat']->nama_kategori} ({$katData['kat']->bobot}%)");
                    $sheet->getStyle("{$ksStr}9:{$keStr}9")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => $c['text']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $c['bg']]],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $c['border']]]],
                    ]);
                }

                $kRecapStartStr = Coordinate::stringFromColumnIndex($colMap['k_skor']);
                $sheet->mergeCells("{$kRecapStartStr}9:{$kEndStr}9")->setCellValue($kRecapStartStr . '9', 'REKAP KETERAMPILAN');
                $sheet->getStyle("{$kRecapStartStr}9:{$kEndStr}9")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '065F46']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'A7F3D0']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '6EE7B7']]],
                ]);

                $sheet->mergeCells("{$rStartStr}9:{$rEndStr}9")->setCellValue($rStartStr . '9', 'STATUS KELULUSAN');
                $sheet->getStyle("{$rStartStr}9:{$rEndStr}9")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '9A3412']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FED7AA']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FDBA74']]],
                ]);

                // ══ 6. LEVEL 3 HEADERS (ROW 10 - ROTATED ITEM HEADERS) ══
                foreach ($pSoalCols as $pInfo) {
                    $colLetter = Coordinate::stringFromColumnIndex($pInfo['col']);
                    $sheet->setCellValue($colLetter . '10', "P{$pInfo['no']}: " . $pInfo['pertanyaan']);
                    $sheet->getStyle($colLetter . '10')->applyFromArray([
                        'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '1E3A8A']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EFF6FF']],
                        'alignment' => ['textRotation' => 90, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM, 'wrapText' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DBEAFE']]],
                    ]);
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['p_benar']) . '10', "Jml Benar\nP");
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['p_skor']) . '10', "Skor P\n(100)");
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['p_nilai']) . '10', "Nilai P\n({$bp}%)");
                $sheet->getStyle(Coordinate::stringFromColumnIndex($colMap['p_benar']) . '10:' . Coordinate::stringFromColumnIndex($colMap['p_nilai']) . '10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '1E3A8A']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DBEAFE']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BFDBFE']]],
                ]);

                foreach ($kKatCols as $katData) {
                    foreach ($katData['ind_cols'] as $indInfo) {
                        $colLetter = Coordinate::stringFromColumnIndex($indInfo['col']);
                        $sheet->setCellValue($colLetter . '10', "K{$indInfo['no']}: " . $indInfo['indikator']);
                        $sheet->getStyle($colLetter . '10')->applyFromArray([
                            'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '065F46']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']],
                            'alignment' => ['textRotation' => 90, 'horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_BOTTOM, 'wrapText' => true],
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'DCFCE7']]],
                        ]);
                    }
                    $subColLetter = Coordinate::stringFromColumnIndex($katData['subtotal_col']);
                    $sheet->setCellValue($subColLetter . '10', "Skor " . mb_substr($katData['kat']->nama_kategori, 0, 12) . "\n(" . $katData['kat']->bobot . "%)");
                    $sheet->getStyle($subColLetter . '10')->applyFromArray([
                        'font' => ['bold' => true, 'size' => 8, 'color' => ['rgb' => '065F46']],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'DCFCE7']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'BBF7D0']]],
                    ]);
                }

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['k_skor']) . '10', "Skor K\n(100)");
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['k_nilai']) . '10', "Nilai K\n({$bk}%)");
                $sheet->getStyle(Coordinate::stringFromColumnIndex($colMap['k_skor']) . '10:' . Coordinate::stringFromColumnIndex($colMap['k_nilai']) . '10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '065F46']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A7F3D0']]],
                ]);

                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['nilai_akhir']) . '10', "NILAI AKHIR\nUKK (P+K)");
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['predikat']) . '10', "PREDIKAT");
                $sheet->setCellValue(Coordinate::stringFromColumnIndex($colMap['status']) . '10', "STATUS\nKELULUSAN");
                $sheet->getStyle(Coordinate::stringFromColumnIndex($colMap['nilai_akhir']) . '10:' . Coordinate::stringFromColumnIndex($colMap['status']) . '10')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 8.5, 'color' => ['rgb' => '9A3412']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFEDD5']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FED7AA']]],
                ]);

                // ══ 7. POPULATE STUDENT DATA ROWS ══
                $dataStartRow = 11;
                $r = $dataStartRow;
                $totalSoalCount = count($soalList);

                foreach ($this->siswas as $idx => $s) {
                    $sheet->getRowDimension($r)->setRowHeight(21);
                    $isOdd = ($idx % 2 === 1);
                    $bgHex = $isOdd ? 'F8FAFC' : 'FFFFFF';

                    $sheet->setCellValue('A' . $r, $idx + 1);
                    $sheet->setCellValue('B' . $r, $s->nis ?: '-');
                    $sheet->setCellValue('C' . $r, $s->nama_lengkap);
                    $sheet->setCellValue('D' . $r, $s->rombel_label ?? $this->rombelLabel);

                    $sheet->getStyle("A{$r}:B{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT)->setIndent(1);
                    $sheet->getStyle("D{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                    // Pengetahuan Items
                    $pScores = $this->allNilaiP->get($s->id, collect())->pluck('nilai', 'soal_id');
                    $pBenar = 0;
                    $pGraded = 0;

                    foreach ($pSoalCols as $soalId => $pInfo) {
                        $colLetter = Coordinate::stringFromColumnIndex($pInfo['col']);
                        if ($pScores->has($soalId)) {
                            $val = (int) $pScores[$soalId];
                            $sheet->setCellValue($colLetter . $r, $val);
                            if ($val === 1) $pBenar++;
                            $pGraded++;
                        } else {
                            $sheet->setCellValue($colLetter . $r, '-');
                        }
                        $sheet->getStyle($colLetter . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    }

                    $hasP = ($pGraded > 0);
                    $skorP = ($hasP && $totalSoalCount > 0) ? round(($pBenar / $totalSoalCount) * 100, 1) : null;
                    $nilaiP = $skorP !== null ? round($skorP * ($bp / 100), 1) : null;

                    $colPBenar = Coordinate::stringFromColumnIndex($colMap['p_benar']);
                    $colPSkor  = Coordinate::stringFromColumnIndex($colMap['p_skor']);
                    $colPNilai = Coordinate::stringFromColumnIndex($colMap['p_nilai']);

                    $sheet->setCellValue($colPBenar . $r, $hasP ? $pBenar : '-');
                    $sheet->setCellValue($colPSkor . $r, $skorP !== null ? number_format($skorP, 1) : '-');
                    $sheet->setCellValue($colPNilai . $r, $nilaiP !== null ? number_format($nilaiP, 1) : '-');

                    $sheet->getStyle("{$colPBenar}{$r}:{$colPNilai}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$colPBenar}{$r}:{$colPNilai}{$r}")->getFont()->setBold(true);

                    // Keterampilan Items
                    $kScores = $this->allNilaiK->get($s->id, collect())->pluck('nilai', 'indikator_id');
                    $totalKWeighted = 0;
                    $hasAnyKGraded = false;

                    foreach ($kKatCols as $katId => $katData) {
                        $kat = $katData['kat'];
                        $katCount = count($katData['ind_cols']);
                        $katSum = 0;
                        $katGraded = 0;

                        foreach ($katData['ind_cols'] as $indId => $indInfo) {
                            $colLetter = Coordinate::stringFromColumnIndex($indInfo['col']);
                            if ($kScores->has($indId)) {
                                $kval = (int) $kScores[$indId];
                                $sheet->setCellValue($colLetter . $r, $kval);
                                $katSum += $kval;
                                $katGraded++;
                                $hasAnyKGraded = true;
                            } else {
                                $sheet->setCellValue($colLetter . $r, '-');
                            }
                            $sheet->getStyle($colLetter . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        }

                        $subColLetter = Coordinate::stringFromColumnIndex($katData['subtotal_col']);
                        if ($katGraded > 0 && $katCount > 0) {
                            $avg = $katSum / $katCount;
                            $catScore = round(($avg / 3) * 100 * ($kat->bobot / 100), 1);
                            $totalKWeighted += $catScore;
                            $sheet->setCellValue($subColLetter . $r, number_format($catScore, 1));
                        } else {
                            $sheet->setCellValue($subColLetter . $r, '-');
                        }
                        $sheet->getStyle($subColLetter . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                        $sheet->getStyle($subColLetter . $r)->getFont()->setBold(true);
                    }

                    $skorK = $hasAnyKGraded ? round($totalKWeighted, 1) : null;
                    $nilaiK = $skorK !== null ? round($skorK * ($bk / 100), 1) : null;

                    $colKSkor  = Coordinate::stringFromColumnIndex($colMap['k_skor']);
                    $colKNilai = Coordinate::stringFromColumnIndex($colMap['k_nilai']);

                    $sheet->setCellValue($colKSkor . $r, $skorK !== null ? number_format($skorK, 1) : '-');
                    $sheet->setCellValue($colKNilai . $r, $nilaiK !== null ? number_format($nilaiK, 1) : '-');
                    $sheet->getStyle("{$colKSkor}{$r}:{$colKNilai}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$colKSkor}{$r}:{$colKNilai}{$r}")->getFont()->setBold(true);

                    // Rekapitulasi Akhir
                    $colAkhir    = Coordinate::stringFromColumnIndex($colMap['nilai_akhir']);
                    $colPredikat = Coordinate::stringFromColumnIndex($colMap['predikat']);
                    $colStatus   = Coordinate::stringFromColumnIndex($colMap['status']);

                    $nilaiAkhir = null;
                    if ($nilaiP !== null && $nilaiK !== null) {
                        $nilaiAkhir = round($nilaiP + $nilaiK, 1);
                    } elseif ($nilaiP !== null) {
                        $nilaiAkhir = round($skorP, 1);
                    } elseif ($nilaiK !== null) {
                        $nilaiAkhir = round($skorK, 1);
                    }

                    $predikat = 'Belum Selesai';
                    $statusText = 'BELUM LENGKAP';

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

                        $statusText = $nilaiAkhir >= 75 ? 'KOMPETEN' : 'BELUM KOMPETEN';
                    }

                    $sheet->setCellValue($colAkhir . $r, $nilaiAkhir !== null ? number_format($nilaiAkhir, 1) : '-');
                    $sheet->setCellValue($colPredikat . $r, $predikat);
                    $sheet->setCellValue($colStatus . $r, $statusText);

                    $sheet->getStyle("{$colAkhir}{$r}:{$colStatus}{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$colAkhir}{$r}:{$colStatus}{$r}")->getFont()->setBold(true);

                    if ($statusText === 'KOMPETEN') {
                        $sheet->getStyle($colStatus . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => '047857']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
                        ]);
                    } elseif ($statusText === 'BELUM KOMPETEN') {
                        $sheet->getStyle($colStatus . $r)->applyFromArray([
                            'font' => ['color' => ['rgb' => 'B91C1C']],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FEF2F2']],
                        ]);
                    }

                    $sheet->getStyle("A{$r}:{$lastColStr}{$r}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgHex]],
                    ]);

                    $r++;
                }

                $lastDataRow = $r - 1;

                // ══ 8. SUMMARY STATISTICS ROWS ══
                if ($this->siswas->isNotEmpty()) {
                    $statRows = [
                        ['label' => 'RATA-RATA KELAS', 'func' => 'AVERAGE'],
                        ['label' => 'NILAI TERTINGGI', 'func' => 'MAX'],
                        ['label' => 'NILAI TERENDAH', 'func' => 'MIN'],
                    ];

                    $numericCols = [
                        $colMap['p_benar'], $colMap['p_skor'], $colMap['p_nilai'],
                        $colMap['k_skor'], $colMap['k_nilai'], $colMap['nilai_akhir']
                    ];
                    foreach ($kKatCols as $katData) {
                        $numericCols[] = $katData['subtotal_col'];
                    }

                    foreach ($statRows as $st) {
                        $sheet->getRowDimension($r)->setRowHeight(22);
                        $sheet->mergeCells("A{$r}:D{$r}")->setCellValue("A{$r}", $st['label']);
                        $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                            'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '334155']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                        ]);

                        foreach ($numericCols as $cIdx) {
                            $cLetter = Coordinate::stringFromColumnIndex($cIdx);
                            $sheet->setCellValue($cLetter . $r, "={$st['func']}({$cLetter}{$dataStartRow}:{$cLetter}{$lastDataRow})");
                            $sheet->getStyle($cLetter . $r)->getNumberFormat()->setFormatCode('0.0');
                            $sheet->getStyle($cLetter . $r)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                            $sheet->getStyle($cLetter . $r)->getFont()->setBold(true);
                        }

                        $sheet->getStyle("A{$r}:{$lastColStr}{$r}")->applyFromArray([
                            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                        ]);

                        $r++;
                    }

                    // Kelulusan Row
                    $sheet->getRowDimension($r)->setRowHeight(22);
                    $sheet->mergeCells("A{$r}:D{$r}")->setCellValue("A{$r}", "JUMLAH KOMPETEN & KELULUSAN");
                    $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '047857']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);

                    $statusColLetter = Coordinate::stringFromColumnIndex($colMap['status']);
                    $sheet->setCellValue("{$statusColLetter}{$r}", "=COUNTIF({$statusColLetter}{$dataStartRow}:{$statusColLetter}{$lastDataRow}, \"KOMPETEN\") & \" Siswa (\" & ROUND((COUNTIF({$statusColLetter}{$dataStartRow}:{$statusColLetter}{$lastDataRow}, \"KOMPETEN\")/{$this->siswas->count()})*100, 1) & \"%)\"");
                    $sheet->getStyle("{$statusColLetter}{$r}")->applyFromArray([
                        'font' => ['bold' => true, 'size' => 9, 'color' => ['rgb' => '047857']],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                    ]);
                    $sheet->getStyle("A{$r}:{$lastColStr}{$r}")->applyFromArray([
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'A7F3D0']]],
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'ECFDF5']],
                    ]);
                    $r++;
                }

                // ══ 9. SIGNATURES BLOCK ══
                $r += 2;
                $sigColLeft = 'C';
                $sigColRight = Coordinate::stringFromColumnIndex(max(1, $lastCol - 2));

                $todayStr = now()->translatedFormat('d F Y');
                $sheet->setCellValue("{$sigColLeft}{$r}", "Mengetahui,");
                $sheet->setCellValue("{$sigColRight}{$r}", "Makassar, " . $todayStr);

                $sheet->setCellValue("{$sigColLeft}" . ($r + 1), "Kepala Program Keahlian,");
                $sheet->setCellValue("{$sigColRight}" . ($r + 1), "Guru Penguji UKK,");

                $sheet->setCellValue("{$sigColLeft}" . ($r + 5), "( ........................................................... )");
                $sheet->setCellValue("{$sigColRight}" . ($r + 5), "( ........................................................... )");

                $sheet->setCellValue("{$sigColLeft}" . ($r + 6), "NIP. ");
                $sheet->setCellValue("{$sigColRight}" . ($r + 6), "NIP. ");

                $sheet->getStyle("{$sigColLeft}{$r}:{$sigColLeft}" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("{$sigColRight}{$r}:{$sigColRight}" . ($r + 6))->getFont()->setSize(9.5);
                $sheet->getStyle("{$sigColLeft}{$r}:{$sigColLeft}" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle("{$sigColRight}{$r}:{$sigColRight}" . ($r + 6))->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Freeze Panes at E11 so student identities (Cols A-D) remain visible while scrolling
                $sheet->freezePane('E11');
            },
        ];
    }
}
