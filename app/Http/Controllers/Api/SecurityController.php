<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IzinMeninggalkanKelas;
use App\Models\Keterlambatan;
use App\Models\MasterSiswa;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SecurityController extends Controller
{
    public function dashboard(Request $request): JsonResponse
    {
        $this->ensureSecurity($request->user());

        $recentLateness = Keterlambatan::with(['siswa.rombels.kelas', 'siswa.rombels.tahunPelajaran'])
            ->whereDate('waktu_dicatat_security', today())
            ->latest('waktu_dicatat_security')
            ->limit(10)
            ->get()
            ->map(fn (Keterlambatan $record) => $this->latenessPayload($record));

        return response()->json([
            'success' => true,
            'data' => [
                'stats' => [
                    'verified_today' => IzinMeninggalkanKelas::query()
                        ->where(function (Builder $query) {
                            $query->whereDate('security_verified_at', today())
                                ->orWhereDate('waktu_kembali_sebenarnya', today());
                        })
                        ->count(),
                    'students_outside' => IzinMeninggalkanKelas::where('status', 'diverifikasi_security')->count(),
                    'late_today' => Keterlambatan::whereDate('waktu_dicatat_security', today())->count(),
                ],
                'recent_lateness' => $recentLateness,
                'generated_at' => now()->toIso8601String(),
            ],
        ]);
    }

    public function searchStudents(Request $request): JsonResponse
    {
        $this->ensureSecurity($request->user());
        $validated = $request->validate([
            'q' => ['required', 'string', 'min:2', 'max:100'],
        ]);
        $search = trim($validated['q']);

        $students = $this->activeStudents()
            ->with(['rombels.kelas', 'rombels.tahunPelajaran'])
            ->where(function (Builder $query) use ($search) {
                $query->where('nis', 'like', "%{$search}%")
                    ->orWhere('nama_lengkap', 'like', "%{$search}%");
            })
            ->orderBy('nama_lengkap')
            ->limit(20)
            ->get()
            ->map(fn (MasterSiswa $student) => $this->studentPayload($student));

        return response()->json([
            'success' => true,
            'data' => $students,
        ]);
    }

    public function scanStudent(Request $request): JsonResponse
    {
        $this->ensureSecurity($request->user());
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:1000'],
        ]);
        $code = $this->extractStudentCode($validated['code']);

        $student = $this->activeStudents()
            ->with(['rombels.kelas', 'rombels.tahunPelajaran'])
            ->where('nis', $code)
            ->first();

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Siswa dengan kode tersebut tidak ditemukan atau sudah tidak aktif.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->studentPayload($student),
        ]);
    }

    public function storeLateness(Request $request): JsonResponse
    {
        $this->ensureSecurity($request->user());
        $validated = $request->validate([
            'master_siswa_id' => ['required', 'exists:master_siswa,id'],
            'reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $student = $this->activeStudents()
            ->with(['rombels.kelas', 'rombels.tahunPelajaran'])
            ->findOrFail($validated['master_siswa_id']);

        $record = DB::transaction(function () use ($request, $student, $validated) {
            $exists = Keterlambatan::where('master_siswa_id', $student->id)
                ->whereDate('waktu_dicatat_security', today())
                ->lockForUpdate()
                ->exists();

            if ($exists) {
                throw ValidationException::withMessages([
                    'master_siswa_id' => ['Siswa ini sudah didata terlambat hari ini.'],
                ]);
            }

            return Keterlambatan::create([
                'master_siswa_id' => $student->id,
                'alasan_siswa' => trim($validated['reason']),
                'dicatat_oleh_security_id' => $request->user()->id,
                'waktu_dicatat_security' => now(),
                'status' => 'dicatat_security',
            ]);
        });

        $record->setRelation('siswa', $student);

        return response()->json([
            'success' => true,
            'message' => 'Keterlambatan berhasil dicatat. Arahkan siswa ke ruang piket.',
            'data' => $this->latenessPayload($record),
        ], 201);
    }

    public function todayHistory(Request $request): JsonResponse
    {
        $this->ensureSecurity($request->user());

        $records = Keterlambatan::with(['siswa.rombels.kelas', 'siswa.rombels.tahunPelajaran'])
            ->whereDate('waktu_dicatat_security', today())
            ->latest('waktu_dicatat_security')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data' => collect($records->items())
                ->map(fn (Keterlambatan $record) => $this->latenessPayload($record)),
            'meta' => [
                'current_page' => $records->currentPage(),
                'last_page' => $records->lastPage(),
                'total' => $records->total(),
            ],
        ]);
    }

    private function activeStudents(): Builder
    {
        return MasterSiswa::query()
            ->where(fn (Builder $query) => $query
                ->whereNull('status')
                ->orWhere('status', '!=', 'alumni'));
    }

    private function studentPayload(MasterSiswa $student): array
    {
        $rombel = $student->rombels
            ->first(fn ($item) => $item->tahunPelajaran?->is_active)
            ?? $student->rombels->sortByDesc('tahun_pelajaran_id')->first();

        return [
            'id' => $student->id,
            'nis' => $student->nis,
            'name' => $student->nama_lengkap,
            'gender' => $student->jenis_kelamin,
            'class_name' => $rombel?->kelas?->nama_kelas ?? '-',
            'major' => $rombel?->kelas?->jurusan,
            'already_late_today' => Keterlambatan::where('master_siswa_id', $student->id)
                ->whereDate('waktu_dicatat_security', today())
                ->exists(),
        ];
    }

    private function latenessPayload(Keterlambatan $record): array
    {
        return [
            'id' => $record->id,
            'uuid' => $record->uuid,
            'student' => $this->studentPayload($record->siswa),
            'reason' => $record->alasan_siswa,
            'status' => $record->status,
            'recorded_at' => $record->waktu_dicatat_security?->toIso8601String(),
        ];
    }

    private function extractStudentCode(string $rawCode): string
    {
        $rawCode = trim($rawCode);
        $query = parse_url($rawCode, PHP_URL_QUERY);
        if ($query) {
            parse_str($query, $parameters);
            foreach (['nis', 'nipd', 'student', 'search'] as $key) {
                if (filled($parameters[$key] ?? null)) {
                    return trim((string) $parameters[$key]);
                }
            }
        }

        if (filter_var($rawCode, FILTER_VALIDATE_URL)) {
            $path = trim((string) parse_url($rawCode, PHP_URL_PATH), '/');
            if ($path !== '') {
                return urldecode((string) str($path)->afterLast('/'));
            }
        }

        return $rawCode;
    }

    private function ensureSecurity(User $user): void
    {
        abort_unless($user->hasRole('Security'), 403, 'Fitur ini hanya tersedia untuk role Security.');
    }
}
