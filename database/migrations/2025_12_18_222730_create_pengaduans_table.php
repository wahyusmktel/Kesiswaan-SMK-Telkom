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
        Schema::create('pengaduans', function (Blueprint $table) {
            $table->id();
            $table->string('nama_pelapor');
            $table->string('hubungan'); // misal: Orang Tua, Wali
            $table->string('nomor_wa');
            $table->string('nama_siswa');
            $table->string('kelas_siswa');
            $table->string('kategori'); // misal: Fasilitas, Guru, Siswa, Lainnya
            $table->text('isi_pengaduan');
            $table->string('status')->default('pending'); // pending, diproses, selesai
            $table->text('catatan_petugas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengaduans');
    }
};
