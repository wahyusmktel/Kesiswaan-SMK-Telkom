<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->date('tanggal')->comment('Tanggal absensi');
            $table->dateTime('waktu_checkin')->nullable()->comment('Waktu check-in');
            $table->dateTime('waktu_checkout')->nullable()->comment('Waktu check-out');
            $table->decimal('lat_checkin', 10, 7)->nullable()->comment('Latitude saat check-in');
            $table->decimal('lng_checkin', 10, 7)->nullable()->comment('Longitude saat check-in');
            $table->decimal('lat_checkout', 10, 7)->nullable()->comment('Latitude saat check-out');
            $table->decimal('lng_checkout', 10, 7)->nullable()->comment('Longitude saat check-out');
            $table->enum('status', ['tepat_waktu', 'terlambat', 'tidak_hadir'])->default('tidak_hadir');
            $table->boolean('dalam_radius_checkin')->default(false)->comment('Apakah checkin dalam radius sekolah');
            $table->boolean('dalam_radius_checkout')->default(false)->comment('Apakah checkout dalam radius sekolah');
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'tanggal'], 'unique_absensi_per_hari');
            $table->index(['tanggal', 'status']);
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi_pegawais');
    }
};
