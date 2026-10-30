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
        Schema::create('spmb_berita_acaras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('spmb_piket_tanggal_id')
                ->nullable()
                ->constrained('spmb_piket_tanggals')
                ->nullOnDelete();
            $table->string('tahun_ajaran', 20)->default('2027/2028');
            $table->string('nomor_surat', 100);
            $table->string('judul', 255)->default('Berita Acara Pelaksanaan Piket SPMB');
            $table->date('tanggal_kegiatan');
            $table->string('waktu_mulai', 20)->default('07:30');
            $table->string('waktu_selesai', 20)->default('12:00');
            $table->string('tempat', 150)->default('SMK Telkom Lampung');
            $table->text('ringkasan_kegiatan');
            $table->unsignedInteger('jumlah_casis_hadir')->default(0);
            $table->unsignedInteger('jumlah_tamu')->default(0);
            $table->text('catatan_kendala')->nullable();
            $table->text('kesimpulan')->nullable();
            
            // Penandatangan Kaur SPMB
            $table->foreignId('kaur_spmb_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->string('nama_penandatangan', 150);
            $table->string('nip_penandatangan', 50)->nullable();
            $table->string('jabatan_penandatangan', 100)->default('KAUR SPMB SMK Telkom Lampung');
            $table->string('tempat_ttd', 100)->default('Pringsewu');
            $table->string('tanggal_ttd', 50)->nullable();

            $table->enum('status', ['Draft', 'Final'])->default('Final');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spmb_berita_acaras');
    }
};
