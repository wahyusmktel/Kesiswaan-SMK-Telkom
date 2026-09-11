<?php

use App\Jobs\SendFingerprintCheckinRemindersJob;
use App\Jobs\SendFingerprintDailyRecapsJob;
use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\CctvCamera;
use App\Models\FingerprintAutoSyncSetting;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\RepositoryUpload;
use App\Services\MediaMtxService;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fingerprint:auto-sync', function () {
    $setting = FingerprintAutoSyncSetting::getSetting();

    if (! $setting->is_enabled) {
        $this->line('Tarik log otomatis fingerprint sedang nonaktif.');

        return 0;
    }

    $now = now();
    $runTime = substr((string) $setting->run_time, 0, 5);

    if ($now->format('H:i') < $runTime) {
        $this->line("Belum waktunya tarik log otomatis. Jadwal hari ini: {$runTime}.");

        return 0;
    }

    // Use the latest due slot. After downtime one pull covers both missed slots.
    $dueAt = $now->copy()->setTimeFromTimeString($setting->run_time);
    if ($setting->second_run_time && $now->format('H:i') >= substr($setting->second_run_time, 0, 5)) {
        $dueAt = $now->copy()->setTimeFromTimeString($setting->second_run_time);
    }

    if ($setting->last_dispatched_at && $setting->last_dispatched_at->greaterThanOrEqualTo($dueAt)) {
        $this->line('Jadwal tarik log otomatis ini sudah dikirim ke antrean.');

        return 0;
    }

    [$dateFrom, $dateTo, $rangeLabel] = match ($setting->range_type) {
        '1_day' => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'hari ini'],
        '2_days' => [$now->copy()->subDay()->startOfDay(), $now->copy()->endOfDay(), '2 hari terakhir'],
        '2_months' => [$now->copy()->subMonthsNoOverflow(2)->startOfDay(), $now->copy()->endOfDay(), '2 bulan terakhir'],
        'all' => [null, null, 'semua data'],
        default => [$now->copy()->subMonthNoOverflow()->startOfDay(), $now->copy()->endOfDay(), '1 bulan terakhir'],
    };

    $devices = FingerprintDevice::query()
        ->where('is_active', true)
        ->when(! empty($setting->device_ids), fn ($query) => $query->whereIn('id', $setting->device_ids))
        ->orderBy('name')
        ->get();

    $results = [];

    foreach ($devices as $device) {
        $hasMappedUser = FingerprintUser::where('fingerprint_device_id', $device->id)
            ->whereNotNull('app_user_id')
            ->exists();

        if (! $hasMappedUser) {
            $results[] = [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'status' => 'skipped',
                'message' => 'Belum ada user mesin yang dimapping.',
            ];

            continue;
        }

        $progressId = 'auto-'.$device->id.'-'.Str::uuid();

        Cache::put("fingerprint:sync-progress:{$progressId}", [
            'status' => 'queued',
            'percent' => 0,
            'message' => 'Job tarik log otomatis masuk antrean worker.',
            'character' => 'Stella menunggu jadwal otomatis',
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ], now()->addHours(6));

        SyncFingerprintAttendancesJob::dispatch(
            $device->id,
            $progressId,
            $dateFrom instanceof Carbon ? $dateFrom->toDateString() : null,
            $dateTo instanceof Carbon ? $dateTo->toDateString() : null,
            $rangeLabel,
        );

        $results[] = [
            'device_id' => $device->id,
            'device_name' => $device->name,
            'status' => 'queued',
            'progress_id' => $progressId,
            'range' => $rangeLabel,
        ];
    }

    $setting->update([
        'last_dispatched_at' => now(),
        'last_progress_ids' => $results,
    ]);

    $queued = collect($results)->where('status', 'queued')->count();
    $skipped = collect($results)->where('status', 'skipped')->count();

    $this->info("Tarik log otomatis dikirim. {$queued} mesin masuk antrean, {$skipped} mesin dilewati.");

    return 0;
})->purpose('Dispatch scheduled fingerprint attendance sync jobs');

Schedule::command('fingerprint:auto-sync')->everyMinute()->withoutOverlapping();

Artisan::command('fingerprint:send-daily-notifications', function () {
    $setting = FingerprintAutoSyncSetting::getSetting();
    if (! $setting->notifications_enabled) {
        $this->line('Notifikasi harian fingerprint sedang nonaktif.');

        return 0;
    }

    $now = now();
    $notificationTime = substr((string) $setting->notification_time, 0, 5);
    if ($now->format('H:i') < $notificationTime) {
        $this->line("Belum waktunya mengirim notifikasi fingerprint. Jadwal hari ini: {$notificationTime}.");

        return 0;
    }

    $dueAt = $now->copy()->setTimeFromTimeString($setting->notification_time);
    if ($setting->last_notification_dispatched_at?->greaterThanOrEqualTo($dueAt)) {
        $this->line('Notifikasi fingerprint hari ini sudah dikirim ke antrean.');

        return 0;
    }

    SendFingerprintDailyRecapsJob::dispatch($now->toDateString());
    $setting->update(['last_notification_dispatched_at' => $now]);
    $this->info("Notifikasi rekap dan pengingat fingerprint dikirim ke antrean untuk pukul {$notificationTime}.");

    return 0;
})->purpose('Dispatch daily fingerprint recap and attendance reminder notifications');

Schedule::command('fingerprint:send-daily-notifications')->everyMinute()->withoutOverlapping();

Artisan::command('fingerprint:send-checkin-reminders', function () {
    $setting = FingerprintAutoSyncSetting::getSetting();
    if (! $setting->notifications_enabled) {
        $this->line('Notifikasi harian fingerprint sedang nonaktif.');

        return 0;
    }

    SendFingerprintCheckinRemindersJob::dispatch();
    $this->info('Pemeriksaan pengingat check-in fingerprint dikirim ke antrean.');

    return 0;
})->purpose('Dispatch fingerprint check-in reminder notifications');

Schedule::command('fingerprint:send-checkin-reminders')
    ->everyMinute()
    ->weekdays()
    ->between('05:00', '17:00')
    ->withoutOverlapping();

Artisan::command('cctv:sync', function (MediaMtxService $mediaMtx) {
    $success = 0;
    $failed = 0;

    CctvCamera::active()->each(function (CctvCamera $camera) use ($mediaMtx, &$success, &$failed) {
        try {
            $mediaMtx->sync($camera);
            $success++;
        } catch (Throwable $exception) {
            $camera->forceFill([
                'last_sync_status' => 'failed',
                'last_sync_message' => 'Gateway tidak dapat menerapkan konfigurasi.',
                'last_synced_at' => now(),
            ])->save();
            report($exception);
            $failed++;
        }
    });

    $this->line("Sinkronisasi CCTV selesai: {$success} berhasil, {$failed} gagal.");

    return $failed > 0 ? 1 : 0;
})->purpose('Synchronize active CCTV paths to MediaMTX');

Schedule::command('cctv:sync')->everyFiveMinutes()->withoutOverlapping();

Artisan::command('repository:cleanup-uploads', function () {
    $disk = Storage::disk(config('repository.disk'));
    $cleaned = 0;

    RepositoryUpload::query()
        ->where('expires_at', '<', now())
        ->each(function (RepositoryUpload $upload) use ($disk, &$cleaned) {
            $directory = trim(config('repository.uploads_directory'), '/').'/'.$upload->id;
            $disk->deleteDirectory($directory);
            $upload->delete();
            $cleaned++;
        });

    $this->info("{$cleaned} sesi upload repository kedaluwarsa dibersihkan.");

    return 0;
})->purpose('Remove expired repository upload sessions');

Schedule::command('repository:cleanup-uploads')->dailyAt('02:30')->withoutOverlapping();
