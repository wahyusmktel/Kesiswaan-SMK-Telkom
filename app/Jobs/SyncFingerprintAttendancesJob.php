<?php

namespace App\Jobs;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Rats\Zkteco\Lib\ZKTeco;
use Throwable;

class SyncFingerprintAttendancesJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $timeout = 600;
    public int $tries = 1;

    public function __construct(
        public int $deviceId,
        public string $progressId,
        public ?string $dateFrom,
        public ?string $dateTo,
        public string $rangeLabel,
    ) {
        $this->onQueue('fingerprint');
    }

    public function handle(): void
    {
        $this->progress([
            'status' => 'running',
            'percent' => 3,
            'message' => 'Menghubungkan ke mesin fingerprint...',
            'character' => 'Menyiapkan jalur data',
        ]);

        $device = FingerprintDevice::findOrFail($this->deviceId);
        $zk = new ZKTeco($device->ip_address, (int) $device->port);

        try {
            if (!$zk->connect()) {
                $this->progress([
                    'status' => 'failed',
                    'percent' => 100,
                    'message' => "Mesin {$device->name} tidak bisa dikoneksikan.",
                    'character' => 'Koneksi ditolak mesin',
                ]);
                return;
            }

            $this->progress([
                'status' => 'running',
                'percent' => 10,
                'message' => 'Membaca log dari mesin...',
                'character' => 'Mengambil jejak scan',
            ]);

            $logs = collect($zk->getAttendance() ?: []);
            $total = max($logs->count(), 1);
            $dateFrom = $this->dateFrom ? Carbon::parse($this->dateFrom)->startOfDay() : null;
            $dateTo = $this->dateTo ? Carbon::parse($this->dateTo)->endOfDay() : null;

            $mappedUsers = FingerprintUser::where('fingerprint_device_id', $device->id)
                ->whereNotNull('app_user_id')
                ->get()
                ->keyBy('user_id');

            if ($mappedUsers->isEmpty()) {
                $zk->disconnect();
                $this->progress([
                    'status' => 'failed',
                    'percent' => 100,
                    'message' => 'Belum ada user mesin yang dimapping ke pegawai.',
                    'character' => 'Mapping pegawai diperlukan',
                ]);
                return;
            }

            $processed = 0;
            $created = 0;
            $updated = 0;
            $skipped = 0;
            $seen = 0;

            foreach ($logs as $row) {
                $seen++;
                $fingerprintUserId = trim((string) ($row['id'] ?? ''));
                $timestamp = $this->parseTimestamp($row['timestamp'] ?? null);

                if ($fingerprintUserId === '' || !$timestamp) {
                    $skipped++;
                    $this->tick($seen, $total, $processed, $created, $updated, $skipped);
                    continue;
                }

                if (($dateFrom && $timestamp->lt($dateFrom)) || ($dateTo && $timestamp->gt($dateTo))) {
                    $this->tick($seen, $total, $processed, $created, $updated, $skipped);
                    continue;
                }

                $fingerprintUser = $mappedUsers->get($fingerprintUserId);
                if (!$fingerprintUser) {
                    $skipped++;
                    $this->tick($seen, $total, $processed, $created, $updated, $skipped);
                    continue;
                }

                $attendance = FingerprintAttendance::updateOrCreate(
                    [
                        'fingerprint_device_id' => $device->id,
                        'user_id' => $fingerprintUserId,
                        'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                    ],
                    [
                        'uid' => $row['uid'] ?? null,
                        'app_user_id' => $fingerprintUser->app_user_id,
                        'status' => isset($row['state']) ? (string) $row['state'] : null,
                        'punch' => isset($row['type']) ? (string) $row['type'] : null,
                    ]
                );

                $processed++;
                if ($attendance->wasRecentlyCreated) {
                    $created++;
                } elseif ($attendance->wasChanged()) {
                    $updated++;
                }

                $this->tick($seen, $total, $processed, $created, $updated, $skipped);
            }

            $zk->disconnect();

            $this->progress([
                'status' => 'finished',
                'percent' => 100,
                'message' => "Tarik log {$this->rangeLabel} selesai. {$processed} log termapping diproses ({$created} baru, {$updated} diperbarui, {$skipped} dilewati).",
                'character' => 'Sinkronisasi selesai',
                'processed' => $processed,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
            ]);
        } catch (Throwable $e) {
            try {
                $zk->disconnect();
            } catch (Throwable) {
                // Ignore disconnect errors after a failed socket operation.
            }

            Log::error('Fingerprint queued sync attendances failed', [
                'device_id' => $device->id,
                'progress_id' => $this->progressId,
                'error' => $e->getMessage(),
            ]);

            $this->progress([
                'status' => 'failed',
                'percent' => 100,
                'message' => 'Gagal tarik log absensi: ' . $e->getMessage(),
                'character' => 'Proses berhenti',
            ]);
        }
    }

    public function failed(Throwable $e): void
    {
        $this->progress([
            'status' => 'failed',
            'percent' => 100,
            'message' => 'Job tarik log gagal: ' . $e->getMessage(),
            'character' => 'Worker menghentikan proses',
        ]);
    }

    private function tick(int $seen, int $total, int $processed, int $created, int $updated, int $skipped): void
    {
        if ($seen % 25 !== 0 && $seen < $total) {
            return;
        }

        $percent = min(98, 10 + (int) floor(($seen / $total) * 88));

        $this->progress([
            'status' => 'running',
            'percent' => $percent,
            'message' => "Memproses {$seen} dari {$total} baris log...",
            'character' => $this->characterMessage($percent),
            'processed' => $processed,
            'created' => $created,
            'updated' => $updated,
            'skipped' => $skipped,
        ]);
    }

    private function progress(array $payload): void
    {
        Cache::put(
            $this->cacheKey(),
            array_merge([
                'status' => 'queued',
                'percent' => 0,
                'message' => 'Menunggu giliran worker...',
                'character' => 'Stella sedang bersiap',
                'processed' => 0,
                'created' => 0,
                'updated' => 0,
                'skipped' => 0,
            ], $payload),
            now()->addHours(2)
        );
    }

    private function cacheKey(): string
    {
        return "fingerprint:sync-progress:{$this->progressId}";
    }

    private function characterMessage(int $percent): string
    {
        return match (true) {
            $percent < 25 => 'Stella mengetuk pintu mesin',
            $percent < 50 => 'Stella memilah user yang sudah mapping',
            $percent < 75 => 'Stella merapikan log absensi',
            default => 'Stella menyelesaikan sinkronisasi',
        };
    }

    private function parseTimestamp($value): ?Carbon
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (Throwable) {
            return null;
        }
    }
}
