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

        // Banner Title
        $rows->push(['LAPORAN HASIL ANALISIS SURVEI KEPUASAN', '', '', '']);
        $rows->push(['SMK TELKOM JAKARTA', '', '', '']);
        $rows->push(['', '', '', '']);

        // Survey Metadata
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

        // Participants stats
        $targetCount = $this->survey->targets()->count();
        $responseCount = $this->survey->responses()->count();
        $allUserIds = array_unique(array_merge(
            $this->survey->targets()->pluck('users.id')->all(),
            $this->survey->responses()->pluck('user_id')->all()
        ));
        $totalTarget = count($allUserIds) > 0 ? count($allUserIds) : max($targetCount, $responseCount);
        $totalPending = max(0, $totalTarget - $responseCount);
        $completionRate = $totalTarget > 0 ? round(($responseCount / $totalTarget) * 100, 1) : 0;

        $rows->push(['RINGKASAN PARTISIPASI RESPONDEN', '', '', '']);
        $rows->push(['Metrik Partisipasi', 'Jumlah Responden', 'Persentase', 'Keterangan']);
        $rows->push(['Total Target Peserta', $totalTarget, '100%', 'Total target peserta yang terdaftar']);
        $rows->push(['Sudah Mengisi Survei', $responseCount, $completionRate . '%', 'Responden telah menyelesaikan kuesioner']);
        $rows->push(['Belum Mengisi Survei', $totalPending, (100 - $completionRate) . '%', 'Responden belum mengisi kuesioner']);
        $rows->push(['', '', '', '']);

        // Breakdown Per Question
        $rows->push(['DISTRIBUSI JAWABAN PER PERTANYAAN', '', '', '']);
        $rows->push(['', '', '', '']);

        $questions = $this->survey->questions()->orderBy('order')->with('answers')->get();

        foreach ($questions as $idx => $q) {
            $qNum = $idx + 1;
            $typeLabel = $q->type === 'multiple_choice' ? 'Pilihan Ganda' : 'Essay / Isian Singkat';
            $rows->push(["Pertanyaan #{$qNum}: {$q->question_text}", '', '', "Tipe: {$typeLabel}"]);

            if ($q->type === 'multiple_choice') {
                $rows->push(['Pilihan Jawaban', 'Jumlah Suara', 'Persentase', 'Distribusi']);
                $answers = $q->answers->pluck('answer_value');
                $counts = $answers->countBy();
                $totalAns = $answers->count();

                foreach ($q->options ?? [] as $opt) {
                    $c = $counts->get($opt, 0);
                    $pct = $totalAns > 0 ? round(($c / $totalAns) * 100, 1) : 0;
                    $rows->push([$opt, $c, "{$pct}%", '']);
                }
                $rows->push(['TOTAL RESPON', $totalAns, '100%', '']);
            } else {
                $totalAnswers = $q->answers->count();
                $rows->push(['Total Jawaban Masuk', "{$totalAnswers} Respon", '', 'Lihat rincian lengkap pada sheet "Data Jawaban"']);
            }

            $rows->push(['', '', '', '']);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        // Title banner
        $sheet->mergeCells('A1:D1');
        $sheet->mergeCells('A2:D2');

        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => '1E1B4B']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '6366F1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
        ]);

        $lastRow = $sheet->getHighestRow();

        // Loop through rows to apply header styling
        for ($r = 1; $r <= $lastRow; $r++) {
            $valA = (string) $sheet->getCell("A{$r}")->getValue();

            // Main section headers
            if (in_array($valA, ['INFORMASI SURVEI', 'RINGKASAN PARTISIPASI RESPONDEN', 'DISTRIBUSI JAWABAN PER PERTANYAAN'])) {
                $sheet->mergeCells("A{$r}:D{$r}");
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '312E81']], // Indigo 900
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(24);
            }

            // Table headers
            if ($valA === 'Metrik Partisipasi' || $valA === 'Pilihan Jawaban') {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '312E81'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E0E7FF']], // Indigo 100
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'C7D2FE']]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(20);
            }

            // Question title headers
            if (str_starts_with($valA, 'Pertanyaan #')) {
                $sheet->mergeCells("A{$r}:C{$r}");
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '0F172A'], 'size' => 10],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F1F5F9']], // Slate 100
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(22);
            }

            // Total row
            if ($valA === 'TOTAL RESPON') {
                $sheet->getStyle("A{$r}:D{$r}")->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => '1E1B4B']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'EEF2FF']],
                    'borders' => ['top' => ['borderStyle' => Border::BORDER_DOUBLE, 'color' => ['rgb' => '6366F1']]],
                ]);
            }
        }

        // Apply grid borders to the participation metrics table
        // Rows 9 to 11
        $sheet->getStyle('A9:D11')->applyFromArray([
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getStyle('B9:C11')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Highlight completion rate row
        $sheet->getStyle('A9:D9')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F8FAFC']]);
        $sheet->getStyle('A10:D10')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'F0FDF4']]); // Soft Green
        $sheet->getStyle('A11:D11')->getFill()->applyFromArray(['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFFBEB']]); // Soft Amber

        return [];
    }
}
