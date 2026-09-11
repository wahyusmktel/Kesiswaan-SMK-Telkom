<?php

namespace App\Services;

use App\Models\GuruIzin;
use App\Models\TelegramUserLink;
use App\Notifications\PengajuanIzinGuruNotification;
use Illuminate\Support\Facades\Log;
use Throwable;

class TelegramLeaveNotificationService
{
    public function __construct(
        private readonly GuruPiketDutyService $duty,
        private readonly TelegramService $telegram,
    ) {}

    public function notifyPicketApprovers(GuruIzin $izin): void
    {
        $izin->loadMissing(['guru.user', 'jadwals.rombel.kelas', 'jadwals.mataPelajaran']);
        $message = 'Ada pengajuan Izin Guru baru dari '.($izin->guru?->nama_lengkap ?? 'pegawai');
        $url = route('piket.persetujuan-izin-guru.index');

        foreach ($this->duty->users() as $approver) {
            $approver->notify(new PengajuanIzinGuruNotification($izin, 'pending_approval', $message, $url));

            foreach ($this->employmentLinks($approver->id) as $link) {
                $text = "🔔 PERSETUJUAN IZIN GURU\n\n"
                    .'Pemohon: '.($izin->guru?->nama_lengkap ?? '-')."\n"
                    .'Kategori: '.$izin->categoryLabel()."\n"
                    .'Jenis: '.$izin->jenis_izin."\n"
                    .'Waktu: '.$izin->tanggal_mulai->format('d-m-Y H:i').' s.d. '.$izin->tanggal_selesai->format('d-m-Y H:i')."\n"
                    .'Alasan: '.$izin->deskripsi."\n\n"
                    .'Anda menerima pesan ini karena bertugas sebagai Guru Piket hari ini.';

                $this->safeReply($link, $text, [
                    'inline_keyboard' => [[
                        ['text' => '✅ Setujui', 'callback_data' => 'piket:approve:'.$izin->id],
                        ['text' => '❌ Tolak', 'callback_data' => 'piket:reject:'.$izin->id],
                    ]],
                ]);
            }
        }
    }

    public function notifyApplicant(GuruIzin $izin, string $message): void
    {
        $izin->loadMissing('guru.user');
        $user = $izin->guru?->user;
        if (! $user) {
            return;
        }

        $text = "📌 PROGRES PENGAJUAN IZIN #{$izin->id}\n\n{$message}\n\n"
            .'Kategori: '.$izin->categoryLabel()."\n"
            .'Jenis: '.$izin->jenis_izin."\n"
            .'Waktu: '.$izin->tanggal_mulai->format('d-m-Y H:i').' s.d. '.$izin->tanggal_selesai->format('d-m-Y H:i');

        foreach ($this->employmentLinks($user->id) as $link) {
            $this->safeReply($link, $text);
        }
    }

    private function employmentLinks(int $userId)
    {
        return TelegramUserLink::query()
            ->where('user_id', $userId)
            ->whereHas('bot', fn ($query) => $query
                ->where('purpose', 'employment')
                ->where('is_active', true)
                ->where('status', 'connected'))
            ->with('bot')
            ->get();
    }

    private function safeReply(TelegramUserLink $link, string $text, ?array $markup = null): void
    {
        try {
            $this->telegram->reply($link->bot, $link->chat_id, $text, $markup);
        } catch (Throwable $error) {
            Log::warning('Telegram teacher leave notification failed.', [
                'telegram_user_link_id' => $link->id,
                'error' => $error->getMessage(),
            ]);
        }
    }
}
