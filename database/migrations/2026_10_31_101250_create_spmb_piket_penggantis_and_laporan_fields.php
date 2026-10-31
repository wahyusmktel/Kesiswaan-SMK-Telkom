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
        // 1. Tabel Petugas Pengganti jika berhalangan hadir pada tanggal tertentu
        Schema::create('spmb_piket_penggantis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_piket_tanggal_id')->constrained('spmb_piket_tanggals')->cascadeOnDelete();
            $table->foreignId('spmb_piket_petugas_id')->constrained('spmb_piket_petugas')->cascadeOnDelete();
            $table->foreignId('master_guru_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_pengganti', 150);
            $table->string('jabatan_pengganti', 100)->nullable();
            $table->string('alasan', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['spmb_piket_tanggal_id', 'spmb_piket_petugas_id'], 'spmb_piket_pengganti_unique');
        });

        // 2. Tambahkan kolom eviden bukti fisik presensi manual & catatan laporan petugas pada spmb_piket_tanggals
        Schema::table('spmb_piket_tanggals', function (Blueprint $table) {
            $table->string('foto_eviden_absen_siswa', 255)->nullable()->after('keterangan');
            $table->string('foto_eviden_absen_ortu', 255)->nullable()->after('foto_eviden_absen_siswa');
            $table->text('catatan_petugas')->nullable()->after('foto_eviden_absen_ortu');
            $table->foreignId('laporan_petugas_by')->nullable()->after('catatan_petugas')->constrained('users')->nullOnDelete();
            $table->timestamp('laporan_petugas_at')->nullable()->after('laporan_petugas_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spmb_piket_tanggals', function (Blueprint $table) {
            $table->dropForeign(['laporan_petugas_by']);
            $table->dropColumn([
                'foto_eviden_absen_siswa',
                'foto_eviden_absen_ortu',
                'catatan_petugas',
                'laporan_petugas_by',
                'laporan_petugas_at',
            ]);
        });

        Schema::dropIfExists('spmb_piket_penggantis');
    }
};
