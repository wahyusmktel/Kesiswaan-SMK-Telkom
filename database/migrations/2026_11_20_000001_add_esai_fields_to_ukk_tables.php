<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ukk_instrumen_soals', function (Blueprint $table) {
            $table->text('kunci_jawaban')->nullable()->after('pertanyaan');
        });

        Schema::table('ukk_nilai_pengetahuan', function (Blueprint $table) {
            $table->unsignedBigInteger('penguji_id')->nullable()->change();
            $table->longText('jawaban_siswa')->nullable()->after('soal_id');
            $table->tinyInteger('nilai')->nullable()->default(null)->change();
            $table->text('catatan_penguji')->nullable()->after('nilai');
            $table->timestamp('waktu_submit')->nullable()->after('catatan_penguji');
        });
    }

    public function down(): void
    {
        Schema::table('ukk_instrumen_soals', function (Blueprint $table) {
            $table->dropColumn('kunci_jawaban');
        });

        Schema::table('ukk_nilai_pengetahuan', function (Blueprint $table) {
            $table->dropColumn(['jawaban_siswa', 'catatan_penguji', 'waktu_submit']);
            $table->unsignedBigInteger('penguji_id')->nullable(false)->change();
            $table->tinyInteger('nilai')->default(0)->change();
        });
    }
};
