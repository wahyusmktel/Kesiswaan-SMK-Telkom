<?php

namespace App\Exports\Sheets;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveySummarySheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected Survey $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function title(): string
    {
        return 'Ringkasan & Analisis';
    }

    public function collection()
    {
        $rows = collect();

        // 1. Banner Title & Subtitle
        $rows->push(['LAPORAN HASIL ANALISIS SURVEI KEPUASAN', '', '', '']);
        $rows->push(['SMK TELKOM LAMPUNG', '', '', '']);
        $rows->push(['', '', '', '']);

        // 2. Survey Metadata
        $rows->push(['INFORMASI SURVEI', '', '', '']);
        $rows->push(['Judul Survei', $this->survey->title, '', '']);
        $rows->push(['Deskripsi', $this->survey->description ?: 'Tidak ada deskripsi', '', '']);
        $rows->push(['Pembuat Survei', $this->survey->creator?->name ?? 'Sistem', '', '']);
        $rows->push(['Status Survei', $this->survey->is_active ? 'Aktif' : 'Draft / Nonaktif', '', '']);

        $periode = 'Fleksibel (Tanpa batas)';
        if ($this->survey->start_at && $this->survey->end_at) {
            $periode = $this->survey->start_at->format('d/m/Y H:i') . ' s/d ' . $this->survey->end_at->format('d/m/Y H:i');
        } elseif ($this->survey->start_at) {
            $periode = 'Mulai ' . $this->survey->start_at->format('d/m/Y H:i');
        } elseif ($this->survey->end_at) {
            $periode = 'Berakhir ' . $this->survey->end_at->format('d/m/Y H:i');
        }
        $rows->push(['Periode Pelaksanaan', $periode, '', '']);
        $rows->push(['Waktu Ekspor Laporan', now()->translatedFormat('l, d F Y H:i:s'), '', '']);
        $rows->push(['', '', '', '']);

        // 3. Participants stats
        $this->survey->loadMissing([
            'creator',
            'questions.answers',
            'responses.respondent',
            'targets',
        ]);

        $targetUsers = $this->survey->targets;
        $respondentUsers = $this->survey->responses->map->respondent->filter();
        $allUsers = $targetUsers->concat($respondentUsers)->unique('id')->values();

        $totalTarget = $allUsers->count();
        $totalSubmitted = $this->survey->responses->count();
        if ($totalTarget === 0 && $totalSubmitted > 0) {
            $totalTarget = $totalSubmitted;
        }
        $totalPending = max(0, $totalTarget - $totalSubmitted);
        $completionRate = $totalTarget > 0 ? round(($totalSubmitted / $totalTarget) * 100, 1) : 0;
        $pendingRate = $totalTarget > 0 ? round(($totalPending / $totalTarget) * 100, 1) : 0;

        $rows->push(['RINGKASAN PARTISIPASI RESPONDEN', '', '', '']);
        $rows->push(['Metrik Partisipasi', 'Jumlah Responden', 'Persentase', 'Keterangan']);
        $rows->push(['Total Target Peserta', $totalTarget, '100%', 'Total target peserta yang terdaftar']);
        $rows->push(['Sudah Mengisi Survei', $totalSubmitted, $completionRate . '%', 'Responden telah menyelesaikan kuesioner']);
        $rows->push(['Belum Mengisi Survei', $totalPending, $pendingRate . '%', 'Responden belum mengisi kuesioner']);
        $rows->push(['', '', '', '']);

        // 4. Breakdown Per Question
        $rows->push(['DISTRIBUSI JAWABAN PER PERTANYAAN', '', '', '']);
        $rows->push(['', '', '', '']);

        $questions = $this->survey->questions->sortBy('order');
        $surveyResponseIds = $this->survey->responses->pluck('id')->all();

        foreach ($questions as $idx => $q) {
            $qNum = $idx + 1;
            $typeLabel = $q->type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay / Isian Singkat';
            $rows->push(["Pertanyaan #{$qNum}: {$q->question_text}", '', '', "Tipe: {$typeLabel}"]);

            $validAnswers = $q->answers->filter(function ($a) use ($surveyResponseIds) {
                return empty($surveyResponseIds) || in_array($a->response_id, $surveyResponseIds);
            });

            if ($q->type === 'multiple_choice') {
                $rows->push(['Pilihan Jawaban', 'Jumlah Suara', 'Persentase', 'Keterangan']);

                // Normalize answer values to handle JSON arrays or whitespace
                $normalizedAnswers = $validAnswers->flatMap(function ($ans) {
                    $val = $ans->answer_value;
                    if ($val === null) {
                        return [];
                    }
                    if (is_array($val)) {
                        return array_map(fn($item) => trim((string) $item), $val);
                    }
                    if (is_string($val)) {
                        $valTrimmed = trim($val);
                        if (str_starts_with($valTrimmed, '[') && str_ends_with($valTrimmed, ']')) {
                            $decoded = json_decode($valTrimmed, true);
                            if (is_array($decoded)) {
                                return array_map(fn($item) => trim((string) $item), $decoded);
                            }
                        }
                        if (str_starts_with($valTrimmed, '"') && str_ends_with($valTrimmed, '"')) {
                            $valTrimmed = trim($valTrimmed, '"');
                        }
                        return [$valTrimmed];
                    }
                    return [trim((string) $val)];
                });

                $counts = $normalizedAnswers->countBy();
                $totalAns = $normalizedAnswers->count();

                foreach ($q->options ?? [] as $opt) {
                    $optTrimmed = trim((string) $opt);
                    $c = $counts->get($optTrimmed);
                    if ($c === null) {
                        $c = $normalizedAnswers->filter(fn($a) => strcasecmp($a, $optTrimmed) === 0)->count();
                    }
                    $pct = $totalAns > 0 ? round(($c / $totalAns) * 100, 1) : 0;
                    $rows->push([$optTrimmed, $c, "{$pct}%", "{$c} dari {$totalAns} suara"]);
                }
                $rows->push(['TOTAL RESPON', $totalAns, '100%', 'Total seluruh suara']);
            } else {
                $totalAnswers = $validAnswers->count();
                $rows->push(['Total Jawaban Masuk', $totalAnswers, '100%', 'Rincian lengkap jawaban essay dapat dilihat pada sheet "Data Jawaban Responden"']);
            }

            $rows->push(['', '', '', '']);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Explicit minimum column dimensions for clean readability
        $sheet->getColumnDimension('A')->setWidth(40);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(45);

        // Title banner
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '1E1B4B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(26);

        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '4338CA']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(2)->setRowHeight(20);

        $lastRow = $sheet->getHighestRow();
        $isInsideParticipationTable = false;
        $isInsideQuestionTable = false;

        for ($r = 1; $r <= $lastRow; $r++) {
            $valA = trim((string) $sheet->getCell("A{$r}")->getValue());

            // Empty row resets table states
            if ($valA === '') {
                $isInsideParticipationTable = false;
                $isInsideQuestionTable = false;
                continue;
            }

            // Main section headers
            if (in_array($valA, ['INFORMASI SURVEI', 'RINGKASAN PARTISIPASI RESPONDEN', 'DISTRIBUSI JAWABAN PER PERTANYAAN'])) {
                $sheet->mergeCells("A{$r}:D{$r}");
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E1B4B']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(24);
                $isInsideParticipationTable = false;
                $isInsideQuestionTable = false;
                continue;
            }

            // Metadata rows (under INFORMASI SURVEI)
            if (in_array($valA, ['Judul Survei', 'Deskripsi', 'Pembuat Survei', 'Status Survei', 'Periode Pelaksanaan', 'Waktu Ekspor Laporan'])) {
                $sheet->mergeCells("B{$r}:D{$r}");
                $sheet->getStyle("A{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '334155'], 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("B{$r}:D{$r}")->applyFromArray([
                    'font' => ['color' => ['rgb' => '0F172A'], 'size' => 10],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(20);
                continue;
            }

            // Table header: Metrik Partisipasi
            if ($valA === 'Metrik Partisipasi') {
                $isInsideParticipationTable = true;
                $isInsideQuestionTable = false;
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '312E81'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("B{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($r)->setRowHeight(22);
                continue;
            }

            // Participation table rows
            if ($isInsideParticipationTable && in_array($valA, ['Total Target Peserta', 'Sudah Mengisi Survei', 'Belum Mengisi Survei'])) {
                $bgColor = 'F8FAFC';
                $numColor = '0F172A';
                $borderColor = 'E2E8F0';

                if ($valA === 'Sudah Mengisi Survei') {
                    $bgColor = 'F0FDF4';
                    $numColor = '15803D';
                    $borderColor = 'BBF7D0';
                } elseif ($valA === 'Belum Mengisi Survei') {
                    $bgColor = 'FFFBEB';
                    $numColor = 'B45309';
                    $borderColor = 'FED7AA';
                }

                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => $borderColor]]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);

                $sheet->getStyle("A{$r}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('334155');
                $sheet->getStyle("B{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => $numColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("C{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => $numColor]],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("D{$r}")->applyFromArray([
                    'font' => ['size' => 9.5, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                $sheet->getRowDimension($r)->setRowHeight(21);
                continue;
            }

            // Question title header
            if (str_starts_with($valA, 'Pertanyaan #')) {
                $sheet->mergeCells("A{$r}:C{$r}");
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("D{$r}")->applyFromArray([
                    'font' => ['italic' => true, 'color' => ['rgb' => '475569'], 'size' => 9.5],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_RIGHT],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(23);
                $isInsideQuestionTable = false;
                continue;
            }

            // Question table header: Pilihan Jawaban
            if ($valA === 'Pilihan Jawaban') {
                $isInsideQuestionTable = true;
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '312E81'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("B{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($r)->setRowHeight(21);
                continue;
            }

            // Question total row: TOTAL RESPON
            if ($valA === 'TOTAL RESPON') {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E1B4B'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                    'borders' => [
                        'top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '6366F1']],
                        'bottom' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '6366F1']],
                        'left' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']],
                        'right' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']],
                    ],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("B{$r}:C{$r}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getRowDimension($r)->setRowHeight(22);
                $isInsideQuestionTable = false;
                continue;
            }

            // Essay response row: Total Jawaban Masuk
            if ($valA === 'Total Jawaban Masuk') {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$r}")->getFont()->setBold(true)->setSize(10)->getColor()->setRGB('334155');
                $sheet->getStyle("B{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1E1B4B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("C{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10, 'color' => ['rgb' => '1E1B4B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("D{$r}")->applyFromArray([
                    'font' => ['size' => 9.5, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(21);
                continue;
            }

            // Multiple choice option data rows
            if ($isInsideQuestionTable) {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle("A{$r}")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '1E293B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getStyle("B{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'size' => 10.5, 'color' => ['rgb' => '0F172A']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("C{$r}")->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '334155']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);
                $sheet->getStyle("D{$r}")->applyFromArray([
                    'font' => ['size' => 9.5, 'color' => ['rgb' => '64748B']],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(20);
            }
        }

        return [];
    }
}
