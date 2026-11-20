<?php

namespace App\Http\Controllers\Kaprodi;

use App\Exports\UkkIndikatorTemplateExport;
use App\Exports\UkkSoalTemplateExport;
use App\Http\Controllers\Controller;
use App\Models\UkkInstrumen;
use App\Models\UkkUjian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;

class InstrumenController extends Controller
{
    public function index()
    {
        $instrumens = UkkInstrumen::with(['ujian.tahunPelajaran'])
            ->withCount(['soalPengetahuan', 'kategoriKeterampilan'])
            ->latest()
            ->paginate(20);

        $ujians = UkkUjian::with('tahunPelajaran')->latest()->get();

        return view('pages.kaprodi.instrumen.index', compact('instrumens', 'ujians'));
    }

    public function create()
    {
        $ujians  = UkkUjian::with('tahunPelajaran')->latest()->get();
        $instrumen = null;
        $initialData = [
            'nama_instrumen'        => '',
            'ukk_ujian_id'          => null,
            'bobot_pengetahuan'     => 30,
            'soal_pengetahuan'      => [],
            'kategori_keterampilan' => [],
        ];

        return view('pages.kaprodi.instrumen.builder', compact('instrumen', 'ujians', 'initialData'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama_instrumen'                                     => 'required|string|max:255',
            'ukk_ujian_id'                                       => 'required|exists:ukk_ujians,id',
            'bobot_pengetahuan'                                  => 'required|integer|min:0|max:100',
            'soal_pengetahuan'                                   => 'nullable|array',
            'soal_pengetahuan.*.id'                              => 'nullable|integer',
            'soal_pengetahuan.*.pertanyaan'                      => 'required|string',
            'soal_pengetahuan.*.kunci_jawaban'                   => 'nullable|string',
            'kategori_keterampilan'                              => 'nullable|array',
            'kategori_keterampilan.*.id'                         => 'nullable|integer',
            'kategori_keterampilan.*.nama_kategori'              => 'required|string|max:255',
            'kategori_keterampilan.*.bobot'                      => 'required|integer|min:0|max:100',
            'kategori_keterampilan.*.indikator'                  => 'nullable|array',
            'kategori_keterampilan.*.indikator.*.id'             => 'nullable|integer',
            'kategori_keterampilan.*.indikator.*.nama_indikator' => 'required|string',
        ]);

        $instrumen = DB::transaction(function () use ($validated) {
            $instrumen = UkkInstrumen::create([
                'ukk_ujian_id'      => $validated['ukk_ujian_id'],
                'nama_instrumen'    => $validated['nama_instrumen'],
                'bobot_pengetahuan' => $validated['bobot_pengetahuan'],
            ]);

            $this->syncNested($instrumen, $validated);

            return $instrumen;
        });

        return response()->json([
            'message'     => 'Instrumen penilaian berhasil disimpan.',
            'instrumen_id' => $instrumen->id,
        ]);
    }

    public function edit(UkkInstrumen $instrumen)
    {
        $instrumen->load([
            'soalPengetahuan',
            'kategoriKeterampilan.indikator',
        ]);

        $ujians = UkkUjian::with('tahunPelajaran')->latest()->get();

        $initialData = [
            'nama_instrumen'        => $instrumen->nama_instrumen,
            'ukk_ujian_id'          => $instrumen->ukk_ujian_id,
            'bobot_pengetahuan'     => $instrumen->bobot_pengetahuan,
            'soal_pengetahuan'      => $instrumen->soalPengetahuan->map(fn ($s) => [
                'id'            => $s->id,
                'pertanyaan'    => $s->pertanyaan,
                'kunci_jawaban' => $s->kunci_jawaban,
            ])->values()->all(),
            'kategori_keterampilan' => $instrumen->kategoriKeterampilan->map(fn ($k) => [
                'id'             => $k->id,
                'nama_kategori'  => $k->nama_kategori,
                'bobot'          => $k->bobot,
                'indikator'      => $k->indikator->map(fn ($i) => [
                    'id'              => $i->id,
                    'nama_indikator'  => $i->nama_indikator,
                ])->values()->all(),
            ])->values()->all(),
        ];

        return view('pages.kaprodi.instrumen.builder', compact('instrumen', 'ujians', 'initialData'));
    }

    public function update(Request $request, UkkInstrumen $instrumen)
    {
        $validated = $request->validate([
            'nama_instrumen'                                     => 'required|string|max:255',
            'ukk_ujian_id'                                       => 'required|exists:ukk_ujians,id',
            'bobot_pengetahuan'                                  => 'required|integer|min:0|max:100',
            'soal_pengetahuan'                                   => 'nullable|array',
            'soal_pengetahuan.*.id'                              => 'nullable|integer',
            'soal_pengetahuan.*.pertanyaan'                      => 'required|string',
            'soal_pengetahuan.*.kunci_jawaban'                   => 'nullable|string',
            'kategori_keterampilan'                              => 'nullable|array',
            'kategori_keterampilan.*.id'                         => 'nullable|integer',
            'kategori_keterampilan.*.nama_kategori'              => 'required|string|max:255',
            'kategori_keterampilan.*.bobot'                      => 'required|integer|min:0|max:100',
            'kategori_keterampilan.*.indikator'                  => 'nullable|array',
            'kategori_keterampilan.*.indikator.*.id'             => 'nullable|integer',
            'kategori_keterampilan.*.indikator.*.nama_indikator' => 'required|string',
        ]);

        DB::transaction(function () use ($instrumen, $validated) {
            $instrumen->update([
                'ukk_ujian_id'      => $validated['ukk_ujian_id'],
                'nama_instrumen'    => $validated['nama_instrumen'],
                'bobot_pengetahuan' => $validated['bobot_pengetahuan'],
            ]);

            $this->syncNested($instrumen, $validated);
        });

        return response()->json(['message' => 'Instrumen penilaian berhasil diperbarui.']);
    }

    public function destroy(UkkInstrumen $instrumen)
    {
        $instrumen->delete(); // cascades to soal, kategori, indikator

        return response()->json(['message' => 'Instrumen penilaian berhasil dihapus.']);
    }

    public function downloadTemplateSoal()
    {
        return Excel::download(new UkkSoalTemplateExport(), 'Template_Soal_Pengetahuan_UKK.xlsx');
    }

    public function downloadTemplateIndikator(Request $request)
    {
        $nama = $request->query('kategori', '');
        return Excel::download(new UkkIndikatorTemplateExport($nama), 'Template_Indikator_Keterampilan_UKK.xlsx');
    }

    public function importSoal(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $path        = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            $items = [];
            foreach (array_slice($rows, 1) as $row) {
                $val   = trim((string) ($row[1] ?? ''));
                $kunci = trim((string) ($row[2] ?? ''));
                if ($val !== '' && count($items) < 100) {
                    $items[] = [
                        'pertanyaan'    => $val,
                        'kunci_jawaban' => $kunci,
                    ];
                }
            }

            if (empty($items)) {
                return response()->json([
                    'message' => 'Tidak ada data yang ditemukan. Pastikan data berada di kolom B mulai baris ke-2.',
                ], 422);
            }

            return response()->json(['items' => $items, 'count' => count($items)]);
        } catch (\Throwable) {
            return response()->json([
                'message' => 'File tidak valid atau rusak. Gunakan template yang tersedia.',
            ], 422);
        }
    }

    public function importIndikator(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'file.required' => 'Pilih file Excel terlebih dahulu.',
            'file.mimes'    => 'Format file harus xlsx, xls, atau csv.',
            'file.max'      => 'Ukuran file maksimal 5 MB.',
        ]);

        try {
            $path        = $request->file('file')->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $rows        = $spreadsheet->getActiveSheet()->toArray(null, true, true, false);

            $items = [];
            foreach (array_slice($rows, 1) as $row) {
                $val = trim((string) ($row[1] ?? ''));
                if ($val !== '' && count($items) < 100) {
                    $items[] = $val;
                }
            }

            if (empty($items)) {
                return response()->json([
                    'message' => 'Tidak ada data yang ditemukan. Pastikan data berada di kolom B mulai baris ke-2.',
                ], 422);
            }

            return response()->json(['items' => $items, 'count' => count($items)]);
        } catch (\Throwable) {
            return response()->json([
                'message' => 'File tidak valid atau rusak. Gunakan template yang tersedia.',
            ], 422);
        }
    }

    private function syncNested(UkkInstrumen $instrumen, array $validated): void
    {
        // ── 1. Soal Pengetahuan (Smart Sync: update existing, insert new, delete removed) ──
        $existingSoal = $instrumen->soalPengetahuan()->get()->keyBy('id');
        $keptSoalIds  = [];

        foreach ($validated['soal_pengetahuan'] ?? [] as $i => $soal) {
            $soalId = $soal['id'] ?? null;
            if ($soalId && $existingSoal->has($soalId)) {
                $existingSoal[$soalId]->update([
                    'pertanyaan'    => $soal['pertanyaan'],
                    'kunci_jawaban' => $soal['kunci_jawaban'] ?? null,
                    'urutan'        => $i + 1,
                ]);
                $keptSoalIds[] = $soalId;
            } else {
                $newSoal = $instrumen->soalPengetahuan()->create([
                    'pertanyaan'    => $soal['pertanyaan'],
                    'kunci_jawaban' => $soal['kunci_jawaban'] ?? null,
                    'urutan'        => $i + 1,
                ]);
                $keptSoalIds[] = $newSoal->id;
            }
        }

        $instrumen->soalPengetahuan()->whereNotIn('id', $keptSoalIds)->delete();

        // ── 2. Kategori Keterampilan & Indikator (Smart Sync) ──
        $existingKat = $instrumen->kategoriKeterampilan()->with('indikator')->get()->keyBy('id');
        $keptKatIds  = [];

        foreach ($validated['kategori_keterampilan'] ?? [] as $i => $kat) {
            $katId = $kat['id'] ?? null;
            if ($katId && $existingKat->has($katId)) {
                $kategori = $existingKat[$katId];
                $kategori->update([
                    'nama_kategori' => $kat['nama_kategori'],
                    'bobot'         => $kat['bobot'],
                    'urutan'        => $i + 1,
                ]);
            } else {
                $kategori = $instrumen->kategoriKeterampilan()->create([
                    'nama_kategori' => $kat['nama_kategori'],
                    'bobot'         => $kat['bobot'],
                    'urutan'        => $i + 1,
                ]);
            }
            $keptKatIds[] = $kategori->id;

            $existingInd = $kategori->indikator->keyBy('id');
            $keptIndIds  = [];

            foreach ($kat['indikator'] ?? [] as $j => $ind) {
                $indId = $ind['id'] ?? null;
                if ($indId && $existingInd->has($indId)) {
                    $existingInd[$indId]->update([
                        'nama_indikator' => $ind['nama_indikator'],
                        'urutan'         => $j + 1,
                    ]);
                    $keptIndIds[] = $indId;
                } else {
                    $newInd = $kategori->indikator()->create([
                        'nama_indikator' => $ind['nama_indikator'],
                        'urutan'         => $j + 1,
                    ]);
                    $keptIndIds[] = $newInd->id;
                }
            }

            $kategori->indikator()->whereNotIn('id', $keptIndIds)->delete();
        }

        $instrumen->kategoriKeterampilan()->whereNotIn('id', $keptKatIds)->delete();
    }
}
