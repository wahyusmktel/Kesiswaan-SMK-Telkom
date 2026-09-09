<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->string('employee_category', 20)->default('guru')->after('user_id')->index();
        });

        DB::table('master_gurus')
            ->whereIn('id', DB::table('dapodik_gurus')
                ->select('master_guru_id')
                ->whereNotNull('master_guru_id')
                ->where('employee_category', 'tpa'))
            ->update(['employee_category' => 'tpa']);
    }

    public function down(): void
    {
        Schema::table('master_gurus', function (Blueprint $table) {
            $table->dropIndex(['employee_category']);
            $table->dropColumn('employee_category');
        });
    }
};
