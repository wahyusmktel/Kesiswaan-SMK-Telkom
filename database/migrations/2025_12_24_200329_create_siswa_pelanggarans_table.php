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
        Schema::create('siswa_pelanggarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            $table->foreignId('poin_peraturan_id')->constrained('poin_peraturans')->onDelete('cascade');
            $table->date('tanggal');
            $table->text('catatan')->nullable();
            $table->foreignId('pelapor_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswa_pelanggarans');
    }
};
