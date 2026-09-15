<?php

namespace App\Services;

use App\Models\GuruIzin;
use App\Models\TelegramBot;
use App\Models\TelegramUserLink;
use App\Models\User;

class TelegramTodayLeaveService
{
    public const MENU_LABEL = '📅 Pegawai Izin Hari Ini';

    public function __construct(
        private readonly TelegramService $telegram,
        private readonly GuruPiketDutyService $picketDuty,
    ) {}

    public function canView(User $user): bool
    {
        return $user->hasAnyRole(['KAUR SDM', 'Kepala Sekolah'])
            || $this->picketDuty->isOnDuty($user);
    }

    public function handle(TelegramBot $bot, TelegramUserLink $link, array $message): bool
    {
        $text = trim((string) data_get($message, 'text', ''));
        $command = strtolower(explode('@', ltrim(strtok($text, ' ') ?: '', '/'), 2)[0]);
        if ($command !== 'izin_hari_ini' && $text !== self::MENU_LABEL) {
            return false;
        }

        $link->loadMissing('user.roles');
        $user = $link->user;
        if ($bot->purpose !== 'employment' || ! $user || ! $this->canView($user)) {
            $this->telegram->reply($bot, $link->chat_id,
                'Daftar pegawai izin hari ini hanya tersedia untuk KAUR SDM, Kepala Sekolah, atau Guru Piket yang bertugas hari ini.',
                $user ? $this->telegram->linkedMenuMarkup($bot, $user) : ['remove_keyboard' => true]);

            return true;
        }

        $today = today();
        $leaves = GuruIzin::query()
            ->with('guru:id,nama_lengkap,employee_category')
            ->fullyApproved()
            ->where('tanggal_mulai', '<=', $today->copy()->endOfDay())
            ->where('tanggal_selesai', '>=', $today->copy()->startOfDay())
            ->orderBy('tanggal_mulai')
            ->get();

        if ($leaves->isEmpty()) {
            $this->telegram->reply($bot, $link->chat_id,
                '📅 Daftar Pegawai Izin Hari Ini · '.$today->locale('id')->translatedFormat('l, d F Y')."\n\nTidak ada pegawai dengan izin yang sudah disetujui untuk hari ini.",
                $this->telegram->linkedMenuMarkup($bot, $user));

            return true;
        }

        $lines = $leaves->map(function (GuruIzin $leave, int $index) use ($today) {
            $from = $leave->tanggal_mulai->lt($today) ? '00:00' : $leave->tanggal_mulai->format('H:i');
            $until = $leave->tanggal_selesai->gt($today->copy()->endOfDay()) ? '23:59' : $leave->tanggal_selesai->format('H:i');
            $name = $leave->guru?->nama_lengkap ?? 'Pegawai tidak ditemukan';
            $category = $leave->guru?->is_tpa ? 'TPA' : 'Guru';

            return ($index + 1).". {$name} ({$category})\n   {$leave->categoryLabel()} · {$leave->jenis_izin}\n   {$from}–{$until}";
        });

        $header = '📅 Daftar Pegawai Izin Hari Ini · '.$today->locale('id')->translatedFormat('l, d F Y')
            ."\nIzin disetujui: {$leaves->count()} pegawai\n\n";
        foreach ($lines->chunk(10) as $index => $chunk) {
            $this->telegram->reply($bot, $link->chat_id,
                ($index === 0 ? $header : "📅 Lanjutan daftar pegawai izin hari ini\n\n").$chunk->implode("\n\n"),
                $this->telegram->linkedMenuMarkup($bot, $user));
        }

        return true;
    }
}
