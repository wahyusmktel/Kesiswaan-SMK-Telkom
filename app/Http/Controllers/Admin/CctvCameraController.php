<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CctvCamera;
use App\Models\User;
use App\Services\MediaMtxService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CctvCameraController extends Controller
{
    public function index(MediaMtxService $mediaMtx)
    {
        $cameras = CctvCamera::withCount('users')
            ->with(['users:id,name,email'])
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(12);

        $users = User::query()
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'Student']))
            ->with('roles:id,name')
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return view('pages.admin.cctv.index', [
            'cameras' => $cameras,
            'users' => $users,
            'gateway' => $mediaMtx->health(),
            'stats' => [
                'active_cameras' => CctvCamera::active()->count(),
                'authorized_users' => CctvCamera::query()
                    ->join('cctv_camera_user', 'cctv_cameras.id', '=', 'cctv_camera_user.cctv_camera_id')
                    ->distinct()
                    ->count('cctv_camera_user.user_id'),
            ],
        ]);
    }

    public function store(Request $request, MediaMtxService $mediaMtx)
    {
        $validated = $request->validate($this->rules(true));
        $this->ensureUsersAreEligible($validated['user_ids'] ?? []);

        $camera = CctvCamera::create([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name']),
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'rtsp_url' => $validated['rtsp_url'],
            'stream_path' => 'cctv-'.Str::lower(Str::random(12)),
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'last_sync_status' => 'pending',
        ]);
        $camera->users()->sync($validated['user_ids'] ?? []);

        $message = 'Kamera berhasil ditambahkan.';
        if ($camera->is_active) {
            $message .= $this->attemptSync($camera, $mediaMtx);
        }

        return back()->with('success', $message);
    }

    public function update(Request $request, CctvCamera $camera, MediaMtxService $mediaMtx)
    {
        $validated = $request->validate($this->rules(false));
        $this->ensureUsersAreEligible($validated['user_ids'] ?? []);

        $wasActive = $camera->is_active;
        $camera->fill([
            'name' => $validated['name'],
            'slug' => $this->uniqueSlug($validated['name'], $camera),
            'location' => $validated['location'] ?? null,
            'description' => $validated['description'] ?? null,
            'is_active' => $request->boolean('is_active'),
            'sort_order' => $validated['sort_order'] ?? 0,
            'last_sync_status' => 'pending',
        ]);

        if (filled($validated['rtsp_url'] ?? null)) {
            $camera->rtsp_url = $validated['rtsp_url'];
        }

        $camera->save();
        $camera->users()->sync($validated['user_ids'] ?? []);

        $message = 'Kamera berhasil diperbarui.';
        if ($camera->is_active) {
            $message .= $this->attemptSync($camera, $mediaMtx);
        } elseif ($wasActive) {
            try {
                $mediaMtx->remove($camera);
                $camera->update([
                    'last_sync_status' => 'disabled',
                    'last_sync_message' => 'Stream dinonaktifkan.',
                    'last_synced_at' => now(),
                ]);
            } catch (\Throwable $exception) {
                Log::warning('Gagal menonaktifkan path CCTV di MediaMTX.', [
                    'camera_id' => $camera->id,
                    'message' => $exception->getMessage(),
                ]);
                $message .= ' Stream lokal tersimpan, tetapi gateway belum dapat dihubungi.';
            }
        }

        return back()->with('success', $message);
    }

    public function destroy(CctvCamera $camera, MediaMtxService $mediaMtx)
    {
        try {
            $mediaMtx->remove($camera);
        } catch (\Throwable $exception) {
            Log::warning('Gagal menghapus path CCTV di MediaMTX.', [
                'camera_id' => $camera->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $camera->delete();

        return back()->with('success', 'Kamera dan seluruh hak aksesnya berhasil dihapus.');
    }

    public function sync(CctvCamera $camera, MediaMtxService $mediaMtx)
    {
        if (! $camera->is_active) {
            return back()->with('error', 'Aktifkan kamera sebelum melakukan sinkronisasi.');
        }

        try {
            $mediaMtx->sync($camera);

            return back()->with('success', 'Kamera berhasil disinkronkan ke MediaMTX.');
        } catch (\Throwable $exception) {
            $this->markSyncFailed($camera, $exception);

            return back()->with('error', 'Sinkronisasi gagal. Periksa status MediaMTX dan URL RTSP kamera.');
        }
    }

    public function syncAll(MediaMtxService $mediaMtx)
    {
        $success = 0;
        $failed = 0;

        CctvCamera::active()->each(function (CctvCamera $camera) use ($mediaMtx, &$success, &$failed) {
            try {
                $mediaMtx->sync($camera);
                $success++;
            } catch (\Throwable $exception) {
                $this->markSyncFailed($camera, $exception);
                $failed++;
            }
        });

        $message = "{$success} kamera berhasil disinkronkan";
        if ($failed > 0) {
            $message .= ", {$failed} kamera gagal.";
        } else {
            $message .= '.';
        }

        return back()->with($failed > 0 ? 'error' : 'success', $message);
    }

    private function rules(bool $rtspRequired): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'location' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:500'],
            'rtsp_url' => [$rtspRequired ? 'required' : 'nullable', 'string', 'max:2048', 'regex:#^rtsps?://#i'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'is_active' => ['nullable', 'boolean'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['integer', 'exists:users,id'],
        ];
    }

    private function ensureUsersAreEligible(array $userIds): void
    {
        if ($userIds === []) {
            return;
        }

        $studentCount = User::whereIn('id', $userIds)
            ->whereHas('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'Student']))
            ->count();

        if ($studentCount > 0) {
            throw ValidationException::withMessages([
                'user_ids' => 'Akses CCTV tidak dapat diberikan kepada pengguna dengan role siswa.',
            ]);
        }
    }

    private function uniqueSlug(string $name, ?CctvCamera $except = null): string
    {
        $base = Str::slug($name) ?: 'kamera';
        $slug = $base;
        $counter = 2;

        while (CctvCamera::where('slug', $slug)
            ->when($except, fn ($query) => $query->whereKeyNot($except->getKey()))
            ->exists()) {
            $slug = $base.'-'.$counter++;
        }

        return $slug;
    }

    private function attemptSync(CctvCamera $camera, MediaMtxService $mediaMtx): string
    {
        try {
            $mediaMtx->sync($camera);

            return ' Stream sudah aktif di gateway.';
        } catch (\Throwable $exception) {
            $this->markSyncFailed($camera, $exception);

            return ' Data tersimpan, tetapi gateway belum dapat disinkronkan.';
        }
    }

    private function markSyncFailed(CctvCamera $camera, \Throwable $exception): void
    {
        $camera->forceFill([
            'last_sync_status' => 'failed',
            'last_sync_message' => 'Gateway tidak dapat menerapkan konfigurasi.',
            'last_synced_at' => now(),
        ])->save();

        Log::warning('Sinkronisasi CCTV ke MediaMTX gagal.', [
            'camera_id' => $camera->id,
            'message' => $exception->getMessage(),
        ]);
    }
}
