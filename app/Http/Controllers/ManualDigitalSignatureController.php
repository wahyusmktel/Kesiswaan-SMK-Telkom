<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;
use App\Models\ManualSignedDocument;
use App\Models\ManualSignedDocumentStep;
use App\Models\User;
use App\Models\UserDigitalSignature;
use chillerlan\QRCode\Common\EccLevel;
use chillerlan\QRCode\Output\QRGdImagePNG;
use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use setasign\Fpdi\Fpdi;
use Symfony\Component\Process\Process;
use Throwable;

class ManualDigitalSignatureController extends Controller
{
    public function index()
    {
        $signature = UserDigitalSignature::where('user_id', Auth::id())->first();
        $documents = ManualSignedDocument::with(['digitalDocument', 'steps.signer', 'steps.digitalDocument'])
            ->where(function ($query) {
                $query->where('user_id', Auth::id())
                    ->orWhereHas('steps', fn ($step) => $step->where('signer_user_id', Auth::id()));
            })
            ->latest()
            ->paginate(10);
        $pendingSteps = ManualSignedDocumentStep::with(['manualDocument.user', 'manualDocument.steps.signer'])
            ->where('signer_user_id', Auth::id())
            ->where('status', ManualSignedDocumentStep::STATUS_PENDING)
            ->latest()
            ->paginate(5, ['*'], 'pending_page');
        $signerUsers = User::query()
            ->with('roles')
            ->where('id', '!=', Auth::id())
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'siswa']))
            ->orderBy('name')
            ->get();
        $previewQrBase64 = $this->qrDataUri(url('/verifikasi/dokumen/preview-manual-signature'));

        return view('pages.tanda-tangan.manual', compact('signature', 'documents', 'pendingSteps', 'signerUsers', 'previewQrBase64'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'nullable|string|max:180',
            'pdf_file' => 'required|file|mimes:pdf|max:51200',
            'pin' => 'required|string',
            'signed_page' => 'required|integer|min:1',
            'qr_x_mm' => 'required|numeric|min:0|max:500',
            'qr_y_mm' => 'required|numeric|min:0|max:500',
            'qr_size_mm' => 'required|numeric|min:18|max:60',
            'next_signer_ids' => 'nullable|array|max:8',
            'next_signer_ids.*' => 'integer|exists:users,id',
        ]);

        $user = Auth::user();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (! $signature || ! $signature->isReady()) {
            return back()->with('error', 'Identitas digital belum aktif. Setup PIN tanda tangan digital terlebih dahulu.')->withInput();
        }

        if (! $signature->verifyPin($data['pin'])) {
            return back()->with('error', 'PIN tanda tangan digital salah.')->withInput();
        }

        $uploadedFile = $request->file('pdf_file');
        $originalPath = $uploadedFile->store('manual-signatures/originals');
        $absoluteOriginal = Storage::path($originalPath);
        $processableOriginal = $absoluteOriginal;
        $temporaryNormalizedPdf = null;

        try {
            try {
                $pageCount = $this->readPageCount($processableOriginal);
            } catch (Throwable $e) {
                $temporaryNormalizedPdf = $this->normalizePdfForFpdi($absoluteOriginal);
                $processableOriginal = $temporaryNormalizedPdf;
                $pageCount = $this->readPageCount($processableOriginal);
            }
        } catch (Throwable $e) {
            Storage::delete($originalPath);
            if ($temporaryNormalizedPdf) {
                @unlink($temporaryNormalizedPdf);
            }
            report($e);
            return back()->with('error', 'PDF tidak bisa dibaca: ' . $e->getMessage())->withInput();
        }

        if ((int) $data['signed_page'] > $pageCount) {
            Storage::delete($originalPath);
            if ($temporaryNormalizedPdf) {
                @unlink($temporaryNormalizedPdf);
            }
            return back()->with('error', "Nomor halaman melebihi total halaman PDF ({$pageCount} halaman).")->withInput();
        }

        $title = $data['title'] ?: pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
        $document = $this->createDigitalDocument(
            $user,
            $title,
            $absoluteOriginal,
            (int) $data['signed_page'],
            (float) $data['qr_x_mm'],
            (float) $data['qr_y_mm'],
            (float) $data['qr_size_mm'],
            1
        );

        $signedPath = 'manual-signatures/signed/' . Str::uuid() . '.pdf';
        Storage::makeDirectory('manual-signatures/signed');

        try {
            $this->stampPdf(
                $processableOriginal,
                Storage::path($signedPath),
                route('verifikasi.dokumen', $document->token),
                (int) $data['signed_page'],
                (float) $data['qr_x_mm'],
                (float) $data['qr_y_mm'],
                (float) $data['qr_size_mm']
            );
        } catch (Throwable $e) {
            Storage::delete([$originalPath, $signedPath]);
            if ($temporaryNormalizedPdf) {
                @unlink($temporaryNormalizedPdf);
            }
            $document->delete();
            report($e);
            return back()->with('error', 'Gagal menempelkan QR ke PDF: ' . $e->getMessage())->withInput();
        }

        if ($temporaryNormalizedPdf) {
            @unlink($temporaryNormalizedPdf);
        }

        $manual = ManualSignedDocument::create([
            'user_id' => $user->id,
            'digital_document_id' => $document->id,
            'title' => $title,
            'original_file_name' => $uploadedFile->getClientOriginalName(),
            'original_file_path' => $originalPath,
            'signed_file_path' => $signedPath,
            'file_size' => $uploadedFile->getSize() ?: 0,
            'page_count' => $pageCount,
            'signed_page' => (int) $data['signed_page'],
            'qr_x_mm' => (float) $data['qr_x_mm'],
            'qr_y_mm' => (float) $data['qr_y_mm'],
            'qr_size_mm' => (float) $data['qr_size_mm'],
        ]);

        $document->update(['reference_id' => $manual->id]);
        $this->createWorkflowSteps($manual, $document, $user, $data);

        return redirect()
            ->route('tanda-tangan.manual.index')
            ->with('success', 'Dokumen berhasil ditandatangani dan diarsipkan.');
    }

    public function continueSign(Request $request, ManualSignedDocument $manualDocument)
    {
        $data = $request->validate([
            'pin' => 'required|string',
            'signed_page' => 'required|integer|min:1',
            'qr_x_mm' => 'required|numeric|min:0|max:500',
            'qr_y_mm' => 'required|numeric|min:0|max:500',
            'qr_size_mm' => 'required|numeric|min:18|max:60',
        ]);

        $user = Auth::user();
        $step = ManualSignedDocumentStep::where('manual_signed_document_id', $manualDocument->id)
            ->where('signer_user_id', $user->id)
            ->where('status', ManualSignedDocumentStep::STATUS_PENDING)
            ->firstOrFail();
        $signature = UserDigitalSignature::where('user_id', $user->id)->first();

        if (! $signature || ! $signature->isReady()) {
            return back()->with('error', 'Identitas digital belum aktif. Setup PIN tanda tangan digital terlebih dahulu.');
        }

        if (! $signature->verifyPin($data['pin'])) {
            return back()->with('error', 'PIN tanda tangan digital salah.');
        }

        abort_unless(Storage::exists($manualDocument->signed_file_path), 404);

        $sourcePath = Storage::path($manualDocument->signed_file_path);
        $processableSource = $sourcePath;
        $temporaryNormalizedPdf = null;

        try {
            try {
                $pageCount = $this->readPageCount($processableSource);
            } catch (Throwable $e) {
                $temporaryNormalizedPdf = $this->normalizePdfForFpdi($sourcePath);
                $processableSource = $temporaryNormalizedPdf;
                $pageCount = $this->readPageCount($processableSource);
            }

            if ((int) $data['signed_page'] > $pageCount) {
                return back()->with('error', "Nomor halaman melebihi total halaman PDF ({$pageCount} halaman).");
            }

            $document = $this->createDigitalDocument(
                $user,
                $manualDocument->title,
                $sourcePath,
                (int) $data['signed_page'],
                (float) $data['qr_x_mm'],
                (float) $data['qr_y_mm'],
                (float) $data['qr_size_mm'],
                $step->sequence
            );

            $signedPath = 'manual-signatures/signed/' . Str::uuid() . '.pdf';
            Storage::makeDirectory('manual-signatures/signed');

            $this->stampPdf(
                $processableSource,
                Storage::path($signedPath),
                route('verifikasi.dokumen', $document->token),
                (int) $data['signed_page'],
                (float) $data['qr_x_mm'],
                (float) $data['qr_y_mm'],
                (float) $data['qr_size_mm']
            );

            $oldSignedPath = $manualDocument->signed_file_path;
            $manualDocument->update([
                'digital_document_id' => $document->id,
                'signed_file_path' => $signedPath,
                'signed_page' => (int) $data['signed_page'],
                'qr_x_mm' => (float) $data['qr_x_mm'],
                'qr_y_mm' => (float) $data['qr_y_mm'],
                'qr_size_mm' => (float) $data['qr_size_mm'],
            ]);
            $document->update(['reference_id' => $manualDocument->id]);

            $step->update([
                'digital_document_id' => $document->id,
                'status' => ManualSignedDocumentStep::STATUS_COMPLETED,
                'signed_page' => (int) $data['signed_page'],
                'qr_x_mm' => (float) $data['qr_x_mm'],
                'qr_y_mm' => (float) $data['qr_y_mm'],
                'qr_size_mm' => (float) $data['qr_size_mm'],
                'signed_at' => now(),
            ]);

            $this->activateNextStep($manualDocument, $step->sequence);

            if ($oldSignedPath !== $signedPath) {
                Storage::delete($oldSignedPath);
            }
        } catch (Throwable $e) {
            if (! empty($signedPath)) {
                Storage::delete($signedPath);
            }
            report($e);
            return back()->with('error', 'Gagal melanjutkan tanda tangan PDF: ' . $e->getMessage());
        } finally {
            if ($temporaryNormalizedPdf) {
                @unlink($temporaryNormalizedPdf);
            }
        }

        return redirect()
            ->route('tanda-tangan.manual.index')
            ->with('success', 'Dokumen berhasil ditandatangani dan diteruskan sesuai alur.');
    }

    public function download(ManualSignedDocument $manualDocument)
    {
        abort_unless($this->canAccessManualDocument($manualDocument), 403);
        abort_unless(Storage::exists($manualDocument->signed_file_path), 404);

        return Storage::download(
            $manualDocument->signed_file_path,
            Str::slug($manualDocument->title) . '-signed.pdf',
            ['Content-Type' => 'application/pdf']
        );
    }

    public function preview(ManualSignedDocument $manualDocument)
    {
        abort_unless($this->canAccessManualDocument($manualDocument), 403);
        abort_unless(Storage::exists($manualDocument->signed_file_path), 404);

        return response()->file(Storage::path($manualDocument->signed_file_path), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . Str::slug($manualDocument->title) . '-preview.pdf"',
        ]);
    }

    private function createDigitalDocument(User $user, string $title, string $sourcePath, int $page, float $xMm, float $yMm, float $sizeMm, int $sequence): DigitalDocument
    {
        $hash = DigitalDocument::generateHash([
            'MANUAL_PDF',
            (string) $user->id,
            $title,
            hash_file('sha256', $sourcePath),
            (string) $page,
            (string) $xMm,
            (string) $yMm,
            (string) $sizeMm,
            (string) $sequence,
        ]);

        return DigitalDocument::create([
            'document_type' => 'MANUAL_PDF',
            'document_title' => $title,
            'document_hash' => $hash,
            'hmac_signature' => DigitalDocument::generateHmac($hash),
            'signed_by' => $user->id,
            'signer_name' => $user->name,
            'signer_nip' => $user->masterGuru?->nip ?? null,
            'signer_role' => $user->getRoleNames()->first() ?? 'Staff',
            'signed_at' => now(),
            'is_valid' => true,
        ]);
    }

    private function createWorkflowSteps(ManualSignedDocument $manual, DigitalDocument $document, User $user, array $data): void
    {
        ManualSignedDocumentStep::create([
            'manual_signed_document_id' => $manual->id,
            'signer_user_id' => $user->id,
            'digital_document_id' => $document->id,
            'sequence' => 1,
            'status' => ManualSignedDocumentStep::STATUS_COMPLETED,
            'signed_page' => (int) $data['signed_page'],
            'qr_x_mm' => (float) $data['qr_x_mm'],
            'qr_y_mm' => (float) $data['qr_y_mm'],
            'qr_size_mm' => (float) $data['qr_size_mm'],
            'signed_at' => now(),
        ]);

        $nextSignerIds = collect($data['next_signer_ids'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0 && $id !== $user->id)
            ->unique()
            ->values();

        foreach ($nextSignerIds as $index => $signerId) {
            ManualSignedDocumentStep::create([
                'manual_signed_document_id' => $manual->id,
                'signer_user_id' => $signerId,
                'sequence' => $index + 2,
                'status' => $index === 0 ? ManualSignedDocumentStep::STATUS_PENDING : ManualSignedDocumentStep::STATUS_WAITING,
            ]);
        }
    }

    private function activateNextStep(ManualSignedDocument $manualDocument, int $completedSequence): void
    {
        $nextStep = ManualSignedDocumentStep::where('manual_signed_document_id', $manualDocument->id)
            ->where('sequence', '>', $completedSequence)
            ->where('status', ManualSignedDocumentStep::STATUS_WAITING)
            ->orderBy('sequence')
            ->first();

        if ($nextStep) {
            $nextStep->update(['status' => ManualSignedDocumentStep::STATUS_PENDING]);
        }
    }

    private function canAccessManualDocument(ManualSignedDocument $manualDocument): bool
    {
        if ($manualDocument->user_id === Auth::id()) {
            return true;
        }

        return $manualDocument->steps()
            ->where('signer_user_id', Auth::id())
            ->exists();
    }

    private function stampPdf(string $sourcePath, string $targetPath, string $verificationUrl, int $targetPage, float $xMm, float $yMm, float $sizeMm): void
    {
        $previousErrorReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            $pdf = new Fpdi('P', 'mm');
            $pageCount = $pdf->setSourceFile($sourcePath);
            $qrPath = $this->temporaryQrPath($verificationUrl);

            for ($page = 1; $page <= $pageCount; $page++) {
                $template = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($template);
                $orientation = $size['width'] > $size['height'] ? 'L' : 'P';

                $pdf->AddPage($orientation, [$size['width'], $size['height']]);
                $pdf->useTemplate($template, 0, 0, $size['width'], $size['height']);

                if ($page === $targetPage) {
                    $safeX = min(max($xMm, 0), max($size['width'] - $sizeMm, 0));
                    $safeY = min(max($yMm, 0), max($size['height'] - $sizeMm, 0));
                    $pdf->Image($qrPath, $safeX, $safeY, $sizeMm, $sizeMm, 'PNG');
                }
            }

            $pdf->Output('F', $targetPath);
            @unlink($qrPath);
        } finally {
            error_reporting($previousErrorReporting);
        }
    }

    private function readPageCount(string $sourcePath): int
    {
        $previousErrorReporting = error_reporting(error_reporting() & ~E_DEPRECATED & ~E_USER_DEPRECATED);

        try {
            $pdfProbe = new Fpdi();
            return $pdfProbe->setSourceFile($sourcePath);
        } finally {
            error_reporting($previousErrorReporting);
        }
    }

    private function normalizePdfForFpdi(string $sourcePath): string
    {
        Storage::makeDirectory('manual-signatures/normalized');
        $normalizedDirectory = storage_path('app/manual-signatures/normalized');
        $targetPath = $normalizedDirectory . '/' . Str::uuid() . '.pdf';
        $errors = [];
        @chmod(storage_path('app/manual-signatures'), 0775);
        @chmod($normalizedDirectory, 0775);

        if (! is_dir($normalizedDirectory) || ! is_writable($normalizedDirectory)) {
            throw new RuntimeException('Folder normalisasi PDF tidak writable: ' . $normalizedDirectory);
        }

        $qpdf = $this->findExecutable(['qpdf']);
        if ($qpdf) {
            $process = new Process([
                $qpdf,
                '--object-streams=disable',
                '--stream-data=uncompress',
                $sourcePath,
                $targetPath,
            ]);
            $process->setTimeout(120);
            $process->run();

            if ($process->isSuccessful() && is_file($targetPath) && filesize($targetPath) > 0) {
                return $targetPath;
            }

            $errors[] = 'qpdf (' . $qpdf . ') gagal: ' . trim($process->getErrorOutput() ?: $process->getOutput() ?: 'output kosong');
        } else {
            $errors[] = 'qpdf tidak ditemukan oleh PHP.';
        }

        $ghostscript = $this->findExecutable(['gs', 'gswin64c', 'gswin32c']);
        if ($ghostscript) {
            $process = new Process([
                $ghostscript,
                '-q',
                '-dNOPAUSE',
                '-dBATCH',
                '-sDEVICE=pdfwrite',
                '-dCompatibilityLevel=1.4',
                '-sOutputFile=' . $targetPath,
                $sourcePath,
            ]);
            $process->setTimeout(180);
            $process->run();

            if ($process->isSuccessful() && is_file($targetPath) && filesize($targetPath) > 0) {
                return $targetPath;
            }

            $errors[] = 'ghostscript (' . $ghostscript . ') gagal: ' . trim($process->getErrorOutput() ?: $process->getOutput() ?: 'output kosong');
        } else {
            $errors[] = 'ghostscript tidak ditemukan oleh PHP.';
        }

        @unlink($targetPath);

        throw new RuntimeException('PDF memakai kompresi modern dan normalisasi otomatis gagal. ' . implode(' | ', $errors));
    }

    private function findExecutable(array $binaries): ?string
    {
        foreach ($binaries as $binary) {
            foreach ($this->candidateExecutablePaths($binary) as $candidatePath) {
                if (is_executable($candidatePath)) {
                    return $candidatePath;
                }
            }

            $process = Process::fromShellCommandline(PHP_OS_FAMILY === 'Windows' ? 'where ' . escapeshellarg($binary) : 'command -v ' . escapeshellarg($binary));
            $process->setTimeout(5);
            $process->run();

            if ($process->isSuccessful()) {
                $path = trim(strtok($process->getOutput(), "\r\n"));
                if ($path !== '') {
                    return $path;
                }
            }
        }

        return null;
    }

    private function candidateExecutablePaths(string $binary): array
    {
        if (PHP_OS_FAMILY === 'Windows') {
            return [];
        }

        return [
            '/usr/bin/' . $binary,
            '/usr/local/bin/' . $binary,
            '/bin/' . $binary,
            '/snap/bin/' . $binary,
        ];
    }

    private function temporaryQrPath(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 6,
            'imageBase64' => false,
            'quietzoneSize' => 1,
            'eccLevel' => EccLevel::M,
        ]);

        $path = storage_path('app/manual-signatures/qr-' . Str::uuid() . '.png');
        $png = (new QRCode($options))->render($url);

        if (str_starts_with($png, 'data:image/png;base64,')) {
            $png = base64_decode(substr($png, strlen('data:image/png;base64,')));
        }

        file_put_contents($path, $png);

        return $path;
    }

    private function qrDataUri(string $url): string
    {
        $options = new QROptions([
            'outputInterface' => QRGdImagePNG::class,
            'scale' => 6,
            'imageBase64' => true,
            'quietzoneSize' => 1,
            'eccLevel' => EccLevel::M,
        ]);

        return (new QRCode($options))->render($url);
    }
}
