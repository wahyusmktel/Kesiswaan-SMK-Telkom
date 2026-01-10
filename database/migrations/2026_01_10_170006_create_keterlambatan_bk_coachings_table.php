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
        Schema::create('keterlambatan_bk_coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keterlambatan_id')->constrained('keterlambatans')->onDelete('cascade');
            $table->date('tanggal_konseling');
            
            // II. Identifikasi Akar Masalah (Deep Dive)
            $table->text('evaluasi_sebelumnya');
            $table->text('faktor_penghambat');
            $table->text('analisis_dampak');
            
            // III. Intervensi Perilaku (The Solution Bridge)
            $table->time('jam_bangun');
            $table->time('jam_berangkat');
            $table->integer('durasi_perjalanan'); // in minutes
            $table->json('strategi_pendukung'); // [alarm, prep, hp, 3rd_party]
            $table->time('hp_limit_time')->nullable();
            
            // IV. Kontrak Perilaku
            $table->string('sanksi_disepakati');
            
            $table->foreignId('pencatat_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterlambatan_bk_coachings');
    }
};
