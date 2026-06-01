<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
            $table->unsignedInteger('jumlah_benar')->nullable()->after('nilai');
            $table->unsignedInteger('jumlah_soal')->nullable()->after('jumlah_benar');
            $table->decimal('nilai_akhir', 6, 2)->nullable()->after('jumlah_soal');
        });
    }

    public function down(): void
    {
        Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
            $table->dropColumn(['jumlah_benar', 'jumlah_soal', 'nilai_akhir']);
        });
    }
};
