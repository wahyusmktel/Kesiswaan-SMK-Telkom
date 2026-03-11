<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_settings', function (Blueprint $table) {
            $table->id();
            $table->time('jam_masuk_batas')->default('07:30:00')->comment('Batas jam check-in (setelahnya dianggap terlambat)');
            $table->time('jam_keluar_batas')->default('16:00:00')->comment('Jam pulang / batas checkout');
            $table->decimal('latitude_sekolah', 10, 7)->default(-5.3971449)->comment('Latitude koordinat sekolah');
            $table->decimal('longitude_sekolah', 10, 7)->default(105.2663993)->comment('Longitude koordinat sekolah');
            $table->integer('radius_meter')->default(200)->comment('Radius toleransi absensi dalam meter');
            $table->timestamps();
        });

        // Insert default setting
        DB::table('absensi_settings')->insert([
            'jam_masuk_batas'     => '07:30:00',
            'jam_keluar_batas'    => '16:00:00',
            'latitude_sekolah'    => -5.3971449,
            'longitude_sekolah'   => 105.2663993,
            'radius_meter'        => 200,
            'created_at'          => now(),
            'updated_at'          => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_settings');
    }
};
