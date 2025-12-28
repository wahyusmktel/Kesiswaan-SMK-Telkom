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
        Schema::create('dapodik_siswa', function (Blueprint $table) {
            $table->id();
            $table->foreignId('master_siswa_id')->constrained('master_siswa')->onDelete('cascade');
            
            // Data Pribadi
            $table->string('nipd')->nullable();
            $table->string('nisn')->nullable();
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->string('agama')->nullable();
            
            // Alamat
            $table->text('alamat')->nullable();
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kode_pos', 10)->nullable();
            
            // Kontak dan Transportasi
            $table->string('jenis_tinggal')->nullable();
            $table->string('alat_transportasi')->nullable();
            $table->string('telepon', 20)->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('email')->nullable();
            
            // Dokumen
            $table->string('skhun')->nullable();
            $table->string('no_peserta_ujian_nasional')->nullable();
            $table->string('no_seri_ijazah')->nullable();
            $table->string('no_registrasi_akta_lahir')->nullable();
            $table->string('no_kk', 20)->nullable();
            
            // KPS/KIP
            $table->string('penerima_kps')->nullable();
            $table->string('no_kps')->nullable();
            $table->string('penerima_kip')->nullable();
            $table->string('nomor_kip')->nullable();
            $table->string('nama_di_kip')->nullable();
            $table->string('nomor_kks')->nullable();
            
            // PIP
            $table->string('layak_pip')->nullable();
            $table->text('alasan_layak_pip')->nullable();
            
            // Bank
            $table->string('bank')->nullable();
            $table->string('nomor_rekening_bank')->nullable();
            $table->string('rekening_atas_nama')->nullable();
            
            // Data Ayah
            $table->string('nama_ayah')->nullable();
            $table->string('tahun_lahir_ayah', 4)->nullable();
            $table->string('jenjang_pendidikan_ayah')->nullable();
            $table->string('pekerjaan_ayah')->nullable();
            $table->string('penghasilan_ayah')->nullable();
            $table->string('nik_ayah', 20)->nullable();
            
            // Data Ibu
            $table->string('nama_ibu')->nullable();
            $table->string('tahun_lahir_ibu', 4)->nullable();
            $table->string('jenjang_pendidikan_ibu')->nullable();
            $table->string('pekerjaan_ibu')->nullable();
            $table->string('penghasilan_ibu')->nullable();
            $table->string('nik_ibu', 20)->nullable();
            
            // Data Wali
            $table->string('nama_wali')->nullable();
            $table->string('tahun_lahir_wali', 4)->nullable();
            $table->string('jenjang_pendidikan_wali')->nullable();
            $table->string('pekerjaan_wali')->nullable();
            $table->string('penghasilan_wali')->nullable();
            $table->string('nik_wali', 20)->nullable();
            
            // Lainnya
            $table->string('rombel_saat_ini')->nullable();
            $table->string('kebutuhan_khusus')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->integer('anak_ke_berapa')->nullable();
            $table->string('lintang', 20)->nullable();
            $table->string('bujur', 20)->nullable();
            $table->integer('berat_badan')->nullable();
            $table->integer('tinggi_badan')->nullable();
            $table->integer('lingkar_kepala')->nullable();
            $table->integer('jumlah_saudara_kandung')->nullable();
            $table->decimal('jarak_rumah_ke_sekolah', 10, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dapodik_siswa');
    }
};
