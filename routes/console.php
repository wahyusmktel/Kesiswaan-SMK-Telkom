<?php

use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\FingerprintAutoSyncSetting;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use Carbon\Carbon;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('fingerprint:auto-sync', function () {
    $setting = FingerprintAutoSyncSetting::getSetting();

    if (!$setting->is_enabled) {
        $this->line('Tarik log otomatis fingerprint sedang nonaktif.');
        return 0;
    }

    $now = now();
    $runTime = substr((string) $setting->run_time, 0, 5);

    if ($now->format('H:i') < $runTime) {
        $this->line("Belum waktunya tarik log otomatis. Jadwal hari ini: {$runTime}.");
        return 0;
    }

    if ($setting->last_dispatched_at?->isSameDay($now)) {
        $this->line('Tarik log otomatis hari ini sudah dikirim ke antrean.');
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
        ->when(!empty($setting->device_ids), fn ($query) => $query->whereIn('id', $setting->device_ids))
        ->orderBy('name')
        ->get();

    $results = [];

    foreach ($devices as $device) {
        $hasMappedUser = FingerprintUser::where('fingerprint_device_id', $device->id)
            ->whereNotNull('app_user_id')
            ->exists();

        if (!$hasMappedUser) {
            $results[] = [
                'device_id' => $device->id,
                'device_name' => $device->name,
                'status' => 'skipped',
                'message' => 'Belum ada user mesin yang dimapping.',
            ];
            continue;
        }

        $progressId = 'auto-' . $device->id . '-' . Str::uuid();

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
