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
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            // Menambahkan kolom kelas_id yang berelasi dengan tabel 'kelas'
            // 'after' digunakan untuk menempatkan kolom setelah 'jumlah_jam' agar rapi
            // 'nullable' ditambahkan agar tidak error jika tabel sudah berisi data sebelumnya
            $table->foreignId('kelas_id')
                ->nullable()
                ->after('jumlah_jam')
                ->constrained('kelas') // Relasi ke tabel 'kelas'
                ->onDelete('cascade'); // Jika Kelas dihapus, Mapel terkait ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mata_pelajarans', function (Blueprint $table) {
            // Hapus foreign key dan kolom jika rollback
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
    }
};
