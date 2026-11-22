<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambah kolom tanggal, jam, dan ruangan pada ukk_penguji
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (!Schema::hasColumn('ukk_penguji', 'tanggal_pelaksanaan')) {
                $table->date('tanggal_pelaksanaan')->nullable()->after('siswa_ids');
            }
            if (!Schema::hasColumn('ukk_penguji', 'jam_mulai')) {
                $table->time('jam_mulai')->nullable()->after('tanggal_pelaksanaan');
            }
            if (!Schema::hasColumn('ukk_penguji', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable()->after('jam_mulai');
            }
            if (!Schema::hasColumn('ukk_penguji', 'ruangan')) {
                $table->string('ruangan', 100)->nullable()->after('jam_selesai');
            }
        });

        // 2. Ubah unique index ukk_penguji_ujian_rombel_user_unique menjadi index biasa
        // agar guru yang sama bisa menguji di sesi/hari yang berbeda (misal Hari 1 siswa 1-25, Hari 2 siswa 26-36)
        try {
            Schema::table('ukk_penguji', function (Blueprint $table) {
                $table->dropUnique('ukk_penguji_ujian_rombel_user_unique');
                $table->index(['ukk_ujian_id', 'rombel_id', 'user_id'], 'ukk_penguji_ujian_rombel_user_index');
            });
        } catch (\Throwable $e) {
            // Ignore if index already modified
        }
    }

    public function down(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pelaksanaan', 'jam_mulai', 'jam_selesai', 'ruangan']);
        });
    }
};
