<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            // 1. Hapus kolom lama
            $table->dropColumn('tahun_ajaran');

            // 2. Tambahkan Foreign Key ke tabel tahun_pelajaran
            $table->foreignId('tahun_pelajaran_id')
                ->after('id') // Biar rapi di depan
                ->constrained('tahun_pelajaran')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            $table->dropForeign(['tahun_pelajaran_id']);
            $table->dropColumn('tahun_pelajaran_id');
            $table->string('tahun_ajaran', 9)->nullable(); // Balikin kolom lama jika rollback
        });
    }
};
