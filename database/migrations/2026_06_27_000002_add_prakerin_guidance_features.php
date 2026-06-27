<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prakerin_jurnals', function (Blueprint $table) {
            $table->foreignId('reviewed_by')->nullable()->after('catatan_pembimbing')->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable()->after('reviewed_by');
        });

        Schema::create('prakerin_konsultasi_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prakerin_rombel_id')->constrained('prakerin_rombels')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('receiver_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->enum('type', ['group', 'private'])->default('group');
            $table->text('message');
            $table->timestamps();
        });

        Schema::create('prakerin_laporan_bimbingans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prakerin_penempatan_id')->constrained('prakerin_penempatans')->cascadeOnDelete();
            $table->foreignId('uploaded_by')->constrained('users')->cascadeOnDelete();
            $table->string('judul');
            $table->string('file_path')->nullable();
            $table->enum('status', ['diajukan', 'ditinjau', 'revisi'])->default('diajukan');
            $table->text('catatan_pembimbing')->nullable();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prakerin_laporan_bimbingans');
        Schema::dropIfExists('prakerin_konsultasi_messages');

        Schema::table('prakerin_jurnals', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reviewed_by');
            $table->dropColumn('reviewed_at');
        });
    }
};
