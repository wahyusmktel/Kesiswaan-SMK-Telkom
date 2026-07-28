<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\Rombel;
use App\Models\TranscriptReprintCorrection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TranscriptReprintCorrectionController extends Controller
{
    public function update(Request $request, MasterSiswa $student)
    {
        $validated = $request->validate([
            'rombel_id' => ['required', 'exists:rombels,id'],
            'corrected_name' => ['required', 'string', 'max:255'],
            'corrected_birth_place' => ['required', 'string', 'max:255'],
            'corrected_birth_date' => ['required', 'date', 'before_or_equal:today'],
            'correction_reason' => ['required', 'string', 'min:5', 'max:2000'],
        ]);

        $rombel = Rombel::with(['kelas', 'tahunPelajaran'])->findOrFail($validated['rombel_id']);
        $this->authorizeArchivedStudent($rombel, $student);

        $newData = [
            'name' => trim($validated['corrected_name']),
            'birth_place' => trim($validated['corrected_birth_place']),
            'birth_date' => $validated['corrected_birth_date'],
        ];

        DB::transaction(function () use ($student, $rombel, $newData, $validated, $request) {
            $correction = TranscriptReprintCorrection::where('master_siswa_id', $student->id)
                ->where('rombel_id', $rombel->id)
                ->lockForUpdate()
                ->first();

            $oldData = $correction
                ? [
                    'name' => $correction->corrected_name,
                    'birth_place' => $correction->corrected_birth_place,
                    'birth_date' => $correction->corrected_birth_date?->toDateString(),
                ]
                : $this->originalIdentity($student);

            if ($oldData === $newData) {
                throw ValidationException::withMessages([
                    'corrected_name' => 'Tidak ada perubahan identitas yang perlu disimpan.',
                ]);
            }

            $correction = TranscriptReprintCorrection::updateOrCreate(
                [
                    'master_siswa_id' => $student->id,
                    'rombel_id' => $rombel->id,
                ],
                [
                    'corrected_name' => $newData['name'],
                    'corrected_birth_place' => $newData['birth_place'],
                    'corrected_birth_date' => $newData['birth_date'],
                    'correction_reason' => trim($validated['correction_reason']),
                    'updated_by' => $request->user()->id,
                ]
            );

            $correction->histories()->create([
                'old_data' => $oldData,
                'new_data' => $newData,
                'reason' => trim($validated['correction_reason']),
                'changed_by' => $request->user()->id,
            ]);
        });

        return back()->with('success', 'Identitas khusus cetak ulang berhasil diperbarui. Data induk siswa tidak berubah.');
    }

    private function authorizeArchivedStudent(Rombel $rombel, MasterSiswa $student): void
    {
        $isArchivedGraduatingClass = $rombel->tahunPelajaran
            && ! $rombel->tahunPelajaran->is_active
            && strcasecmp($rombel->tahunPelajaran->semester, 'Genap') === 0
            && preg_match('/^\s*(XII|12)(?=\s|[-_.]|$)/i', (string) $rombel->kelas?->nama_kelas);

        abort_unless($isArchivedGraduatingClass, 403, 'Koreksi hanya tersedia untuk arsip kelas lulusan.');
        abort_unless(
            $rombel->siswa()->where('master_siswa.id', $student->id)->exists(),
            404,
            'Siswa tidak ditemukan pada rombel arsip tersebut.'
        );
    }

    private function originalIdentity(MasterSiswa $student): array
    {
        $student->loadMissing('dapodik');
        $birthDate = $student->dapodik?->tanggal_lahir ?? $student->tanggal_lahir;

        return [
            'name' => $student->nama_lengkap,
            'birth_place' => $student->dapodik?->tempat_lahir ?? $student->tempat_lahir ?? '',
            'birth_date' => $birthDate?->toDateString(),
        ];
    }
}
