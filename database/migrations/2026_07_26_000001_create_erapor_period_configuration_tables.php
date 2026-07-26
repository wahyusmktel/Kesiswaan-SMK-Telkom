<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erapor_period_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')
                ->unique()
                ->constrained('tahun_pelajaran')
                ->cascadeOnDelete();
            $table->enum('workflow_status', [
                'setup',
                'assessment',
                'validation',
                'published',
                'locked',
            ])->default('setup');
            $table->timestamp('score_entry_starts_at')->nullable();
            $table->timestamp('score_entry_ends_at')->nullable();
            $table->date('report_date')->nullable();
            $table->foreignId('configured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('configured_at')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });

        Schema::create('erapor_rombel_curricula', function (Blueprint $table) {
            $table->id();
            $table->foreignId('erapor_period_setting_id')
                ->constrained('erapor_period_settings')
                ->cascadeOnDelete();
            $table->foreignId('rombel_id')->unique()->constrained('rombels')->cascadeOnDelete();
            $table->foreignId('erapor_ref_curriculum_id')
                ->constrained('erapor_ref_curricula')
                ->restrictOnDelete();
            $table->foreignId('configured_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('configured_at');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(
                ['erapor_period_setting_id', 'erapor_ref_curriculum_id'],
                'erapor_rombel_curriculum_period_ref_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('erapor_rombel_curricula');
        Schema::dropIfExists('erapor_period_settings');
    }
};
