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
        if (!Schema::hasTable('rab_submissions')) {
            Schema::create('rab_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('nomor_pengajuan')->nullable();
                $table->string('judul_pengajuan');
                $table->string('tahun_anggaran', 20)->default(date('Y'));
                $table->string('nomor_akun', 50)->nullable();
                $table->string('nama_akun')->nullable();
                $table->text('drk')->nullable();
                $table->string('kebutuhan_waktu')->nullable();
                $table->string('status', 30)->default('draft'); // draft, diajukan, disetujui, ditolak
                $table->decimal('total_anggaran', 15, 2)->default(0);

                // Data Penandatangan & Persetujuan
                $table->string('pembuat_nama')->nullable();
                $table->string('pembuat_nik', 50)->nullable();
                $table->string('pembuat_jabatan')->nullable();
                $table->date('tanggal_dibuat')->nullable();

                $table->text('catatan_anggaran')->nullable();

                $table->string('pemeriksa_nama')->nullable();
                $table->string('pemeriksa_nik', 50)->nullable();
                $table->string('pemeriksa_jabatan')->nullable();
                $table->date('tanggal_diperiksa')->nullable();

                $table->string('penyetuju_nama')->nullable();
                $table->string('penyetuju_nik', 50)->nullable();
                $table->string('penyetuju_jabatan')->nullable();
                $table->date('tanggal_disetujui')->nullable();

                $table->text('catatan')->nullable();

                $table->timestamps();
            });
        }

        if (!Schema::hasTable('rab_items')) {
            Schema::create('rab_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('rab_submission_id')->constrained('rab_submissions')->cascadeOnDelete();
                $table->text('uraian_kegiatan');
                $table->text('spesifikasi')->nullable();
                $table->string('satuan', 50)->default('unit');
                $table->decimal('volume', 10, 2)->default(1);
                $table->decimal('harga_satuan', 15, 2)->default(0);
                $table->decimal('jumlah', 15, 2)->default(0);
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rab_items');
        Schema::dropIfExists('rab_submissions');
    }
};
