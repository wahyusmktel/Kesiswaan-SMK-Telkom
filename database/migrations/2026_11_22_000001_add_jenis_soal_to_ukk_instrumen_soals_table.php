<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ukk_instrumen_soals') && !Schema::hasColumn('ukk_instrumen_soals', 'jenis_soal')) {
            Schema::table('ukk_instrumen_soals', function (Blueprint $table) {
                $table->string('jenis_soal', 30)->default('esai')->after('kunci_jawaban')
                    ->comment('esai (mandiri oleh siswa) atau bilingual (wawancara lisan oleh penguji)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('ukk_instrumen_soals') && Schema::hasColumn('ukk_instrumen_soals', 'jenis_soal')) {
            Schema::table('ukk_instrumen_soals', function (Blueprint $table) {
                $table->dropColumn('jenis_soal');
            });
        }
    }
};
