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
        Schema::create('spmb_peserta_seleksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('spmb_piket_tanggal_id')->nullable()->constrained('spmb_piket_tanggals')->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->string('nomor_pendaftaran', 50)->unique();
            $table->string('nama_lengkap', 150);
            $table->string('nisn', 20)->nullable();
            $table->string('nik', 20)->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->default('L');
            $table->string('asal_sekolah', 150)->nullable();
            $table->string('jurusan_pilihan', 100);
            $table->string('jurusan_pilihan_2', 100)->nullable();
            $table->string('no_wa_siswa', 30)->nullable();
            $table->string('nama_orang_tua', 150)->nullable();
            $table->string('hubungan_wali', 50)->nullable()->default('Orang Tua');
            $table->string('no_wa_orang_tua', 30)->nullable();
            
            // Password plaintext cadangan agar Kaur SPMB bisa mencetak/memberikan kartu akun
            $table->string('raw_password', 100)->nullable();

            // Status Kehadiran
            $table->enum('status_kehadiran_siswa', ['Terjadwal', 'Hadir', 'Tidak Hadir', 'Menunggu'])->default('Terjadwal');
            $table->enum('status_kehadiran_ortu', ['Terjadwal', 'Hadir', 'Tidak Hadir', 'Menunggu'])->default('Terjadwal');
            $table->timestamp('waktu_kehadiran_siswa')->nullable();
            $table->timestamp('waktu_kehadiran_ortu')->nullable();

            // Status Tahapan Seleksi (disiapkan untuk fitur test TPA, buta warna, wawancara)
            $table->enum('status_seleksi', ['Terjadwal', 'Sedang Seleksi', 'Lulus', 'Tidak Lulus', 'Cadangan', 'Menunggu'])->default('Terjadwal');
            $table->text('catatan')->nullable();

            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_peserta_seleksis');
    }
};
