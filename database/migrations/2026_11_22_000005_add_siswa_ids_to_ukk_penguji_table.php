<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (!Schema::hasColumn('ukk_penguji', 'siswa_ids')) {
                $table->json('siswa_ids')->nullable()->after('no_urut_selesai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (Schema::hasColumn('ukk_penguji', 'siswa_ids')) {
                $table->dropColumn('siswa_ids');
            }
        });
    }
};
