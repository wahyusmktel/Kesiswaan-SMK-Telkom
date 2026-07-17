<?php

namespace App\Services;

use App\Models\AppSetting;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\StudentMasterBookAttachment;
use App\Models\TranscriptConfig;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;
use setasign\Fpdi\PdfReader\StreamReader;
use Throwable;

class StudentMasterBookPdfService
{
    public function studentPacket(MasterSiswa $student, Rombel $rombel): string
    {
        $student->loadMissing(['dapodik', 'masterBook.periods', 'masterBook.attachments']);
        $mainPdf = Pdf::loadView('pdf.student-master-book', $this->viewData($student, $rombel))
            ->setPaper('a4')
            ->output();

        $pdfAttachments = $student->masterBook?->attachments
            ->filter(fn (StudentMasterBookAttachment $item) => $item->mime_type === 'application/pdf')
            ->filter(fn (StudentMasterBookAttachment $item) => Storage::exists($item->file_path))
            ->values() ?? collect();

        if ($pdfAttachments->isEmpty()) {
            return $mainPdf;
        }

        $sources = [$mainPdf];
        foreach ($pdfAttachments as $attachment) {
            $sources[] = Storage::get($attachment->file_path);
        }

        return $this->merge($sources);
    }

    public function classPacket(Rombel $rombel): string
    {
        return $this->merge($rombel->siswa->map(
            fn (MasterSiswa $student) => $this->studentPacket($student, $rombel)
        )->all());
    }

    private function viewData(MasterSiswa $student, Rombel $rombel): array
    {
        $config = TranscriptConfig::first();

        return [
            'student' => $student,
            'book' => $student->masterBook,
            'rombel' => $rombel,
            'schoolName' => $config?->school_name ?? AppSetting::first()?->school_name ?? config('app.name'),
            'npsn' => $config?->npsn,
            'principalName' => $config?->principal_name,
            'principalNip' => $config?->principal_nip,
            'imageAttachments' => $student->masterBook?->attachments
                ->filter(fn (StudentMasterBookAttachment $item) => str_starts_with($item->mime_type, 'image/'))
                ->filter(fn (StudentMasterBookAttachment $item) => Storage::exists($item->file_path))
                ->map(function (StudentMasterBookAttachment $item) {
                    $content = Storage::get($item->file_path);
                    $item->data_uri = 'data:'.$item->mime_type.';base64,'.base64_encode($content);

                    return $item;
                }) ?? collect(),
        ];
    }

    private function merge(array $sources): string
    {
        $previousErrorReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            $merged = new Fpdi;

            foreach ($sources as $source) {
                try {
                    $pageCount = $merged->setSourceFile(StreamReader::createByString($source));
                    for ($page = 1; $page <= $pageCount; $page++) {
                        $template = $merged->importPage($page);
                        $size = $merged->getTemplateSize($template);
                        $orientation = $size['width'] > $size['height'] ? 'L' : 'P';
                        $merged->AddPage($orientation, [$size['width'], $size['height']]);
                        $merged->useTemplate($template);
                    }
                } catch (Throwable) {
                    // Lampiran PDF yang terenkripsi/tidak didukung dilewati agar cetak utama tetap berhasil.
                }
            }

            return $merged->Output('S');
        } finally {
            error_reporting($previousErrorReporting);
        }
    }
}
