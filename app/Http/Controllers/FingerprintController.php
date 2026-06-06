<?php

namespace App\Http\Controllers;

use App\Exports\FingerprintAttendanceMonitoringExport;
use App\Jobs\SyncFingerprintAttendancesJob;
use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintAttendanceSetting;
use App\Models\FingerprintSecurityShift;
use App\Models\FingerprintSecurityShiftAssignment;
use App\Models\FingerprintUser;
use App\Models\JadwalPelajaran;
use App\Models\MasterGuru;
use App\Models\User;
use App\Support\AttendanceDuration;
use App\Support\EmploymentStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;
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

    public function timeSettings()
    {
        $setting = FingerprintAttendanceSetting::getSetting();
        $securityShifts = FingerprintSecurityShift::orderBy('starts_at')->get();
        $securityEmployees = User::with(['masterGuru.dapodikGuru', 'securityShiftAssignment'])
            ->whereHas('masterGuru.dapodikGuru', fn ($query) => $query->where('status_kepegawaian', EmploymentStatus::SECURITY))
            ->orderBy('name')
            ->get();

        return view('pages.fingerprint.time-settings', compact('setting', 'securityShifts', 'securityEmployees'));
    }

    public function updateTimeSettings(Request $request)
    {
        $data = $request->validate([
            'checkin_start' => ['required', 'date_format:H:i'],
            'checkin_end' => ['required', 'date_format:H:i', 'after:checkin_start'],
            'checkout_start' => ['required', 'date_format:H:i'],
            'checkout_end' => ['required', 'date_format:H:i', 'after:checkout_start'],
        ], [
            'checkin_end.after' => 'Jam akhir datang harus lebih besar dari jam mulai datang.',
            'checkout_end.after' => 'Jam akhir checkout harus lebih besar dari jam mulai checkout.',
        ]);

        FingerprintAttendanceSetting::getSetting()->update([
            'checkin_start' => $data['checkin_start'] . ':00',
            'checkin_end' => $data['checkin_end'] . ':00',
            'checkout_start' => $data['checkout_start'] . ':00',
            'checkout_end' => $data['checkout_end'] . ':00',
        ]);

        return back()->with('success', 'Seting waktu absensi fingerprint berhasil diperbarui.');
    }

    public function updateSecurityShiftSettings(Request $request)
    {
        $data = $request->validate([
            'shifts' => ['required', 'array'],
            'shifts.*.starts_at' => ['required', 'date_format:H:i'],
            'shifts.*.ends_at' => ['required', 'date_format:H:i'],
            'assignments' => ['nullable', 'array'],
            'assignments.*' => ['nullable', 'exists:fingerprint_security_shifts,id'],
        ]);

        foreach ($data['shifts'] as $shiftId => $shiftData) {
            $shift = FingerprintSecurityShift::find($shiftId);
            if (!$shift) {
                continue;
            }

            $shift->update([
                'starts_at' => $shiftData['starts_at'] . ':00',
                'ends_at' => $shiftData['ends_at'] . ':00',
                'is_overnight' => $shiftData['ends_at'] <= $shiftData['starts_at'],
            ]);
        }

        foreach (($data['assignments'] ?? []) as $userId => $shiftId) {
            if ($shiftId) {
                FingerprintSecurityShiftAssignment::updateOrCreate(
                    ['app_user_id' => $userId],
                    ['fingerprint_security_shift_id' => $shiftId]
                );
            } else {
                FingerprintSecurityShiftAssignment::where('app_user_id', $userId)->delete();
            }
        }

        return back()->with('success', 'Seting shift security berhasil diperbarui.');
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

    public function monitoring(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date)->toDateString() : now()->toDateString();
        $rows = $this->monitoringRows($request, $date)->paginate(30)->withQueryString();
        $allDevices = FingerprintDevice::orderBy('name')->get();
        $setting = FingerprintAttendanceSetting::getSetting();
        $this->applyMonitoringRules($rows->getCollection(), $date, $setting);
        $statsRows = $this->monitoringRows($request, $date)->get();
        $this->applyMonitoringRules($statsRows, $date, $setting);

        $stats = [
            'total' => $statsRows->count(),
            'present' => $statsRows->filter(fn ($row) => $row->first_scan)->count(),
            'complete' => $statsRows->where('monitoring_status_text', 'Hadir Lengkap')->count(),
            'incomplete' => $statsRows->where('monitoring_status_text', 'Belum Scan Pulang')->count(),
            'absent' => $statsRows->where('monitoring_status_text', 'Belum Ada Scan')->count(),
        ];

        return view('pages.fingerprint.monitoring', compact('rows', 'allDevices', 'date', 'stats', 'setting'));
    }

    public function exportMonitoring(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->date)->toDateString() : now()->toDateString();
        $rows = $this->monitoringRows($request, $date)->get();
        $setting = FingerprintAttendanceSetting::getSetting();
        $this->applyMonitoringRules($rows, $date, $setting);
        $fileName = 'monitoring-absensi-fingerprint-' . Carbon::parse($date)->format('Y-m-d') . '.xlsx';

        return Excel::download(new FingerprintAttendanceMonitoringExport($rows, $date, $request->only(['search', 'device_id']), $setting), $fileName);
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

        if (!FingerprintUser::where('fingerprint_device_id', $device->id)->whereNotNull('app_user_id')->exists()) {
            $message = 'Belum ada user mesin yang dimapping ke pegawai. Lakukan mapping manual terlebih dahulu.';

            if ($request->expectsJson()) {
                return response()->json(['message' => $message], 422);
            }

            return back()->with('error', $message);
        }

        $progressId = (string) Str::uuid();
        $initialPayload = [
            'status' => 'queued',
            'percent' => 0,
            'message' => 'Job tarik log masuk antrean worker.',
            'character' => 'Stella menunggu giliran',
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ];

        Cache::put($this->progressCacheKey($progressId), $initialPayload, now()->addHours(2));

        SyncFingerprintAttendancesJob::dispatch(
            $device->id,
            $progressId,
            $dateFrom?->toDateString(),
            $dateTo?->toDateString(),
            $rangeLabel,
        );

        if ($request->expectsJson()) {
            return response()->json([
                'progress_id' => $progressId,
                'status_url' => route('fingerprint.sync-progress', $progressId),
                'message' => 'Tarik log berjalan di background.',
            ]);
        }

        return back()->with('success', 'Tarik log berjalan di background. Pastikan queue worker aktif.');
    }

    public function syncProgress(string $progressId)
    {
        return response()->json(Cache::get($this->progressCacheKey($progressId), [
            'status' => 'missing',
            'percent' => 0,
            'message' => 'Progress tidak ditemukan atau sudah kedaluwarsa.',
            'character' => 'Data progress tidak tersedia',
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'skipped' => 0,
        ]));
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

    private function monitoringRows(Request $request, string $date)
    {
        $daily = FingerprintAttendance::query()
            ->select([
                'fingerprint_device_id',
                'user_id',
                DB::raw('MIN(timestamp) as first_scan'),
                DB::raw('MAX(timestamp) as last_scan'),
                DB::raw('COUNT(*) as total_scan'),
            ])
            ->whereDate('timestamp', $date)
            ->whereNotNull('app_user_id')
            ->groupBy('fingerprint_device_id', 'user_id');

        return FingerprintUser::query()
            ->with(['device', 'appUser.masterGuru.dapodikGuru', 'appUser.securityShiftAssignment.shift'])
            ->whereNotNull('app_user_id')
            ->leftJoinSub($daily, 'daily', function ($join) {
                $join->on('fingerprint_users.fingerprint_device_id', '=', 'daily.fingerprint_device_id')
                    ->on('fingerprint_users.user_id', '=', 'daily.user_id');
            })
            ->select([
                'fingerprint_users.*',
                'daily.first_scan',
                'daily.last_scan',
                'daily.total_scan',
            ])
            ->when($request->filled('device_id'), fn ($query) => $query->where('fingerprint_users.fingerprint_device_id', $request->device_id))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($sub) use ($search) {
                    $sub->where('fingerprint_users.user_id', 'like', "%{$search}%")
                        ->orWhere('fingerprint_users.name', 'like', "%{$search}%")
                        ->orWhereHas('appUser', fn ($userQuery) => $userQuery->where('name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%"))
                        ->orWhereHas('appUser.masterGuru', fn ($guruQuery) => $guruQuery->where('nama_lengkap', 'like', "%{$search}%")->orWhere('kode_guru', 'like', "%{$search}%"));
                });
            })
            ->orderByRaw('daily.first_scan is null asc')
            ->orderBy('daily.first_scan')
            ->orderBy('fingerprint_users.name');
    }

    private function applyMonitoringRules($rows, string $date, FingerprintAttendanceSetting $setting): void
    {
        foreach ($rows as $row) {
            $this->applyMonitoringRule($row, $date, $setting);
        }
    }

    private function applyMonitoringRule(FingerprintUser $row, string $date, FingerprintAttendanceSetting $setting): void
    {
        $firstScan = $row->first_scan ? Carbon::parse($row->first_scan) : null;
        $lastScan = $row->last_scan ? Carbon::parse($row->last_scan) : null;
        $totalScan = (int) ($row->total_scan ?? 0);
        $status = EmploymentStatus::normalize($row->appUser?->masterGuru?->dapodikGuru?->status_kepegawaian);
        $rule = $this->attendanceRuleFor($row, $date, $setting, $status);

        if (($rule['use_shift_window'] ?? false) && $rule['start_at'] && $rule['end_at']) {
            $shiftLogs = FingerprintAttendance::where('fingerprint_device_id', $row->fingerprint_device_id)
                ->where('user_id', $row->user_id)
                ->whereBetween('timestamp', [$rule['start_at'], $rule['end_at']])
                ->orderBy('timestamp')
                ->get(['timestamp']);

            $firstScan = $shiftLogs->first()?->timestamp;
            $lastScan = $shiftLogs->last()?->timestamp;
            $totalScan = $shiftLogs->count();
        }

        $hasCheckout = $firstScan && $lastScan && !$firstScan->equalTo($lastScan);
        $lateMinutes = 0;
        $earlyMinutes = 0;
        $notes = [];

        if ($rule['required']) {
            if ($firstScan && $rule['checkin_deadline'] && $firstScan->greaterThan($rule['checkin_deadline'])) {
                $lateMinutes = (int) ceil($rule['checkin_deadline']->diffInMinutes($firstScan));
                $notes[] = 'Terlambat ' . AttendanceDuration::humanizeMinutes($lateMinutes);
            }

            if ($hasCheckout && $rule['checkout_minimum'] && $lastScan->lessThan($rule['checkout_minimum'])) {
                $earlyMinutes = (int) ceil($lastScan->diffInMinutes($rule['checkout_minimum']));
                $notes[] = 'Pulang cepat ' . AttendanceDuration::humanizeMinutes($earlyMinutes);
            }
        } elseif (!empty($rule['note'])) {
            $notes[] = $rule['note'];
        }

        $statusText = match (true) {
            !$rule['required'] && !$firstScan => 'Tidak Wajib Hadir',
            !$rule['required'] && (bool) $firstScan => 'Hadir Opsional',
            !$firstScan => 'Belum Ada Scan',
            $hasCheckout => 'Hadir Lengkap',
            default => 'Belum Scan Pulang',
        };

        $statusClass = match ($statusText) {
            'Hadir Lengkap' => 'bg-emerald-50 text-emerald-700',
            'Hadir Opsional' => 'bg-blue-50 text-blue-700',
            'Tidak Wajib Hadir' => 'bg-gray-100 text-gray-600',
            'Belum Scan Pulang' => 'bg-amber-50 text-amber-700',
            default => 'bg-red-50 text-red-700',
        };

        $row->setAttribute('first_scan', $firstScan);
        $row->setAttribute('last_scan', $lastScan);
        $row->setAttribute('total_scan', $totalScan);
        $row->setAttribute('monitoring_status_text', $statusText);
        $row->setAttribute('monitoring_status_class', $statusClass);
        $row->setAttribute('monitoring_notes', $notes ?: ['Sesuai jadwal']);
        $row->setAttribute('monitoring_late_minutes', $lateMinutes);
        $row->setAttribute('monitoring_early_minutes', $earlyMinutes);
        $row->setAttribute('monitoring_required', $rule['required']);
        $row->setAttribute('monitoring_rule_label', $rule['label']);
    }

    private function attendanceRuleFor(FingerprintUser $row, string $date, FingerprintAttendanceSetting $setting, ?string $status): array
    {
        if ($status === EmploymentStatus::PART_TIME) {
            return $this->partTimeAttendanceRule($row, $date);
        }

        if ($status === EmploymentStatus::SECURITY) {
            return $this->securityAttendanceRule($row, $date);
        }

        return $this->fullDayAttendanceRule($date, $setting);
    }

    private function fullDayAttendanceRule(string $date, FingerprintAttendanceSetting $setting): array
    {
        $day = Carbon::parse($date)->dayOfWeekIso;
        $required = $day >= 1 && $day <= 5;

        return [
            'required' => $required,
            'checkin_deadline' => $required ? Carbon::parse($date . ' ' . $setting->checkin_end) : null,
            'checkout_minimum' => $required ? Carbon::parse($date . ' ' . $setting->checkout_start) : null,
            'start_at' => $required ? Carbon::parse($date . ' ' . $setting->checkin_start) : null,
            'end_at' => $required ? Carbon::parse($date . ' ' . $setting->checkout_end) : null,
            'use_shift_window' => false,
            'label' => 'Full day',
            'note' => $required ? null : 'Tidak wajib hadir akhir pekan',
        ];
    }

    private function partTimeAttendanceRule(FingerprintUser $row, string $date): array
    {
        $masterGuruId = $row->appUser?->masterGuru?->id;
        $dayName = $this->indonesianDayName(Carbon::parse($date));

        if (!$masterGuruId) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Part time',
                'note' => 'Data guru belum terhubung',
            ];
        }

        $schedule = JadwalPelajaran::where('master_guru_id', $masterGuruId)
            ->where('hari', $dayName)
            ->selectRaw('MIN(jam_mulai) as starts_at, MAX(jam_selesai) as ends_at, COUNT(*) as total')
            ->first();

        if (!$schedule || (int) $schedule->total === 0) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Part time',
                'note' => 'Tidak ada jadwal mengajar',
            ];
        }

        return [
            'required' => true,
            'checkin_deadline' => Carbon::parse($date . ' ' . $schedule->starts_at),
            'checkout_minimum' => Carbon::parse($date . ' ' . $schedule->ends_at),
            'start_at' => Carbon::parse($date . ' ' . $schedule->starts_at),
            'end_at' => Carbon::parse($date . ' ' . $schedule->ends_at),
            'use_shift_window' => false,
            'label' => 'Part time ' . substr($schedule->starts_at, 0, 5) . '-' . substr($schedule->ends_at, 0, 5),
            'note' => null,
        ];
    }

    private function securityAttendanceRule(FingerprintUser $row, string $date): array
    {
        $shift = $row->appUser?->securityShiftAssignment?->shift;

        if (!$shift) {
            return [
                'required' => false,
                'checkin_deadline' => null,
                'checkout_minimum' => null,
                'start_at' => null,
                'end_at' => null,
                'use_shift_window' => false,
                'label' => 'Security',
                'note' => 'Shift security belum diset',
            ];
        }

        $startAt = Carbon::parse($date . ' ' . $shift->starts_at);
        $endAt = Carbon::parse($date . ' ' . $shift->ends_at);
        if ($shift->is_overnight || $endAt->lessThanOrEqualTo($startAt)) {
            $endAt->addDay();
        }

        return [
            'required' => true,
            'checkin_deadline' => $startAt,
            'checkout_minimum' => $endAt,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'use_shift_window' => true,
            'label' => $shift->name . ' ' . $startAt->format('H:i') . '-' . $endAt->format('H:i'),
            'note' => null,
        ];
    }

    private function indonesianDayName(Carbon $date): string
    {
        return [
            1 => 'Senin',
            2 => 'Selasa',
            3 => 'Rabu',
            4 => 'Kamis',
            5 => 'Jumat',
            6 => 'Sabtu',
            7 => 'Minggu',
        ][$date->dayOfWeekIso];
    }

    private function redirectWithSuccess(string $route, string $message)
    {
        if (function_exists('toast')) {
            toast($message, 'success');
        }

        return redirect()->route($route)->with('success', $message);
    }

    private function progressCacheKey(string $progressId): string
    {
        return "fingerprint:sync-progress:{$progressId}";
    }
}
