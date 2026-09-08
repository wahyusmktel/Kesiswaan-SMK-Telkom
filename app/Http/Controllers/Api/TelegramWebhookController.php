<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Services\TelegramService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramBot $telegramBot, TelegramService $telegram)
    {
        abort_unless($telegramBot->is_active && hash_equals($telegramBot->webhook_secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token')), 403);

        $message = $request->input('message');
        if (! is_array($message) || data_get($message, 'chat.type') !== 'private') {
            return response()->json(['ok' => true]);
        }

        $chatId = (string) data_get($message, 'chat.id');
        $fromId = (string) data_get($message, 'from.id');
        $contact = data_get($message, 'contact');
        if (is_array($contact)) {
            if ((string) ($contact['user_id'] ?? '') !== $fromId) {
                $telegram->reply($telegramBot, $chatId, 'Demi keamanan, silakan bagikan nomor Telegram Anda sendiri melalui tombol yang tersedia.');

                return response()->json(['ok' => true]);
            }

            $phone = $this->normalizePhone((string) ($contact['phone_number'] ?? ''));
            $users = User::whereNotNull('phone_number')->get(['id', 'name', 'phone_number'])
                ->filter(fn ($user) => $this->normalizePhone($user->phone_number) === $phone);
            if ($users->count() !== 1) {
                $telegram->reply($telegramBot, $chatId, 'Nomor HP belum ditemukan atau digunakan lebih dari satu akun SISFO. Hubungi Superadmin untuk memperbaiki nomor HP akun Anda.');

                return response()->json(['ok' => true]);
            }

            $user = $users->first();
            TelegramUserLink::where('telegram_bot_id', $telegramBot->id)->where('chat_id', $chatId)->where('user_id', '!=', $user->id)->delete();
            TelegramUserLink::updateOrCreate(
                ['telegram_bot_id' => $telegramBot->id, 'user_id' => $user->id],
                [
                    'chat_id' => $chatId,
                    'telegram_user_id' => $fromId,
                    'telegram_username' => data_get($message, 'from.username'),
                    'telegram_name' => trim(data_get($message, 'from.first_name', '').' '.data_get($message, 'from.last_name', '')),
                    'linked_at' => now(),
                    'last_interaction_at' => now(),
                ],
            );
            $telegram->reply($telegramBot, $chatId, 'Akun Telegram berhasil terhubung dengan SISFO atas nama '.$user->name.'. Notifikasi dari '.$telegramBot->name.' sekarang dapat diterima.', ['remove_keyboard' => true]);

            return response()->json(['ok' => true]);
        }

        $telegram->reply($telegramBot, $chatId, 'Selamat datang di '.$telegramBot->name.'. Hubungkan akun Telegram dengan SISFO menggunakan nomor HP yang terdaftar.', [
            'keyboard' => [[['text' => 'Bagikan Nomor HP Saya', 'request_contact' => true]]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
        ]);

        return response()->json(['ok' => true]);
    }

    private function normalizePhone(?string $phone): string
    {
        $phone = preg_replace('/\D/', '', (string) $phone);
        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }

        return $phone;
    }
}
