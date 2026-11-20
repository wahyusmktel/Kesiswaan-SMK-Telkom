<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ukk_ujians') && !Schema::hasColumn('ukk_ujians', 'is_bilingual')) {
            Schema::table('ukk_ujians', function (Blueprint $table) {
                $table->boolean('is_bilingual')->default(false)->after('tanggal_pelaksanaan');
            });
        }

        if (Schema::hasTable('ukk_penguji') && !Schema::hasColumn('ukk_penguji', 'peran')) {
            Schema::table('ukk_penguji', function (Blueprint $table) {
                $table->string('peran', 30)->default('semua')->after('user_id')
                    ->comment('semua, pengetahuan, keterampilan');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ukk_ujians') && Schema::hasColumn('ukk_ujians', 'is_bilingual')) {
            Schema::table('ukk_ujians', function (Blueprint $table) {
                $table->dropColumn('is_bilingual');
            });
        }

        if (Schema::hasTable('ukk_penguji') && Schema::hasColumn('ukk_penguji', 'peran')) {
            Schema::table('ukk_penguji', function (Blueprint $table) {
                $table->dropColumn('peran');
            });
        }
    }
};
