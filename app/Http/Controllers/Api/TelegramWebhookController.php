<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TelegramBot;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Services\TelegramPicketApprovalService;
use App\Services\TelegramService;
use App\Services\TelegramTeacherLeaveService;
use Illuminate\Http\Request;

class TelegramWebhookController extends Controller
{
    public function __invoke(Request $request, TelegramBot $telegramBot, TelegramService $telegram, TelegramTeacherLeaveService $teacherLeave, TelegramPicketApprovalService $picketApproval)
    {
        abort_unless($telegramBot->is_active && hash_equals($telegramBot->webhook_secret, (string) $request->header('X-Telegram-Bot-Api-Secret-Token')), 403);
        $telegram->beginWebhookReply();
        $teacherLeave->beginWebhookReply();

        $callback = $request->input('callback_query');
        if (is_array($callback) && data_get($callback, 'message.chat.type') === 'private') {
            $chatId = (string) data_get($callback, 'message.chat.id');
            $fromId = (string) data_get($callback, 'from.id');
            $link = TelegramUserLink::query()
                ->where('telegram_bot_id', $telegramBot->id)
                ->where(fn ($query) => $query->where('chat_id', $chatId)->orWhere('telegram_user_id', $fromId))
                ->with(['user.roles', 'user.masterGuru'])
                ->first();
            if ($link) {
                $picketApproval->handleCallback($telegramBot, $link, $callback);
            } else {
                $telegram->answerCallbackQuery($telegramBot, (string) data_get($callback, 'id'), 'Hubungkan akun SISFO Anda terlebih dahulu.');
            }

            return $this->telegramResponse($telegram);
        }

        $message = $request->input('message');
        if (! is_array($message) || data_get($message, 'chat.type') !== 'private') {
            return $this->telegramResponse($telegram);
        }

        $chatId = (string) data_get($message, 'chat.id');
        $fromId = (string) data_get($message, 'from.id');
        $linkQuery = TelegramUserLink::query()
            ->where('telegram_bot_id', $telegramBot->id)
            ->with(['user.roles', 'user.masterGuru']);
        $existingLink = (clone $linkQuery)->where('chat_id', $chatId)->first();
        if (! $existingLink && $fromId !== '') {
            $existingLink = (clone $linkQuery)->where('telegram_user_id', $fromId)->first();
        }
        if ($existingLink) {
            if (! $existingLink->last_interaction_at || $existingLink->last_interaction_at->lt(now()->subMinutes(5))) {
                $existingLink->forceFill(['last_interaction_at' => now()])->saveQuietly();
            }
            if (! $picketApproval->handleMessage($telegramBot, $existingLink, $message)) {
                $teacherLeave->handle($telegramBot, $existingLink, $message);
            }

            return response()->json($teacherLeave->takeWebhookReply() ?? ['ok' => true]);
        }

        $contact = data_get($message, 'contact');
        if (is_array($contact)) {
            if ((string) ($contact['user_id'] ?? '') !== $fromId) {
                $telegram->reply($telegramBot, $chatId, 'Demi keamanan, silakan bagikan nomor Telegram Anda sendiri melalui tombol yang tersedia.');

                return $this->telegramResponse($telegram);
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

                return $this->telegramResponse($telegram);
            }
            if ($users->count() > 1) {
                $telegram->reply($telegramBot, $chatId, 'Nomor HP '.$this->displayPhone($phone).' digunakan oleh lebih dari satu data Dapodik Guru. Hubungi Superadmin agar nomor duplikat diperbaiki.');

                return $this->telegramResponse($telegram);
            }

            $user = $users->first();
            TelegramUserLink::where('telegram_bot_id', $telegramBot->id)->where('chat_id', $chatId)->where('user_id', '!=', $user->id)->delete();
            $link = TelegramUserLink::updateOrCreate(
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
            $telegram->markAccountLinked($telegramBot, $link->chat_id, $user);
            $telegram->reply($telegramBot, $chatId, 'Akun Telegram berhasil terhubung dengan SISFO atas nama '.$user->name.'. Notifikasi dari '.$telegramBot->name.' sekarang dapat diterima.', $telegram->linkedMenuMarkup($telegramBot, $user));

            return $this->telegramResponse($telegram);
        }

        $telegram->sendOnboarding($telegramBot, $chatId);

        return $this->telegramResponse($telegram);
    }

    private function telegramResponse(TelegramService $telegram)
    {
        return response()->json($telegram->takeWebhookReply() ?? ['ok' => true]);
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
