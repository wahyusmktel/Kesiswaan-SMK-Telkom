<?php

namespace App\Http\Controllers;

use App\Models\FingerprintAttendance;
use App\Models\FingerprintDevice;
use App\Models\FingerprintUser;
use App\Models\MasterGuru;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Rats\Zkteco\Lib\ZKTeco;
use Throwable;

class FingerprintController extends Controller
{
    public function index(Request $request)
    {
        $devices = FingerprintDevice::withCount(['fingerprintUsers', 'attendances'])
            ->latest()
            ->paginate(10);

        $attendances = $this->attendanceQuery($request)->paginate(20)->withQueryString();
        $allDevices = FingerprintDevice::orderBy('name')->get();

        return view('pages.fingerprint.index', compact('devices', 'attendances', 'allDevices'));
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
        $attendances = $this->attendanceQuery($request)->paginate(25)->withQueryString();
        $allDevices = FingerprintDevice::orderBy('name')->get();

        return view('pages.fingerprint.logs', compact('attendances', 'allDevices'));
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
                $appUser = $this->matchLocalUser($fingerprintUserId, $name);

                FingerprintUser::updateOrCreate(
                    [
                        'fingerprint_device_id' => $device->id,
                        'user_id' => $fingerprintUserId,
                    ],
                    [
                        'uid' => $row['uid'] ?? null,
                        'app_user_id' => $appUser?->id,
                        'name' => $name ?: $fingerprintUserId,
                        'role' => isset($row['role']) ? (string) $row['role'] : null,
                        'password' => $row['password'] ?? null,
                        'cardno' => isset($row['cardno']) ? trim((string) $row['cardno']) : null,
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

    public function syncAttendances($id)
    {
        $device = FingerprintDevice::findOrFail($id);

        try {
            [$zk, $connected] = $this->connectDevice($device);
            if (!$connected) {
                return back()->with('error', "Mesin {$device->name} tidak bisa dikoneksikan.");
            }

            $logs = $zk->getAttendance();
            $synced = 0;

            foreach ($logs as $row) {
                $fingerprintUserId = trim((string) ($row['id'] ?? ''));
                $timestamp = $this->parseTimestamp($row['timestamp'] ?? null);
                if ($fingerprintUserId === '' || !$timestamp) {
                    continue;
                }

                $fingerprintUser = FingerprintUser::where('fingerprint_device_id', $device->id)
                    ->where('user_id', $fingerprintUserId)
                    ->first();
                $appUser = $fingerprintUser?->appUser ?? $this->matchLocalUser($fingerprintUserId, $fingerprintUser?->name);

                FingerprintAttendance::updateOrCreate(
                    [
                        'fingerprint_device_id' => $device->id,
                        'user_id' => $fingerprintUserId,
                        'timestamp' => $timestamp->format('Y-m-d H:i:s'),
                    ],
                    [
                        'uid' => $row['uid'] ?? null,
                        'app_user_id' => $appUser?->id,
                        'status' => isset($row['state']) ? (string) $row['state'] : null,
                        'punch' => isset($row['type']) ? (string) $row['type'] : null,
                    ]
                );

                $synced++;
            }

            $zk->disconnect();

            return back()->with('success', "Tarik log absensi selesai. {$synced} log diproses tanpa duplikasi.");
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

    private function matchLocalUser(?string $fingerprintUserId, ?string $name): ?User
    {
        $fingerprintUserId = trim((string) $fingerprintUserId);
        $name = trim((string) $name);

        if ($fingerprintUserId !== '' && ctype_digit($fingerprintUserId)) {
            $user = User::find((int) $fingerprintUserId);
            if ($user) {
                return $user;
            }
        }

        if ($fingerprintUserId !== '') {
            $matchColumns = array_filter(
                ['nik', 'nuptk', 'kode_guru'],
                fn ($column) => Schema::hasColumn('master_gurus', $column)
            );

            if (!empty($matchColumns)) {
                $masterGuru = MasterGuru::with('user')
                    ->where(function ($query) use ($fingerprintUserId, $matchColumns) {
                        foreach ($matchColumns as $column) {
                            $query->orWhere($column, $fingerprintUserId);
                        }
                    })
                    ->first();

                if ($masterGuru?->user) {
                    return $masterGuru->user;
                }
            }
        }

        if ($name !== '') {
            $user = User::where('name', $name)->first();
            if ($user) {
                return $user;
            }

            $masterGuru = MasterGuru::where('nama_lengkap', $name)->with('user')->first();
            if ($masterGuru?->user) {
                return $masterGuru->user;
            }
        }

        return null;
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
