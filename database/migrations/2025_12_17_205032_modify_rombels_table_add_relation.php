<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            // 1. Tambahkan kolom relasi baru
            // PENTING: Tambahkan ->nullable() agar data lama yang sudah ada tidak error
            $table->foreignId('tahun_pelajaran_id')
                ->nullable() // <--- INI KUNCINYA
                ->after('id')
                ->constrained('tahun_pelajaran')
                ->onDelete('cascade');

            // 2. Kolom 'tahun_ajaran' lama TIDAK DIHAPUS (sesuai request)
            // $table->dropColumn('tahun_ajaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rombels', function (Blueprint $table) {
            // Hapus foreign key dan kolom barunya saja jika rollback
            $table->dropForeign(['tahun_pelajaran_id']);
            $table->dropColumn('tahun_pelajaran_id');
        });
    }
};
