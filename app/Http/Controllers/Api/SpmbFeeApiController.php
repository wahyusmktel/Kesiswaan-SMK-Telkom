<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\SpmbFee;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class SpmbFeeApiController extends Controller
{
    public function __invoke(): JsonResponse
    {
        $payload = Cache::remember('api.spmb.fees', now()->addMinutes(5), function (): array {
            $fees = SpmbFee::query()
                ->where('is_active', true)
                ->orderBy('sort_order')
                ->orderBy('id')
                ->get();
            $academicYear = AppSetting::query()->value('spmb_academic_year') ?: '2027/2028';
            $total = $fees->sum('amount');
            $lastUpdatedAt = $fees->max('updated_at');

            return [
                'data' => $fees->values()->map(fn (SpmbFee $fee, int $index): array => [
                    'id' => $fee->id,
                    'number' => $index + 1,
                    'name' => $fee->name,
                    'amount' => $fee->amount,
                    'formatted_amount' => $this->formatRupiah($fee->amount),
                ])->all(),
                'meta' => [
                    'academic_year' => $academicYear,
                    'total' => $total,
                    'formatted_total' => $this->formatRupiah($total),
                    'last_updated_at' => $lastUpdatedAt?->toIso8601String(),
                ],
            ];
        });

        return response()->json($payload)
            ->header('Cache-Control', 'public, max-age=300, s-maxage=300');
    }

    private function formatRupiah(int $amount): string
    {
        return 'Rp '.number_format($amount, 0, ',', '.');
    }
}
