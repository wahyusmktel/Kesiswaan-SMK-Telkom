<?php

namespace App\Services;

use App\Models\GoogleDriveConnection;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class GoogleDriveService
{
    public function connectionFor(User $user): ?GoogleDriveConnection
    {
        return GoogleDriveConnection::where('user_id', $user->id)->first();
    }

    public function listFiles(User $user, ?string $search = null): array
    {
        $token = $this->accessToken($user);
        $query = 'trashed = false';

        if ($search) {
            $safeSearch = str_replace(["'", '\\'], ["\\'", '\\\\'], $search);
            $query .= " and name contains '{$safeSearch}'";
        }

        $response = Http::withToken($token)
            ->acceptJson()
            ->get('https://www.googleapis.com/drive/v3/files', [
                'q' => $query,
                'pageSize' => 60,
                'orderBy' => 'modifiedTime desc',
                'fields' => 'files(id,name,mimeType,size,modifiedTime,webViewLink,iconLink,thumbnailLink,shared)',
                'supportsAllDrives' => 'true',
                'includeItemsFromAllDrives' => 'true',
            ])
            ->throw()
            ->json();

        return $response['files'] ?? [];
    }

    public function upload(User $user, UploadedFile $file): array
    {
        $token = $this->accessToken($user);
        $boundary = 'sisfo_drive_' . Str::random(24);
        $metadata = [
            'name' => $file->getClientOriginalName(),
            'appProperties' => [
                'uploaded_from' => config('app.name'),
                'uploaded_by_user_id' => (string) $user->id,
            ],
        ];

        $body = "--{$boundary}\r\n"
            . "Content-Type: application/json; charset=UTF-8\r\n\r\n"
            . json_encode($metadata) . "\r\n"
            . "--{$boundary}\r\n"
            . 'Content-Type: ' . ($file->getMimeType() ?: 'application/octet-stream') . "\r\n\r\n"
            . file_get_contents($file->getRealPath()) . "\r\n"
            . "--{$boundary}--";

        return Http::withToken($token)
            ->withHeaders(['Content-Type' => "multipart/related; boundary={$boundary}"])
            ->withBody($body, "multipart/related; boundary={$boundary}")
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,mimeType,size,webViewLink,modifiedTime')
            ->throw()
            ->json();
    }

    public function download(User $user, string $fileId): array
    {
        $token = $this->accessToken($user);
        $metadata = Http::withToken($token)
            ->acceptJson()
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'fields' => 'id,name,mimeType',
                'supportsAllDrives' => 'true',
            ])
            ->throw()
            ->json();

        $mimeType = $metadata['mimeType'] ?? 'application/octet-stream';
        $name = $metadata['name'] ?? 'google-drive-file';

        if (str_starts_with($mimeType, 'application/vnd.google-apps.')) {
            $exportMime = $this->exportMimeType($mimeType);
            $contents = Http::withToken($token)
                ->get("https://www.googleapis.com/drive/v3/files/{$fileId}/export", ['mimeType' => $exportMime])
                ->throw()
                ->body();

            return [
                'name' => $name . $this->exportExtension($exportMime),
                'mime_type' => $exportMime,
                'contents' => $contents,
            ];
        }

        $contents = Http::withToken($token)
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'alt' => 'media',
                'supportsAllDrives' => 'true',
            ])
            ->throw()
            ->body();

        return [
            'name' => $name,
            'mime_type' => $mimeType,
            'contents' => $contents,
        ];
    }

    public function delete(User $user, string $fileId): void
    {
        Http::withToken($this->accessToken($user))
            ->delete("https://www.googleapis.com/drive/v3/files/{$fileId}", ['supportsAllDrives' => 'true'])
            ->throw();
    }

    public function share(User $user, string $fileId, string $mode, array $emails = []): array
    {
        $token = $this->accessToken($user);

        if ($mode === 'anyone') {
            $this->ensureAnyonePermission($token, $fileId);
        } else {
            $this->removeAnyonePermissions($token, $fileId);

            foreach ($emails as $email) {
                $this->createEmailPermission($token, $fileId, $email);
            }
        }

        return Http::withToken($token)
            ->acceptJson()
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}", [
                'fields' => 'id,name,webViewLink,shared',
                'supportsAllDrives' => 'true',
            ])
            ->throw()
            ->json();
    }

    public function accessToken(User $user): string
    {
        $connection = $this->connectionFor($user);

        if (!$connection) {
            throw new RuntimeException('Google Drive belum terhubung.');
        }

        if (!$connection->token_expires_at || $connection->token_expires_at->gt(now()->addMinutes(2))) {
            return $connection->access_token;
        }

        if (!$connection->refresh_token) {
            throw new RuntimeException('Sesi Google Drive kedaluwarsa. Hubungkan ulang Google Drive.');
        }

        $payload = Http::asForm()
            ->post('https://oauth2.googleapis.com/token', [
                'client_id' => config('services.google.client_id'),
                'client_secret' => config('services.google.client_secret'),
                'refresh_token' => $connection->refresh_token,
                'grant_type' => 'refresh_token',
            ])
            ->throw()
            ->json();

        $connection->update([
            'access_token' => $payload['access_token'],
            'token_expires_at' => now()->addSeconds((int) ($payload['expires_in'] ?? 3600)),
            'scopes' => isset($payload['scope']) ? explode(' ', $payload['scope']) : $connection->scopes,
        ]);

        return $connection->fresh()->access_token;
    }

    private function exportMimeType(string $googleMimeType): string
    {
        return match ($googleMimeType) {
            'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            default => 'application/pdf',
        };
    }

    private function exportExtension(string $mimeType): string
    {
        return match ($mimeType) {
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => '.xlsx',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation' => '.pptx',
            default => '.pdf',
        };
    }

    private function ensureAnyonePermission(string $token, string $fileId): void
    {
        $permissions = $this->permissions($token, $fileId);
        $exists = collect($permissions)->contains(fn ($permission) => ($permission['type'] ?? null) === 'anyone');

        if ($exists) {
            return;
        }

        Http::withToken($token)
            ->acceptJson()
            ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                'type' => 'anyone',
                'role' => 'reader',
                'allowFileDiscovery' => false,
            ])
            ->throw();
    }

    private function removeAnyonePermissions(string $token, string $fileId): void
    {
        foreach ($this->permissions($token, $fileId) as $permission) {
            if (($permission['type'] ?? null) !== 'anyone') {
                continue;
            }

            Http::withToken($token)
                ->delete("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions/{$permission['id']}")
                ->throw();
        }
    }

    private function createEmailPermission(string $token, string $fileId, string $email): void
    {
        Http::withToken($token)
            ->acceptJson()
            ->post("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions?sendNotificationEmail=true", [
                'type' => 'user',
                'role' => 'reader',
                'emailAddress' => $email,
            ])
            ->throw();
    }

    private function permissions(string $token, string $fileId): array
    {
        $response = Http::withToken($token)
            ->acceptJson()
            ->get("https://www.googleapis.com/drive/v3/files/{$fileId}/permissions", [
                'fields' => 'permissions(id,type,emailAddress,role)',
                'supportsAllDrives' => 'true',
            ])
            ->throw()
            ->json();

        return $response['permissions'] ?? [];
    }
}
