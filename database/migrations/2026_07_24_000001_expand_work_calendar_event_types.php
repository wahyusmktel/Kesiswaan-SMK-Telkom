<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_calendar_events', function (Blueprint $table) {
            $table->string('type', 100)->default('holiday')->change();
            $table->boolean('is_non_working')->default(false)->after('type')->index();
        });

        DB::table('work_calendar_events')
            ->whereIn('type', ['holiday', 'collective_leave'])
            ->update(['is_non_working' => true]);
    }

    public function down(): void
    {
        DB::table('work_calendar_events')
            ->whereNotIn('type', ['holiday', 'collective_leave'])
            ->update(['type' => 'holiday']);

        Schema::table('work_calendar_events', function (Blueprint $table) {
            $table->dropIndex(['is_non_working']);
            $table->dropColumn('is_non_working');
            $table->enum('type', ['holiday', 'collective_leave'])->default('holiday')->change();
        });
    }
};
