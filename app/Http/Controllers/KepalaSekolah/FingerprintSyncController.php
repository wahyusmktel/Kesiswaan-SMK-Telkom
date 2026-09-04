<?php

namespace App\Http\Controllers\KepalaSekolah;

use App\Http\Controllers\Controller;
use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\FingerprintDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class FingerprintSyncController extends Controller
{
    public function store(Request $request)
    {
        $driver = config('queue.connections.'.config('queue.default').'.driver');
        abort_if(in_array($driver, ['sync', 'null'], true), 422, 'Antrean background belum dikonfigurasi. Hubungi Super Admin untuk mengaktifkan worker fingerprint.');
        $devices = FingerprintDevice::where('is_active', true)
            ->whereHas('fingerprintUsers', fn ($query) => $query->whereNotNull('app_user_id'))
            ->get(['id', 'name']);
        abort_if($devices->isEmpty(), 422, 'Tidak ada mesin aktif dengan mapping akun. Hubungi Super Admin.');
        $date = today()->toDateString();
        $batch = (string) Str::uuid();
        $jobs = $devices->map(fn ($device) => ['id' => (string) Str::uuid(), 'device_id' => $device->id, 'name' => $device->name])->all();
        Cache::put('headmaster:fp:'.$batch, ['owner' => $request->user()->id, 'jobs' => $jobs, 'date' => $date], now()->addHours(2));
        foreach ($jobs as $job) {
            Cache::put('fingerprint:sync-progress:'.$job['id'], ['status' => 'queued', 'percent' => 0], now()->addHours(2));
            try {
                SyncFingerprintAttendancesJob::dispatch($job['device_id'], $job['id'], $date, $date, 'hari ini', false);
            } catch (\Throwable $exception) {
                report($exception);
                Cache::put('fingerprint:sync-progress:'.$job['id'], ['status' => 'failed', 'percent' => 100], now()->addHours(2));
            }
        }

        return response()->json(['status_url' => route('kepala-sekolah.fingerprint-sync.show', $batch), 'date' => $date]);
    }

    public function show(Request $request, string $batch)
    {
        $data = Cache::get('headmaster:fp:'.$batch);
        abort_unless($data && $data['owner'] === $request->user()->id, 404);
        $jobs = collect($data['jobs'])->map(function ($job) {
            $progress = Cache::get('fingerprint:sync-progress:'.$job['id'], ['status' => 'missing', 'percent' => 0]);

            return ['name' => $job['name'], 'status' => $progress['status'], 'percent' => $progress['percent']];
        });

        return response()->json(['jobs' => $jobs, 'done' => $jobs->every(fn ($job) => in_array($job['status'], ['finished', 'failed', 'missing'], true))]);
    }
}
