<?php

namespace App\Services;

use App\Models\RepositoryFile;
use App\Models\RepositorySetting;

class RepositoryUrlService
{
    public function linksFor(RepositoryFile $file, ?RepositorySetting $settings = null): array
    {
        $settings ??= RepositorySetting::current();
        $path = route('repository.download', $file, false);

        return [
            'public' => $this->join($settings->public_base_url ?: config('repository.public_base_url'), $path),
            'local' => $this->join($settings->local_base_url ?: config('repository.local_base_url'), $path),
        ];
    }

    private function join(?string $baseUrl, string $path): ?string
    {
        if (! $baseUrl) {
            return null;
        }

        return rtrim($baseUrl, '/').'/'.ltrim($path, '/');
    }
}
