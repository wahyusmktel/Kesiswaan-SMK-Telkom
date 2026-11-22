<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            if (!Schema::hasColumn('ukk_penguji', 'no_urut_mulai')) {
                $table->unsignedSmallInteger('no_urut_mulai')->nullable()->after('peran');
            }
            if (!Schema::hasColumn('ukk_penguji', 'no_urut_selesai')) {
                $table->unsignedSmallInteger('no_urut_selesai')->nullable()->after('no_urut_mulai');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ukk_penguji', function (Blueprint $table) {
            $table->dropColumn(['no_urut_mulai', 'no_urut_selesai']);
        });
    }
};
