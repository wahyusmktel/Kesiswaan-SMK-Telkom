<?php

use App\Models\OkrUnit;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okr_units', function (Blueprint $table) {
            $table->foreignId('parent_id')->nullable()->after('id')->constrained('okr_units')->nullOnDelete();
        });

        // Set parent unit Kurikulum untuk unit Kaprodi
        $kurikulum = OkrUnit::where('code', 'KURIKULUM')->first();
        if ($kurikulum) {
            OkrUnit::whereIn('code', ['KAPRODI-PPLGIM-ANIMASI', 'KAPRODI-TJKT'])
                ->update(['parent_id' => $kurikulum->id]);
        }
    }

    public function down(): void
    {
        Schema::table('okr_units', function (Blueprint $table) {
            $table->dropForeign(['parent_id']);
            $table->dropColumn('parent_id');
        });
    }
};
