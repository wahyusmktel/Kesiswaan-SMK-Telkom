<?php

namespace App\Services;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintAutoSyncSetting;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\TelegramBot;
use App\Models\TelegramLog;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use App\Models\WorkCalendarEvent;
use App\Support\AttendanceDuration;
use App\Support\EmploymentStatus;
use App\Support\MyFingerprintAttendance;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class FingerprintWhatsappNotificationService
{
    public const EVENT_KEY = 'fingerprint_rekap_harian';

    public const REMINDER_EVENT_KEY = 'fingerprint_peringatan_harian';

    public const CHECKIN_REMINDER_LOG_EVENT_KEY = 'fingerprint_pengingat_checkin_harian';

    public function __construct(private readonly WhatsappService $whatsappService, private readonly TelegramService $telegramService) {}

    public function sendToday(?Carbon $date = null, bool $manual = false): array
    {
        $template = WhatsappTemplate::where('event_key', self::EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $today = ($date ?? today())->copy()->startOfDay();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];
        [$channel, $bot] = $this->deliveryContext();
        if ($channel === 'telegram' && ! $bot) {
            return $result + ['configuration_error' => 'Bot Telegram kepegawaian belum aktif atau belum dipilih.'];
        }

        $users = User::query()
            ->with(['masterGuru.dapodikGuru', 'securityShiftAssignment.shift', 'telegramLinks'])
            ->when($channel === 'whatsapp', fn ($query) => $query->whereNotNull('phone_number')->where('phone_number', '!=', ''))
            ->when($channel === 'telegram', fn ($query) => $query->whereHas('telegramLinks', fn ($links) => $links->where('telegram_bot_id', $bot->id)))
            ->whereDoesntHave('roles', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['siswa']))
            ->whereHas('masterGuru', fn ($query) => $query->where('is_active', true))
            ->whereIn('id', FingerprintAttendance::query()
                ->select('app_user_id')
                ->whereNotNull('app_user_id')
                ->whereDate('timestamp', $today))
            ->get();

        foreach ($users as $user) {
            try {
                Cache::lock("fingerprint:{$channel}-recap:{$today->toDateString()}:{$user->id}", 60)
                    ->block(5, function () use ($user, $today, $manual, $channel, $bot, &$result) {
                        if (! $manual && $this->alreadySent($user, $today, self::EVENT_KEY, $channel, $bot)) {
                            $result['skipped']++;

                            return;
                        }

                        $recap = MyFingerprintAttendance::dailyRecaps(
                            $user,
                            $today->copy()->startOfDay(),
                            $today->copy()->endOfDay(),
                        )->first();

                        if (! $recap) {
                            $result['skipped']++;

                            return;
                        }

                        $response = $this->sendNotification(
                            $channel,
                            $bot,
                            $user,
                            self::EVENT_KEY,
                            $this->templateData($user, $recap, $today),
                            $user->name,
                            $manual ? 'fingerprint_rekap_manual' : 'fingerprint_rekap',
                            $today->toDateString(),
                            $manual ? self::EVENT_KEY.'_manual' : null,
                        );

                        $result[$response['success'] ? 'sent' : 'failed']++;
                    });
            } catch (LockTimeoutException) {
                $result['skipped']++;
            } catch (Throwable $e) {
                $result['failed']++;
                Log::error('Gagal mengirim rekap fingerprint pegawai.', [
                    'channel' => $channel,
                    'user_id' => $user->id,
                    'date' => $today->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    public function sendRemindersToday(?Carbon $notificationDate = null, bool $manual = false): array
    {
        $template = WhatsappTemplate::where('event_key', self::REMINDER_EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $date = ($notificationDate ?? today())->copy()->startOfDay();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];
        [$channel, $bot] = $this->deliveryContext();
        if ($channel === 'telegram' && ! $bot) {
            return $result + ['configuration_error' => 'Bot Telegram kepegawaian belum aktif atau belum dipilih.'];
        }
        if ($date->isWeekend() || WorkCalendarEvent::eventFor($date)) {
            return $result;
        }

        $teachers = MasterGuru::query()
            ->with(['user.telegramLinks', 'dapodikGuru'])
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query
                ->when($channel === 'whatsapp', fn ($users) => $users->whereNotNull('phone_number')->where('phone_number', '!=', ''))
                ->when($channel === 'telegram', fn ($users) => $users->whereHas('telegramLinks', fn ($links) => $links->where('telegram_bot_id', $bot->id))))
            ->get(['id', 'user_id', 'nama_lengkap', 'employee_category']);
        $dayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$date->dayOfWeekIso - 1];
        $schedules = JadwalPelajaran::inActiveAcademicPeriod()
            ->whereIn('master_guru_id', $teachers->modelKeys())
            ->where('hari', $dayName)
            ->select('master_guru_id', DB::raw('MIN(jam_mulai) as starts_at'))
            ->groupBy('master_guru_id')
            ->get()
            ->keyBy('master_guru_id');
        $leaves = GuruIzin::fullyApproved()
            ->whereIn('master_guru_id', $teachers->modelKeys())
            ->where('tanggal_mulai', '<=', $date->copy()->endOfDay())
            ->where('tanggal_selesai', '>=', $date->copy()->startOfDay())
            ->pluck('master_guru_id')
            ->all();
        $scans = FingerprintAttendance::query()
            ->whereIn('app_user_id', $teachers->pluck('user_id'))
            ->whereBetween('timestamp', [$date->copy()->startOfDay(), $date->copy()->endOfDay()])
            ->select('app_user_id', DB::raw('MIN(timestamp) as first_scan'))
            ->groupBy('app_user_id')
            ->get()
            ->keyBy('app_user_id');
        $setting = FingerprintAttendanceSetting::getSetting();

        foreach ($teachers as $teacher) {
            $employment = EmploymentStatus::normalize($teacher->dapodikGuru?->status_kepegawaian);
            $isTpa = $teacher->is_tpa;
            if ((! $isTpa && ! in_array($employment, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME], true))
                || in_array($teacher->id, $leaves, true)) {
                $result['skipped']++;

                continue;
            }

            $schedule = $schedules->get($teacher->id);
            if (! $isTpa && $employment === EmploymentStatus::PART_TIME && ! $schedule) {
                $result['skipped']++;

                continue;
            }

            $deadline = Carbon::parse($date->toDateString().' '.(! $isTpa && $employment === EmploymentStatus::PART_TIME ? $schedule->starts_at : $setting->checkin_end));
            $firstScan = $scans->get($teacher->user_id)?->first_scan;
            $firstScan = $firstScan ? Carbon::parse($firstScan) : null;
            if (now()->lessThanOrEqualTo($deadline) || ($firstScan && $firstScan->lessThanOrEqualTo($deadline))) {
                $result['skipped']++;

                continue;
            }

            try {
                if (! $manual && $this->alreadySent($teacher->user, $date, self::REMINDER_EVENT_KEY, $channel, $bot)) {
                    $result['skipped']++;

                    continue;
                }

                $lateMinutes = $firstScan ? (int) ceil($deadline->diffInMinutes($firstScan)) : 0;
                $status = $firstScan ? 'Terlambat' : 'Tidak Hadir';
                $response = $this->sendNotification(
                    $channel,
                    $bot,
                    $teacher->user,
                    self::REMINDER_EVENT_KEY,
                    [
                        'nama_pegawai' => $teacher->nama_lengkap,
                        'tanggal' => $date->locale('id')->translatedFormat('l, d F Y'),
                        'status_kehadiran' => $status,
                        'jam_masuk' => $firstScan?->format('H:i') ?? 'Belum tercatat',
                        'batas_masuk' => $deadline->format('H:i'),
                        'durasi_terlambat' => $lateMinutes > 0 ? AttendanceDuration::humanizeMinutes($lateMinutes) : '-',
                        'catatan' => $firstScan ? 'Fingerprint masuk tercatat melewati batas waktu.' : 'Belum ada fingerprint masuk sampai waktu pengiriman notifikasi.',
                    ],
                    $teacher->nama_lengkap,
                    $manual ? 'fingerprint_peringatan_manual' : 'fingerprint_peringatan',
                    $date->toDateString(),
                    $manual ? self::REMINDER_EVENT_KEY.'_manual' : null,
                );
                $result[$response['success'] ? 'sent' : 'failed']++;
            } catch (Throwable $e) {
                $result['failed']++;
                Log::error('Gagal mengirim pengingat fingerprint pegawai.', [
                    'channel' => $channel,
                    'user_id' => $teacher->user->id,
                    'date' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    public function sendCheckinRemindersNow(?Carbon $notificationTime = null): array
    {
        $template = WhatsappTemplate::where('event_key', self::REMINDER_EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $now = ($notificationTime ?? now())->copy();
        $date = $now->copy()->startOfDay();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];
        [$channel, $bot] = $this->deliveryContext();
        if ($channel === 'telegram' && ! $bot) {
            return $result + ['configuration_error' => 'Bot Telegram kepegawaian belum aktif atau belum dipilih.'];
        }
        if ($date->isWeekend() || WorkCalendarEvent::eventFor($date)) {
            return $result;
        }

        $teachers = MasterGuru::query()
            ->with(['user.telegramLinks', 'dapodikGuru'])
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query
                ->when($channel === 'whatsapp', fn ($users) => $users->whereNotNull('phone_number')->where('phone_number', '!=', ''))
                ->when($channel === 'telegram', fn ($users) => $users->whereHas('telegramLinks', fn ($links) => $links->where('telegram_bot_id', $bot->id))))
            ->get(['id', 'user_id', 'nama_lengkap', 'employee_category']);
        $dayName = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'][$date->dayOfWeekIso - 1];
        $schedules = JadwalPelajaran::inActiveAcademicPeriod()
            ->whereIn('master_guru_id', $teachers->modelKeys())
            ->where('hari', $dayName)
            ->select('master_guru_id', DB::raw('MIN(jam_mulai) as starts_at'))
            ->groupBy('master_guru_id')
            ->get()
            ->keyBy('master_guru_id');
        $leaves = GuruIzin::fullyApproved()
            ->whereIn('master_guru_id', $teachers->modelKeys())
            ->where('tanggal_mulai', '<=', $date->copy()->endOfDay())
            ->where('tanggal_selesai', '>=', $date)
            ->pluck('master_guru_id')
            ->all();
        $checkedInUserIds = FingerprintAttendance::query()
            ->whereIn('app_user_id', $teachers->pluck('user_id'))
            ->whereBetween('timestamp', [$date, $now])
            ->whereNotNull('app_user_id')
            ->distinct()
            ->pluck('app_user_id')
            ->map(fn ($id) => (int) $id)
            ->all();
        $setting = FingerprintAttendanceSetting::getSetting();

        foreach ($teachers as $teacher) {
            $employment = EmploymentStatus::normalize($teacher->dapodikGuru?->status_kepegawaian);
            $isTpa = $teacher->is_tpa;
            if ((! $isTpa && ! in_array($employment, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME], true))
                || in_array($teacher->id, $leaves, true)
                || in_array((int) $teacher->user_id, $checkedInUserIds, true)) {
                $result['skipped']++;

                continue;
            }

            $schedule = $schedules->get($teacher->id);
            if (! $isTpa && $employment === EmploymentStatus::PART_TIME && ! $schedule) {
                $result['skipped']++;

                continue;
            }

            $expectedAt = Carbon::parse($date->toDateString().' '.(! $isTpa && $employment === EmploymentStatus::PART_TIME
                ? $schedule->starts_at
                : $setting->checkin_end));
            $reminderAt = $expectedAt->copy()->addMinutes(10);
            if ($now->lt($reminderAt)) {
                $result['skipped']++;

                continue;
            }

            try {
                Cache::lock("fingerprint:{$channel}-checkin-reminder:{$date->toDateString()}:{$teacher->user_id}", 60)
                    ->block(2, function () use ($teacher, $date, $now, $expectedAt, $channel, $bot, &$result) {
                        if ($this->alreadySent($teacher->user, $date, self::CHECKIN_REMINDER_LOG_EVENT_KEY, $channel, $bot)) {
                            $result['skipped']++;

                            return;
                        }

                        $response = $this->sendNotification(
                            $channel,
                            $bot,
                            $teacher->user,
                            self::REMINDER_EVENT_KEY,
                            [
                                'nama_pegawai' => $teacher->nama_lengkap,
                                'tanggal' => $date->locale('id')->translatedFormat('l, d F Y'),
                                'status_kehadiran' => 'Belum Check-in',
                                'jam_masuk' => 'Belum tercatat',
                                'batas_masuk' => $expectedAt->format('H:i'),
                                'durasi_terlambat' => AttendanceDuration::humanizeMinutes((int) ceil($expectedAt->diffInMinutes($now))),
                                'catatan' => 'Belum ada fingerprint masuk hingga sedikitnya 10 menit setelah jadwal. Segera lakukan fingerprint check-in apabila Anda sudah berada di sekolah.',
                            ],
                            $teacher->nama_lengkap,
                            'fingerprint_peringatan',
                            $date->toDateString(),
                            self::CHECKIN_REMINDER_LOG_EVENT_KEY,
                        );
                        $result[$response['success'] ? 'sent' : 'failed']++;
                    });
            } catch (LockTimeoutException) {
                $result['skipped']++;
            } catch (Throwable $e) {
                $result['failed']++;
                Log::error('Gagal mengirim pengingat check-in fingerprint pegawai.', [
                    'channel' => $channel,
                    'user_id' => $teacher->user_id,
                    'date' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    private function alreadySent(User $user, Carbon $date, string $eventKey, string $channel, ?TelegramBot $bot): bool
    {
        if ($channel === 'telegram') {
            return TelegramLog::query()
                ->where('telegram_bot_id', $bot?->id)
                ->where('recipient_user_id', $user->id)
                ->where('event_key', $eventKey)
                ->whereDate('notification_date', $date)
                ->whereIn('status', ['sent', 'delivered'])
                ->exists();
        }

        return WhatsappLog::query()
            ->where('recipient_user_id', $user->id)
            ->where('event_key', $eventKey)
            ->whereDate('notification_date', $date)
            ->whereIn('status', ['sent', 'delivered'])
            ->exists();
    }

    private function deliveryContext(): array
    {
        $setting = FingerprintAutoSyncSetting::getSetting();
        $channel = $setting->notification_channel === 'telegram' ? 'telegram' : 'whatsapp';
        $bot = $channel === 'telegram'
            ? TelegramBot::whereKey($setting->telegram_bot_id)->where('purpose', 'employment')->where('is_active', true)->where('status', 'connected')->first()
            : null;

        return [$channel, $bot];
    }

    private function sendNotification(string $channel, ?TelegramBot $bot, User $user, string $eventKey, array $data, string $recipientName, string $logType, string $notificationDate, ?string $logEventKey): array
    {
        if ($channel === 'telegram' && $bot) {
            return $this->telegramService->sendTemplateNotification($bot, $user, $eventKey, $data, $recipientName, $logType, $notificationDate, $logEventKey);
        }

        return $this->whatsappService->sendTemplateNotification($user->phone_number, $eventKey, $data, $recipientName, $logType, $user->id, $notificationDate, $logEventKey);
    }

    private function templateData(User $user, object $recap, Carbon $date): array
    {
        $totalScans = (int) $recap->total_scan;
        $lateMinutes = (int) ($recap->monitoring_late_minutes ?? 0);

        return [
            'nama_pegawai' => $user->name,
            'tanggal' => $date->locale('id')->translatedFormat('l, d F Y'),
            'jam_masuk' => Carbon::parse($recap->scan_masuk)->format('H:i'),
            'jam_pulang' => $totalScans > 1
                ? Carbon::parse($recap->scan_keluar)->format('H:i')
                : 'Belum tercatat',
            'total_scan' => (string) $totalScans,
            'status_kehadiran' => $recap->monitoring_status_text ?? 'Hadir',
            'catatan' => implode('; ', $recap->monitoring_notes ?? ['Sesuai jadwal']),
            'durasi_terlambat' => $lateMinutes > 0
                ? AttendanceDuration::humanizeMinutes($lateMinutes)
                : 'Tidak terlambat',
        ];
    }
}
