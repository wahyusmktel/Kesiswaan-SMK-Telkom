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
        Schema::create('millionaire_sets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Guru Kelas who created it
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('millionaire_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('set_id')->constrained('millionaire_sets')->onDelete('cascade');
            $table->text('question');
            $table->string('option_a');
            $table->string('option_b');
            $table->string('option_c');
            $table->string('option_d');
            $table->char('correct_answer', 1); // A, B, C, D
            $table->integer('level'); // 1 to 15
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('millionaire_questions');
        Schema::dropIfExists('millionaire_sets');
    }
};
