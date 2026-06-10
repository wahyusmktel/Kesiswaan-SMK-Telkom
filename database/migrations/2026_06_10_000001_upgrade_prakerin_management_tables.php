<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->string('nomor_mou')->nullable()->after('nama_pic');
            $table->date('tanggal_mou')->nullable()->after('nomor_mou');
            $table->date('tanggal_akhir_mou')->nullable()->after('tanggal_mou');
            $table->boolean('is_mou_active')->default(true)->after('tanggal_akhir_mou');
            $table->decimal('latitude', 10, 7)->nullable()->after('is_mou_active');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
            $table->text('catatan_mou')->nullable()->after('longitude');
        });

        Schema::create('prakerin_pembimbings', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['internal', 'external']);
            $table->foreignId('master_guru_id')->nullable()->constrained('master_gurus')->nullOnDelete();
            $table->foreignId('prakerin_industri_id')->nullable()->constrained('prakerin_industris')->nullOnDelete();
            $table->string('nama');
            $table->string('jabatan')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('prakerin_rombels', function (Blueprint $table) {
            $table->id();
            $table->string('nama_rombel');
            $table->foreignId('prakerin_industri_id')->constrained('prakerin_industris')->cascadeOnDelete();
            $table->foreignId('pembimbing_internal_id')->nullable()->constrained('prakerin_pembimbings')->nullOnDelete();
            $table->foreignId('pembimbing_external_id')->nullable()->constrained('prakerin_pembimbings')->nullOnDelete();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();
        });

        Schema::table('prakerin_penempatans', function (Blueprint $table) {
            $table->foreignId('prakerin_rombel_id')->nullable()->after('id')->constrained('prakerin_rombels')->nullOnDelete();
            $table->unique('master_siswa_id', 'prakerin_penempatans_master_siswa_unique');
        });

        Schema::create('prakerin_settings', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal_mulai')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->time('jam_check_in_mulai')->nullable();
            $table->time('jam_check_in_selesai')->nullable();
            $table->time('jam_check_out_mulai')->nullable();
            $table->time('jam_check_out_selesai')->nullable();
            $table->text('instruksi_jurnal')->nullable();
            $table->boolean('wajib_foto_absensi')->default(true);
            $table->boolean('wajib_lokasi')->default(true);
            $table->timestamps();
        });

        Schema::create('prakerin_absensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prakerin_penempatan_id')->constrained('prakerin_penempatans')->cascadeOnDelete();
            $table->date('tanggal');
            $table->time('check_in_at')->nullable();
            $table->decimal('check_in_latitude', 10, 7)->nullable();
            $table->decimal('check_in_longitude', 10, 7)->nullable();
            $table->string('check_in_photo')->nullable();
            $table->time('check_out_at')->nullable();
            $table->decimal('check_out_latitude', 10, 7)->nullable();
            $table->decimal('check_out_longitude', 10, 7)->nullable();
            $table->string('check_out_photo')->nullable();
            $table->string('status')->default('hadir');
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->unique(['prakerin_penempatan_id', 'tanggal']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prakerin_absensis');
        Schema::dropIfExists('prakerin_settings');
        Schema::table('prakerin_penempatans', function (Blueprint $table) {
            $table->dropUnique('prakerin_penempatans_master_siswa_unique');
            $table->dropConstrainedForeignId('prakerin_rombel_id');
        });
        Schema::dropIfExists('prakerin_rombels');
        Schema::dropIfExists('prakerin_pembimbings');
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->dropColumn(['nomor_mou', 'tanggal_mou', 'tanggal_akhir_mou', 'is_mou_active', 'latitude', 'longitude', 'catatan_mou']);
        });
    }
};
