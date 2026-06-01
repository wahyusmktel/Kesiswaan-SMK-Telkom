<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ujian_semester_mapels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_semester_id')->constrained('ujian_semesters')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->unsignedInteger('jumlah_soal')->default(0);
            $table->timestamps();

            $table->unique(['ujian_semester_id', 'mata_pelajaran_id'], 'ujian_semester_mapel_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_semester_mapels');
    }
};
