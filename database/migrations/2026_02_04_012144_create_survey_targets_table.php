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
        Schema::create('survey_targets', function (Blueprint $header) {
            $header->id();
            $header->foreignId('survey_id')->constrained()->onDelete('cascade');
            $header->foreignId('user_id')->constrained()->onDelete('cascade');
            $header->timestamps();

            // Prevent duplicate entries
            $header->unique(['survey_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_targets');
    }
};
