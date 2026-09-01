<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepositoryFile;
use App\Models\RepositorySetting;
use App\Models\RepositoryUpload;
use App\Services\RepositoryUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;
use Throwable;

class RepositoryUploadController extends Controller
{
    public function initialize(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'original_name' => ['required', 'string', 'max:255'],
            'size' => ['required', 'integer', 'min:1'],
            'mime_type' => ['nullable', 'string', 'max:255'],
        ]);

        $originalName = basename(str_replace('\\', '/', $validated['original_name']));
        $originalName = trim((string) preg_replace('/[\x00-\x1F\x7F]/u', '', $originalName));
        abort_if($originalName === '', 422, 'Nama file tidak valid.');
        $extension = Str::lower((string) pathinfo($originalName, PATHINFO_EXTENSION));
        $allowedExtensions = config('repository.allowed_extensions', []);
        $maxFileSize = (int) config('repository.max_file_size');

        if ($extension === '' || ! in_array($extension, $allowedExtensions, true)) {
            throw ValidationException::withMessages([
                'original_name' => 'Jenis file tidak diizinkan. Gunakan arsip, dokumen, media, installer, atau image disk yang didukung.',
            ]);
        }

        if ((int) $validated['size'] > $maxFileSize) {
            throw ValidationException::withMessages([
                'size' => 'Ukuran file melebihi batas repository '.RepositoryFile::make(['size' => $maxFileSize])->human_size.'.',
            ]);
        }

        $chunkSize = max(256 * 1024, (int) config('repository.chunk_size'));
        $upload = RepositoryUpload::create([
            'id' => (string) Str::uuid(),
            'user_id' => $request->user()->id,
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'original_name' => $originalName,
            'extension' => $extension,
            'client_mime_type' => $validated['mime_type'] ?? null,
            'size' => (int) $validated['size'],
            'chunk_size' => $chunkSize,
            'total_chunks' => (int) ceil(((int) $validated['size']) / $chunkSize),
            'expires_at' => now()->addHours((int) config('repository.upload_ttl_hours', 24)),
        ]);

        Storage::disk(config('repository.disk'))->makeDirectory($this->uploadDirectory($upload));

        return response()->json([
            'upload_id' => $upload->id,
            'chunk_size' => $upload->chunk_size,
            'total_chunks' => $upload->total_chunks,
            'expires_at' => $upload->expires_at->toIso8601String(),
        ], 201);
    }

    public function chunk(Request $request, RepositoryUpload $upload, int $index)
    {
        $this->guardUpload($request, $upload);
        abort_unless($index >= 0 && $index < $upload->total_chunks, 422, 'Indeks chunk tidak valid.');

        $expectedSize = $index === $upload->total_chunks - 1
            ? $upload->size - ($index * $upload->chunk_size)
            : $upload->chunk_size;

        $disk = Storage::disk(config('repository.disk'));
        $directory = $this->uploadDirectory($upload);
        $disk->makeDirectory($directory);
        $partPath = $disk->path($directory.'/upload.part');
        $input = $request->getContent(true);
        $output = fopen($partPath, 'c+b');

        if (! is_resource($input) || $output === false) {
            throw new RuntimeException('Tidak dapat membuka stream upload repository.');
        }

        $written = 0;
        $hasExtraByte = false;

        try {
            if (! flock($output, LOCK_EX)) {
                throw new RuntimeException('File upload sedang digunakan proses lain.');
            }

            if (fseek($output, $index * $upload->chunk_size) !== 0) {
                throw new RuntimeException('Tidak dapat menentukan posisi chunk.');
            }

            $written = (int) stream_copy_to_stream($input, $output, $expectedSize);
            $hasExtraByte = fread($input, 1) !== '';
            fflush($output);
            flock($output, LOCK_UN);
        } finally {
            fclose($input);
            fclose($output);
        }

        if ($written !== $expectedSize || $hasExtraByte) {
            abort(422, "Ukuran chunk tidak sesuai. Diterima {$written} byte, seharusnya {$expectedSize} byte.");
        }

        $disk->put($directory."/{$index}.done", (string) $expectedSize);

        return response()->json([
            'uploaded_chunk' => $index,
            'received_bytes' => $written,
        ]);
    }

    public function complete(Request $request, RepositoryUpload $upload, RepositoryUrlService $urlService)
    {
        $this->guardUpload($request, $upload);
        $disk = Storage::disk(config('repository.disk'));
        $directory = $this->uploadDirectory($upload);
        $partPath = $directory.'/upload.part';

        for ($index = 0; $index < $upload->total_chunks; $index++) {
            abort_unless($disk->exists($directory."/{$index}.done"), 422, "Chunk {$index} belum diterima.");
        }

        abort_unless($disk->exists($partPath) && $disk->size($partPath) === $upload->size, 422, 'Ukuran file akhir tidak sesuai.');

        $publicToken = (string) Str::uuid();
        $finalPath = trim(config('repository.files_directory'), '/').'/'.$publicToken.'.'.$upload->extension;
        $disk->makeDirectory(dirname($finalPath));
        $disk->move($partPath, $finalPath);

        try {
            $mimeType = mime_content_type($disk->path($finalPath)) ?: $upload->client_mime_type ?: 'application/octet-stream';

            $file = DB::transaction(function () use ($upload, $publicToken, $finalPath, $mimeType) {
                $file = RepositoryFile::create([
                    'public_token' => $publicToken,
                    'title' => $upload->title,
                    'description' => $upload->description,
                    'original_name' => $upload->original_name,
                    'path' => $finalPath,
                    'extension' => $upload->extension,
                    'mime_type' => $mimeType,
                    'size' => $upload->size,
                    'is_active' => true,
                    'uploaded_by' => $upload->user_id,
                    'published_at' => now(),
                ]);

                $upload->delete();

                return $file;
            });
        } catch (Throwable $exception) {
            $disk->move($finalPath, $partPath);
            throw $exception;
        }

        $disk->deleteDirectory($directory);

        return response()->json([
            'message' => 'File berhasil dipublikasikan ke repository.',
            'file' => [
                'id' => $file->public_token,
                'title' => $file->title,
                'links' => $urlService->linksFor($file, RepositorySetting::current()),
            ],
        ]);
    }

    public function cancel(Request $request, RepositoryUpload $upload)
    {
        $this->guardUpload($request, $upload, allowExpired: true);
        Storage::disk(config('repository.disk'))->deleteDirectory($this->uploadDirectory($upload));
        $upload->delete();

        return response()->noContent();
    }

    private function guardUpload(Request $request, RepositoryUpload $upload, bool $allowExpired = false): void
    {
        abort_unless($upload->user_id === $request->user()->id, 403);
        abort_if(! $allowExpired && $upload->expires_at->isPast(), 410, 'Sesi upload sudah kedaluwarsa.');
    }

    private function uploadDirectory(RepositoryUpload $upload): string
    {
        return trim(config('repository.uploads_directory'), '/').'/'.$upload->id;
    }
}
