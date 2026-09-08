<?php

namespace App\Services;

use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\LmsAssignment;
use App\Models\LmsMaterial;
use App\Models\TelegramBot;
use App\Models\TelegramConversation;
use App\Models\TelegramUserLink;
use App\Models\User;
use App\Notifications\PengajuanIzinGuruNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TelegramTeacherLeaveService
{
    private const FLOW = 'teacher_leave';

    public function __construct(private readonly TelegramService $telegram) {}

    public function handle(TelegramBot $bot, TelegramUserLink $link, array $message): void
    {
        $link->loadMissing('user.masterGuru');
        $user = $link->user;
        $chatId = (string) $link->chat_id;
        $text = trim((string) data_get($message, 'text', ''));
        $command = $this->command($text);
        $conversation = $this->conversation($link);

        if ($bot->purpose !== 'employment' || ! $user?->hasRole('Guru Kelas')) {
            $conversation?->delete();
            $this->telegram->markAccountLinked($bot, $chatId, $user);
            $this->telegram->reply(
                $bot,
                $chatId,
                'Akun Telegram Anda sudah terhubung dengan SISFO atas nama '.($user?->name ?? 'pegawai').'. Notifikasi dari '.$bot->name.' dalam keadaan aktif.',
                ['remove_keyboard' => true],
            );

            return;
        }

        if (in_array($command, ['start', 'menu', 'status'], true) || $text === '📋 Status Izin Terakhir') {
            if ($command === 'status' || $text === '📋 Status Izin Terakhir') {
                $this->sendLatestStatus($bot, $link);
            } else {
                $this->sendMenu($bot, $user, $chatId);
            }

            return;
        }

        if ($command === 'batal' || $text === '❌ Batalkan') {
            $conversation?->delete();
            $this->telegram->reply($bot, $chatId, 'Pengisian izin dibatalkan. Tidak ada pengajuan yang disimpan.', $this->telegram->linkedMenuMarkup($bot, $user));

            return;
        }

        if ($command === 'izin' || $text === '📝 Ajukan Izin Guru') {
            if (! $user->masterGuru) {
                $this->telegram->reply($bot, $chatId, 'Data Master Guru belum terhubung dengan akun Anda. Hubungi Superadmin sebelum mengajukan izin.');

                return;
            }
            $conversation?->delete();
            $conversation = TelegramConversation::create([
                'telegram_bot_id' => $bot->id,
                'telegram_user_link_id' => $link->id,
                'flow' => self::FLOW,
                'step' => 'category',
                'payload' => [],
                'expires_at' => now()->addHours(6),
            ]);
            $this->telegram->reply($bot, $chatId, "Pengajuan Izin Guru\n\nPilih kategori izin:", $this->keyboard([
                ['🏫 Lingkungan Sekolah'],
                ['🚗 Luar Sekolah / Tidak Masuk'],
                ['⏰ Datang Terlambat'],
                ['❌ Batalkan'],
            ]));

            return;
        }

        if (! $conversation) {
            $this->sendMenu($bot, $user, $chatId);

            return;
        }

        match ($conversation->step) {
            'category' => $this->receiveCategory($bot, $conversation, $chatId, $text),
            'type' => $this->receiveType($bot, $conversation, $chatId, $text),
            'start' => $this->receiveStart($bot, $conversation, $chatId, $text),
            'end' => $this->receiveEnd($bot, $conversation, $chatId, $text),
            'schedule_resource' => $this->receiveScheduleResource($bot, $conversation, $chatId, $text),
            'description' => $this->receiveDescription($bot, $conversation, $chatId, $text),
            'confirmation' => $this->receiveConfirmation($bot, $conversation, $link, $text),
            default => $this->resetInvalidConversation($bot, $conversation, $user, $chatId),
        };
    }

    private function receiveCategory(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        $category = match ($text) {
            '🏫 Lingkungan Sekolah' => 'sekolah',
            '🚗 Luar Sekolah / Tidak Masuk' => 'luar',
            '⏰ Datang Terlambat' => 'terlambat',
            default => null,
        };
        if (! $category) {
            $this->telegram->reply($bot, $chatId, 'Silakan pilih kategori melalui tombol yang tersedia.');

            return;
        }
        $this->advance($conversation, 'type', ['category' => $category]);
        $this->telegram->reply($bot, $chatId, 'Pilih jenis izin:', $this->keyboard([
            ['Sakit', 'Dinas'],
            ['Keperluan Pribadi', 'Lainnya'],
            ['❌ Batalkan'],
        ]));
    }

    private function receiveType(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        if (! in_array($text, ['Sakit', 'Dinas', 'Keperluan Pribadi', 'Lainnya'], true)) {
            $this->telegram->reply($bot, $chatId, 'Silakan pilih jenis izin melalui tombol yang tersedia.');

            return;
        }
        $this->advance($conversation, 'start', ['type' => $text]);
        $this->telegram->reply($bot, $chatId, "Ketik tanggal dan waktu mulai dengan format:\nDD-MM-YYYY HH:MM\n\nContoh: 10-09-2026 07:00", ['remove_keyboard' => true]);
    }

    private function receiveStart(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        $date = $this->parseDate($text);
        if (! $date) {
            $this->telegram->reply($bot, $chatId, 'Format belum benar. Contoh waktu mulai: 10-09-2026 07:00');

            return;
        }
        $this->advance($conversation, 'end', ['start' => $date->format('Y-m-d H:i:s')]);
        $this->telegram->reply($bot, $chatId, "Ketik tanggal dan waktu selesai dengan format:\nDD-MM-YYYY HH:MM\n\nContoh: 10-09-2026 16:00");
    }

    private function receiveEnd(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        $end = $this->parseDate($text);
        $start = Carbon::parse($conversation->payload['start']);
        if (! $end) {
            $this->telegram->reply($bot, $chatId, 'Format belum benar. Contoh waktu selesai: 10-09-2026 16:00');

            return;
        }
        if ($end->lt($start)) {
            $this->telegram->reply($bot, $chatId, 'Waktu selesai tidak boleh sebelum waktu mulai. Silakan ketik ulang.');

            return;
        }

        $schedules = $this->affectedSchedules($conversation->link->user->masterGuru->id, $start, $end);
        $this->advance($conversation, $schedules->isEmpty() ? 'description' : 'schedule_resource', [
            'end' => $end->format('Y-m-d H:i:s'),
            'schedule_ids' => $schedules->pluck('id')->all(),
            'schedule_index' => 0,
            'schedule_resources' => [],
        ]);
        if ($schedules->isEmpty()) {
            $this->askDescription($bot, $chatId);

            return;
        }
        $this->askScheduleResource($bot, $conversation->fresh(), $chatId);
    }

    private function receiveScheduleResource(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        $payload = $conversation->payload;
        $options = $payload['current_resource_options'] ?? [];
        $selected = filter_var($text, FILTER_VALIDATE_INT);
        if ($selected === false || ! isset($options[$selected - 1])) {
            $this->telegram->reply($bot, $chatId, 'Balas dengan nomor materi atau tugas yang tersedia.');

            return;
        }

        $scheduleId = $payload['schedule_ids'][$payload['schedule_index']];
        $resources = $payload['schedule_resources'] ?? [];
        $resources[(string) $scheduleId] = $options[$selected - 1];
        $nextIndex = $payload['schedule_index'] + 1;
        $this->advance($conversation, $nextIndex >= count($payload['schedule_ids']) ? 'description' : 'schedule_resource', [
            'schedule_resources' => $resources,
            'schedule_index' => $nextIndex,
            'current_resource_options' => [],
        ]);

        if ($nextIndex >= count($payload['schedule_ids'])) {
            $this->askDescription($bot, $chatId);
        } else {
            $this->askScheduleResource($bot, $conversation->fresh(), $chatId);
        }
    }

    private function receiveDescription(TelegramBot $bot, TelegramConversation $conversation, string $chatId, string $text): void
    {
        if (mb_strlen($text) < 5) {
            $this->telegram->reply($bot, $chatId, 'Alasan terlalu singkat. Tuliskan alasan minimal 5 karakter.');

            return;
        }
        if (mb_strlen($text) > 1000) {
            $this->telegram->reply($bot, $chatId, 'Alasan maksimal 1.000 karakter. Silakan ringkas alasan Anda.');

            return;
        }

        $this->advance($conversation, 'confirmation', ['description' => $text]);
        $payload = $conversation->fresh()->payload;
        $labels = ['sekolah' => 'Lingkungan Sekolah', 'luar' => 'Luar Sekolah / Tidak Masuk', 'terlambat' => 'Datang Terlambat'];
        $summary = "Periksa Pengajuan Izin\n\n"
            .'Kategori: '.$labels[$payload['category']]."\n"
            .'Jenis: '.$payload['type']."\n"
            .'Mulai: '.Carbon::parse($payload['start'])->format('d-m-Y H:i')."\n"
            .'Selesai: '.Carbon::parse($payload['end'])->format('d-m-Y H:i')."\n"
            .'Jadwal terdampak: '.count($payload['schedule_ids'] ?? [])."\n"
            .'Alasan: '.$payload['description']."\n\nKirim pengajuan ini?";
        $this->telegram->reply($bot, $chatId, $summary, $this->keyboard([
            ['✅ Kirim Pengajuan'],
            ['❌ Batalkan'],
        ]));
    }

    private function receiveConfirmation(TelegramBot $bot, TelegramConversation $conversation, TelegramUserLink $link, string $text): void
    {
        if ($text !== '✅ Kirim Pengajuan') {
            $this->telegram->reply($bot, $link->chat_id, 'Tekan Kirim Pengajuan untuk menyimpan atau Batalkan untuk membatalkan.');

            return;
        }

        $payload = $conversation->payload;
        $guru = $link->user->masterGuru;
        $start = Carbon::parse($payload['start']);
        $end = Carbon::parse($payload['end']);
        $overlap = GuruIzin::where('master_guru_id', $guru->id)
            ->where('tanggal_mulai', '<=', $end)
            ->where('tanggal_selesai', '>=', $start)
            ->where('status_piket', '!=', 'ditolak')
            ->where('status_kurikulum', '!=', 'ditolak')
            ->where('status_sdm', '!=', 'ditolak')
            ->exists();
        if ($overlap) {
            $conversation->delete();
            $this->telegram->reply($bot, $link->chat_id, 'Pengajuan tidak disimpan karena terdapat izin lain pada rentang waktu yang sama.', $this->telegram->linkedMenuMarkup($bot, $link->user));

            return;
        }

        $affected = $this->affectedSchedules($guru->id, $start, $end);
        if ($affected->pluck('id')->sort()->values()->all() !== collect($payload['schedule_ids'] ?? [])->sort()->values()->all()) {
            $conversation->delete();
            $this->telegram->reply($bot, $link->chat_id, 'Jadwal mengajar berubah selama pengisian. Silakan ajukan ulang agar data penugasan sesuai.', $this->telegram->linkedMenuMarkup($bot, $link->user));

            return;
        }
        foreach ($affected as $schedule) {
            if (! $this->resourceStillValid($schedule, $payload['schedule_resources'][(string) $schedule->id] ?? null)) {
                $conversation->delete();
                $this->telegram->reply($bot, $link->chat_id, 'Materi atau tugas LMS yang dipilih sudah berubah. Silakan ajukan ulang agar penugasan tetap valid.', $this->telegram->linkedMenuMarkup($bot, $link->user));

                return;
            }
        }

        $izin = DB::transaction(function () use ($guru, $payload) {
            $terlambat = $payload['category'] === 'terlambat';
            $izin = GuruIzin::create([
                'master_guru_id' => $guru->id,
                'tanggal_mulai' => $payload['start'],
                'tanggal_selesai' => $payload['end'],
                'jenis_izin' => $payload['type'],
                'kategori_penyetujuan' => $payload['category'],
                'deskripsi' => $payload['description'],
                'status_piket' => $terlambat ? 'disetujui' : 'menunggu',
                'status_kurikulum' => $terlambat ? 'disetujui' : 'menunggu',
                'status_sdm' => 'menunggu',
            ]);

            $pivot = [];
            foreach ($payload['schedule_resources'] ?? [] as $scheduleId => $resource) {
                $pivot[$scheduleId] = [
                    'lms_material_id' => $resource['type'] === 'material' ? $resource['id'] : null,
                    'lms_assignment_id' => $resource['type'] === 'assignment' ? $resource['id'] : null,
                ];
            }
            if ($pivot) {
                $izin->jadwals()->sync($pivot);
            }

            return $izin;
        });

        $conversation->delete();
        $this->notifyApprovers($izin, $guru->nama_lengkap);
        $this->telegram->reply(
            $bot,
            $link->chat_id,
            "✅ Pengajuan izin #{$izin->id} berhasil dikirim dan sedang menunggu persetujuan. Gunakan Status Izin Terakhir untuk memantau prosesnya.",
            $this->telegram->linkedMenuMarkup($bot, $link->user),
        );
    }

    private function askScheduleResource(TelegramBot $bot, TelegramConversation $conversation, string $chatId): void
    {
        $payload = $conversation->payload;
        $schedule = JadwalPelajaran::with(['rombel.kelas', 'mataPelajaran'])->find($payload['schedule_ids'][$payload['schedule_index']]);
        $materials = LmsMaterial::where('master_guru_id', $schedule->master_guru_id)
            ->where('rombel_id', $schedule->rombel_id)->where('mata_pelajaran_id', $schedule->mata_pelajaran_id)
            ->where('is_published', true)->orderBy('title')->get(['id', 'title']);
        $assignments = LmsAssignment::where('master_guru_id', $schedule->master_guru_id)
            ->where('rombel_id', $schedule->rombel_id)->where('mata_pelajaran_id', $schedule->mata_pelajaran_id)
            ->orderBy('title')->get(['id', 'title']);
        $options = $materials->map(fn ($item) => ['type' => 'material', 'id' => $item->id, 'label' => 'Materi: '.$item->title])
            ->concat($assignments->map(fn ($item) => ['type' => 'assignment', 'id' => $item->id, 'label' => 'Tugas: '.$item->title]))
            ->values()->all();

        if (! $options) {
            $conversation->delete();
            $this->telegram->reply($bot, $chatId, 'Pengajuan melalui bot dihentikan karena jadwal '.$this->scheduleName($schedule).' belum memiliki materi/tugas LMS. Tambahkan materi atau tugas di SISFO, lalu ajukan kembali.', $this->telegram->linkedMenuMarkup($bot, $conversation->link->user));

            return;
        }

        $this->advance($conversation, 'schedule_resource', ['current_resource_options' => $options]);
        $lines = collect($options)->map(fn ($option, $index) => ($index + 1).'. '.$option['label'])->implode("\n");
        $this->telegram->reply($bot, $chatId, 'Jadwal terdampak '.($payload['schedule_index'] + 1).'/'.count($payload['schedule_ids']).': '.$this->scheduleName($schedule)."\n\nPilih satu materi/tugas dengan membalas nomornya:\n{$lines}", ['remove_keyboard' => true]);
    }

    private function askDescription(TelegramBot $bot, string $chatId): void
    {
        $this->telegram->reply($bot, $chatId, 'Tuliskan alasan atau deskripsi izin Anda (minimal 5 karakter):', ['remove_keyboard' => true]);
    }

    private function sendMenu(TelegramBot $bot, User $user, string $chatId): void
    {
        $this->telegram->markAccountLinked($bot, $chatId, $user);
        $this->telegram->reply($bot, $chatId, 'Akun Telegram Anda sudah terhubung dengan SISFO atas nama '.$user->name.'. Silakan pilih layanan yang dibutuhkan.', $this->telegram->linkedMenuMarkup($bot, $user));
    }

    private function sendLatestStatus(TelegramBot $bot, TelegramUserLink $link): void
    {
        $izin = GuruIzin::where('master_guru_id', $link->user->masterGuru?->id)->latest()->first();
        if (! $izin) {
            $text = 'Belum ada riwayat pengajuan izin guru.';
        } else {
            $status = "Piket: {$izin->status_piket}\nKurikulum: {$izin->status_kurikulum}\nSDM: {$izin->status_sdm}";
            if ($izin->status_kepala_sekolah !== 'tidak_diperlukan') {
                $status .= "\nKepala Sekolah: {$izin->status_kepala_sekolah}";
            }
            $text = "Pengajuan Izin Terakhir #{$izin->id}\n{$izin->jenis_izin}\n".$izin->tanggal_mulai->format('d-m-Y H:i').' s.d. '.$izin->tanggal_selesai->format('d-m-Y H:i')."\n\n{$status}";
        }
        $this->telegram->reply($bot, $link->chat_id, $text, $this->telegram->linkedMenuMarkup($bot, $link->user));
    }

    private function affectedSchedules(int $guruId, Carbon $start, Carbon $end)
    {
        $days = ['Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa', 'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'];

        return JadwalPelajaran::where('master_guru_id', $guruId)
            ->inActiveAcademicPeriod()
            ->where('hari', $days[$start->format('l')])
            ->where('jam_mulai', '<', $end->format('H:i:s'))
            ->where('jam_selesai', '>', $start->format('H:i:s'))
            ->orderBy('jam_mulai')
            ->get();
    }

    private function notifyApprovers(GuruIzin $izin, string $teacherName): void
    {
        if ($izin->kategori_penyetujuan === 'terlambat') {
            $approvers = User::whereHas('roles', fn ($query) => $query->where('name', 'KAUR SDM'))->get();
            $message = 'Ada pengajuan Izin Terlambat baru dari '.$teacherName;
            $url = route('sdm.persetujuan-izin-guru.index');
        } else {
            $approvers = User::whereHas('roles', fn ($query) => $query->where('name', 'Guru Piket'))->get();
            $message = 'Ada pengajuan Izin Guru baru dari '.$teacherName;
            $url = route('piket.persetujuan-izin-guru.index');
        }
        foreach ($approvers as $approver) {
            $approver->notify(new PengajuanIzinGuruNotification($izin, 'pending_approval', $message, $url));
        }
    }

    private function resourceStillValid(JadwalPelajaran $schedule, ?array $resource): bool
    {
        if (! $resource || ! isset($resource['type'], $resource['id'])) {
            return false;
        }

        $model = match ($resource['type']) {
            'material' => LmsMaterial::query()->where('is_published', true),
            'assignment' => LmsAssignment::query(),
            default => null,
        };

        return $model?->whereKey($resource['id'])
            ->where('master_guru_id', $schedule->master_guru_id)
            ->where('rombel_id', $schedule->rombel_id)
            ->where('mata_pelajaran_id', $schedule->mata_pelajaran_id)
            ->exists() ?? false;
    }

    private function conversation(TelegramUserLink $link): ?TelegramConversation
    {
        $conversation = TelegramConversation::where('telegram_user_link_id', $link->id)->where('flow', self::FLOW)->first();
        if ($conversation?->expires_at?->isPast()) {
            $conversation->delete();

            return null;
        }

        return $conversation;
    }

    private function advance(TelegramConversation $conversation, string $step, array $values): void
    {
        $conversation->update([
            'step' => $step,
            'payload' => array_merge($conversation->payload ?? [], $values),
            'expires_at' => now()->addHours(6),
        ]);
    }

    private function parseDate(string $value): ?Carbon
    {
        foreach (['d-m-Y H:i', 'Y-m-d H:i'] as $format) {
            try {
                $date = Carbon::createFromFormat('!'.$format, $value);
                if ($date && $date->format($format) === $value) {
                    return $date;
                }
            } catch (\Throwable) {
                // Try the next accepted format.
            }
        }

        return null;
    }

    private function command(string $text): ?string
    {
        if (! str_starts_with($text, '/')) {
            return null;
        }

        return strtolower(explode('@', explode(' ', substr($text, 1), 2)[0], 2)[0]);
    }

    private function keyboard(array $rows): array
    {
        return ['keyboard' => array_map(fn ($row) => array_map(fn ($text) => ['text' => $text], $row), $rows), 'resize_keyboard' => true, 'one_time_keyboard' => true];
    }

    private function scheduleName(JadwalPelajaran $schedule): string
    {
        return ($schedule->rombel?->kelas?->nama_kelas ?? 'Kelas').' · '.($schedule->mataPelajaran?->nama_mapel ?? 'Pelajaran').' · '.$schedule->jam_mulai.'-'.$schedule->jam_selesai;
    }

    private function resetInvalidConversation(TelegramBot $bot, TelegramConversation $conversation, User $user, string $chatId): void
    {
        $conversation->delete();
        $this->telegram->reply($bot, $chatId, 'Sesi pengisian tidak valid dan telah direset. Silakan mulai kembali.', $this->telegram->linkedMenuMarkup($bot, $user));
    }
}
