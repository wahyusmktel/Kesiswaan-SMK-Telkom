<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->string('data_source', 30)->default('legacy')->after('status')->index();
            $table->boolean('is_data_verified')->default(true)->after('data_source')->index();
        });

        Schema::table('dapodik_siswa', function (Blueprint $table) {
            $table->dropForeign(['master_siswa_id']);
        });
        Schema::table('dapodik_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('master_siswa_id')->nullable()->change();
            $table->string('nama')->nullable()->after('master_siswa_id');
            $table->foreign('master_siswa_id')->references('id')->on('master_siswa')->nullOnDelete();
            $table->index(['nipd', 'nisn'], 'dapodik_siswa_identity_index');
        });

        Schema::create('student_registrations', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_token')->unique();
            $table->string('registration_number', 30)->unique();
            $table->string('source', 20)->default('public');
            $table->string('status', 20)->default('pending')->index();
            $table->string('nama_lengkap');
            $table->string('nisn', 20)->nullable()->index();
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['L', 'P']);
            $table->text('alamat');
            $table->string('nomor_hp', 25);
            $table->string('email')->nullable();
            $table->string('sekolah_asal')->nullable();
            $table->string('nama_orang_tua')->nullable();
            $table->string('nomor_hp_orang_tua', 25)->nullable();
            $table->foreignId('master_siswa_id')->nullable()->unique()->constrained('master_siswa')->nullOnDelete();
            $table->foreignId('dapodik_siswa_id')->nullable()->unique()->constrained('dapodik_siswa')->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->foreignId('mapped_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('mapped_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_registrations');

        Schema::table('dapodik_siswa', function (Blueprint $table) {
            $table->dropForeign(['master_siswa_id']);
            $table->dropIndex('dapodik_siswa_identity_index');
            $table->dropColumn('nama');
        });
        Schema::table('dapodik_siswa', function (Blueprint $table) {
            $table->unsignedBigInteger('master_siswa_id')->nullable(false)->change();
            $table->foreign('master_siswa_id')->references('id')->on('master_siswa')->cascadeOnDelete();
        });

        Schema::table('master_siswa', function (Blueprint $table) {
            $table->dropIndex(['data_source']);
            $table->dropIndex(['is_data_verified']);
            $table->dropColumn(['data_source', 'is_data_verified']);
        });
    }
};
