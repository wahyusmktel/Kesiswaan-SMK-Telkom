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
            $users = User::query()
                ->with('masterGuru.dapodikGuru')
                ->whereHas('masterGuru.dapodikGuru', fn ($dapodik) => $dapodik
                    ->whereNotNull('hp')
                    ->where('hp', '!=', ''))
                ->get(['id', 'name'])
                ->filter(fn ($user) => $this->normalizePhone($user->masterGuru?->dapodikGuru?->hp) === $phone);
            if ($users->isEmpty()) {
                $telegram->reply($telegramBot, $chatId, 'Nomor HP '.$this->displayPhone($phone).' belum ditemukan pada kolom HP Dapodik Guru yang terhubung ke akun SISFO. Hubungi Superadmin untuk memeriksa data Dapodik dan hubungan akun Anda.');

                return response()->json(['ok' => true]);
            }
            if ($users->count() > 1) {
                $telegram->reply($telegramBot, $chatId, 'Nomor HP '.$this->displayPhone($phone).' digunakan oleh lebih dari satu data Dapodik Guru. Hubungi Superadmin agar nomor duplikat diperbaiki.');

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

        $telegram->reply($telegramBot, $chatId, "Selamat datang di {$telegramBot->name}!\n\nAgar Anda dapat menerima rekap absensi, pengingat keterlambatan, dan notifikasi kepegawaian dari SISFO, tekan tombol Bagikan Nomor HP Saya di bawah ini. Nomor Telegram harus sama dengan kolom HP pada Dapodik Guru Anda.\n\nGunakan tombol tersebut agar Telegram mengirim nomor milik Anda secara aman; jangan mengirim kontak secara manual.", [
            'keyboard' => [[['text' => '📱 Bagikan Nomor HP Saya', 'request_contact' => true]]],
            'resize_keyboard' => true,
            'one_time_keyboard' => true,
            'input_field_placeholder' => 'Tekan tombol untuk menghubungkan akun SISFO',
        ]);

        return response()->json(['ok' => true]);
    }

    private function normalizePhone(?string $phone): string
    {
        $phone = preg_replace('/\D/', '', (string) $phone);
        if (str_starts_with($phone, '620')) {
            return '62'.substr($phone, 3);
        }
        if (str_starts_with($phone, '0')) {
            return '62'.substr($phone, 1);
        }
        if (str_starts_with($phone, '8')) {
            return '62'.$phone;
        }

        return $phone;
    }

    private function displayPhone(string $phone): string
    {
        return str_starts_with($phone, '62') ? '+'.$phone : $phone;
    }
}
