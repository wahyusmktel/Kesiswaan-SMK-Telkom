<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('assessment_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran')->cascadeOnDelete();
            $table->string('title');
            $table->string('semester', 20);
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->index(['tahun_pelajaran_id', 'semester', 'is_active']);
        });

        Schema::create('assessment_instruments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained('assessment_periods')->cascadeOnDelete();
            $table->string('type', 50);
            $table->string('title');
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->unique(['assessment_period_id', 'type']);
        });

        Schema::create('assessment_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_instrument_id')->constrained('assessment_instruments')->cascadeOnDelete();
            $table->text('question_text');
            $table->string('answer_type', 30);
            $table->json('options')->nullable();
            $table->unsignedSmallInteger('max_score')->default(5);
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('assessment_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_period_id')->constrained('assessment_periods')->cascadeOnDelete();
            $table->foreignId('assessment_instrument_id')->constrained('assessment_instruments')->cascadeOnDelete();
            $table->foreignId('assessor_user_id')->constrained('users')->cascadeOnDelete();
            $table->string('assessable_type', 100);
            $table->unsignedBigInteger('assessable_id');
            $table->index(['assessable_type', 'assessable_id']);
            $table->decimal('score', 5, 2)->default(0);
            $table->timestamp('submitted_at')->nullable();
            $table->timestamps();
            $table->unique(['assessment_instrument_id', 'assessor_user_id', 'assessable_type', 'assessable_id'], 'assessment_unique_response');
        });

        Schema::create('assessment_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assessment_response_id')->constrained('assessment_responses')->cascadeOnDelete();
            $table->foreignId('assessment_question_id')->constrained('assessment_questions')->cascadeOnDelete();
            $table->json('answer_value')->nullable();
            $table->decimal('score', 6, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assessment_answers');
        Schema::dropIfExists('assessment_responses');
        Schema::dropIfExists('assessment_questions');
        Schema::dropIfExists('assessment_instruments');
        Schema::dropIfExists('assessment_periods');
    }
};
