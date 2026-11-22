<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            // 1. Tambah index biasa untuk ukk_ujian_id agar foreign key tidak memblokir drop unique
            $table->index('ukk_ujian_id', 'ukk_penguji_ukk_ujian_id_index');
        });

        Schema::table('ukk_penguji', function (Blueprint $table) {
            // 2. Drop unique constraint lama
            $table->dropUnique('ukk_penguji_ukk_ujian_id_user_id_unique');

            // 3. Tambah unique index baru: kombinasi ujian, rombel, dan user
            $table->unique(['ukk_ujian_id', 'rombel_id', 'user_id'], 'ukk_penguji_ujian_rombel_user_unique');
        });
    }

    public function down(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            $table->dropUnique('ukk_penguji_ujian_rombel_user_unique');
            $table->unique(['ukk_ujian_id', 'user_id'], 'ukk_penguji_ukk_ujian_id_user_id_unique');
            $table->dropIndex('ukk_penguji_ukk_ujian_id_index');
        });
    }
};
