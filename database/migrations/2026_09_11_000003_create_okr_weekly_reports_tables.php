<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_weekly_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_period_id')->constrained()->cascadeOnDelete();
            $table->foreignId('okr_unit_id')->constrained()->cascadeOnDelete();
            $table->date('week_start');
            $table->date('week_end');
            $table->text('weekly_focus')->nullable();
            $table->text('support_needed')->nullable();
            $table->enum('status', ['draft', 'submitted', 'reviewed'])->default('draft');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('review_notes')->nullable();
            $table->timestamps();

            $table->unique(['okr_period_id', 'okr_unit_id', 'week_start'], 'okr_weekly_reports_period_unit_week_unique');
            $table->index(['week_start', 'status']);
        });

        Schema::create('okr_weekly_report_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_weekly_report_id')->constrained()->cascadeOnDelete();
            $table->foreignId('okr_plan_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('priority_order');
            $table->text('commitment');
            $table->text('measurable_target');
            $table->text('cross_unit_dependencies')->nullable();
            $table->text('approval_needs')->nullable();
            $table->text('actual_result')->nullable();
            $table->decimal('completion_percent', 5, 2)->default(0);
            $table->enum('final_status', ['not_started', 'on_progress', 'completed', 'blocked'])->default('not_started');
            $table->text('blockers')->nullable();
            $table->text('next_follow_up')->nullable();
            $table->string('evidence_path')->nullable();
            $table->timestamps();

            $table->unique(['okr_weekly_report_id', 'priority_order'], 'okr_weekly_items_report_order_unique');
            $table->index(['final_status', 'completion_percent']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_weekly_report_items');
        Schema::dropIfExists('okr_weekly_reports');
    }
};
