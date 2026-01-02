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
        Schema::create('lms_materials', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('master_guru_id');
            $table->unsignedBigInteger('rombel_id');
            $table->string('title');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->boolean('is_published')->default(true);
            $table->timestamps();

            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
            $table->foreign('master_guru_id')->references('id')->on('master_gurus')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
        });

        Schema::create('lms_assignments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mata_pelajaran_id');
            $table->unsignedBigInteger('master_guru_id');
            $table->unsignedBigInteger('rombel_id');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->integer('points')->default(100);
            $table->timestamps();

            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->onDelete('cascade');
            $table->foreign('master_guru_id')->references('id')->on('master_gurus')->onDelete('cascade');
            $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
        });

        Schema::create('lms_submissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lms_assignment_id');
            $table->unsignedBigInteger('master_siswa_id');
            $table->text('content')->nullable();
            $table->string('file_path')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->timestamps();

            $table->foreign('lms_assignment_id')->references('id')->on('lms_assignments')->onDelete('cascade');
            $table->foreign('master_siswa_id')->references('id')->on('master_siswa')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lms_submissions');
        Schema::dropIfExists('lms_assignments');
        Schema::dropIfExists('lms_materials');
    }
};
