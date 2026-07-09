<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\DigitalDocument;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TranscriptConfig;
use App\Models\TranscriptNumber;
use App\Models\TranscriptSubject;
use App\Models\UserDigitalSignature;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TranscriptPrintController extends Controller
{
    public function index(Request $request)
    {
        $rombels = $this->finalRombels()->get();
        $selectedRombel = $request->filled('rombel_id')
            ? Rombel::with(['kelas', 'siswa.dapodik', 'siswa.transcriptDiplomaNumber'])->find($request->rombel_id)
            : null;

        $students = $selectedRombel
            ? $selectedRombel->siswa->sortBy('nama_lengkap')->values()
            : collect();

        return view('pages.operator.transcript.print', compact('rombels', 'selectedRombel', 'students'));
    }

    public function student(Request $request, MasterSiswa $student)
    {
        $student->load(['dapodik', 'rombels.kelas', 'transcriptDiplomaNumber', 'transcriptGrades.subject']);
        $config = TranscriptConfig::firstOrCreate([]);
        $subjects = $this->subjects()->get();
        $transcriptNumbers = $this->transcriptNumbers(collect([$student]), $config);
        $transcriptQrCodes = $this->transcriptQrCodes(collect([$student]), $subjects, $config, $transcriptNumbers);

        $pdf = Pdf::loadView('pdf.transcript', [
            'config' => $config,
            'students' => collect([$student]),
            'subjects' => $subjects,
            'transcriptNumbers' => $transcriptNumbers,
            'transcriptQrCodes' => $transcriptQrCodes,
            'letterheadDataUri' => $this->dataUri($config->letterhead_path),
            'watermarkDataUri' => $this->dataUri($config->watermark_path),
            'single' => true,
        ])->setPaper($this->paper($config), 'portrait');

        return $pdf->stream('Transkrip_' . str($student->nama_lengkap)->slug('_') . '.pdf');
    }

    public function classroom(Request $request)
    {
        $data = $request->validate(['rombel_id' => 'required|exists:rombels,id']);
        $rombel = Rombel::with(['kelas', 'siswa.dapodik', 'siswa.rombels.kelas', 'siswa.transcriptDiplomaNumber', 'siswa.transcriptGrades.subject'])
            ->findOrFail($data['rombel_id']);
        $config = TranscriptConfig::firstOrCreate([]);
        $subjects = $this->subjects()->get();
        $students = $rombel->siswa->sortBy('nama_lengkap')->values();
        $transcriptNumbers = $this->transcriptNumbers($students, $config);
        $transcriptQrCodes = $this->transcriptQrCodes($students, $subjects, $config, $transcriptNumbers);

        $pdf = Pdf::loadView('pdf.transcript', [
            'config' => $config,
            'students' => $students,
            'subjects' => $subjects,
            'transcriptNumbers' => $transcriptNumbers,
            'transcriptQrCodes' => $transcriptQrCodes,
            'letterheadDataUri' => $this->dataUri($config->letterhead_path),
            'watermarkDataUri' => $this->dataUri($config->watermark_path),
            'single' => false,
        ])->setPaper($this->paper($config), 'portrait');

        return $pdf->stream('Transkrip_' . str($rombel->kelas?->nama_kelas ?? 'kelas')->slug('_') . '.pdf');
    }

    private function finalRombels()
    {
        return Rombel::with('kelas')
            ->whereHas('kelas', function ($query) {
                $query->where('nama_kelas', 'like', '%XII%')
                    ->orWhere('nama_kelas', 'like', '%12%');
            })
            ->orderByDesc('tahun_ajaran')
            ->orderBy('kelas_id');
    }

    private function subjects()
    {
        return TranscriptSubject::where('is_active', true)
            ->orderBy('group')
            ->orderBy('sort_order')
            ->orderBy('name');
    }

    private function paper(TranscriptConfig $config): string|array
    {
        return match ($config->paper_size ?? 'A4') {
            'F4' => [0, 0, 595.28, 935.43],
            'Letter' => 'letter',
            'Legal' => 'legal',
            default => 'a4',
        };
    }

    private function dataUri(?string $path): ?string
    {
        if (! $path || ! Storage::disk('public')->exists($path)) {
            return null;
        }

        $absolutePath = Storage::disk('public')->path($path);
        $mime = mime_content_type($absolutePath) ?: 'image/png';

        return 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($absolutePath));
    }

    private function transcriptNumbers(Collection $students, TranscriptConfig $config): array
    {
        $start = $config->number_start ?: '400.3.11/800.01';
        $suffix = $config->number_suffix ?: '/SMKTEL-LPG/KURL.03/V/2026';
        $parsed = $this->parseRunningNumber($start);
        $numbers = [];
        $orderedIds = $this->finalStudentsOrder()->pluck('id')->values();
        $nextNumber = $this->nextAvailableTranscriptSequence($parsed);

        DB::transaction(function () use ($students, $parsed, $suffix, $orderedIds, &$nextNumber, &$numbers) {
            foreach ($students->sortBy(fn ($student) => $orderedIds->search($student->id) === false ? PHP_INT_MAX : $orderedIds->search($student->id)) as $student) {
                $record = TranscriptNumber::where('master_siswa_id', $student->id)->lockForUpdate()->first();

                if (! $record) {
                    $number = $this->formatTranscriptNumber($parsed, $nextNumber, $suffix);

                    while (TranscriptNumber::where('number', $number)->exists()) {
                        $nextNumber++;
                        $number = $this->formatTranscriptNumber($parsed, $nextNumber, $suffix);
                    }

                    $record = TranscriptNumber::create([
                        'master_siswa_id' => $student->id,
                        'number' => $number,
                        'locked_at' => now(),
                    ]);

                    $nextNumber++;
                }

                $numbers[$student->id] = $record->number;
            }
        });

        return $numbers;
    }

    private function nextAvailableTranscriptSequence(array $parsed): int
    {
        $max = TranscriptNumber::where('number', 'like', $parsed['prefix'] . '%')
            ->pluck('number')
            ->map(fn ($number) => $this->extractTranscriptSequence($number, $parsed))
            ->filter()
            ->max();

        return max($parsed['number'], $max ? $max + 1 : $parsed['number']);
    }

    private function extractTranscriptSequence(string $number, array $parsed): ?int
    {
        $pattern = '/^' . preg_quote($parsed['prefix'], '/') . '(\d+)/';

        return preg_match($pattern, $number, $matches) ? (int) $matches[1] : null;
    }

    private function formatTranscriptNumber(array $parsed, int $number, string $suffix): string
    {
        return $parsed['prefix'] . str_pad((string) $number, $parsed['width'], '0', STR_PAD_LEFT) . $suffix;
    }

    private function transcriptQrCodes(Collection $students, Collection $subjects, TranscriptConfig $config, array $transcriptNumbers): array
    {
        $signature = UserDigitalSignature::where('auto_sign_transcript', true)
            ->where('is_active', true)
            ->whereNotNull('pin_hash')
            ->whereHas('user', fn ($query) => $query->role('Kepala Sekolah'))
            ->with('user')
            ->first();

        if (! $signature || ! $signature->user) {
            return [];
        }

        $qrCodes = [];

        foreach ($students as $student) {
            $hashParts = $this->transcriptHashParts($student, $subjects, $config, $transcriptNumbers[$student->id] ?? '-');
            $hash = DigitalDocument::generateHash($hashParts);
            $hmac = DigitalDocument::generateHmac($hash);

            $document = DigitalDocument::where('document_type', 'TRANSKRIP_NILAI')
                ->where('reference_id', $student->id)
                ->first();

            $signerData = [
                'document_title' => 'Transkrip Nilai - ' . ($student->nama_lengkap ?? 'Siswa'),
                'document_hash' => $hash,
                'hmac_signature' => $hmac,
                'signed_by' => $signature->user->id,
                'signer_name' => $config->principal_name ?: $signature->user->name,
                'signer_nip' => $config->principal_nip,
                'signer_role' => 'Kepala Sekolah',
                'signed_at' => now(),
                'is_valid' => true,
                'revoked_at' => null,
                'revoke_reason' => null,
            ];

            if ($document) {
                $document->update($signerData);
                $document = $document->refresh();
            } else {
                $document = DigitalDocument::create(array_merge($signerData, [
                    'document_type' => 'TRANSKRIP_NILAI',
                    'reference_id' => $student->id,
                ]));
            }

            $qrCodes[$student->id] = $this->qrBase64(route('verifikasi.dokumen', $document->token));
        }

        return $qrCodes;
    }

    private function transcriptHashParts(MasterSiswa $student, Collection $subjects, TranscriptConfig $config, string $transcriptNumber): array
    {
        $gradeMap = $student->transcriptGrades->keyBy('transcript_subject_id');
        $gradeParts = $subjects
            ->map(fn ($subject) => $subject->id . ':' . ($gradeMap->get($subject->id)?->score ?? '-'))
            ->values()
            ->all();

        return array_merge([
            'TRANSKRIP_NILAI',
            (string) $student->id,
            (string) ($student->nis ?? ''),
            (string) ($student->dapodik?->nisn ?? ''),
            (string) $student->nama_lengkap,
            $transcriptNumber,
            (string) ($student->transcriptDiplomaNumber?->diploma_number ?? ''),
            (string) ($config->school_name ?? ''),
            (string) ($config->npsn ?? ''),
            (string) ($config->graduation_date?->toDateString() ?? ''),
            (string) ($config->signature_date?->toDateString() ?? ''),
        ], $gradeParts);
    }

    private function qrBase64(string $url): string
    {
        $options = new \chillerlan\QRCode\QROptions([
            'outputInterface' => \chillerlan\QRCode\Output\QRGdImagePNG::class,
            'scale' => 4,
            'imageBase64' => true,
            'quietzoneSize' => 1,
            'eccLevel' => \chillerlan\QRCode\Common\EccLevel::M,
        ]);

        return (new \chillerlan\QRCode\QRCode($options))->render($url);
    }

    private function parseRunningNumber(string $start): array
    {
        if (preg_match('/^(.*?)(\d+)$/', $start, $matches)) {
            return [
                'prefix' => $matches[1],
                'number' => (int) $matches[2],
                'width' => strlen($matches[2]),
            ];
        }

        return [
            'prefix' => Str::finish($start, '.'),
            'number' => 1,
            'width' => 2,
        ];
    }

    private function finalStudentsOrder(): Collection
    {
        return MasterSiswa::select('master_siswa.id', DB::raw('MIN(rombels.kelas_id) as sort_kelas'), DB::raw('MIN(master_siswa.nama_lengkap) as sort_name'))
            ->join('rombel_siswa', 'master_siswa.id', '=', 'rombel_siswa.master_siswa_id')
            ->join('rombels', 'rombels.id', '=', 'rombel_siswa.rombel_id')
            ->join('kelas', 'kelas.id', '=', 'rombels.kelas_id')
            ->where(function ($query) {
                $query->where('kelas.nama_kelas', 'like', '%XII%')
                    ->orWhere('kelas.nama_kelas', 'like', '%12%');
            })
            ->groupBy('master_siswa.id')
            ->orderBy('sort_kelas')
            ->orderBy('sort_name')
            ->get();
    }
}
