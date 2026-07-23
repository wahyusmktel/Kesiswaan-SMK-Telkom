<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone_number', 20)->nullable()->after('email');
        });

        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->foreignId('recipient_user_id')
                ->nullable()
                ->after('whatsapp_device_id')
                ->constrained('users')
                ->nullOnDelete();
            $table->string('event_key')->nullable()->after('type')->index();
            $table->date('notification_date')->nullable()->after('event_key')->index();
        });

        DB::table('whatsapp_templates')->insertOrIgnore([
            'event_key' => 'fingerprint_rekap_harian',
            'title' => 'Rekap Absensi Fingerprint Harian Pegawai',
            'category' => 'presensi',
            'is_enabled' => true,
            'template_text' => "*REKAP ABSENSI FINGERPRINT HARIAN*\n\nYth. Bapak/Ibu *{nama_pegawai}*,\n\nBerikut kami sampaikan rekap absensi fingerprint Anda:\n- Tanggal: {tanggal}\n- Jam masuk: {jam_masuk}\n- Jam pulang: {jam_pulang}\n- Jumlah scan: {total_scan}\n- Status: *{status_kehadiran}*\n- Catatan: {catatan}\n- Durasi terlambat: {durasi_terlambat}\n\nData ini tercatat otomatis setelah sinkronisasi mesin fingerprint. Apabila terdapat ketidaksesuaian, silakan menghubungi KAUR SDM.\n\nTerima kasih atas kedisiplinan dan kontribusi Bapak/Ibu.\n_\"Kedisiplinan yang dijaga hari ini membangun kualitas kerja yang lebih baik esok hari.\"_\n\n_Sistem Informasi SMK Telkom Lampung_",
            'variables' => json_encode(['nama_pegawai', 'tanggal', 'jam_masuk', 'jam_pulang', 'total_scan', 'status_kehadiran', 'catatan', 'durasi_terlambat']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('whatsapp_templates')
            ->where('event_key', 'fingerprint_rekap_harian')
            ->delete();

        Schema::table('whatsapp_logs', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipient_user_id');
            $table->dropIndex(['event_key']);
            $table->dropIndex(['notification_date']);
            $table->dropColumn(['event_key', 'notification_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone_number');
        });
    }
};
