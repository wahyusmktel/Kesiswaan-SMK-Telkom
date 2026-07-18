<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('okr_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->nullable()->constrained('tahun_pelajaran')->nullOnDelete();
            $table->string('title');
            $table->text('vision')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->enum('status', ['draft', 'active', 'closed'])->default('active');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['status', 'tahun_pelajaran_id']);
        });

        Schema::create('okr_units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('name');
            $table->json('role_names')->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('okr_objectives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_period_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30);
            $table->text('title');
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['okr_period_id', 'code']);
        });

        Schema::create('okr_key_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_objective_id')->constrained()->cascadeOnDelete();
            $table->string('code', 30);
            $table->string('title');
            $table->text('description');
            $table->enum('metric_type', ['percentage', 'number', 'currency', 'boolean'])->default('percentage');
            $table->decimal('baseline_value', 15, 2)->default(0);
            $table->decimal('target_value', 15, 2)->default(100);
            $table->string('metric_unit', 40)->default('%');
            $table->date('due_date')->nullable();
            $table->decimal('weight', 6, 2)->default(1);
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->timestamps();
            $table->unique(['okr_objective_id', 'code']);
        });

        Schema::create('okr_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_key_result_id')->constrained()->cascadeOnDelete();
            $table->foreignId('okr_unit_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('okr_plans')->cascadeOnDelete();
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('level', ['annual', 'monthly', 'weekly']);
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('starts_at')->nullable();
            $table->date('ends_at')->nullable();
            $table->decimal('target_value', 15, 2)->nullable();
            $table->decimal('current_value', 15, 2)->nullable();
            $table->string('metric_unit', 40)->nullable();
            $table->decimal('weight', 6, 2)->default(1);
            $table->decimal('progress_percent', 5, 2)->default(0);
            $table->enum('status', ['not_started', 'in_progress', 'at_risk', 'completed', 'cancelled'])->default('not_started');
            $table->text('success_indicator')->nullable();
            $table->text('latest_evaluation')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['okr_unit_id', 'level', 'status']);
            $table->index(['okr_key_result_id', 'okr_unit_id']);
        });

        Schema::create('okr_progress_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('okr_plan_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('progress_before', 5, 2)->default(0);
            $table->decimal('progress_after', 5, 2);
            $table->decimal('current_value', 15, 2)->nullable();
            $table->enum('status', ['not_started', 'in_progress', 'at_risk', 'completed', 'cancelled']);
            $table->text('note');
            $table->string('evidence_path')->nullable();
            $table->date('recorded_at');
            $table->timestamps();
            $table->index(['okr_plan_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('okr_progress_updates');
        Schema::dropIfExists('okr_plans');
        Schema::dropIfExists('okr_key_results');
        Schema::dropIfExists('okr_objectives');
        Schema::dropIfExists('okr_units');
        Schema::dropIfExists('okr_periods');
    }
};
