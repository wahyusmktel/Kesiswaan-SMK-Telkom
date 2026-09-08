<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->time('second_run_time')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->dropColumn('second_run_time');
        });
    }
};
