<?php

namespace App\Http\Controllers;

use App\Models\GoogleDriveConnection;
use App\Services\GoogleDriveService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class CloudGoogleDriveController extends Controller
{
    public function connect()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('cloud-files.google-drive.callback'))
            ->scopes($this->driveScopes())
            ->with([
                'access_type' => 'offline',
                'prompt' => 'consent',
                'include_granted_scopes' => 'true',
            ])
            ->redirect();
    }

    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('cloud-files.google-drive.callback'))
                ->user();
            $user = Auth::user();

            GoogleDriveConnection::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'google_id' => $googleUser->getId(),
                    'email' => $googleUser->getEmail(),
                    'access_token' => $googleUser->token,
                    'refresh_token' => $googleUser->refreshToken
                        ?: GoogleDriveConnection::where('user_id', $user->id)->value('refresh_token'),
                    'token_expires_at' => $googleUser->expiresIn ? now()->addSeconds((int) $googleUser->expiresIn) : null,
                    'scopes' => $this->driveScopes(),
                    'connected_at' => now(),
                ]
            );

            toast('Google Drive berhasil dihubungkan.', 'success');
        } catch (Exception $e) {
            return redirect()->route('cloud-files.index')
                ->with('error', 'Gagal menghubungkan Google Drive: ' . $e->getMessage());
        }

        return redirect()->route('cloud-files.index');
    }

    public function disconnect()
    {
        GoogleDriveConnection::where('user_id', Auth::id())->delete();

        toast('Koneksi Google Drive diputus.', 'success');
        return redirect()->route('cloud-files.index');
    }

    public function download(string $fileId, GoogleDriveService $drive)
    {
        try {
            $file = $drive->download(Auth::user(), $fileId);
        } catch (Exception $e) {
            return redirect()->route('cloud-files.index')
                ->with('error', 'Gagal mengunduh file Google Drive: ' . $e->getMessage());
        }

        return response($file['contents'], 200, [
            'Content-Type' => $file['mime_type'],
            'Content-Disposition' => 'attachment; filename="' . addslashes($file['name']) . '"',
        ]);
    }

    public function destroy(string $fileId, GoogleDriveService $drive)
    {
        try {
            $drive->delete(Auth::user(), $fileId);
            toast('File Google Drive berhasil dihapus.', 'success');
        } catch (Exception $e) {
            return redirect()->route('cloud-files.index')
                ->with('error', 'Gagal menghapus file Google Drive: ' . $e->getMessage());
        }

        return redirect()->route('cloud-files.index');
    }

    public function share(Request $request, string $fileId, GoogleDriveService $drive)
    {
        $data = $request->validate([
            'share_mode' => ['required', 'in:anyone,email'],
            'emails' => ['nullable', 'string', 'max:2000'],
        ]);

        $emails = collect(preg_split('/[\s,;]+/', $data['emails'] ?? '', -1, PREG_SPLIT_NO_EMPTY))
            ->map(fn ($email) => trim($email))
            ->filter(fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();

        if ($data['share_mode'] === 'email' && empty($emails)) {
            return response()->json([
                'message' => 'Isi minimal satu email undangan yang valid.',
            ], 422);
        }

        try {
            $file = $drive->share(Auth::user(), $fileId, $data['share_mode'], $emails);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Gagal mengatur berbagi file: ' . $e->getMessage(),
            ], 422);
        }

        return response()->json([
            'message' => 'Seting berbagi berhasil disimpan.',
            'link' => $file['webViewLink'] ?? null,
            'shared' => (bool) ($file['shared'] ?? true),
        ]);
    }

    private function driveScopes(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', config('services.google.drive_scopes')))));
    }
}
