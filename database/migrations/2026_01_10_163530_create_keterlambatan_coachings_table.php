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
        Schema::create('keterlambatan_coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('keterlambatan_id')->constrained('keterlambatans')->onDelete('cascade');
            $table->date('tanggal_coaching');
            $table->enum('lokasi', ['langsung', 'online']);
            
            // Tahap GROW
            $table->text('goal_response');
            $table->text('reality_response');
            $table->text('options_response');
            $table->text('will_response');
            
            // Rencana Aksi & Komitmen (User asked for list, text area is more flexible or we can use JSON)
            $table->text('rencana_aksi');
            $table->text('konsekuensi_logis');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('keterlambatan_coachings');
    }
};
