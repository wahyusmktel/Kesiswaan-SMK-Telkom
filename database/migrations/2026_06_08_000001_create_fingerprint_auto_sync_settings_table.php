<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_enabled')->default(false);
            $table->time('run_time')->default('23:30:00');
            $table->string('range_type', 30)->default('1_day');
            $table->json('device_ids')->nullable();
            $table->timestamp('last_dispatched_at')->nullable();
            $table->json('last_progress_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_auto_sync_settings');
    }
};
