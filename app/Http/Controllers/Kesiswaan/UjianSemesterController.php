<?php

namespace App\Http\Controllers\Kesiswaan;

use App\Exports\UjianSemesterNilaiExport;
use App\Http\Controllers\Controller;
use App\Models\MataPelajaran;
use App\Models\MasterSiswa;
use App\Models\NilaiUjianSemester;
use App\Models\Rombel;
use App\Models\TahunPelajaran;
use App\Models\UjianSemester;
use App\Models\UjianSemesterMapel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class UjianSemesterController extends Controller
{
    public function index(Request $request)
    {
        $tahunAktif = TahunPelajaran::where('is_active', true)->first();

        $ujians = UjianSemester::with(['tahunPelajaran', 'ujianMapels.mataPelajaran.kelas'])
            ->withCount('nilai')
            ->withMax('nilai', 'imported_at')
            ->latest()
            ->paginate(10, ['*'], 'ujian_page');

        $selectedUjian = $request->filled('ujian_id')
            ? UjianSemester::with(['tahunPelajaran', 'ujianMapels.mataPelajaran.kelas'])->find($request->ujian_id)
            : null;

        $allowedMapels = $selectedUjian
            ? $selectedUjian->ujianMapels->sortBy(fn ($item) => $item->mataPelajaran?->nama_mapel)
            : collect();

        $kelasOptions = $selectedUjian
            ? NilaiUjianSemester::where('ujian_semester_id', $selectedUjian->id)
                ->whereNotNull('kelas')
                ->select('kelas')
                ->distinct()
                ->orderBy('kelas')
                ->pluck('kelas')
            : collect();

        $nilai = null;
        $nilaiStats = [
            'total' => 0,
            'matched' => 0,
            'unmatched' => 0,
            'mapel' => 0,
            'nilai_terbesar' => null,
            'nilai_terkecil' => null,
            'rata_rata' => null,
        ];

        if ($selectedUjian) {
            $nilaiQuery = $this->nilaiQuery($request, $selectedUjian)
                ->with(['mataPelajaran', 'siswa', 'rombel.kelas']);

            $statsQuery = $this->nilaiQuery($request, $selectedUjian);
            $nilaiStats = [
                'total' => (clone $statsQuery)->count(),
                'matched' => (clone $statsQuery)->whereNotNull('master_siswa_id')->count(),
                'unmatched' => (clone $statsQuery)->whereNull('master_siswa_id')->count(),
                'mapel' => (clone $statsQuery)->distinct('mata_pelajaran_id')->count('mata_pelajaran_id'),
                'nilai_terbesar' => (clone $statsQuery)->max('nilai_akhir'),
                'nilai_terkecil' => (clone $statsQuery)->min('nilai_akhir'),
                'rata_rata' => (clone $statsQuery)->avg('nilai_akhir'),
            ];

            $sort = $request->get('sort', 'kelas');
            if ($sort === 'nilai_desc') {
                $nilaiQuery->orderByDesc('nilai_akhir')->orderBy('nama_lengkap');
            } elseif ($sort === 'nilai_asc') {
                $nilaiQuery->orderBy('nilai_akhir')->orderBy('nama_lengkap');
            } else {
                $nilaiQuery->orderBy('kelas')->orderBy('nomor_urut')->orderBy('nama_lengkap');
            }

            $nilai = $nilaiQuery
                ->paginate(25, ['*'], 'nilai_page')
                ->withQueryString();
        }

        $mataPelajaran = MataPelajaran::with('kelas')
            ->orderBy('nama_mapel')
            ->orderBy('kode_mapel')
            ->get();

        return view('pages.kesiswaan.ujian-semester.index', compact(
            'tahunAktif',
            'ujians',
            'selectedUjian',
            'allowedMapels',
            'kelasOptions',
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
            'mapels' => 'required|array|min:1',
            'mapels.*.mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'mapels.*.jumlah_soal' => 'required|integer|min:1|max:500',
        ]);

        $ujian = DB::transaction(function () use ($validated, $tahunAktif, $request) {
            $ujian = UjianSemester::create([
                'nama_ujian' => $validated['nama_ujian'],
                'kode_ujian' => $validated['kode_ujian'] ?? null,
                'tanggal_ujian' => $validated['tanggal_ujian'] ?? null,
                'keterangan' => $validated['keterangan'] ?? null,
                'tahun_pelajaran_id' => $tahunAktif->id,
                'semester' => $tahunAktif->semester,
                'created_by' => $request->user()?->id,
            ]);

            $this->syncMapelRows($ujian, $validated['mapels']);

            return $ujian;
        });

        toast('Data ujian semester berhasil ditambahkan.', 'success');

        return redirect()->route('kesiswaan.ujian-semester.index', ['ujian_id' => $ujian->id]);
    }

    public function storeMapel(Request $request, UjianSemester $ujianSemester)
    {
        $validated = $request->validate([
            'mapels' => 'required|array|min:1',
            'mapels.*.mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'mapels.*.jumlah_soal' => 'required|integer|min:1|max:500',
        ]);

        $this->syncMapelRows($ujianSemester, $validated['mapels']);

        toast('Daftar mata pelajaran ujian berhasil diperbarui.', 'success');

        return redirect()->route('kesiswaan.ujian-semester.index', ['ujian_id' => $ujianSemester->id]);
    }

    public function import(Request $request)
    {
        $validated = $request->validate([
            'ujian_semester_id' => 'required|exists:ujian_semesters,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'file_nilai' => 'required|file|mimes:xls,xlsx,csv|max:10240',
        ]);

        $ujian = UjianSemester::findOrFail($validated['ujian_semester_id']);
        $ujianMapel = UjianSemesterMapel::with('mataPelajaran')
            ->where('ujian_semester_id', $ujian->id)
            ->where('mata_pelajaran_id', $validated['mata_pelajaran_id'])
            ->first();

        if (!$ujianMapel) {
            toast('Mata pelajaran belum didaftarkan pada ujian ini.', 'error');
            return back();
        }

        $file = $request->file('file_nilai');

        try {
            $rows = $this->readNilaiRows($file->getRealPath(), $ujianMapel->jumlah_soal);
            $result = $this->saveNilaiRows($rows, $ujian, $ujianMapel, $file->getClientOriginalName(), $request->user()?->id);

            toast("Import selesai. {$result['imported']} data tersimpan, {$result['unmatched']} NIS belum cocok dengan master siswa.", 'success');
        } catch (\Throwable $e) {
            report($e);
            toast('Gagal import nilai: ' . $e->getMessage(), 'error');
        }

        return redirect()->route('kesiswaan.ujian-semester.index', [
            'ujian_id' => $ujian->id,
            'mata_pelajaran_id' => $ujianMapel->mata_pelajaran_id,
        ]);
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'ujian_id' => 'required|exists:ujian_semesters,id',
            'mata_pelajaran_id' => 'nullable|exists:mata_pelajarans,id',
            'kelas' => 'nullable|string|max:255',
            'sort' => 'nullable|in:kelas,nilai_desc,nilai_asc',
        ]);

        $ujian = UjianSemester::with('tahunPelajaran')->findOrFail($validated['ujian_id']);
        $data = $this->nilaiQuery($request, $ujian)
            ->with(['mataPelajaran', 'siswa', 'rombel.kelas'])
            ->orderBy('kelas')
            ->orderByDesc('nilai_akhir')
            ->get();

        $filename = 'rekap-nilai-' . str($ujian->nama_ujian)->slug() . '-' . now()->format('Ymd-His') . '.xlsx';

        return Excel::download(new UjianSemesterNilaiExport($ujian, $data, $request->only(['mata_pelajaran_id', 'kelas'])), $filename);
    }

    public function reportPdf(Request $request)
    {
        $validated = $request->validate([
            'ujian_id' => 'required|exists:ujian_semesters,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajarans,id',
            'kelas' => 'nullable|string|max:255',
            'sort' => 'nullable|in:kelas,nilai_desc,nilai_asc',
        ]);

        $ujian = UjianSemester::with('tahunPelajaran')->findOrFail($validated['ujian_id']);
        $mapel = MataPelajaran::with('kelas')->findOrFail($validated['mata_pelajaran_id']);
        $ujianMapel = UjianSemesterMapel::where('ujian_semester_id', $ujian->id)
            ->where('mata_pelajaran_id', $mapel->id)
            ->firstOrFail();

        $data = $this->nilaiQuery($request, $ujian)
            ->with(['mataPelajaran', 'siswa', 'rombel.kelas'])
            ->orderBy('kelas')
            ->orderByDesc('nilai_akhir')
            ->get();

        $pdf = Pdf::loadView('pdf.nilai-ujian-semester', [
            'ujian' => $ujian,
            'mapel' => $mapel,
            'ujianMapel' => $ujianMapel,
            'data' => $data,
            'kelas' => $request->kelas,
            'stats' => [
                'total' => $data->count(),
                'max' => $data->max('nilai_akhir'),
                'min' => $data->min('nilai_akhir'),
                'avg' => $data->avg('nilai_akhir'),
            ],
        ])->setPaper('a4', 'portrait');

        return $pdf->stream('laporan-nilai-' . str($ujian->nama_ujian)->slug() . '.pdf');
    }

    public function destroy(UjianSemester $ujianSemester)
    {
        $ujianSemester->delete();

        toast('Data ujian semester berhasil dihapus.', 'success');

        return redirect()->route('kesiswaan.ujian-semester.index');
    }

    private function nilaiQuery(Request $request, UjianSemester $ujian)
    {
        return NilaiUjianSemester::query()
            ->where('ujian_semester_id', $ujian->id)
            ->when($request->filled('mata_pelajaran_id'), fn ($query) => $query->where('mata_pelajaran_id', $request->mata_pelajaran_id))
            ->when($request->filled('kelas'), fn ($query) => $query->where('kelas', $request->kelas))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('kode_peserta', 'like', '%' . $search . '%')
                        ->orWhere('nama_lengkap', 'like', '%' . $search . '%')
                        ->orWhere('kelas', 'like', '%' . $search . '%');
                });
            });
    }

    private function syncMapelRows(UjianSemester $ujian, array $rows): void
    {
        foreach ($rows as $row) {
            UjianSemesterMapel::updateOrCreate(
                [
                    'ujian_semester_id' => $ujian->id,
                    'mata_pelajaran_id' => $row['mata_pelajaran_id'],
                ],
                ['jumlah_soal' => $row['jumlah_soal']]
            );

            $this->recalculateExistingScores($ujian, (int) $row['mata_pelajaran_id'], (int) $row['jumlah_soal']);
        }
    }

    private function recalculateExistingScores(UjianSemester $ujian, int $mataPelajaranId, int $jumlahSoal): void
    {
        if ($jumlahSoal <= 0) {
            return;
        }

        NilaiUjianSemester::where('ujian_semester_id', $ujian->id)
            ->where('mata_pelajaran_id', $mataPelajaranId)
            ->get()
            ->each(function (NilaiUjianSemester $nilai) use ($jumlahSoal) {
                $jumlahBenar = $nilai->jumlah_benar ?? (int) $nilai->nilai;

                $nilai->update([
                    'jumlah_benar' => $jumlahBenar,
                    'jumlah_soal' => $jumlahSoal,
                    'nilai_akhir' => min(100, round(($jumlahBenar / $jumlahSoal) * 100, 2)),
                    'nilai' => min(100, round(($jumlahBenar / $jumlahSoal) * 100, 2)),
                ]);
            });
    }

    private function readNilaiRows(string $path, int $jumlahSoal): array
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

            $jumlahBenar = $this->cellToInteger($sheet->getCellByColumnAndRow(5, $row)->getValue()) ?? 0;
            $nilaiAkhir = $jumlahSoal > 0 ? min(100, round(($jumlahBenar / $jumlahSoal) * 100, 2)) : null;

            $rows[] = [
                'nomor_urut' => $this->cellToInteger($sheet->getCellByColumnAndRow(1, $row)->getValue()),
                'kode_peserta' => $kodePeserta,
                'nama_lengkap' => $namaLengkap,
                'kelas' => $this->cellToString($sheet->getCellByColumnAndRow(4, $row)->getValue()),
                'jumlah_benar' => $jumlahBenar,
                'jumlah_soal' => $jumlahSoal,
                'nilai_akhir' => $nilaiAkhir,
                'baris_excel' => $row,
            ];
        }

        if (count($rows) === 0) {
            throw new \RuntimeException('Tidak ada data nilai yang terbaca mulai baris 8.');
        }

        return $rows;
    }

    private function saveNilaiRows(array $rows, UjianSemester $ujian, UjianSemesterMapel $ujianMapel, string $fileName, ?int $userId): array
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

        DB::transaction(function () use ($rows, $ujian, $ujianMapel, $fileName, $userId, $siswaByNis, $rombelsByKelas, $now, &$unmatched) {
            foreach ($rows as $row) {
                $siswa = $siswaByNis->get($row['kode_peserta']);
                $rombel = $rombelsByKelas->get($row['kelas']);

                if (!$siswa) {
                    $unmatched++;
                }

                NilaiUjianSemester::updateOrCreate(
                    [
                        'ujian_semester_id' => $ujian->id,
                        'mata_pelajaran_id' => $ujianMapel->mata_pelajaran_id,
                        'kode_peserta' => $row['kode_peserta'],
                    ],
                    [
                        'master_siswa_id' => $siswa?->id,
                        'rombel_id' => $rombel?->id,
                        'nomor_urut' => $row['nomor_urut'],
                        'nama_lengkap' => $row['nama_lengkap'],
                        'kelas' => $row['kelas'],
                        'nilai' => $row['nilai_akhir'],
                        'jumlah_benar' => $row['jumlah_benar'],
                        'jumlah_soal' => $row['jumlah_soal'],
                        'nilai_akhir' => $row['nilai_akhir'],
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
}
