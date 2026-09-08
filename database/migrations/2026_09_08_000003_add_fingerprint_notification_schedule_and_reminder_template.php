<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->boolean('notifications_enabled')->default(true);
            $table->time('notification_time')->default('18:00:00');
            $table->timestamp('last_notification_dispatched_at')->nullable();
        });

        DB::table('whatsapp_templates')->insertOrIgnore([
            'event_key' => 'fingerprint_peringatan_harian',
            'title' => 'Pengingat Keterlambatan / Tidak Hadir Pegawai',
            'category' => 'presensi',
            'is_enabled' => true,
            'template_text' => "*PENGINGAT ABSENSI FINGERPRINT*\n\nYth. Bapak/Ibu *{nama_pegawai}*,\n\nRekap kehadiran pada {tanggal} mencatat status Anda: *{status_kehadiran}*.\n- Jam masuk: {jam_masuk}\n- Batas masuk: {batas_masuk}\n- Durasi terlambat: {durasi_terlambat}\n- Catatan: {catatan}\n\nPesan ini merupakan pengingat kedisiplinan otomatis. Jika data belum sesuai atau terdapat kendala saat melakukan fingerprint, silakan segera menghubungi KAUR SDM.\n\n_Sistem Informasi SMK Telkom Lampung_",
            'variables' => json_encode(['nama_pegawai', 'tanggal', 'status_kehadiran', 'jam_masuk', 'batas_masuk', 'durasi_terlambat', 'catatan']),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        DB::table('whatsapp_templates')->where('event_key', 'fingerprint_peringatan_harian')->delete();

        Schema::table('fingerprint_auto_sync_settings', function (Blueprint $table) {
            $table->dropColumn(['notifications_enabled', 'notification_time', 'last_notification_dispatched_at']);
        });
    }
};
