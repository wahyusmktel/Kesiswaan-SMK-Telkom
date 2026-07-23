<?php

namespace App\Services;

use App\Models\FingerprintAttendance;
use App\Models\User;
use App\Models\WhatsappLog;
use App\Models\WhatsappTemplate;
use App\Support\AttendanceDuration;
use App\Support\MyFingerprintAttendance;
use Illuminate\Contracts\Cache\LockTimeoutException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

class FingerprintWhatsappNotificationService
{
    public const EVENT_KEY = 'fingerprint_rekap_harian';

    public function __construct(private readonly WhatsappService $whatsappService) {}

    public function sendToday(): array
    {
        $template = WhatsappTemplate::where('event_key', self::EVENT_KEY)->first();
        if (! $template?->is_enabled) {
            return ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => true];
        }

        $today = today();
        $result = ['sent' => 0, 'skipped' => 0, 'failed' => 0, 'disabled' => false];

        $users = User::query()
            ->with(['masterGuru.dapodikGuru', 'securityShiftAssignment.shift'])
            ->whereNotNull('phone_number')
            ->where('phone_number', '!=', '')
            ->whereDoesntHave('roles', fn ($query) => $query->whereRaw('LOWER(name) = ?', ['siswa']))
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

    private function alreadySent(User $user, Carbon $date): bool
    {
        return WhatsappLog::query()
            ->where('recipient_user_id', $user->id)
            ->where('event_key', self::EVENT_KEY)
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
