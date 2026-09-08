<?php

namespace App\Services;

use App\Models\TelegramBot;
use App\Models\TelegramLog;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Models\WhatsappTemplate;
use Illuminate\Support\Facades\Http;
use Throwable;

class TelegramService
{
    public function verifyAndRegisterWebhook(TelegramBot $bot): array
    {
        try {
            $identityResponse = $this->request($bot, 'getMe');
            if (! $identityResponse->successful() || ! $identityResponse->json('ok')) {
                throw new \RuntimeException((string) $identityResponse->json('description', 'Token bot tidak valid.'));
            }
            $identity = $identityResponse->json('result');
            $webhook = $this->request($bot, 'setWebhook', [
                'url' => route('telegram.webhook', $bot->slug),
                'secret_token' => $bot->webhook_secret,
                'allowed_updates' => ['message'],
                'drop_pending_updates' => false,
            ]);
            if (! $webhook->successful() || ! $webhook->json('ok')) {
                throw new \RuntimeException((string) $webhook->json('description', 'Telegram menolak webhook.'));
            }

            $this->configureBotProfile($bot);

            $bot->update([
                'bot_username' => $identity['username'] ?? null,
                'status' => 'connected',
                'last_error' => null,
                'last_verified_at' => now(),
            ]);

            return ['success' => true, 'username' => $identity['username'] ?? null];
        } catch (Throwable $e) {
            $message = str_replace($bot->bot_token, '[TOKEN DISEMBUNYIKAN]', $e->getMessage());
            $bot->update(['status' => 'error', 'last_error' => $message]);

            return ['success' => false, 'message' => $message];
        }
    }

    public function sendTemplateNotification(
        TelegramBot $bot,
        User $user,
        string $eventKey,
        array $data,
        string $recipientName,
        string $logType,
        string $notificationDate,
        ?string $logEventKey = null,
    ): array {
        $template = WhatsappTemplate::where('event_key', $eventKey)->first();
        $link = TelegramUserLink::where('telegram_bot_id', $bot->id)->where('user_id', $user->id)->first();
        if (! $template?->is_enabled || ! $link) {
            return ['success' => false, 'message' => 'Template nonaktif atau akun Telegram pegawai belum terhubung.'];
        }

        $message = $template->template_text;
        foreach ($data as $key => $value) {
            $message = str_replace('{'.$key.'}', (string) $value, $message);
        }

        $log = TelegramLog::create([
            'telegram_bot_id' => $bot->id,
            'recipient_user_id' => $user->id,
            'chat_id' => $link->chat_id,
            'recipient_name' => $recipientName,
            'message' => $message,
            'type' => $logType,
            'event_key' => $logEventKey ?: $eventKey,
            'notification_date' => $notificationDate,
            'status' => 'pending',
        ]);

        try {
            $response = $this->request($bot, 'sendMessage', ['chat_id' => $link->chat_id, 'text' => $message]);
            if (! $response->successful() || ! $response->json('ok')) {
                throw new \RuntimeException((string) $response->json('description', 'Telegram menolak pesan.'));
            }
            $log->update(['status' => 'sent', 'sent_at' => now(), 'response_data' => $response->json()]);

            return ['success' => true, 'log' => $log];
        } catch (Throwable $e) {
            $error = str_replace($bot->bot_token, '[TOKEN DISEMBUNYIKAN]', $e->getMessage());
            $log->update(['status' => 'failed', 'error_message' => $error]);

            return ['success' => false, 'message' => $error, 'log' => $log];
        }
    }

    public function reply(TelegramBot $bot, string $chatId, string $text, ?array $replyMarkup = null): void
    {
        $payload = ['chat_id' => $chatId, 'text' => $text];
        if ($replyMarkup) {
            $payload['reply_markup'] = $replyMarkup;
        }
        $this->request($bot, 'sendMessage', $payload)->throw();
    }

    public function sendTestNotification(TelegramUserLink $link): array
    {
        $link->loadMissing(['bot', 'user']);
        $bot = $link->bot;
        if (! $bot?->is_active || $bot->status !== 'connected') {
            return ['success' => false, 'message' => 'Bot Telegram tidak aktif atau belum terhubung.'];
        }

        $message = "✅ TES NOTIFIKASI BERHASIL\n\nHalo {$link->user?->name}, akun Telegram Anda telah terhubung dengan SISFO melalui {$bot->name}.\n\nPesan ini adalah pengujian koneksi. Notifikasi rekap absensi dan kepegawaian akan dikirim melalui bot ini sesuai jadwal yang ditetapkan.\n\n_Sistem Informasi SMK Telkom Lampung_";
        $log = TelegramLog::create([
            'telegram_bot_id' => $bot->id,
            'recipient_user_id' => $link->user_id,
            'chat_id' => $link->chat_id,
            'recipient_name' => $link->user?->name,
            'message' => $message,
            'type' => 'connection_test',
            'event_key' => 'telegram_connection_test',
            'notification_date' => today(),
            'status' => 'pending',
        ]);

        try {
            $response = $this->request($bot, 'sendMessage', [
                'chat_id' => $link->chat_id,
                'text' => $message,
            ]);
            if (! $response->successful() || ! $response->json('ok')) {
                throw new \RuntimeException((string) $response->json('description', 'Telegram menolak pesan uji.'));
            }
            $log->update(['status' => 'sent', 'sent_at' => now(), 'response_data' => $response->json()]);

            return ['success' => true, 'message' => 'Pesan uji berhasil dikirim kepada '.$link->user?->name.'.'];
        } catch (Throwable $e) {
            $error = str_replace($bot->bot_token, '[TOKEN DISEMBUNYIKAN]', $e->getMessage());
            $log->update(['status' => 'failed', 'error_message' => $error]);

            return ['success' => false, 'message' => 'Pesan uji gagal dikirim: '.$error];
        }
    }

    private function request(TelegramBot $bot, string $method, array $payload = [])
    {
        return Http::asJson()->acceptJson()->timeout(20)->post(
            'https://api.telegram.org/bot'.$bot->bot_token.'/'.$method,
            $payload,
        );
    }

    private function configureBotProfile(TelegramBot $bot): void
    {
        $requests = [
            'setMyCommands' => [
                'commands' => [[
                    'command' => 'start',
                    'description' => 'Mulai dan hubungkan akun SISFO',
                ]],
            ],
            'setMyShortDescription' => [
                'short_description' => 'Notifikasi resmi kepegawaian SMK Telkom Lampung.',
            ],
            'setMyDescription' => [
                'description' => 'Selamat datang di '.$bot->name.'. Tekan Mulai, lalu bagikan nomor HP Telegram yang sama dengan nomor WhatsApp Anda di Group Sekolah untuk menerima notifikasi kepegawaian dari SISFO.',
            ],
        ];

        foreach ($requests as $method => $payload) {
            $response = $this->request($bot, $method, $payload);
            if (! $response->successful() || ! $response->json('ok')) {
                throw new \RuntimeException((string) $response->json('description', 'Telegram gagal memperbarui profil bot.'));
            }
        }
    }
}
