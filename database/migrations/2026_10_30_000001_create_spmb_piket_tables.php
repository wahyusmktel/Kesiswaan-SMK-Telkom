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
        Schema::create('spmb_piket_kelompoks', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20)->index();
            $table->string('nama_kelompok', 100);
            $table->string('waktu', 50)->default('07.30 - 12.00');
            $table->text('keterangan')->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->timestamps();
        });

        Schema::create('spmb_piket_petugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_piket_kelompok_id')->constrained('spmb_piket_kelompoks')->cascadeOnDelete();
            $table->foreignId('master_guru_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_petugas', 150);
            $table->string('jabatan', 100)->nullable();
            $table->unsignedInteger('urutan')->default(1);
            $table->string('keterangan', 255)->nullable();
            $table->timestamps();
        });

        Schema::create('spmb_piket_tanggals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_piket_kelompok_id')->constrained('spmb_piket_kelompoks')->cascadeOnDelete();
            $table->date('tanggal')->index();
            $table->string('hari', 20)->default('Sabtu');
            $table->string('waktu', 50)->nullable();
            $table->string('status', 30)->default('Terjadwal'); // Terjadwal, Terlaksana, Libur, Diganti
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_piket_tanggals');
        Schema::dropIfExists('spmb_piket_petugas');
        Schema::dropIfExists('spmb_piket_kelompoks');
    }
};
