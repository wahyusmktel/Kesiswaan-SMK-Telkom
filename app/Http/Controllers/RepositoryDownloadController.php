<?php

namespace App\Http\Controllers;

use App\Models\RepositoryFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\HeaderUtils;

class RepositoryDownloadController extends Controller
{
    public function __invoke(RepositoryFile $repositoryFile)
    {
        abort_unless($repositoryFile->is_active, 404);

        $disk = Storage::disk(config('repository.disk'));
        abort_unless($disk->exists($repositoryFile->path), 404, 'File repository tidak ditemukan.');

        $headers = [
            'Content-Type' => $repositoryFile->mime_type ?: 'application/octet-stream',
            'Content-Disposition' => HeaderUtils::makeDisposition(
                HeaderUtils::DISPOSITION_ATTACHMENT,
                $repositoryFile->original_name,
                Str::ascii($repositoryFile->original_name) ?: 'download'
            ),
            'Cache-Control' => 'public, max-age=3600, no-transform',
            'Accept-Ranges' => 'bytes',
            'X-Content-Type-Options' => 'nosniff',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
        ];

        if (config('repository.download_driver') === 'nginx') {
            $encodedPath = collect(explode('/', ltrim($repositoryFile->path, '/')))
                ->map(fn (string $segment) => rawurlencode($segment))
                ->implode('/');

            return response('', 200, $headers + [
                'Content-Length' => (string) $repositoryFile->size,
                'X-Accel-Redirect' => rtrim(config('repository.accel_redirect_prefix'), '/').'/'.$encodedPath,
            ]);
        }

        return response()->download(
            $disk->path($repositoryFile->path),
            $repositoryFile->original_name,
            $headers
        )->setAutoEtag()->setAutoLastModified();
    }
}
