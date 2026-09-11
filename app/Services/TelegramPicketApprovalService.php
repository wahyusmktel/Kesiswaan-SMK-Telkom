<?php

namespace App\Services;

use App\Models\GuruIzin;
use App\Models\TelegramBot;
use App\Models\TelegramConversation;
use App\Models\TelegramUserLink;
use Throwable;

class TelegramPicketApprovalService
{
    private const FLOW = 'picket_leave_rejection';

    public function __construct(
        private readonly TelegramService $telegram,
        private readonly GuruPiketDutyService $duty,
        private readonly PicketTeacherLeaveDecisionService $decisions,
    ) {}

    public function handleCallback(TelegramBot $bot, TelegramUserLink $link, array $callback): void
    {
        $callbackId = (string) data_get($callback, 'id');
        $data = (string) data_get($callback, 'data');
        if (! preg_match('/^piket:(approve|reject):(\d+)$/', $data, $matches)) {
            $this->answer($bot, $callbackId, 'Perintah tidak dikenali.');

            return;
        }

        $link->loadMissing('user.roles');
        if ($bot->purpose !== 'employment' || ! $link->user || ! $this->duty->isOnDuty($link->user)) {
            $this->answer($bot, $callbackId, 'Akses ditolak: Anda tidak sedang bertugas sebagai Guru Piket hari ini.');

            return;
        }

        $izin = GuruIzin::find($matches[2]);
        if (! $izin || $izin->status_piket !== 'menunggu') {
            $this->answer($bot, $callbackId, 'Izin ini sudah diputuskan atau tidak tersedia.');

            return;
        }

        $chatId = (string) data_get($callback, 'message.chat.id', $link->chat_id);
        $messageId = (int) data_get($callback, 'message.message_id', 0);
        if ($matches[1] === 'reject') {
            TelegramConversation::updateOrCreate(
                ['telegram_user_link_id' => $link->id, 'flow' => self::FLOW],
                [
                    'telegram_bot_id' => $bot->id,
                    'step' => 'reason',
                    'payload' => ['guru_izin_id' => $izin->id, 'source_message_id' => $messageId],
                    'expires_at' => now()->addHour(),
                ],
            );
            $this->answer($bot, $callbackId, 'Silakan tulis alasan penolakan.');
            $this->telegram->reply($bot, $chatId, "Tuliskan alasan penolakan untuk pengajuan izin #{$izin->id} (minimal 5 karakter). Ketik /batal untuk membatalkan.", ['remove_keyboard' => true]);

            return;
        }

        $this->answer($bot, $callbackId, 'Persetujuan sedang diproses.');
        try {
            $result = $this->decisions->approve($izin, $link->user);
            $this->clearKeyboard($bot, $chatId, $messageId);
            $this->telegram->reply($bot, $chatId, "✅ Pengajuan izin #{$izin->id} berhasil disetujui.\n\n{$result['message']}", $this->telegram->linkedMenuMarkup($bot, $link->user));
        } catch (Throwable $error) {
            $this->telegram->reply($bot, $chatId, 'Keputusan tidak dapat diproses: '.$this->friendlyError($error), $this->telegram->linkedMenuMarkup($bot, $link->user));
        }
    }

    public function handleMessage(TelegramBot $bot, TelegramUserLink $link, array $message): bool
    {
        $text = trim((string) data_get($message, 'text', ''));
        $command = strtolower(explode('@', ltrim(strtok($text, ' ') ?: '', '/'), 2)[0]);
        $conversation = TelegramConversation::query()
            ->where('telegram_user_link_id', $link->id)
            ->where('flow', self::FLOW)
            ->first();

        if ($conversation?->expires_at?->isPast()) {
            $conversation->delete();
            $conversation = null;
        }

        if ($conversation) {
            if ($command === 'batal' || $text === '❌ Batalkan') {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Penolakan dibatalkan. Izin belum diputuskan.', $this->telegram->linkedMenuMarkup($bot, $link->user));

                return true;
            }
            if (mb_strlen($text) < 5) {
                $this->telegram->reply($bot, $link->chat_id, 'Alasan penolakan minimal 5 karakter. Silakan tulis kembali.');

                return true;
            }

            $link->loadMissing('user.roles');
            if (! $link->user || ! $this->duty->isOnDuty($link->user)) {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Penolakan tidak diproses karena Anda tidak sedang bertugas sebagai Guru Piket hari ini.');

                return true;
            }

            $izin = GuruIzin::find($conversation->payload['guru_izin_id'] ?? null);
            try {
                if (! $izin) {
                    throw new \RuntimeException('Pengajuan izin tidak ditemukan.');
                }
                $this->decisions->reject($izin, $link->user, $text);
                $this->clearKeyboard($bot, $link->chat_id, (int) ($conversation->payload['source_message_id'] ?? 0));
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, "❌ Pengajuan izin #{$izin->id} berhasil ditolak. Pemohon telah menerima notifikasi beserta catatan Anda.", $this->telegram->linkedMenuMarkup($bot, $link->user));
            } catch (Throwable $error) {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Keputusan tidak dapat diproses: '.$this->friendlyError($error), $this->telegram->linkedMenuMarkup($bot, $link->user));
            }

            return true;
        }

        if ($command !== 'persetujuan_piket' && $text !== '✅ Persetujuan Guru Piket') {
            return false;
        }

        $link->loadMissing('user.roles');
        if ($bot->purpose !== 'employment' || ! $link->user || ! $this->duty->isOnDuty($link->user)) {
            $markup = $link->user ? $this->telegram->linkedMenuMarkup($bot, $link->user) : ['remove_keyboard' => true];
            $this->telegram->reply($bot, $link->chat_id, 'Menu persetujuan hanya aktif bagi Guru Piket yang terjadwal hari ini.', $markup);

            return true;
        }

        $pending = GuruIzin::query()->with('guru')->whereIn('kategori_penyetujuan', ['sekolah', 'luar'])->where('status_piket', 'menunggu')->latest()->limit(10)->get();
        if ($pending->isEmpty()) {
            $this->telegram->reply($bot, $link->chat_id, 'Tidak ada pengajuan izin yang menunggu persetujuan Guru Piket.', $this->telegram->linkedMenuMarkup($bot, $link->user));

            return true;
        }

        $this->telegram->reply($bot, $link->chat_id, 'Terdapat '.$pending->count().' pengajuan yang menunggu. Pilih tindakan pada kartu berikut.');
        foreach ($pending as $izin) {
            $this->telegram->reply($bot, $link->chat_id, $this->summary($izin), [
                'inline_keyboard' => [[
                    ['text' => '✅ Setujui', 'callback_data' => 'piket:approve:'.$izin->id],
                    ['text' => '❌ Tolak', 'callback_data' => 'piket:reject:'.$izin->id],
                ]],
            ]);
        }

        return true;
    }

    private function summary(GuruIzin $izin): string
    {
        return "Pengajuan #{$izin->id}\n"
            .'Pemohon: '.($izin->guru?->nama_lengkap ?? '-')."\n"
            .'Kategori: '.$izin->categoryLabel()."\n"
            .'Jenis: '.$izin->jenis_izin."\n"
            .'Waktu: '.$izin->tanggal_mulai->format('d-m-Y H:i').' s.d. '.$izin->tanggal_selesai->format('d-m-Y H:i')."\n"
            .'Alasan: '.$izin->deskripsi;
    }

    private function answer(TelegramBot $bot, string $callbackId, string $text): void
    {
        if ($callbackId === '') {
            return;
        }
        try {
            $this->telegram->answerCallbackQuery($bot, $callbackId, $text);
        } catch (Throwable) {
        }
    }

    private function clearKeyboard(TelegramBot $bot, string $chatId, int $messageId): void
    {
        if ($messageId < 1) {
            return;
        }
        try {
            $this->telegram->clearInlineKeyboard($bot, $chatId, $messageId);
        } catch (Throwable) {
        }
    }

    private function friendlyError(Throwable $error): string
    {
        return $error->getMessage() ?: 'silakan muat ulang status izin.';
    }
}
