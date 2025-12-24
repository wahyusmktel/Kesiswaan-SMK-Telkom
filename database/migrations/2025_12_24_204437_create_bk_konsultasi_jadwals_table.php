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
        Schema::create('bk_konsultasi_jadwals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->foreignId('guru_bk_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('perihal');
            $table->date('tanggal_rencana');
            $table->time('jam_rencana');
            $table->string('tempat')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->text('catatan_bk')->nullable(); // Ditulis oleh BK saat selesai
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bk_konsultasi_jadwals');
    }
};
