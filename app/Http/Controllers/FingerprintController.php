<?php

namespace App\Http\Controllers;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\MasterGuru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Rats\Zkteco\Lib\ZKTeco;
use Throwable;

class FingerprintController extends Controller
{
    public function index(Request $request)
    {
        $devices = FingerprintDevice::withCount(['fingerprintUsers', 'attendances'])
            ->latest()
            ->paginate(10);

        $fingerprintUsers = $this->fingerprintUserQuery($request)->paginate(20)->withQueryString();
        $allDevices = FingerprintDevice::orderBy('name')->get();

        return view('pages.fingerprint.index', compact('devices', 'fingerprintUsers', 'allDevices'));
    }

    public function create()
    {
        $device = new FingerprintDevice([
            'name' => 'GF1600',
            'ip_address' => '192.168.135.2',
            'port' => 4370,
            'is_active' => true,
        ]);

        return view('pages.fingerprint.form', compact('device'));
    }

    public function store(Request $request)
    {
        FingerprintDevice::create($this->validatedDevice($request));

        return $this->redirectWithSuccess('fingerprint.index', 'Mesin fingerprint berhasil ditambahkan.');
    }

    public function edit(FingerprintDevice $fingerprint)
    {
        $device = $fingerprint;

        return view('pages.fingerprint.form', compact('device'));
    }

    public function update(Request $request, FingerprintDevice $fingerprint)
    {
        $fingerprint->update($this->validatedDevice($request));

        return $this->redirectWithSuccess('fingerprint.index', 'Mesin fingerprint berhasil diperbarui.');
    }

    public function destroy(FingerprintDevice $fingerprint)
    {
        $fingerprint->delete();

        return $this->redirectWithSuccess('fingerprint.index', 'Mesin fingerprint berhasil dihapus.');
    }

    public function logs(Request $request)
    {
        [$dateFrom, $dateTo] = $this->resolveLogDateRange($request);
        $summaryQuery = FingerprintAttendance::query()
            ->select([
                'app_user_id',
                DB::raw('COUNT(*) as total_logs'),
                DB::raw('COUNT(DISTINCT DATE(timestamp)) as total_days'),
                DB::raw('MIN(timestamp) as first_scan'),
                DB::raw('MAX(timestamp) as last_scan'),
            ])
            ->with('appUser.masterGuru')
            ->whereNotNull('app_user_id')
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->when($request->filled('device_id'), fn ($query) => $query->where('fingerprint_device_id', $request->device_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->whereHas('appUser', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas('masterGuru', fn ($guruQuery) => $guruQuery->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->groupBy('app_user_id')
            ->orderByDesc(DB::raw('MAX(timestamp)'));

        $summaries = $summaryQuery->paginate(25)->withQueryString();
        $allDevices = FingerprintDevice::orderBy('name')->get();

        return view('pages.fingerprint.logs', compact('summaries', 'allDevices', 'dateFrom', 'dateTo'));
    }

    public function attendanceDetail(Request $request, User $user)
    {
        [$dateFrom, $dateTo] = $this->resolveDetailDateRange($request);

        $attendances = FingerprintAttendance::with('device')
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->orderByDesc('timestamp')
            ->paginate(50)
            ->withQueryString();

        $dailyRecaps = FingerprintAttendance::query()
            ->select([
                DB::raw('DATE(timestamp) as tanggal'),
                DB::raw('MIN(timestamp) as scan_masuk'),
                DB::raw('MAX(timestamp) as scan_keluar'),
                DB::raw('COUNT(*) as total_scan'),
            ])
            ->where('app_user_id', $user->id)
            ->when($dateFrom, fn ($query) => $query->whereDate('timestamp', '>=', $dateFrom))
            ->when($dateTo, fn ($query) => $query->whereDate('timestamp', '<=', $dateTo))
            ->groupBy(DB::raw('DATE(timestamp)'))
            ->orderByDesc(DB::raw('DATE(timestamp)'))
            ->get();

        $user->load('masterGuru');

        return view('pages.fingerprint.attendance-detail', compact('user', 'attendances', 'dailyRecaps', 'dateFrom', 'dateTo'));
    }

    public function mappings(Request $request)
    {
        $fingerprintUsers = $this->fingerprintUserQuery($request)
            ->orderByRaw('app_user_id is null desc')
            ->paginate(25)
            ->withQueryString();

        $employees = User::with('masterGuru')
            ->whereDoesntHave('roles', fn ($query) => $query->whereIn('name', ['Siswa', 'siswa', 'Kantin']))
            ->orderBy('name')
            ->get();

        $allDevices = FingerprintDevice::orderBy('name')->get();

        return view('pages.fingerprint.mappings', compact('fingerprintUsers', 'employees', 'allDevices'));
    }

    public function updateMapping(Request $request, FingerprintUser $fingerprintUser)
    {
        $data = $request->validate([
            'app_user_id' => ['nullable', 'exists:users,id'],
        ]);

        $fingerprintUser->update([
            'app_user_id' => $data['app_user_id'] ?? null,
        ]);

        FingerprintAttendance::where('fingerprint_device_id', $fingerprintUser->fingerprint_device_id)
            ->where('user_id', $fingerprintUser->user_id)
            ->update(['app_user_id' => $data['app_user_id'] ?? null]);

        return back()->with('success', 'Mapping pegawai berhasil diperbarui.');
    }

    public function testConnection($id)
    {
        $device = FingerprintDevice::findOrFail($id);

        try {
            [$zk, $connected] = $this->connectDevice($device);
            if (!$connected) {
                return back()->with('error', "Mesin {$device->name} tidak merespons.");
            }

            $serialNumber = trim((string) ($zk->serialNumber() ?: ''));
            if ($serialNumber !== '' && $serialNumber !== $device->serial_number) {
                $device->update(['serial_number' => $serialNumber]);
            }
            $zk->disconnect();

            return back()->with('success', "Koneksi berhasil. Serial: " . ($serialNumber ?: '-'));
        } catch (Throwable $e) {
            Log::warning('Fingerprint test connection failed', ['device_id' => $device->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal konek ke mesin: ' . $e->getMessage());
        }
    }

    public function syncUsers($id)
    {
        $device = FingerprintDevice::findOrFail($id);

        try {
            [$zk, $connected] = $this->connectDevice($device);
            if (!$connected) {
                return back()->with('error', "Mesin {$device->name} tidak bisa dikoneksikan.");
            }

            $users = $zk->getUser();
            $synced = 0;

            foreach ($users as $row) {
                $fingerprintUserId = trim((string) ($row['userid'] ?? ''));
                if ($fingerprintUserId === '') {
                    continue;
                }

                $name = trim((string) ($row['name'] ?? ''));
                FingerprintUser::updateOrCreate(
                    [
                        'fingerprint_device_id' => $device->id,
                        'user_id' => $fingerprintUserId,
                    ],
                    [
                        'uid' => $row['uid'] ?? null,
                        'name' => $name ?: $fingerprintUserId,
                        'role' => isset($row['role']) ? (string) $row['role'] : null,
                        'password' => $row['password'] ?? null,
                        'cardno' => isset($row['cardno']) ? trim((string) $row['cardno']) : null,
                        'machine_registered_at' => $this->parseTimestamp($row['created_at'] ?? $row['register_time'] ?? $row['registered_at'] ?? null),
                        'last_synced_at' => now(),
                    ]
                );

                $synced++;
            }

            $zk->disconnect();

            return back()->with('success', "Tarik data user selesai. {$synced} user tersimpan/diperbarui.");
        } catch (Throwable $e) {
            Log::error('Fingerprint sync users failed', ['device_id' => $device->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal tarik data user: ' . $e->getMessage());
        }
    }

    public function syncAttendances(Request $request, $id)
    {
        $device = FingerprintDevice::findOrFail($id);
        [$dateFrom, $dateTo, $rangeLabel] = $this->resolveSyncRange($request);

        try {
            [$zk, $connected] = $this->connectDevice($device);
            if (!$connected) {
                return back()->with('error', "Mesin {$device->name} tidak bisa dikoneksikan.");
            }

            $logs = $zk->getAttendance();
            $mappedUsers = FingerprintUser::where('fingerprint_device_id', $device->id)
                ->whereNotNull('app_user_id')
                ->get()
                ->keyBy('user_id');

            if ($mappedUsers->isEmpty()) {
                $zk->disconnect();
                return back()->with('error', 'Belum ada user mesin yang dimapping ke pegawai. Lakukan mapping manual terlebih dahulu.');
            }

            $processed = 0;
            $created = 0;
            $updated = 0;
            $skipped = 0;

            foreach ($logs as $row) {
                $fingerprintUserId = trim((string) ($row['id'] ?? ''));
                $timestamp = $this->parseTimestamp($row['timestamp'] ?? null);
                if ($fingerprintUserId === '' || !$timestamp) {
                    continue;
                }

                if (($dateFrom && $timestamp->lt($dateFrom->copy()->startOfDay())) || ($dateTo && $timestamp->gt($dateTo->copy()->endOfDay()))) {
                    continue;
                }

                $fingerprintUser = $mappedUsers->get($fingerprintUserId);
                if (!$fingerprintUser) {
                    $skipped++;
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
                $attendance->wasRecentlyCreated ? $created++ : ($attendance->wasChanged() ? $updated++ : null);
            }

            $zk->disconnect();

            return back()->with('success', "Tarik log absensi {$rangeLabel} selesai. {$processed} log termapping diproses ({$created} baru, {$updated} diperbarui, {$skipped} dilewati karena belum mapping).");
        } catch (Throwable $e) {
            Log::error('Fingerprint sync attendances failed', ['device_id' => $device->id, 'error' => $e->getMessage()]);
            return back()->with('error', 'Gagal tarik log absensi: ' . $e->getMessage());
        }
    }

    private function validatedDevice(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'ip_address' => ['required', 'ip', 'max:45'],
            'port' => ['required', 'numeric', 'min:1', 'max:65535'],
            'serial_number' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }

    private function connectDevice(FingerprintDevice $device): array
    {
        $zk = new ZKTeco($device->ip_address, (int) $device->port);
        $connected = (bool) $zk->connect();

        return [$zk, $connected];
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

    private function fingerprintUserQuery(Request $request)
    {
        return FingerprintUser::with(['device', 'appUser.masterGuru'])
            ->when($request->filled('device_id'), fn ($query) => $query->where('fingerprint_device_id', $request->device_id))
            ->when($request->filled('mapping_status'), function ($query) use ($request) {
                $request->mapping_status === 'mapped'
                    ? $query->whereNotNull('app_user_id')
                    : $query->whereNull('app_user_id');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->where('user_id', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhereHas('appUser', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('appUser.masterGuru', fn ($guruQuery) => $guruQuery->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->latest('last_synced_at')
            ->latest();
    }

    private function resolveSyncRange(Request $request): array
    {
        $data = $request->validate([
            'range_type' => ['nullable', 'in:1_day,2_days,1_month,2_months,custom,all'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $type = $data['range_type'] ?? '1_month';
        $today = now();

        return match ($type) {
            '1_day' => [$today->copy()->startOfDay(), $today->copy()->endOfDay(), 'hari ini'],
            '2_days' => [$today->copy()->subDay()->startOfDay(), $today->copy()->endOfDay(), '2 hari terakhir'],
            '2_months' => [$today->copy()->subMonthsNoOverflow(2)->startOfDay(), $today->copy()->endOfDay(), '2 bulan terakhir'],
            'custom' => [
                !empty($data['date_from']) ? Carbon::parse($data['date_from'])->startOfDay() : null,
                !empty($data['date_to']) ? Carbon::parse($data['date_to'])->endOfDay() : null,
                'rentang kustom',
            ],
            'all' => [null, null, 'semua data'],
            default => [$today->copy()->subMonthNoOverflow()->startOfDay(), $today->copy()->endOfDay(), '1 bulan terakhir'],
        };
    }

    private function resolveLogDateRange(Request $request): array
    {
        $dateFrom = $request->filled('date_from') ? Carbon::parse($request->date_from)->startOfDay() : now()->subMonthNoOverflow()->startOfDay();
        $dateTo = $request->filled('date_to') ? Carbon::parse($request->date_to)->endOfDay() : now()->endOfDay();

        return [$dateFrom, $dateTo];
    }

    private function resolveDetailDateRange(Request $request): array
    {
        $mode = $request->input('range', '1_month');

        if ($mode === 'all') {
            return [null, null];
        }

        if ($mode === 'day' && $request->filled('date')) {
            $date = Carbon::parse($request->date);
            return [$date->copy()->startOfDay(), $date->copy()->endOfDay()];
        }

        return [now()->subMonthNoOverflow()->startOfDay(), now()->endOfDay()];
    }

    private function attendanceQuery(Request $request)
    {
        return FingerprintAttendance::with(['device', 'appUser.masterGuru'])
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('timestamp', '>=', $request->date_from))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('timestamp', '<=', $request->date_to))
            ->when($request->filled('device_id'), fn ($query) => $query->where('fingerprint_device_id', $request->device_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->where('user_id', 'like', "%{$search}%")
                        ->orWhereHas('appUser', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%"))
                        ->orWhereHas('appUser.masterGuru', fn ($guruQuery) => $guruQuery->where('nama_lengkap', 'like', "%{$search}%"));
                });
            })
            ->latest('timestamp');
    }

    private function redirectWithSuccess(string $route, string $message)
    {
        if (function_exists('toast')) {
            toast($message, 'success');
        }

        return redirect()->route($route)->with('success', $message);
    }
}
