<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('okr_weekly_report_items', function (Blueprint $table) {
            $table->foreignId('okr_progress_update_id')
                ->nullable()
                ->after('evidence_path')
                ->constrained('okr_progress_updates')
                ->nullOnDelete();
            $table->foreignId('progress_applied_by')
                ->nullable()
                ->after('okr_progress_update_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamp('progress_applied_at')->nullable()->after('progress_applied_by');
        });
    }

    public function down(): void
    {
        Schema::table('okr_weekly_report_items', function (Blueprint $table) {
            $table->dropConstrainedForeignId('okr_progress_update_id');
            $table->dropConstrainedForeignId('progress_applied_by');
            $table->dropColumn('progress_applied_at');
        });
    }
};
