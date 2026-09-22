<?php

namespace App\Exports;

use App\Exports\Sheets\SurveyPendingSheet;
use App\Exports\Sheets\SurveyResponsesSheet;
use App\Exports\Sheets\SurveySummarySheet;
use App\Models\Survey;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class SurveyExport implements WithMultipleSheets
{
    use Exportable;

    protected Survey $survey;

    public function __construct($survey)
    {
        if ($survey instanceof Survey) {
            $this->survey = $survey;
        } else {
            $this->survey = Survey::with([
                'creator',
                'questions.answers',
                'responses.respondent.masterSiswa.rombels.kelas',
                'responses.respondent.roles',
                'targets.masterSiswa.rombels.kelas',
                'targets.roles',
            ])->findOrFail($survey);
        }
    }

    public function sheets(): array
    {
        return [
            new SurveySummarySheet($this->survey),
            new SurveyResponsesSheet($this->survey),
            new SurveyPendingSheet($this->survey),
        ];
    }
}
