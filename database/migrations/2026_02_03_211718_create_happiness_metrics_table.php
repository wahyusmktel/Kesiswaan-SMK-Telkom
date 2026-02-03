<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('happiness_metrics', function (Blueprint $table) {
            $table->id();
            $table->string('device_fingerprint', 64)->index(); // Hash of browser fingerprint
            $table->string('ip_address', 45)->nullable(); // IPv4 or IPv6
            $table->enum('mood_level', ['sangat_bahagia', 'bahagia', 'netral', 'sedih', 'sangat_sedih']);
            $table->integer('mood_score'); // 1-5 scale
            $table->string('user_agent')->nullable();
            $table->date('submitted_date')->index(); // For daily limit check
            $table->timestamps();

            // Ensure one submission per device per day
            $table->unique(['device_fingerprint', 'submitted_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('happiness_metrics');
    }
};
