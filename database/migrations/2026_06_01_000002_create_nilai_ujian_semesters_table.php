<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nilai_ujian_semesters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ujian_semester_id')->constrained('ujian_semesters')->cascadeOnDelete();
            $table->foreignId('mata_pelajaran_id')->constrained('mata_pelajarans')->cascadeOnDelete();
            $table->foreignId('master_siswa_id')->nullable()->constrained('master_siswa')->nullOnDelete();
            $table->foreignId('rombel_id')->nullable()->constrained('rombels')->nullOnDelete();
            $table->unsignedInteger('nomor_urut')->nullable();
            $table->string('kode_peserta');
            $table->string('nama_lengkap');
            $table->string('kelas')->nullable();
            $table->decimal('nilai', 6, 2)->nullable();
            $table->unsignedInteger('baris_excel')->nullable();
            $table->string('nama_file')->nullable();
            $table->foreignId('imported_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('imported_at')->nullable();
            $table->timestamps();

            $table->unique(['ujian_semester_id', 'mata_pelajaran_id', 'kode_peserta'], 'nilai_ujian_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nilai_ujian_semesters');
    }
};
