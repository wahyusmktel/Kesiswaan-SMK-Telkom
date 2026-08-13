<?php

namespace App\Http\Controllers;

use App\Models\AppSetting;
use App\Models\TranscriptConfig;
use App\Services\FingerprintAttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class FingerprintAttendanceReportController extends Controller
{
    public function __construct(private readonly FingerprintAttendanceReportService $reportService) {}

    public function index(Request $request)
    {
        $previewUrl = null;
        $downloadUrl = null;
        $reportType = $request->input('report_type');

        if ($reportType === 'monthly') {
            $data = $request->validate([
                'month' => ['required', 'date_format:Y-m'],
                'include_inactive' => ['nullable', 'boolean'],
                'include_attachments' => ['nullable', 'boolean'],
            ]);
            $params = [
                'month' => $data['month'],
                'include_inactive' => $request->boolean('include_inactive') ? 1 : 0,
                'include_attachments' => $request->boolean('include_attachments') ? 1 : 0,
            ];
            $previewUrl = route('fingerprint.reports.monthly', $params);
            $downloadUrl = route('fingerprint.reports.monthly', $params + ['download' => 1]);
        }

        if ($reportType === 'daily') {
            $data = $request->validate(['date' => ['required', 'date']]);
            $params = ['date' => $data['date']];
            $previewUrl = route('fingerprint.reports.daily', $params);
            $downloadUrl = route('fingerprint.reports.daily', $params + ['download' => 1]);
        }

        return view('pages.fingerprint.reports.index', compact(
            'previewUrl',
            'downloadUrl',
            'reportType'
        ));
    }

    public function monthly(Request $request)
    {
        $data = $request->validate([
            'month' => ['required', 'date_format:Y-m'],
            'include_inactive' => ['nullable', Rule::in(['0', '1', 0, 1])],
            'include_attachments' => ['nullable', Rule::in(['0', '1', 0, 1])],
            'download' => ['nullable', Rule::in(['0', '1', 0, 1])],
        ]);
        $month = Carbon::createFromFormat('Y-m', $data['month'])->startOfMonth()->locale('id');
        $report = $this->reportService->monthly(
            $month,
            $request->boolean('include_inactive'),
            $request->boolean('include_attachments')
        );
        $filename = 'Laporan Kehadiran Bulan '.$month->translatedFormat('F Y').'.pdf';
        $pdf = Pdf::loadView('pdf.fingerprint-attendance-monthly-report', [
            ...$report,
            ...$this->schoolIdentity(),
            'generatedAt' => now()->locale('id'),
        ])->setPaper('a4', 'portrait');

        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    public function daily(Request $request)
    {
        $data = $request->validate([
            'date' => ['required', 'date'],
            'download' => ['nullable', Rule::in(['0', '1', 0, 1])],
        ]);
        $date = Carbon::parse($data['date'])->locale('id');
        $report = $this->reportService->daily($date);
        $filename = 'Laporan Kehadiran Harian '.$date->translatedFormat('l - d F Y').'.pdf';
        $pdf = Pdf::loadView('pdf.fingerprint-attendance-daily-report', [
            ...$report,
            ...$this->schoolIdentity(),
            'generatedAt' => now()->locale('id'),
        ])->setPaper('a4', 'portrait');

        return $request->boolean('download') ? $pdf->download($filename) : $pdf->stream($filename);
    }

    private function schoolIdentity(): array
    {
        $settings = AppSetting::first();
        $transcript = TranscriptConfig::first();

        return [
            'schoolName' => $settings?->school_name ?: $transcript?->school_name ?: 'SMK TELKOM LAMPUNG',
            'npsn' => $transcript?->npsn ?: '69944770',
            'district' => 'Kec. Gadingrejo',
            'regency' => 'Kab. Pringsewu',
            'province' => 'Prov. Lampung',
        ];
    }
}
