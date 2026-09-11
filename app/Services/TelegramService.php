<?php

namespace App\Services;

use App\Models\TelegramBot;
use App\Models\TelegramLog;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Models\WhatsappTemplate;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramService
{
    /** @var array<string, PendingRequest> */
    private array $clients = [];

    private bool $deferWebhookReply = false;

    private ?array $pendingWebhookReply = null;

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
                'allowed_updates' => ['message', 'callback_query'],
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

        if ($this->deferWebhookReply) {
            if ($this->pendingWebhookReply) {
                $pending = $this->pendingWebhookReply;
                $method = $pending['method'];
                unset($pending['method']);
                $this->request($bot, $method, $pending)->throw();
            }
            $this->pendingWebhookReply = ['method' => 'sendMessage'] + $payload;

            return;
        }

        $this->request($bot, 'sendMessage', $payload)->throw();
    }

    public function beginWebhookReply(): void
    {
        $this->deferWebhookReply = (bool) config('services.telegram.webhook_reply', true);
        $this->pendingWebhookReply = null;
    }

    public function takeWebhookReply(): ?array
    {
        $reply = $this->pendingWebhookReply;
        $this->pendingWebhookReply = null;
        $this->deferWebhookReply = false;

        return $reply;
    }

    public function sendOnboarding(TelegramBot $bot, string $chatId): void
    {
        $this->setChatCommands($bot, $chatId, [[
            'command' => 'start',
            'description' => 'Mulai dan hubungkan akun SISFO',
        ]]);
        $this->reply($bot, $chatId, "Selamat datang di {$bot->name}!\n\nAgar Anda dapat menerima rekap absensi, pengingat keterlambatan, dan notifikasi kepegawaian dari SISFO, tekan tombol Bagikan Nomor HP Saya di bawah ini. Nomor Telegram harus sama dengan nomor WhatsApp yang bergabung di Group Sekolah, apabila ada perbedaan silahkan hubungi admin.\n\nGunakan tombol tersebut agar Telegram mengirim nomor milik Anda secara aman; jangan mengirim kontak secara manual.", [
            'keyboard' => [[['text' => '📱 Bagikan Nomor HP Saya', 'request_contact' => true]]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'input_field_placeholder' => 'Tekan tombol untuk menghubungkan akun SISFO',
        ]);
    }

    public function markAccountLinked(TelegramBot $bot, string $chatId, ?User $user = null): void
    {
        $commands = [[
            'command' => 'status',
            'description' => 'Lihat status hubungan akun SISFO',
        ]];
        if ($bot->purpose === 'employment' && $user?->masterGuru && ($user->hasRole('Guru Kelas') || $user->masterGuru->is_tpa || $user->hasRole('KAUR SDM'))) {
            $commands[] = ['command' => 'izin', 'description' => 'Ajukan izin pegawai'];
            $commands[] = ['command' => 'status_izin', 'description' => 'Lihat status izin terakhir'];
            $commands[] = ['command' => 'batal', 'description' => 'Batalkan pengisian izin'];
        }
        if ($bot->purpose === 'employment' && $user?->hasRole('Guru Piket')) {
            $commands[] = ['command' => 'persetujuan_piket', 'description' => 'Lihat izin menunggu persetujuan Piket'];
        }
        if ($bot->purpose === 'employment' && $user?->hasRole('Kurikulum')) {
            $commands[] = ['command' => 'persetujuan_kurikulum', 'description' => 'Lihat izin menunggu persetujuan Kurikulum'];
        }
        if ($bot->purpose === 'employment' && $user?->hasRole('KAUR SDM')) {
            $commands[] = ['command' => 'persetujuan_sdm', 'description' => 'Lihat izin menunggu persetujuan SDM'];
        }
        if ($bot->purpose === 'employment' && $user?->hasRole('Kepala Sekolah')) {
            $commands[] = ['command' => 'persetujuan_kepsek', 'description' => 'Lihat izin menunggu persetujuan Kepala Sekolah'];
        }
        if ($bot->purpose === 'employment' && $user?->masterGuru) {
            $commands[] = ['command' => 'rekap_absensi', 'description' => 'Rekap fingerprint 7 hari terakhir'];
        }

        $commandsHash = hash('sha256', json_encode($commands, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
        $link = TelegramUserLink::query()
            ->where('telegram_bot_id', $bot->id)
            ->where('chat_id', $chatId)
            ->first(['id', 'commands_hash']);
        if ($link?->commands_hash === $commandsHash) {
            return;
        }

        $updateCommands = function () use ($bot, $chatId, $commands, $commandsHash, $link): void {
            try {
                $this->setChatCommands($bot, $chatId, $commands);
                if ($link) {
                    $link->forceFill(['commands_hash' => $commandsHash])->saveQuietly();
                }
            } catch (Throwable $error) {
                Log::warning('Telegram chat commands could not be refreshed.', [
                    'telegram_bot_id' => $bot->id,
                    'duration_context' => 'after_webhook_response',
                    'error' => $this->sanitizeError($bot, $error),
                ]);
            }
        };

        if ($this->deferWebhookReply) {
            app()->terminating($updateCommands);

            return;
        }

        $updateCommands();
    }

    public function linkedMenuMarkup(TelegramBot $bot, User $user): array
    {
        if ($bot->purpose !== 'employment' || (! $user->masterGuru && ! $user->hasRole('Guru Kelas') && ! $user->hasRole('Guru Piket'))) {
            return ['remove_keyboard' => true];
        }

        $rows = [];
        if ($user->masterGuru && ($user->hasRole('Guru Kelas') || $user->masterGuru->is_tpa || $user->hasRole('KAUR SDM'))) {
            $rows[] = [['text' => '📝 Ajukan Izin Pegawai']];
            $rows[] = [['text' => '📋 Status Izin Terakhir']];
        }
        if ($user->masterGuru) {
            $rows[] = [['text' => '📊 Rekap Fingerprint 7 Hari']];
        }
        if ($user->hasRole('Guru Piket')) {
            $rows[] = [['text' => '✅ Persetujuan Guru Piket']];
        }
        if ($user->hasRole('Kurikulum')) {
            $rows[] = [['text' => '✅ Persetujuan Waka Kurikulum']];
        }
        if ($user->hasRole('KAUR SDM')) {
            $rows[] = [['text' => '✅ Persetujuan KAUR SDM']];
        }
        if ($user->hasRole('Kepala Sekolah')) {
            $rows[] = [['text' => '✅ Persetujuan Kepala Sekolah']];
        }

        return [
            'keyboard' => $rows,
            'resize_keyboard' => true,
            'is_persistent' => true,
            'input_field_placeholder' => 'Pilih layanan SISFO',
        ];
    }

    public function answerCallbackQuery(TelegramBot $bot, string $callbackQueryId, string $text): void
    {
        $this->request($bot, 'answerCallbackQuery', [
            'callback_query_id' => $callbackQueryId,
            'text' => $text,
        ])->throw();
    }

    public function clearInlineKeyboard(TelegramBot $bot, string $chatId, int $messageId): void
    {
        $this->request($bot, 'editMessageReplyMarkup', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'reply_markup' => ['inline_keyboard' => []],
        ])->throw();
    }

    public function prepareAccountForRelinking(TelegramUserLink $link): array
    {
        $link->loadMissing(['bot', 'user']);
        $bot = $link->bot;
        if (! $bot?->is_active || $bot->status !== 'connected') {
            return ['success' => false, 'message' => 'Bot Telegram tidak aktif sehingga tombol hubungkan ulang belum dapat dikirim.'];
        }

        try {
            $this->sendOnboarding($bot, $link->chat_id);

            return ['success' => true, 'message' => 'Hubungan akun Telegram dilepas dan tombol hubungkan ulang telah dikirim.'];
        } catch (Throwable $e) {
            return [
                'success' => false,
                'message' => 'Hubungan akun dilepas, tetapi Telegram gagal menampilkan tombol hubungkan ulang: '.$this->sanitizeError($bot, $e),
            ];
        }
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
        $startedAt = microtime(true);

        try {
            return $this->client($bot)->post($method, $payload);
        } finally {
            $durationMs = (int) round((microtime(true) - $startedAt) * 1000);
            if ($durationMs >= config('services.telegram.slow_request_ms', 1500)) {
                Log::warning('Telegram Bot API responded slowly.', [
                    'telegram_bot_id' => $bot->id,
                    'method' => $method,
                    'duration_ms' => $durationMs,
                ]);
            }
        }
    }

    private function client(TelegramBot $bot): PendingRequest
    {
        $token = $bot->bot_token;
        $key = $bot->id.':'.hash('sha256', $token);
        if (isset($this->clients[$key])) {
            return $this->clients[$key];
        }

        $options = [];
        if (config('services.telegram.force_ipv4', true) && defined('CURLOPT_IPRESOLVE')) {
            $options['curl'] = [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4];
        }

        return $this->clients[$key] = Http::baseUrl('https://api.telegram.org/bot'.$token.'/')
            ->asJson()
            ->acceptJson()
            ->connectTimeout(config('services.telegram.connect_timeout', 3))
            ->timeout(config('services.telegram.timeout', 10))
            ->withOptions($options);
    }

    private function setChatCommands(TelegramBot $bot, string $chatId, array $commands): void
    {
        $response = $this->request($bot, 'setMyCommands', [
            'commands' => $commands,
            'scope' => ['type' => 'chat', 'chat_id' => $chatId],
        ]);
        if (! $response->successful() || ! $response->json('ok')) {
            throw new \RuntimeException((string) $response->json('description', 'Telegram gagal memperbarui menu akun.'));
        }
    }

    private function sanitizeError(TelegramBot $bot, Throwable $error): string
    {
        return str_replace($bot->bot_token, '[TOKEN DISEMBUNYIKAN]', $error->getMessage());
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
