<?php

namespace App\Services;

use App\Models\WhatsappDevice;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    /**
     * Send a WhatsApp message using the active or specified device.
     */
    public function sendMessage(string $recipient, string $message, string $type = 'general', ?int $deviceId = null): array
    {
        // 1. Get the device
        $device = null;
        if ($deviceId) {
            $device = WhatsappDevice::find($deviceId);
        } else {
            $device = WhatsappDevice::where('is_default', true)->where('is_active', true)->first()
                ?? WhatsappDevice::where('is_active', true)->first();
        }

        if (! $device) {
            return [
                'success' => false,
                'message' => 'Tidak ada perangkat WhatsApp aktif yang terdaftar.',
            ];
        }

        // 2. Format phone number: remove non-numeric, convert leading 0 to 62
        $formattedRecipient = preg_replace('/[^0-9]/', '', $recipient);
        if (str_starts_with($formattedRecipient, '0')) {
            $formattedRecipient = '62'.substr($formattedRecipient, 1);
        }

        // 3. Create initial log
        $log = WhatsappLog::create([
            'whatsapp_device_id' => $device->id,
            'recipient' => $formattedRecipient,
            'recipient_name' => null,
            'message' => $message,
            'type' => $type,
            'status' => 'pending',
        ]);

        // 4. Send request based on provider
        try {
            $response = $this->dispatchToProvider($device, $formattedRecipient, $message);

            if ($response['success']) {
                $log->update([
                    'status' => 'sent',
                    'sent_at' => now(),
                    'response_data' => $response['data'],
                ]);

                return [
                    'success' => true,
                    'message' => 'Pesan berhasil dikirim.',
                    'log' => $log,
                ];
            } else {
                $log->update([
                    'status' => 'failed',
                    'error_message' => $response['error'],
                    'response_data' => $response['data'] ?? null,
                ]);

                return [
                    'success' => false,
                    'message' => 'Gagal mengirim pesan: '.$response['error'],
                    'log' => $log,
                ];
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send error: '.$e->getMessage());
            $log->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat mengirim pesan.',
                'log' => $log,
            ];
        }
    }

    /**
     * Dispatch the message to the appropriate API provider.
     */
    private function dispatchToProvider(WhatsappDevice $device, string $recipient, string $message): array
    {
        $provider = $device->provider;
        $apiKey = $device->api_key;
        $serverUrl = $device->server_url;

        // Provider berbayar lama tetap dapat menggunakan mode demo.
        if ($provider !== 'node_baileys'
            && (empty($apiKey) || $apiKey === 'DEMO_API_KEY_SMK_TELKOM_2026')) {
            return [
                'success' => true,
                'data' => [
                    'simulation' => true,
                    'message' => 'Simulasi pengiriman berhasil (Demo API Key).',
                ],
            ];
        }

        switch ($provider) {
            case 'fonnte':
                $url = $serverUrl ?: 'https://api.fonnte.com/send';
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->asForm()->post($url, [
                    'target' => $recipient,
                    'message' => $message,
                    'countryCode' => '62',
                ]);

                $body = $response->json();
                if ($response->successful() && ($body['status'] ?? false)) {
                    return ['success' => true, 'data' => $body];
                }

                return [
                    'success' => false,
                    'error' => $body['reason'] ?? 'HTTP Error '.$response->status(),
                    'data' => $body,
                ];

            case 'wablas':
                $url = rtrim($serverUrl ?: 'https://api.wablas.com', '/').'/api/send-message';
                $response = Http::withHeaders([
                    'Authorization' => $apiKey,
                ])->asForm()->post($url, [
                    'phone' => $recipient,
                    'message' => $message,
                ]);

                $body = $response->json();
                if ($response->successful() && (($body['status'] ?? false) || ($body['status'] ?? '') === 'pending')) {
                    return ['success' => true, 'data' => $body];
                }

                return [
                    'success' => false,
                    'error' => $body['message'] ?? 'HTTP Error '.$response->status(),
                    'data' => $body,
                ];

            case 'node_baileys':
                $configuredUrl = $serverUrl ?: config('services.whatsapp_gateway.base_url');
                $url = str_ends_with(rtrim($configuredUrl, '/'), '/api/send')
                    ? $configuredUrl
                    : rtrim($configuredUrl, '/').'/api/send';
                $nodeApiKey = $apiKey ?: config('services.whatsapp_gateway.api_key');
                $client = Http::acceptJson()->timeout(30);
                if (filled($nodeApiKey)) {
                    $client = $client->withToken($nodeApiKey);
                }
                $response = $client->post($url, [
                    'session' => $device->session_id,
                    'to' => $recipient,
                    'message' => $message,
                ]);

                $body = $response->json();
                if ($response->successful() && ($body['success'] ?? ($body['status'] ?? false))) {
                    return ['success' => true, 'data' => $body];
                }

                return [
                    'success' => false,
                    'error' => $body['message'] ?? 'HTTP Error '.$response->status(),
                    'data' => $body,
                ];

            case 'custom_http':
            default:
                if (empty($serverUrl)) {
                    return ['success' => false, 'error' => 'Server URL tidak dikonfigurasi untuk Custom HTTP.'];
                }
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer '.$apiKey,
                ])->post($serverUrl, [
                    'to' => $recipient,
                    'message' => $message,
                    'session' => $device->session_id,
                ]);

                $body = $response->json();
                if ($response->successful()) {
                    return ['success' => true, 'data' => $body];
                }

                return [
                    'success' => false,
                    'error' => 'HTTP Error '.$response->status(),
                    'data' => $body,
                ];
        }
    }

    /**
     * Send a template-based notification.
     */
    public function sendTemplateNotification(string $recipient, string $eventKey, array $data, ?string $recipientName = null): array
    {
        $template = WhatsappTemplate::where('event_key', $eventKey)->first();
        if (! $template || ! $template->is_enabled) {
            return [
                'success' => false,
                'message' => "Template untuk event '{$eventKey}' tidak ditemukan atau dinonaktifkan.",
            ];
        }

        $message = $template->template_text;
        foreach ($data as $key => $val) {
            $message = str_replace('{'.$key.'}', $val, $message);
        }

        $result = $this->sendMessage($recipient, $message, $template->category);
        if ($result['success'] && $recipientName && isset($result['log'])) {
            $result['log']->update(['recipient_name' => $recipientName]);
        }

        return $result;
    }
}
