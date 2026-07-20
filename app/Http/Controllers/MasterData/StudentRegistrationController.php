<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\DapodikSiswa;
use App\Models\MasterSiswa;
use App\Models\StudentRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class StudentRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $allowedStatuses = ['pending', 'approved', 'mapped', 'rejected'];
        $status = in_array($status, $allowedStatuses, true) ? $status : 'pending';
        $search = trim((string) $request->get('search'));

        $registrations = StudentRegistration::with(['masterSiswa.rombels.kelas', 'dapodikSiswa', 'reviewer', 'mapper'])
            ->where('status', $status)
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('nama_lengkap', 'like', "%{$search}%")
                        ->orWhere('registration_number', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(15);

        $counts = StudentRegistration::selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $unmappedDapodikCount = DapodikSiswa::whereNull('master_siswa_id')->count();

        return view('pages.master-data.student-registration.index', compact(
            'registrations',
            'counts',
            'status',
            'unmappedDapodikCount'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate($this->rules());

        if ($this->hasActiveDuplicate($validated)) {
            return back()->withInput()->with('error', 'Data serupa sudah terdaftar atau masih menunggu pemetaan.');
        }

        DB::transaction(function () use ($validated, $request) {
            $registration = StudentRegistration::create($validated + [
                'source' => 'staff',
                'status' => 'pending',
            ]);
            $this->approveRegistration($registration, $request->user()->id);
        });

        return back()->with('success', 'Siswa sementara berhasil dibuat dan siap dimasukkan ke rombel.');
    }

    public function approve(Request $request, StudentRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah pernah ditinjau.');
        }

        DB::transaction(function () use ($registration, $request) {
            $lockedRegistration = StudentRegistration::lockForUpdate()->findOrFail($registration->id);
            if ($lockedRegistration->status !== 'pending') {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'registration' => 'Pendaftaran ini sudah diproses oleh pengguna lain.',
                ]);
            }
            $this->approveRegistration($lockedRegistration, $request->user()->id);
        });

        return back()->with('success', 'Pendaftaran disetujui. Data siswa sementara telah dibuat.');
    }

    public function reject(Request $request, StudentRegistration $registration)
    {
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Pendaftaran ini sudah pernah ditinjau.');
        }

        $validated = $request->validate(['notes' => 'required|string|max:1000']);
        $registration->update([
            'status' => 'rejected',
            'notes' => $validated['notes'],
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Pendaftaran ditolak dan alasan telah dicatat.');
    }

    public function searchDapodik(Request $request)
    {
        $search = trim((string) $request->get('q'));

        $records = DapodikSiswa::whereNull('master_siswa_id')
            ->when($search, function ($query) use ($search) {
                $query->where(function ($nested) use ($search) {
                    $nested->where('nama', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%")
                        ->orWhere('nipd', 'like', "%{$search}%");
                });
            })
            ->orderBy('nama')
            ->limit(25)
            ->get(['id', 'nama', 'nipd', 'nisn', 'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin']);

        return response()->json($records);
    }

    public function map(Request $request, StudentRegistration $registration)
    {
        $validated = $request->validate([
            'dapodik_siswa_id' => [
                'required',
                Rule::exists('dapodik_siswa', 'id')->whereNull('master_siswa_id'),
            ],
        ]);

        if ($registration->status !== 'approved' || !$registration->master_siswa_id) {
            return back()->with('error', 'Hanya siswa sementara yang sudah disetujui yang dapat dipetakan.');
        }

        try {
            DB::transaction(function () use ($registration, $validated, $request) {
                $registration = StudentRegistration::lockForUpdate()->findOrFail($registration->id);
                $dapodik = DapodikSiswa::lockForUpdate()->findOrFail($validated['dapodik_siswa_id']);
                $student = MasterSiswa::lockForUpdate()->findOrFail($registration->master_siswa_id);

                if ($dapodik->master_siswa_id) {
                    throw new \RuntimeException('Data Dapodik sudah dipetakan oleh pengguna lain.');
                }
                if (!$dapodik->nipd) {
                    throw new \RuntimeException('Data Dapodik belum memiliki NIPD resmi.');
                }
                if (MasterSiswa::where('nis', $dapodik->nipd)->where('id', '!=', $student->id)->exists()) {
                    throw new \RuntimeException('NIPD resmi sudah digunakan siswa lain. Periksa data sebelum memetakan.');
                }

                $student->update([
                    'nis' => $dapodik->nipd,
                    'nama_lengkap' => $dapodik->nama ?: $student->nama_lengkap,
                    'jenis_kelamin' => $dapodik->jenis_kelamin ?: $student->jenis_kelamin,
                    'tempat_lahir' => $dapodik->tempat_lahir ?: $student->tempat_lahir,
                    'tanggal_lahir' => $dapodik->tanggal_lahir ?: $student->tanggal_lahir,
                    'alamat' => $dapodik->alamat ?: $student->alamat,
                    'data_source' => 'dapodik',
                    'is_data_verified' => true,
                    'last_synced_at' => now(),
                ]);

                $dapodik->update(['master_siswa_id' => $student->id]);
                $registration->update([
                    'status' => 'mapped',
                    'dapodik_siswa_id' => $dapodik->id,
                    'mapped_by' => $request->user()->id,
                    'mapped_at' => now(),
                ]);

                if ($student->user) {
                    $updates = ['name' => $student->nama_lengkap];
                    $officialEmail = $student->nis . '@smktelkom-lpg.sch.id';
                    if (!\App\Models\User::where('email', $officialEmail)->where('id', '!=', $student->user->id)->exists()) {
                        $updates['email'] = $officialEmail;
                    }
                    $student->user->update($updates);
                }
            });
        } catch (\Throwable $exception) {
            Log::warning('Student registration mapping failed', [
                'registration_id' => $registration->id,
                'message' => $exception->getMessage(),
            ]);

            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Pemetaan berhasil. Identitas sementara telah diperbarui menggunakan data resmi Dapodik.');
    }

    public function biodata(StudentRegistration $registration)
    {
        return response()->json($registration);
    }

    private function approveRegistration(StudentRegistration $registration, int $reviewerId): void
    {
        $temporaryNis = 'TMP-' . str_replace('REG-', '', $registration->registration_number);
        $student = MasterSiswa::create([
            'nis' => $temporaryNis,
            'nama_lengkap' => $registration->nama_lengkap,
            'jenis_kelamin' => $registration->jenis_kelamin,
            'tempat_lahir' => $registration->tempat_lahir,
            'tanggal_lahir' => $registration->tanggal_lahir,
            'alamat' => $registration->alamat,
            'status' => 'aktif',
            'data_source' => 'registrasi',
            'is_data_verified' => false,
        ]);

        $registration->update([
            'status' => 'approved',
            'master_siswa_id' => $student->id,
            'reviewed_by' => $reviewerId,
            'reviewed_at' => now(),
        ]);
    }

    private function rules(): array
    {
        return [
            'nama_lengkap' => 'required|string|max:255',
            'nisn' => 'nullable|digits:10',
            'nik' => 'nullable|digits:16',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'required|date|before:today',
            'jenis_kelamin' => 'required|in:L,P',
            'alamat' => 'required|string|max:1000',
            'nomor_hp' => ['required', 'regex:/^[0-9]{9,15}$/'],
            'email' => 'nullable|email|max:255',
            'sekolah_asal' => 'nullable|string|max:255',
            'nama_orang_tua' => 'nullable|string|max:255',
            'nomor_hp_orang_tua' => ['nullable', 'regex:/^[0-9]{9,15}$/'],
        ];
    }

    private function hasActiveDuplicate(array $data): bool
    {
        return StudentRegistration::whereIn('status', ['pending', 'approved'])
            ->where(function ($query) use ($data) {
                if (!empty($data['nisn'])) {
                    $query->where('nisn', $data['nisn']);
                } else {
                    $query->where('nama_lengkap', $data['nama_lengkap'])
                        ->whereDate('tanggal_lahir', $data['tanggal_lahir']);
                }
            })
            ->exists();
    }


    public function schoolOrigins(\Illuminate\Http\Request $request)
    {
        $registrations = \App\Models\StudentRegistration::whereNotNull('sekolah_asal')
            ->where('sekolah_asal', '!=', '')
            ->get();

        $groups = [];

        foreach ($registrations as $reg) {
            $originalName = trim($reg->sekolah_asal);
            $normalizedKey = $this->getNormalizedKey($originalName);
            if ($normalizedKey === '') {
                continue;
            }

            $matchedIndex = null;
            foreach ($groups as $idx => $group) {
                $groupKey = $group['normalized_key'];

                if ($groupKey === $normalizedKey) {
                    $matchedIndex = $idx;
                    break;
                }

                $len1 = strlen($groupKey);
                $len2 = strlen($normalizedKey);

                if (abs($len1 - $len2) <= 3) {
                    $distance = levenshtein($groupKey, $normalizedKey);
                    $threshold = min($len1, $len2) >= 8 ? 2 : 1;

                    $prefix1 = substr($groupKey, 0, 3);
                    $prefix2 = substr($normalizedKey, 0, 3);

                    if ($prefix1 === $prefix2 && $distance <= $threshold) {
                        $matchedIndex = $idx;
                        break;
                    }
                }
            }

            if ($matchedIndex !== null) {
                $groups[$matchedIndex]['registrations'][] = $reg;
                if (!in_array($originalName, $groups[$matchedIndex]['variations'], true)) {
                    $groups[$matchedIndex]['variations'][] = $originalName;
                }
                $groups[$matchedIndex]['name_counts'][$originalName] = ($groups[$matchedIndex]['name_counts'][$originalName] ?? 0) + 1;
            } else {
                $groups[] = [
                    'normalized_key' => $normalizedKey,
                    'name_counts' => [$originalName => 1],
                    'variations' => [$originalName],
                    'registrations' => [$reg]
                ];
            }
        }

        foreach ($groups as &$group) {
            arsort($group['name_counts']);
            $group['canonical_name'] = key($group['name_counts']);
            $group['total_registrations'] = count($group['registrations']);
        }
        unset($group);

        usort($groups, function ($a, $b) {
            return strcmp($a['canonical_name'], $b['canonical_name']);
        });

        $search = trim((string) $request->get('search'));
        if ($search !== '') {
            $searchLower = strtolower($search);
            $groups = array_filter($groups, function ($group) use ($searchLower) {
                if (str_contains(strtolower($group['canonical_name']), $searchLower)) {
                    return true;
                }
                foreach ($group['variations'] as $v) {
                    if (str_contains(strtolower($v), $searchLower)) {
                        return true;
                    }
                }
                return false;
            });
        }

        return view('pages.master-data.student-registration.school-origins', compact('groups', 'search'));
    }

    public function updateSchoolOrigin(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'original_names' => 'required|array',
            'original_names.*' => 'required|string',
            'standardized_name' => 'required|string|max:255',
        ]);

        $originalNames = $validated['original_names'];
        $standardizedName = trim($validated['standardized_name']);

        $updatedCount = \App\Models\StudentRegistration::whereIn('sekolah_asal', $originalNames)
            ->update(['sekolah_asal' => $standardizedName]);

        return back()->with('success', 'Berhasil memperbarui ' . $updatedCount . ' data sekolah asal menjadi \'' . $standardizedName . '\'.');
    }

    private function getNormalizedKey(string $name): string
    {
        $key = strtolower(trim($name));
        $key = str_replace(['negeri', 'negri'], 'n', $key);
        $key = str_replace(['.', ',', '-', '_', '/', '\\', '(', ')'], ' ', $key);
        $key = preg_replace('/[^a-z0-9]/', '', $key);
        return $key;
    }
}

