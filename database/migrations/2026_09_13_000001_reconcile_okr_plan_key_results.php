<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('okr_plans')) {
            return;
        }

        // Maksimal kedalaman saat ini tiga tingkat. Beberapa putaran memastikan
        // perbaikan induk diteruskan sampai ke seluruh anak dan cucunya.
        for ($depth = 0; $depth < 3; $depth++) {
            $mismatches = DB::table('okr_plans as child')
                ->join('okr_plans as parent', 'parent.id', '=', 'child.parent_id')
                ->whereColumn('child.okr_key_result_id', '!=', 'parent.okr_key_result_id')
                ->select('child.id', 'parent.okr_key_result_id')
                ->orderBy('child.id')
                ->get();

            if ($mismatches->isEmpty()) {
                break;
            }

            $mismatches->each(function ($plan) {
                DB::table('okr_plans')
                    ->where('id', $plan->id)
                    ->update([
                        'okr_key_result_id' => $plan->okr_key_result_id,
                        'updated_at' => now(),
                    ]);
            });
        }
    }

    public function down(): void
    {
        // Relasi lama yang tidak konsisten tidak dipulihkan.
    }
};
