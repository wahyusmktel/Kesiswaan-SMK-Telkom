<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RepositoryFile;
use App\Models\RepositorySetting;
use App\Services\RepositoryUrlService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class RepositoryController extends Controller
{
    public function index(Request $request, RepositoryUrlService $urlService)
    {
        $settings = RepositorySetting::current();
        $search = trim((string) $request->query('search'));

        $files = RepositoryFile::query()
            ->with('uploader:id,name')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('title', 'like', "%{$search}%")
                        ->orWhere('original_name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%");
                });
            })
            ->latest('published_at')
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        $links = $files->getCollection()->mapWithKeys(
            fn (RepositoryFile $file) => [$file->id => $urlService->linksFor($file, $settings)]
        );

        $stats = [
            'files' => RepositoryFile::count(),
            'active' => RepositoryFile::where('is_active', true)->count(),
            'bytes' => (int) RepositoryFile::sum('size'),
        ];
        $stats['human_bytes'] = RepositoryFile::make(['size' => $stats['bytes']])->human_size;

        return view('pages.super-admin.repository.index', compact(
            'files', 'links', 'settings', 'stats', 'search'
        ));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'local_base_url' => ['nullable', 'url:http,https', 'max:255'],
            'public_base_url' => ['required', 'url:http,https', 'max:255'],
        ]);

        $settings = RepositorySetting::query()->firstOrNew();
        $settings->fill([
            'local_base_url' => $this->normalizeUrl($validated['local_base_url'] ?? null),
            'public_base_url' => $this->normalizeUrl($validated['public_base_url']),
        ])->save();

        return back()->with('success', 'Alamat repository lokal dan publik berhasil disimpan.');
    }

    public function update(Request $request, RepositoryFile $repositoryFile)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $repositoryFile->update([
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'published_at' => $request->boolean('is_active')
                ? ($repositoryFile->published_at ?? now())
                : $repositoryFile->published_at,
        ]);

        return back()->with('success', 'Informasi file berhasil diperbarui.');
    }

    public function destroy(RepositoryFile $repositoryFile)
    {
        Storage::disk(config('repository.disk'))->delete($repositoryFile->path);
        $repositoryFile->delete();

        return back()->with('success', 'File repository berhasil dihapus permanen.');
    }

    private function normalizeUrl(?string $url): ?string
    {
        return $url ? rtrim(trim($url), '/') : null;
    }
}
