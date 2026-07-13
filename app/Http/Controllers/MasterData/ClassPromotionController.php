<?php

namespace App\Http\Controllers\MasterData;

use App\Http\Controllers\Controller;
use App\Models\ClassPromotion;
use App\Models\TahunPelajaran;
use App\Services\ClassPromotionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ClassPromotionController extends Controller
{
    public function __construct(private readonly ClassPromotionService $promotionService)
    {
    }

    public function index(Request $request)
    {
        $target = TahunPelajaran::where('is_active', true)->first();
        $sources = TahunPelajaran::where('semester', 'Genap')
            ->when($target, fn ($query) => $query->where('id', '!=', $target->id))
            ->orderByDesc('tahun')
            ->get();
        $source = $request->filled('source_tahun_pelajaran_id')
            ? $sources->firstWhere('id', (int) $request->integer('source_tahun_pelajaran_id'))
            : $this->recommendedSource($sources, $target);

        $preview = null;
        $previewError = null;

        if ($source && $target) {
            try {
                $preview = $this->promotionService->preview($source, $target);
            } catch (ValidationException $exception) {
                $previewError = collect($exception->errors())->flatten()->implode(' ');
            }
        }

        $history = ClassPromotion::with(['sourceTahunPelajaran', 'targetTahunPelajaran', 'processor'])
            ->latest()
            ->paginate(10);

        return view('pages.master-data.rombel.promotion', compact(
            'target',
            'sources',
            'source',
            'preview',
            'previewError',
            'history'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'source_tahun_pelajaran_id' => 'required|exists:tahun_pelajaran,id',
        ]);

        $source = TahunPelajaran::findOrFail($validated['source_tahun_pelajaran_id']);
        $target = TahunPelajaran::where('is_active', true)->first();

        if (!$target) {
            return back()->with('error', 'Aktifkan tahun pelajaran tujuan terlebih dahulu.');
        }

        try {
            $result = $this->promotionService->process($source, $target, $request->user());

            return redirect()->route('master-data.rombel.promotion.index')
                ->with('success', "Kenaikan kelas selesai: {$result->promoted_count} siswa naik kelas dan {$result->graduated_count} siswa menjadi alumni.");
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            Log::error('Class promotion failed', [
                'source_tahun_pelajaran_id' => $source->id,
                'target_tahun_pelajaran_id' => $target->id,
                'user_id' => $request->user()->id,
                'exception' => $exception,
            ]);

            return back()->withInput()->with('error', 'Kenaikan kelas gagal diproses. Periksa data kelas, rombel, dan wali kelas.');
        }
    }

    private function recommendedSource($sources, ?TahunPelajaran $target): ?TahunPelajaran
    {
        if (!$target || !preg_match('/^(\d{4})\/(\d{4})$/', $target->tahun, $targetYears)) {
            return $sources->first();
        }

        return $sources->first(function (TahunPelajaran $source) use ($targetYears) {
            return preg_match('/^(\d{4})\/(\d{4})$/', $source->tahun, $sourceYears)
                && $sourceYears[2] === $targetYears[1];
        }) ?? $sources->first();
    }
}
