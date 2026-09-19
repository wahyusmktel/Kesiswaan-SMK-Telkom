<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->softDeletes();
            $table->string('deletion_reason', 100)->nullable()->after('status');
            $table->text('deletion_notes')->nullable()->after('deletion_reason');
            $table->foreignId('deleted_by')->nullable()->after('deletion_notes')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('master_siswa', function (Blueprint $table) {
            $table->dropForeign(['deleted_by']);
            $table->dropColumn(['deleted_by', 'deletion_notes', 'deletion_reason', 'deleted_at']);
        });
    }
};
