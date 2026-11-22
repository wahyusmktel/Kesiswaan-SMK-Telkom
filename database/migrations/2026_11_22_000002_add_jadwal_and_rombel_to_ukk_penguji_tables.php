<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tambahkan kolom jadwal dan ruangan pada ukk_rombel_mappings
        Schema::table('ukk_rombel_mappings', function (Blueprint $table) {
            if (!Schema::hasColumn('ukk_rombel_mappings', 'tanggal_pelaksanaan')) {
                $table->date('tanggal_pelaksanaan')->nullable()->after('rombel_id');
            }
            if (!Schema::hasColumn('ukk_rombel_mappings', 'jam_mulai')) {
                $table->time('jam_mulai')->nullable()->after('tanggal_pelaksanaan');
            }
            if (!Schema::hasColumn('ukk_rombel_mappings', 'jam_selesai')) {
                $table->time('jam_selesai')->nullable()->after('jam_mulai');
            }
            if (!Schema::hasColumn('ukk_rombel_mappings', 'ruangan')) {
                $table->string('ruangan', 100)->nullable()->after('jam_selesai');
            }
        });

        // 2. Tambahkan rombel_id pada ukk_penguji agar penguji dapat ditugaskan per-kelas
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (!Schema::hasColumn('ukk_penguji', 'rombel_id')) {
                $table->unsignedBigInteger('rombel_id')->nullable()->after('user_id');
                $table->foreign('rombel_id')->references('id')->on('rombels')->onDelete('cascade');
            }
        });

        // Drop unique constraint lama ['ukk_ujian_id', 'user_id'] agar penguji bisa menguji lebih dari 1 kelas dalam ujian yang sama
        try {
            Schema::table('ukk_penguji', function (Blueprint $table) {
                $table->dropUnique('ukk_penguji_ukk_ujian_id_user_id_unique');
            });
        } catch (\Throwable $e) {
            // Abaikan jika nama unique constraint berbeda
        }
    }

    public function down(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (Schema::hasColumn('ukk_penguji', 'rombel_id')) {
                $table->dropForeign(['rombel_id']);
                $table->dropColumn('rombel_id');
            }
        });

        Schema::table('ukk_rombel_mappings', function (Blueprint $table) {
            $table->dropColumn(['tanggal_pelaksanaan', 'jam_mulai', 'jam_selesai', 'ruangan']);
        });
    }
};
