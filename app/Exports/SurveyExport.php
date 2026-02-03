<?php

namespace App\Exports;

use App\Models\SurveyAnswer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SurveyExport implements FromCollection, WithHeadings, WithMapping
{
    protected $surveyId;

    public function __construct($surveyId)
    {
        $this->surveyId = $surveyId;
    }

    public function collection()
    {
        return SurveyAnswer::whereHas('response', function ($query) {
            $query->where('survey_id', $this->surveyId);
        })->with(['response.respondent', 'question'])->get();
    }

    public function headings(): array
    {
        return [
            'Responden',
            'Pertanyaan',
            'Jawaban',
            'Waktu Mengisi',
        ];
    }

    public function map($answer): array
    {
        return [
            $answer->response->respondent->name,
            $answer->question->question_text,
            $answer->answer_value,
            $answer->created_at->format('d/m/Y H:i'),
        ];
    }
}
