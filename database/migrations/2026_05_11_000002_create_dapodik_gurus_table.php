<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dapodik_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_guru_id')->nullable()->constrained('master_gurus')->onDelete('set null');

            // Identitas utama
            $table->string('nik', 20)->nullable()->unique();
            $table->string('nama');
            $table->string('nuptk', 20)->nullable();
            $table->string('jenis_kelamin', 1)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('agama')->nullable();
            $table->string('kewarganegaraan', 5)->nullable()->default('ID');

            // Data kepegawaian
            $table->string('nip', 30)->nullable();
            $table->string('status_kepegawaian')->nullable();
            $table->string('jenis_ptk')->nullable();
            $table->string('tugas_tambahan')->nullable();
            $table->string('sk_cpns')->nullable();
            $table->date('tanggal_cpns')->nullable();
            $table->string('sk_pengangkatan')->nullable();
            $table->date('tmt_pengangkatan')->nullable();
            $table->string('lembaga_pengangkatan')->nullable();
            $table->string('pangkat_golongan')->nullable();
            $table->string('sumber_gaji')->nullable();
            $table->date('tmt_pns')->nullable();

            // Sertifikasi & keahlian
            $table->string('lisensi_kepala_sekolah', 10)->nullable();
            $table->string('diklat_kepengawasan', 10)->nullable();
            $table->string('keahlian_braille', 10)->nullable();
            $table->string('keahlian_bahasa_isyarat', 10)->nullable();
            $table->string('nuks', 30)->nullable();

            // Alamat
            $table->string('alamat_jalan')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('nama_dusun')->nullable();
            $table->string('desa_kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();

            // Kontak
            $table->string('telepon', 20)->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('email_dapodik')->nullable();

            // Data keluarga
            $table->string('nama_ibu_kandung')->nullable();
            $table->string('status_perkawinan')->nullable();
            $table->string('nama_pasangan')->nullable();
            $table->string('nip_pasangan', 30)->nullable();
            $table->string('pekerjaan_pasangan')->nullable();
            $table->string('no_kk', 20)->nullable();

            // Keuangan & dokumen
            $table->string('npwp', 30)->nullable();
            $table->string('nama_wajib_pajak')->nullable();
            $table->string('bank')->nullable();
            $table->string('no_rekening', 30)->nullable();
            $table->string('rekening_atas_nama')->nullable();
            $table->string('karpeg', 20)->nullable();
            $table->string('karis_karsu', 20)->nullable();

            // Koordinat
            $table->string('lintang')->nullable();
            $table->string('bujur')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dapodik_gurus');
    }
};
