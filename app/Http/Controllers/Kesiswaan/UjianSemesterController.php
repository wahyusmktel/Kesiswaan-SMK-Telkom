<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\MasterSiswa;
use App\Models\NilaiUjianSemester;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UjianSemester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UjianSemesterController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ujians = UjianSemester::with('tahunPelajaran')
            ->withCount('nilai')
            ->withMax('nilai', 'imported_at')
            ->latest()
            ->paginate(10, ['*'], 'ujian_page');

        $ujianOptions = UjianSemester::with('tahunPelajaran')
            ->latest()
            ->get();

        $selectedUjian = null;
        $nilai = null;
        $nilaiStats = [
            'total' => 0,
            'matched' => 0,
            'unmatched' => 0,
            'mapel' => 0,
        ];

        if ($request->filled('ujian_id')) {
            $selectedUjian = UjianSemester::with('tahunPelajaran')->find($request->ujian_id);

            if ($selectedUjian) {
                $nilaiQuery = NilaiUjianSemester::with(['mataPelajaran', 'siswa', 'rombel.kelas'])
                    ->where('ujian_semester_id', $selectedUjian->id)
                    ->when($request->filled('mata_pelajaran_id'), function ($query) use ($request) {
                        $query->where('mata_pelajaran_id', $request->mata_pelajaran_id);
                    })
                    ->when($request->filled('search'), function ($query) use ($request) {
                        $search = $request->search;
                        $query->where(function ($q) use ($search) {
                            $q->where('kode_peserta', 'like', '%' . $search . '%')
                                ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                                ->orWhere('kelas', 'like', '%' . $search . '%');
                        });
                    });

                $baseStatsQuery = NilaiUjianSemester::where('ujian_semester_id', $selectedUjian->id);
                $nilaiStats = [
                    'total' => (clone $baseStatsQuery)->count(),
                    'matched' => (clone $baseStatsQuery)->whereNotNull('master_siswa_id')->count(),
                    'unmatched' => (clone $baseStatsQuery)->whereNull('master_siswa_id')->count(),
                    'mapel' => (clone $baseStatsQuery)->distinct('mata_pelajaran_id')->count('mata_pelajaran_id'),
                ];

                $nilai = $nilaiQuery
                    ->orderBy('mata_pelajaran_id')
                    ->orderBy('kelas')
                    ->orderBy('nomor_urut')
                    ->paginate(25, ['*'], 'nilai_page')
                    ->withQueryString();
            }
        }

        $mataPelajaran = MataPelajaran::with('kelas')
            ->orderBy('nama_mapel')
            ->orderBy('kode_mapel')
            ->get();

        return view('pages.kesiswaan.ujian-semester.index', compact(
            'tahunAktif',
            'ujians',
            'ujianOptions',
            'selectedUjian',
            'nilai',
            'nilaiStats',
            'mataPelajaran'
        ));
    }

    public function store(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        if (!$tahunAktif) {
            toast('Tahun pelajaran aktif belum diset.', 'error');
            return back()->withInput();
        }

        $validated = $request->validate([
            'nama_ujian' => 'required|string|max:255',
            'kode_ujian' => 'nullable|string|max:100',
            'tanggal_ujian' => 'nullable|date',
            'keterangan' => 'nullable|string|max:1000',
        ]);

        $ujian = UjianSemester::create([
            ...$validated,
            'tahun_pelajaran_id' => $tahunAktif->id,
            'semester' => $tahunAktif->semester,
            'created_by' => $request->user()?->id,
        ]);

        toast('Data ujian semester berhasil ditambahkan.', 'success');

        return redirect()->route('kesiswaan.ujian-semester.index', ['ujian_id' => $ujian->id]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'ujian_semester_id' => 'required|exists:ujian_semesters,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'file_nilai' => 'required|file|mimes:xls,xlsx,csv|max:10240',
        ]);

        $ujian = UjianSemester::findOrFail($validated['ujian_semester_id']);
        $mapel = MataPelajaran::findOrFail($validated['mata_pelajaran_id']);
        $file = $request->file('file_nilai');

        try {
            $rows = $this->readNilaiRows($file->getRealPath());
            $result = $this->saveNilaiRows($rows, $ujian, $mapel, $file->getClientOriginalName(), $request->user()?->id);

            toast("Import selesai. {$result['imported']} data tersimpan, {$result['unmatched']} NIS belum cocok dengan master siswa.", 'success');
        } catch (\Throwable $e) {
            report($e);
            toast('Gagal import nilai: ' . $e->getMessage(), 'error');
        }

        return redirect()->route('kesiswaan.ujian-semester.index', [
            'ujian_id' => $ujian->id,
            'mata_pelajaran_id' => $mapel->id,
        ]);
    }

    public function destroy(UjianSemester $ujianSemester)
    {
        $ujianSemester->delete();

        toast('Data ujian semester berhasil dihapus.', 'success');

        return redirect()->route('kesiswaan.ujian-semester.index');
    }

    private function readNilaiRows(string $path): array
    {
        $reader = IOFactory::createReaderForFile($path);
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($path);
        $sheet = $spreadsheet->getActiveSheet();

        $expectedHeaders = ['no', 'kode_peserta', 'nama_lengkap', 'kelas', 'nilai'];
        $headers = [];

        for ($column = 1; $column <= 5; $column++) {
            $headers[] = $this->normalizeHeader($sheet->getCellByColumnAndRow($column, 7)->getValue());
        }

        if ($headers !== $expectedHeaders) {
            throw new \RuntimeException('Format Excel tidak sesuai. Header baris 7 harus: No, Kode Peserta, Nama Lengkap, Kelas, Nilai.');
        }

        $rows = [];
        $highestRow = $sheet->getHighestDataRow();

        for ($row = 8; $row <= $highestRow; $row++) {
            $kodePeserta = $this->cellToString($sheet->getCellByColumnAndRow(2, $row)->getValue());
            $namaLengkap = $this->cellToString($sheet->getCellByColumnAndRow(3, $row)->getValue());

            if ($kodePeserta === '' && $namaLengkap === '') {
                continue;
            }

            $rows[] = [
                'nomor_urut' => $this->cellToInteger($sheet->getCellByColumnAndRow(1, $row)->getValue()),
                'kode_peserta' => $kodePeserta,
                'nama_lengkap' => $namaLengkap,
                'kelas' => $this->cellToString($sheet->getCellByColumnAndRow(4, $row)->getValue()),
                'nilai' => $this->cellToDecimal($sheet->getCellByColumnAndRow(5, $row)->getValue()),
                'baris_excel' => $row,
            ];
        }

        if (count($rows) === 0) {
            throw new \RuntimeException('Tidak ada data nilai yang terbaca mulai baris 8.');
        }

        return $rows;
    }

    private function saveNilaiRows(array $rows, UjianSemester $ujian, MataPelajaran $mapel, string $fileName, ?int $userId): array
    {
        $siswaByNis = MasterSiswa::whereIn('nis', collect($rows)->pluck('kode_peserta')->filter()->unique())
            ->get()
            ->keyBy('nis');

        $rombelsByKelas = Rombel::with('kelas')
            ->where('tahun_pelajaran_id', $ujian->tahun_pelajaran_id)
            ->whereHas('kelas', function ($query) use ($rows) {
                $query->whereIn('nama_kelas', collect($rows)->pluck('kelas')->filter()->unique());
            })
            ->get()
            ->keyBy(fn ($rombel) => $rombel->kelas?->nama_kelas);

        $unmatched = 0;
        $now = now();

        DB::transaction(function () use ($rows, $ujian, $mapel, $fileName, $userId, $siswaByNis, $rombelsByKelas, $now, &$unmatched) {
            foreach ($rows as $row) {
                $siswa = $siswaByNis->get($row['kode_peserta']);
                $rombel = $rombelsByKelas->get($row['kelas']);

                if (!$siswa) {
                    $unmatched++;
                }

                NilaiUjianSemester::updateOrCreate(
                    [
                        'ujian_semester_id' => $ujian->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'kode_peserta' => $row['kode_peserta'],
                    ],
                    [
                        'master_siswa_id' => $siswa?->id,
                        'rombel_id' => $rombel?->id,
                        'nomor_urut' => $row['nomor_urut'],
                        'nama_lengkap' => $row['nama_lengkap'],
                        'kelas' => $row['kelas'],
                        'nilai' => $row['nilai'],
                        'baris_excel' => $row['baris_excel'],
                        'nama_file' => $fileName,
                        'imported_by' => $userId,
                        'imported_at' => $now,
                    ]
                );
            }
        });

        return [
            'imported' => count($rows),
            'unmatched' => $unmatched,
        ];
    }

    private function normalizeHeader($value): string
    {
        return preg_replace('/[^a-z0-9]+/', '_', strtolower(trim((string) $value)));
    }

    private function cellToString($value): string
    {
        if (is_numeric($value) && floor((float) $value) == (float) $value) {
            return (string) (int) $value;
        }

        return trim((string) $value);
    }

    private function cellToInteger($value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    private function cellToDecimal($value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_string($value)) {
            $value = str_replace(',', '.', $value);
        }

        return is_numeric($value) ? (float) $value : null;
    }
}
