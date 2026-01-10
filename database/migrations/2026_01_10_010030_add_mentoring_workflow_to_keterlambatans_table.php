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
        Schema::table('keterlambatans', function (Blueprint $table) {
            // Kolom untuk Pendampingan Wali Kelas
            $table->timestamp('waktu_pendampingan_wali_kelas')->nullable()->after('waktu_verifikasi_guru_kelas');
            $table->text('catatan_wali_kelas')->nullable()->after('waktu_pendampingan_wali_kelas');

            // Kolom untuk Pembinaan BK
            $table->foreignId('pembinaan_oleh_bk_id')->nullable()->after('catatan_wali_kelas')->constrained('users');
            $table->timestamp('waktu_pembinaan_bk')->nullable()->after('pembinaan_oleh_bk_id');
            $table->text('catatan_bk')->nullable()->after('waktu_pembinaan_bk');

            // Update status enum
            $table->string('status')->change(); // Temporary change to string to allow adding values easily if needed, or just change the enum
        });

        // Use DB statement for better compatibility with enum changes if needed, 
        // but since we might be on MySQL/PostgreSQL, let's just make it a string for now or use the change() properly.
        // Actually, to be safe and flexible, let's use string for status or update the enum.
        // Let's stick with updating the enum if possible.
    }

    public function down(): void
    {
        Schema::table('keterlambatans', function (Blueprint $table) {
            $table->dropForeign(['pembinaan_oleh_bk_id']);
            $table->dropColumn([
                'waktu_pendampingan_wali_kelas',
                'catatan_wali_kelas',
                'pembinaan_oleh_bk_id',
                'waktu_pembinaan_bk',
                'catatan_bk'
            ]);
        });
    }
};
