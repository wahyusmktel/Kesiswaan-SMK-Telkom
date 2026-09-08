<?php

namespace App\Services;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintAttendanceSetting;
use App\Models\GuruIzin;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\User;
use App\Models\WorkCalendarEvent;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
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

    public function __construct(private readonly WhatsappService $whatsappService) {}

    public function sendToday(?Carbon $date = null): array
    {
        $template = WhatsappTemplate::where('event_key', self::EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $today = ($date ?? today())->copy()->startOfDay();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];

        $users = User::query()
            ->with(['masterGuru.dapodikGuru', 'securityShiftAssignment.shift'])
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->whereDoesntHave('roles', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['siswa']))
            ->whereHas('masterGuru', fn ($query) => $query->where('is_active', true))
            ->whereIn('id', FingerprintAttendance::query()
                ->select('app_user_id')
                ->whereNotNull('app_user_id')
                ->whereDate('timestamp', $today))
            ->get();

        foreach ($users as $user) {
            try {
                Cache::lock("fingerprint:wa-recap:{$today->toDateString()}:{$user->id}", 60)
                    ->block(5, function () use ($user, $today, &$result) {
                        if ($this->alreadySent($user, $today)) {
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

                        $response = $this->whatsappService->sendTemplateNotification(
                            $user->phone_number,
                            self::EVENT_KEY,
                            $this->templateData($user, $recap, $today),
                            $user->name,
                            'fingerprint_rekap',
                            $user->id,
                            $today->toDateString(),
                        );

                        $result[$response['success'] ? 'sent' : 'failed']++;
                    });
            } catch (LockTimeoutException) {
                $result['skipped']++;
            } catch (Throwable $e) {
                $result['failed']++;
                Log::error('Gagal mengirim rekap fingerprint WhatsApp pegawai.', [
                    'user_id' => $user->id,
                    'date' => $today->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    public function sendRemindersToday(?Carbon $notificationDate = null): array
    {
        $template = WhatsappTemplate::where('event_key', self::REMINDER_EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $date = ($notificationDate ?? today())->copy()->startOfDay();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];
        if ($date->isWeekend() || WorkCalendarEvent::eventFor($date)) {
            return $result;
        }

        $teachers = MasterGuru::query()
            ->with(['user', 'dapodikGuru'])
            ->where('is_active', true)
            ->whereNotNull('user_id')
            ->whereHas('user', fn ($query) => $query->whereNotNull('phone_number')->where('phone_number', '!=', ''))
            ->get(['id', 'user_id', 'nama_lengkap']);
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
            if (! in_array($employment, [EmploymentStatus::PERMANENT, EmploymentStatus::FULL_TIME, EmploymentStatus::PART_TIME], true)
                || in_array($teacher->id, $leaves, true)) {
                $result['skipped']++;

                continue;
            }

            $schedule = $schedules->get($teacher->id);
            if ($employment === EmploymentStatus::PART_TIME && ! $schedule) {
                $result['skipped']++;

                continue;
            }

            $deadline = Carbon::parse($date->toDateString().' '.($employment === EmploymentStatus::PART_TIME ? $schedule->starts_at : $setting->checkin_end));
            $firstScan = $scans->get($teacher->user_id)?->first_scan;
            $firstScan = $firstScan ? Carbon::parse($firstScan) : null;
            if (now()->lessThanOrEqualTo($deadline) || ($firstScan && $firstScan->lessThanOrEqualTo($deadline))) {
                $result['skipped']++;

                continue;
            }

            try {
                if ($this->alreadySent($teacher->user, $date, self::REMINDER_EVENT_KEY)) {
                    $result['skipped']++;

                    continue;
                }

                $lateMinutes = $firstScan ? (int) ceil($deadline->diffInMinutes($firstScan)) : 0;
                $status = $firstScan ? 'Terlambat' : 'Tidak Hadir';
                $response = $this->whatsappService->sendTemplateNotification(
                    $teacher->user->phone_number,
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
                    'fingerprint_peringatan',
                    $teacher->user->id,
                    $date->toDateString(),
                );
                $result[$response['success'] ? 'sent' : 'failed']++;
            } catch (Throwable $e) {
                $result['failed']++;
                Log::error('Gagal mengirim pengingat fingerprint WhatsApp pegawai.', [
                    'user_id' => $teacher->user->id,
                    'date' => $date->toDateString(),
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $result;
    }

    private function alreadySent(User $user, Carbon $date, string $eventKey = self::EVENT_KEY): bool
    {
        return WhatsappLog::query()
            ->where('recipient_user_id', $user->id)
            ->where('event_key', $eventKey)
            ->whereDate('notification_date', $date)
            ->whereIn('status', ['sent', 'delivered'])
            ->exists();
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
