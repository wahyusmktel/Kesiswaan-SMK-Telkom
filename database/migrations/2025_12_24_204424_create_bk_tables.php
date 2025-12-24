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
        Schema::create('bk_pembinaan_rutins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->foreignId('guru_bk_id')->constrained('users')->onDelete('cascade');
            $table->string('semester'); // Ganjil/Genap
            $table->foreignId('tahun_pelajaran_id')->constrained('tahun_pelajaran');
            $table->date('tanggal');
            $table->text('kondisi_siswa')->nullable();
            $table->text('catatan_pembinaan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_pembinaan_rutins');
    }
};
