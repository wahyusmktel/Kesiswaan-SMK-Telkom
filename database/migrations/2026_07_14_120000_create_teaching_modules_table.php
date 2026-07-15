<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teaching_modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->string('program_keahlian', 150);
            $table->foreignId('mata_pelajaran_id')->nullable()->constrained('mata_pelajarans')->nullOnDelete();
            $table->string('mata_pelajaran');
            $table->string('fase', 20);
            $table->string('nama_penyusun');
            $table->string('instansi');
            $table->foreignId('tahun_pelajaran_id')->nullable()->constrained('tahun_pelajaran')->nullOnDelete();
            $table->string('tahun_pelajaran', 20);
            $table->string('semester', 20);
            $table->string('nama_modul');
            $table->string('alokasi_waktu', 50);
            $table->string('jenjang', 30);
            $table->string('kelas', 50);
            $table->string('kode_modul', 50);
            $table->string('jumlah_murid', 30);
            $table->text('lingkup_materi');
            $table->json('content')->nullable();
            $table->unsignedTinyInteger('content_version')->default(1);
            $table->string('status', 20)->default('draft');
            $table->timestamps();

            $table->unique(
                ['teacher_id', 'tahun_pelajaran_id', 'kode_modul'],
                'teaching_modules_teacher_year_code_unique'
            );
            $table->index(['teacher_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teaching_modules');
    }
};
