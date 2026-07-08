<?php

namespace App\Http\Controllers;

use App\Models\DigitalDocument;
use App\Models\ManualSignedDocument;
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
        $documents = ManualSignedDocument::with('digitalDocument')
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pages.tanda-tangan.manual', compact('signature', 'documents'));
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
        $hash = DigitalDocument::generateHash([
            'MANUAL_PDF',
            (string) $user->id,
            $title,
            hash_file('sha256', $absoluteOriginal),
            (string) $data['signed_page'],
            (string) $data['qr_x_mm'],
            (string) $data['qr_y_mm'],
            (string) $data['qr_size_mm'],
        ]);

        $document = DigitalDocument::create([
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

        return redirect()
            ->route('tanda-tangan.manual.index')
            ->with('success', 'Dokumen berhasil ditandatangani dan diarsipkan.');
    }

    public function download(ManualSignedDocument $manualDocument)
    {
        abort_unless($manualDocument->user_id === Auth::id(), 403);
        abort_unless(Storage::exists($manualDocument->signed_file_path), 404);

        return Storage::download(
            $manualDocument->signed_file_path,
            Str::slug($manualDocument->title) . '-signed.pdf',
            ['Content-Type' => 'application/pdf']
        );
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
                    $safeY = min(max($yMm - 3, 0), max($size['height'] - $sizeMm, 0));
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
}
