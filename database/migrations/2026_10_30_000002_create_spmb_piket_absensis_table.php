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
        Schema::create('spmb_piket_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_piket_petugas_id')->constrained('spmb_piket_petugas')->cascadeOnDelete();
            $table->foreignId('spmb_piket_tanggal_id')->constrained('spmb_piket_tanggals')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('Hadir'); // Hadir, Izin, Sakit, Alpa
            $table->time('jam_absen')->nullable();
            $table->string('foto_eviden')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('metode_input', 30)->default('mandiri'); // mandiri, manual_kaur
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['spmb_piket_petugas_id', 'spmb_piket_tanggal_id'], 'spmb_piket_absensi_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_piket_absensis');
    }
};
