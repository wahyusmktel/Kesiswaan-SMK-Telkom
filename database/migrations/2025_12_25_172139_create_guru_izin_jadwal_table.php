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
        Schema::create('guru_izin_jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_izin_id')->constrained('guru_izins')->onDelete('cascade');
            $table->foreignId('jadwal_pelajaran_id')->constrained('jadwal_pelajarans')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_izin_jadwal');
    }
};
