<?php

namespace App\Services;

use App\Models\CctvCamera;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class MediaMtxService
{
    public function health(): array
    {
        try {
            $response = $this->client()->get('/v3/info');

            return [
                'online' => $response->successful(),
                'message' => $response->successful()
                    ? 'MediaMTX terhubung'
                    : 'MediaMTX merespons dengan HTTP '.$response->status(),
            ];
        } catch (\Throwable) {
            return ['online' => false, 'message' => 'MediaMTX belum dapat dihubungi'];
        }
    }

    public function sync(CctvCamera $camera): void
    {
        $path = rawurlencode($camera->stream_path);
        $payload = [
            'source' => $camera->rtsp_url,
            'sourceOnDemand' => true,
            'sourceOnDemandStartTimeout' => '15s',
            'sourceOnDemandCloseAfter' => '30s',
            'rtspTransport' => 'tcp',
        ];

        $exists = $this->client()->get("/v3/config/paths/get/{$path}")->successful();
        $response = $exists
            ? $this->client()->patch("/v3/config/paths/patch/{$path}", $payload)
            : $this->client()->post("/v3/config/paths/add/{$path}", $payload);

        if (! $response->successful()) {
            throw new RuntimeException('MediaMTX menolak konfigurasi kamera (HTTP '.$response->status().').');
        }

        $camera->forceFill([
            'last_sync_status' => 'synced',
            'last_sync_message' => 'Konfigurasi tersinkronisasi.',
            'last_synced_at' => now(),
        ])->save();
    }

    public function remove(CctvCamera $camera): void
    {
        $response = $this->client()->delete('/v3/config/paths/delete/'.rawurlencode($camera->stream_path));

        if (! $response->successful() && $response->status() !== 404) {
            throw new RuntimeException('MediaMTX gagal menghapus stream (HTTP '.$response->status().').');
        }
    }

    public function manifestUrl(CctvCamera $camera): string
    {
        return rtrim((string) config('services.cctv.hls_base_url'), '/')
            .'/'.rawurlencode($camera->stream_path).'/index.m3u8';
    }

    private function client(): PendingRequest
    {
        $request = Http::baseUrl(rtrim((string) config('services.cctv.mediamtx_api_url'), '/'))
            ->acceptJson()
            ->asJson()
            ->connectTimeout(3)
            ->timeout(10);

        $username = config('services.cctv.mediamtx_api_user');
        $password = config('services.cctv.mediamtx_api_password');

        return filled($username) ? $request->withBasicAuth($username, (string) $password) : $request;
    }
}
