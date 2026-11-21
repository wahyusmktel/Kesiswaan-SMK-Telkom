<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\MasterSiswa;
use App\Models\UkkInstrumenSoal;
use App\Models\UkkNilaiPengetahuan;
use App\Models\UkkNilaiKeterampilan;
use App\Models\UkkUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UkkSiswaController extends Controller
{
    private function getStudent(): MasterSiswa
    {
        $user = Auth::user();
        $siswa = $user->masterSiswa;

        if (!$siswa) {
            abort(403, 'Akun Anda tidak terhubung dengan data Master Siswa.');
        }

        return $siswa;
    }

    private function authorizeStudent(UkkUjian $ujian, MasterSiswa $siswa): void
    {
        $hasSoalEsai = $ujian->instrumens
            ->flatMap(fn ($i) => $i->soalPengetahuan)
            ->where('jenis_soal', '!=', 'bilingual')
            ->isNotEmpty();

        if ($ujian->is_bilingual && !$hasSoalEsai) {
            abort(403, 'Ujian ini menggunakan sistem wawancara bilingual langsung dengan penguji dan tidak dikerjakan secara mandiri di sini.');
        }

        $rombelIds = $siswa->rombels->pluck('id');
        $isEligible = $ujian->rombels()->whereIn('rombels.id', $rombelIds)->exists();

        abort_unless($isEligible, 403, 'Anda tidak terdaftar sebagai peserta ujian UKK ini.');
    }

    public function index()
    {
        $siswa = $this->getStudent();
        $rombelIds = $siswa->rombels->pluck('id');

        $ujians = UkkUjian::with([
            'tahunPelajaran',
            'instrumens.soalPengetahuan',
            'rombels.kelas',
        ])
        ->whereHas('rombels', fn ($q) => $q->whereIn('rombels.id', $rombelIds))
        ->latest('tanggal_pelaksanaan')
        ->latest()
        ->get();

        // Saring: tampilkan jika bukan full bilingual atau memiliki butir soal esai mandiri
        $ujians = $ujians->filter(function ($ujian) {
            $soalEsaiCount = $ujian->instrumens
                ->flatMap(fn ($i) => $i->soalPengetahuan)
                ->where('jenis_soal', '!=', 'bilingual')
                ->count();

            return !$ujian->is_bilingual || $soalEsaiCount > 0;
        })->values();

        foreach ($ujians as $ujian) {
            // Hanya hitung soal esai yang dikerjakan siswa
            $allSoalIds = $ujian->instrumens
                ->flatMap(fn ($i) => $i->soalPengetahuan)
                ->where('jenis_soal', '!=', 'bilingual')
                ->pluck('id');

            $ujian->total_soal = $allSoalIds->count();

            $existingNilai = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
                ->whereIn('soal_id', $allSoalIds)
                ->get();

            $ujian->soal_dijawab = $existingNilai->filter(fn ($n) => !empty(trim((string)$n->jawaban_siswa)))->count();
            $ujian->soal_dinilai = $existingNilai->filter(fn ($n) => $n->nilai !== null)->count();

            // Nilai Akhir jika sudah dinilai penguji
            $ujian->skor_p = null;
            if ($ujian->total_soal > 0 && $ujian->soal_dinilai >= $ujian->total_soal) {
                $benar = $existingNilai->filter(fn ($n) => (int)$n->nilai === 1)->count();
                $ujian->skor_p = round($benar / $ujian->total_soal * 100, 1);
            }
        }

        return view('pages.siswa.ukk.index', compact('siswa', 'ujians'));
    }

    public function show(UkkUjian $ujian)
    {
        $siswa = $this->getStudent();
        $this->authorizeStudent($ujian, $siswa);

        $ujian->load([
            'tahunPelajaran',
            'rombels.kelas',
            'instrumens.soalPengetahuan' => fn ($q) => $q->orderBy('urutan'),
        ]);

        $hasBilingualPart = $ujian->is_bilingual || $ujian->instrumens
            ->flatMap(fn ($i) => $i->soalPengetahuan)
            ->where('jenis_soal', 'bilingual')
            ->isNotEmpty();

        // Hanya tampilkan soal esai mandiri untuk siswa
        foreach ($ujian->instrumens as $ins) {
            $ins->setRelation(
                'soalPengetahuan',
                $ins->soalPengetahuan->where('jenis_soal', '!=', 'bilingual')->values()
            );
        }

        $allSoalIds = $ujian->instrumens->flatMap(fn ($i) => $i->soalPengetahuan->pluck('id'));

        $nilaiP = UkkNilaiPengetahuan::where('master_siswa_id', $siswa->id)
            ->whereIn('soal_id', $allSoalIds)
            ->get()
            ->keyBy('soal_id');

        $jawabanMap = [];
        $statusMap  = [];
        foreach ($allSoalIds as $sId) {
            $rec = $nilaiP->get($sId);
            $jawabanMap[$sId] = $rec ? (string)$rec->jawaban_siswa : '';
            $statusMap[$sId]  = [
                'is_submitted' => $rec && !empty(trim((string)$rec->jawaban_siswa)),
                'is_graded'    => $rec && $rec->nilai !== null,
                'nilai'        => $rec?->nilai,
                'catatan'      => $rec?->catatan_penguji,
                'waktu_submit' => $rec?->waktu_submit?->isoFormat('D MMMM Y, HH:mm'),
            ];
        }

        return view('pages.siswa.ukk.show', compact('ujian', 'siswa', 'jawabanMap', 'statusMap', 'hasBilingualPart'));
    }

    public function simpanJawaban(Request $request, UkkUjian $ujian)
    {
        $siswa = $this->getStudent();
        $this->authorizeStudent($ujian, $siswa);

        $request->validate([
            'jawaban'   => 'nullable|array',
            'jawaban.*' => 'nullable|string',
        ]);

        $instrumenIds = $ujian->instrumens()->pluck('id');
        $validSoalIds = UkkInstrumenSoal::whereIn('instrumen_id', $instrumenIds)
            ->where('jenis_soal', '!=', 'bilingual')
            ->pluck('id');

        DB::transaction(function () use ($request, $siswa, $validSoalIds) {
            foreach ($request->input('jawaban', []) as $soalId => $text) {
                if (!$validSoalIds->contains((int) $soalId)) continue;

                $record = UkkNilaiPengetahuan::firstOrNew([
                    'master_siswa_id' => $siswa->id,
                    'soal_id'         => (int) $soalId,
                ]);

                $record->jawaban_siswa = $text !== null ? trim($text) : null;
                $record->waktu_submit  = now();
                $record->save();
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Jawaban esai berhasil disimpan.',
            'waktu'   => now()->isoFormat('HH:mm:ss') . ' WIB',
        ]);
    }
}
