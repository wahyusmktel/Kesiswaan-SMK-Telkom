<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_weekly_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_weekly_report_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('progress_percent', 5, 2);
            $table->enum('status', ['not_started', 'on_progress', 'completed', 'blocked']);
            $table->text('note');
            $table->text('blockers')->nullable();
            $table->string('evidence_path')->nullable();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->index(['okr_weekly_report_item_id', 'recorded_at'], 'okr_weekly_progress_item_recorded_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_weekly_progress_updates');
    }
};
