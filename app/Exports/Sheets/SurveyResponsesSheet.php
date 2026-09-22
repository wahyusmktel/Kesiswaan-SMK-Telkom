<?php

namespace App\Exports\Sheets;

use App\Models\Survey;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SurveyResponsesSheet implements FromCollection, WithTitle, WithStyles, ShouldAutoSize
{
    protected Survey $survey;

    public function __construct(Survey $survey)
    {
        $this->survey = $survey;
    }

    public function title(): string
    {
        return 'Data Jawaban Responden';
    }

    public function collection()
    {
        $rows = collect();

        // Banner info
        $rows->push(['DATA JAWABAN RESPONDEN SURVEI KEPUASAN']);
        $rows->push(['Survei: ' . $this->survey->title . ' | Total Responden: ' . $this->survey->responses()->count() . ' orang']);
        $rows->push(['']);

        // Load questions
        $questions = $this->survey->questions()->orderBy('order')->get();

        // Build header row
        $headers = [
            'No',
            'Nama Responden',
            'NIS',
            'Kelas / Unit',
            'Peran',
            'Waktu Mengisi',
        ];

        foreach ($questions as $idx => $q) {
            $num = $idx + 1;
            $type = $q->type === 'multiple_choice' ? '(PG)' : '(Essay)';
            $headers[] = "Q{$num} {$type}: {$q->question_text}";
        }
        $rows->push($headers);

        // Load responses with answers and respondent
        $responses = $this->survey->responses()
            ->with([
                'respondent.masterSiswa.rombels.kelas',
                'respondent.roles',
                'answers',
            ])
            ->latest()
            ->get();

        $no = 1;
        foreach ($responses as $response) {
            $user = $response->respondent;
            $isStudent = $user?->hasRole('Siswa') || $user?->masterSiswa !== null;
            $nis = $user?->masterSiswa?->nis ?? '-';
            $rombel = $user?->masterSiswa?->rombels->first();
            $kelas = $rombel?->kelas?->nama_kelas ?? ($isStudent ? 'Siswa' : ($user?->roles->pluck('name')->first() ?? 'Pengguna'));
            $roles = $user?->roles->pluck('name')->implode(', ') ?: 'Pengguna';
            $waktu = $response->created_at ? $response->created_at->format('d/m/Y H:i') : '-';

            $answersByQuestionId = $response->answers->keyBy('question_id');

            $row = [
                $no++,
                $user?->name ?? 'Pengguna Anonim',
                $nis,
                $kelas,
                $roles,
                $waktu,
            ];

            foreach ($questions as $q) {
                $ans = $answersByQuestionId->get($q->id);
                $val = $ans?->answer_value ?? '—';

                // If stored as JSON array (multiple choice or checkbox)
                if (is_string($val) && str_starts_with($val, '[') && str_ends_with($val, ']')) {
                    $decoded = json_decode($val, true);
                    if (is_array($decoded)) {
                        $val = implode(', ', $decoded);
                    }
                }

                $row[] = (string) $val;
            }

            $rows->push($row);
        }

        if ($responses->isEmpty()) {
            $emptyRow = array_fill(0, count($headers), '');
            $emptyRow[0] = 'Belum ada data responden yang mengisi survei ini.';
            $rows->push($emptyRow);
        }

        return $rows;
    }

    public function styles(Worksheet $sheet)
    {
        $lastRow = $sheet->getHighestRow();
        $lastColString = $sheet->getHighestColumn();

        // Sheet Title
        $sheet->getStyle('A1')->applyFromArray([
            'font' => ['bold' => true, 'size' => 15, 'color' => ['rgb' => '1E1B4B']],
        ]);
        $sheet->getStyle('A2')->applyFromArray([
            'font' => ['bold' => false, 'size' => 10, 'color' => ['rgb' => '64748B']],
        ]);

        $headerRow = 4;

        // Table Header Styling
        $sheet->getStyle("A{$headerRow}:{$lastColString}{$headerRow}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']], // Slate 900
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '0F172A']]],
        ]);
        $sheet->getRowDimension($headerRow)->setRowHeight(32);

        // Data Rows Styling
        if ($lastRow > $headerRow) {
            $sheet->getStyle("A5:{$lastColString}{$lastRow}")->applyFromArray([
                'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E2E8F0']]],
            ]);

            // Column alignments
            $sheet->getStyle("A5:A{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C5:F{$lastRow}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            // Zebra striping
            for ($r = 5; $r <= $lastRow; $r++) {
                $fillColor = ($r % 2 === 0) ? 'F8FAFC' : 'FFFFFF';
                $sheet->getStyle("A{$r}:{$lastColString}{$r}")->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $fillColor]],
                ]);
                $sheet->getRowDimension($r)->setRowHeight(22);
            }
        }

        // Freeze panes at row 5 (below headers) and column G (after Waktu Mengisi)
        $sheet->freezePane('G5');

        // AutoFilter
        $sheet->setAutoFilter("A{$headerRow}:{$lastColString}{$lastRow}");

        return [];
    }
}
