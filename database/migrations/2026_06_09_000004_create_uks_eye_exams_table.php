<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('uks_eye_exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('handled_by')->constrained('users')->cascadeOnDelete();
            $table->enum('examinee_type', ['siswa', 'pegawai']);
            $table->foreignId('master_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('examined_at');
            $table->string('color_blind_result', 40);
            $table->text('color_blind_notes')->nullable();
            $table->string('visual_acuity_right', 20)->nullable();
            $table->string('visual_acuity_left', 20)->nullable();
            $table->text('eye_health_findings')->nullable();
            $table->text('recommendation')->nullable();
            $table->string('conclusion', 80);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['examinee_type', 'examined_at']);
            $table->index(['master_siswa_id', 'examined_at']);
            $table->index(['user_id', 'examined_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('uks_eye_exams');
    }
};
