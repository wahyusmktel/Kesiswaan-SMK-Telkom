<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prakerin_rombels', function (Blueprint $table) {
            $table->boolean('gunakan_periode_kustom')->default(false)->after('pembimbing_external_id');
            $table->boolean('gunakan_waktu_absensi_kustom')->default(false)->after('tanggal_selesai');
            $table->time('jam_check_in_mulai')->nullable()->after('gunakan_waktu_absensi_kustom');
            $table->time('jam_check_in_selesai')->nullable()->after('jam_check_in_mulai');
            $table->time('jam_check_out_mulai')->nullable()->after('jam_check_in_selesai');
            $table->time('jam_check_out_selesai')->nullable()->after('jam_check_out_mulai');
        });
    }

    public function down(): void
    {
        Schema::table('prakerin_rombels', function (Blueprint $table) {
            $table->dropColumn([
                'gunakan_periode_kustom',
                'gunakan_waktu_absensi_kustom',
                'jam_check_in_mulai',
                'jam_check_in_selesai',
                'jam_check_out_mulai',
                'jam_check_out_selesai',
            ]);
        });
    }
};
