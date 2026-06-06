<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fingerprint_attendance_settings', function (Blueprint $table) {
            $table->id();
            $table->time('checkin_start')->default('06:00:00');
            $table->time('checkin_end')->default('07:30:00');
            $table->time('checkout_start')->default('15:30:00');
            $table->time('checkout_end')->default('18:00:00');
            $table->timestamps();
        });

        DB::table('fingerprint_attendance_settings')->insert([
            'checkin_start' => '06:00:00',
            'checkin_end' => '07:30:00',
            'checkout_start' => '15:30:00',
            'checkout_end' => '18:00:00',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('fingerprint_attendance_settings');
    }
};
