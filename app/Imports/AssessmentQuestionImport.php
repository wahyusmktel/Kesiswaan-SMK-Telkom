<?php

namespace App\Imports;

use App\Models\AssessmentInstrument;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class AssessmentQuestionImport implements ToCollection, WithHeadingRow
{
    public function __construct(private readonly AssessmentInstrument $instrument)
    {
    }

    public function collection(Collection $rows): void
    {
        $order = (int) $this->instrument->questions()->max('order') + 1;

        foreach ($rows as $row) {
            $text = trim((string) ($row['pertanyaan'] ?? $row['question'] ?? ''));
            if ($text === '') {
                continue;
            }

            $type = strtolower(trim((string) ($row['tipe'] ?? $row['type'] ?? 'single_choice')));
            $type = match ($type) {
                'ya/tidak', 'ya_tidak', 'yes/no', 'yes_no' => 'yes_no',
                'multi', 'multiple', 'multiple_choice', 'pilihan_multi' => 'multiple_choice',
                'teks', 'text', 'essay', 'saran' => 'text',
                default => 'single_choice',
            };

            $options = trim((string) ($row['pilihan'] ?? $row['options'] ?? ''));
            $parsedOptions = $type === 'yes_no'
                ? ['Ya', 'Tidak']
                : collect(preg_split('/[|;,]/', $options))->map(fn ($item) => trim($item))->filter()->values()->all();

            $this->instrument->questions()->create([
                'question_text' => $text,
                'answer_type' => $type,
                'options' => in_array($type, ['single_choice', 'multiple_choice'], true) ? $parsedOptions : ($type === 'yes_no' ? ['Ya', 'Tidak'] : null),
                'max_score' => max(1, (int) ($row['skor_maksimal'] ?? $row['max_score'] ?? 5)),
                'order' => $order++,
            ]);
        }
    }
}
