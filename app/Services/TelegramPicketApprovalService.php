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

    public function handleCallback(TelegramBot $bot, TelegramUserLink $link, array $callback, bool $acknowledge = true): void
    {
        $callbackId = (string) data_get($callback, 'id');
        $data = (string) data_get($callback, 'data');
        if (! preg_match('/^(piket|kurikulum|sdm|kepsek):(approve|reject):(\d+)$/', $data, $matches)) {
            $this->answerWhenRequested($acknowledge, $bot, $callbackId, 'Perintah tidak dikenali.');

            return;
        }

        $link->loadMissing('user.roles');
        $stage = $matches[1];
        if ($bot->purpose !== 'employment' || ! $link->user || ! $this->authorized($stage, $link->user)) {
            $this->answerWhenRequested($acknowledge, $bot, $callbackId, 'Akses ditolak: akun atau jadwal tugas Anda tidak sesuai tahap persetujuan ini.');

            return;
        }

        $izin = GuruIzin::find($matches[3]);
        if (! $izin || ! $this->isPending($stage, $izin)) {
            $this->answerWhenRequested($acknowledge, $bot, $callbackId, 'Izin ini sudah diputuskan atau tidak tersedia.');

            return;
        }

        $chatId = (string) data_get($callback, 'message.chat.id', $link->chat_id);
        $messageId = (int) data_get($callback, 'message.message_id', 0);
        if ($matches[2] === 'reject') {
            TelegramConversation::updateOrCreate(
                ['telegram_user_link_id' => $link->id, 'flow' => self::FLOW],
                [
                    'telegram_bot_id' => $bot->id,
                    'step' => 'reason',
                    'payload' => ['guru_izin_id' => $izin->id, 'stage' => $stage, 'source_message_id' => $messageId],
                    'expires_at' => now()->addHour(),
                ],
            );
            $this->answerWhenRequested($acknowledge, $bot, $callbackId, 'Silakan tulis alasan penolakan.');
            $this->telegram->reply($bot, $chatId, "Tuliskan alasan penolakan untuk pengajuan izin #{$izin->id} (minimal 5 karakter). Ketik /batal untuk membatalkan.", ['remove_keyboard' => true]);

            return;
        }

        $this->answerWhenRequested($acknowledge, $bot, $callbackId, 'Persetujuan sedang diproses.');
        try {
            $result = $this->approve($stage, $izin, $link->user);
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
            $stage = (string) ($conversation->payload['stage'] ?? 'piket');
            if (! $link->user || ! $this->authorized($stage, $link->user)) {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Penolakan tidak diproses karena akun atau jadwal tugas Anda tidak lagi sesuai tahap persetujuan ini.');

                return true;
            }

            $izin = GuruIzin::find($conversation->payload['guru_izin_id'] ?? null);
            try {
                if (! $izin) {
                    throw new \RuntimeException('Pengajuan izin tidak ditemukan.');
                }
                $this->reject($stage, $izin, $link->user, $text);
                $this->clearKeyboard($bot, $link->chat_id, (int) ($conversation->payload['source_message_id'] ?? 0));
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, "❌ Pengajuan izin #{$izin->id} berhasil ditolak. Pemohon telah menerima notifikasi beserta catatan Anda.", $this->telegram->linkedMenuMarkup($bot, $link->user));
            } catch (Throwable $error) {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Keputusan tidak dapat diproses: '.$this->friendlyError($error), $this->telegram->linkedMenuMarkup($bot, $link->user));
            }

            return true;
        }

        $stage = match (true) {
            $command === 'persetujuan_piket' || $text === '✅ Persetujuan Guru Piket' => 'piket',
            $command === 'persetujuan_kurikulum' || $text === '✅ Persetujuan Waka Kurikulum' => 'kurikulum',
            $command === 'persetujuan_sdm' || $text === '✅ Persetujuan KAUR SDM' => 'sdm',
            $command === 'persetujuan_kepsek' || $text === '✅ Persetujuan Kepala Sekolah' => 'kepsek',
            default => null,
        };
        if (! $stage) {
            return false;
        }

        $link->loadMissing('user.roles');
        if ($bot->purpose !== 'employment' || ! $link->user || ! $this->authorized($stage, $link->user)) {
            $markup = $link->user ? $this->telegram->linkedMenuMarkup($bot, $link->user) : ['remove_keyboard' => true];
            $this->telegram->reply($bot, $link->chat_id, 'Menu persetujuan tidak tersedia karena akun atau jadwal tugas Anda tidak sesuai tahap tersebut.', $markup);

            return true;
        }

        $pendingQuery = GuruIzin::query()->with('guru');
        match ($stage) {
            'piket' => $pendingQuery->whereIn('kategori_penyetujuan', ['sekolah', 'luar'])->where('status_piket', 'menunggu'),
            'kurikulum' => $pendingQuery->where('kategori_penyetujuan', 'luar')->where('status_piket', 'disetujui')->where('status_kurikulum', 'menunggu'),
            'sdm' => $pendingQuery->where(fn ($approval) => $approval
                ->whereIn('kategori_penyetujuan', ['luar', 'tidak_masuk', 'terlambat'])
                ->orWhere(fn ($school) => $school->where('kategori_penyetujuan', 'sekolah')
                    ->whereHas('guru', fn ($guru) => $guru->where('employee_category', \App\Models\MasterGuru::CATEGORY_TPA))))
                ->where('status_kurikulum', 'disetujui')->where('status_sdm', 'menunggu'),
            'kepsek' => $pendingQuery->where('status_sdm', 'disetujui')->where('status_kepala_sekolah', 'menunggu'),
        };
        $pending = $pendingQuery->latest()->limit(10)->get();
        if ($pending->isEmpty()) {
            $this->telegram->reply($bot, $link->chat_id, 'Tidak ada pengajuan izin yang menunggu pada tahap persetujuan Anda.', $this->telegram->linkedMenuMarkup($bot, $link->user));

            return true;
        }

        $this->telegram->reply($bot, $link->chat_id, 'Terdapat '.$pending->count().' pengajuan yang menunggu. Pilih tindakan pada kartu berikut.');
        foreach ($pending as $izin) {
            $this->telegram->reply($bot, $link->chat_id, $this->summary($izin), [
                'inline_keyboard' => [[
                    ['text' => '✅ Setujui', 'callback_data' => $stage.':approve:'.$izin->id],
                    ['text' => '❌ Tolak', 'callback_data' => $stage.':reject:'.$izin->id],
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

    private function authorized(string $stage, \App\Models\User $user): bool
    {
        return match ($stage) {
            'piket' => $this->duty->isOnDuty($user),
            'kurikulum' => $user->hasRole('Kurikulum'),
            'sdm' => $user->hasRole('KAUR SDM'),
            'kepsek' => $user->hasRole('Kepala Sekolah'),
            default => false,
        };
    }

    private function isPending(string $stage, GuruIzin $izin): bool
    {
        return match ($stage) {
            'piket' => in_array($izin->kategori_penyetujuan, ['sekolah', 'luar'], true) && $izin->status_piket === 'menunggu',
            'kurikulum' => $izin->kategori_penyetujuan === 'luar' && $izin->status_piket === 'disetujui' && $izin->status_kurikulum === 'menunggu',
            'sdm' => (in_array($izin->kategori_penyetujuan, ['luar', 'tidak_masuk', 'terlambat'], true) || ($izin->kategori_penyetujuan === 'sekolah' && $izin->guru?->is_tpa)) && $izin->status_kurikulum === 'disetujui' && $izin->status_sdm === 'menunggu',
            'kepsek' => $izin->status_sdm === 'disetujui' && $izin->status_kepala_sekolah === 'menunggu',
            default => false,
        };
    }

    private function approve(string $stage, GuruIzin $izin, \App\Models\User $user): array
    {
        return match ($stage) {
            'piket' => $this->decisions->approve($izin, $user),
            'kurikulum' => $this->decisions->approveKurikulum($izin, $user),
            'sdm' => $this->decisions->approveSdm($izin, $user),
            'kepsek' => $this->decisions->approveHeadmaster($izin, $user),
        };
    }

    private function reject(string $stage, GuruIzin $izin, \App\Models\User $user, string $note): array
    {
        return match ($stage) {
            'piket' => $this->decisions->reject($izin, $user, $note),
            'kurikulum' => $this->decisions->rejectKurikulum($izin, $user, $note),
            'sdm' => $this->decisions->rejectSdm($izin, $user, $note),
            'kepsek' => $this->decisions->rejectHeadmaster($izin, $user, $note),
        };
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

    private function answerWhenRequested(bool $acknowledge, TelegramBot $bot, string $callbackId, string $text): void
    {
        if ($acknowledge) {
            $this->answer($bot, $callbackId, $text);
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
        return $error instanceof \Symfony\Component\HttpKernel\Exception\HttpExceptionInterface
            ? ($error->getMessage() ?: 'izin ini sudah diputuskan oleh pejabat lain.')
            : 'terjadi gangguan saat menyimpan keputusan. Silakan coba kembali.';
    }
}
