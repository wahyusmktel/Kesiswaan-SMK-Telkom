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
        Schema::create('guru_izins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_guru_id')->constrained('master_gurus')->onDelete('cascade');
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->string('jenis_izin'); // e.g., Sakit, Dinas, Keperluan Pribadi
            $table->text('deskripsi');
            $table->string('dokumen_pdf')->nullable();
            
            // Approval status
            $table->enum('status_piket', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->enum('status_kurikulum', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            $table->enum('status_sdm', ['menunggu', 'disetujui', 'ditolak'])->default('menunggu');
            
            // Tracker approvers
            $table->foreignId('piket_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('kurikulum_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('sdm_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->timestamp('piket_at')->nullable();
            $table->timestamp('kurikulum_at')->nullable();
            $table->timestamp('sdm_at')->nullable();
            
            $table->text('catatan_piket')->nullable();
            $table->text('catatan_kurikulum')->nullable();
            $table->text('catatan_sdm')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_izins');
    }
};
