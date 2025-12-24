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
        Schema::create('siswa_panggilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->string('nomor_surat')->unique();
            $table->date('tanggal_panggilan');
            $table->time('jam_panggilan');
            $table->string('tempat_panggilan');
            $table->string('perihal');
            $table->enum('status', ['terkirim', 'hadir', 'tidak_hadir'])->default('terkirim');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_panggilans');
    }
};
