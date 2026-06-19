<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('lesson_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('class_id');
            $table->unsignedBigInteger('subject_id');
            $table->date('teach_date');
            $table->string('topic');
            $table->text('learning_objectives');
            $table->text('pre_assessment')->nullable();
            $table->json('methods')->nullable();
            $table->json('activities')->nullable();
            $table->json('resources')->nullable();
            $table->text('final_assessment')->nullable();
            $table->json('differentiation')->nullable();
            $table->integer('duration_minutes')->default(90);
            $table->enum('status', ['draft', 'published', 'done'])->default('draft');
            $table->text('reflection')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('class_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('subject_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
        });

        Schema::create('lesson_todos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lesson_plan_id');
            $table->string('todo_text');
            $table->enum('category', ['materi', 'media', 'administrasi', 'ruangan', 'lainnya'])->default('lainnya');
            $table->boolean('is_done')->default(false);
            $table->dateTime('due_before')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('lesson_plan_id')->references('id')->on('lesson_plans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_todos');
        Schema::dropIfExists('lesson_plans');
    }
};
