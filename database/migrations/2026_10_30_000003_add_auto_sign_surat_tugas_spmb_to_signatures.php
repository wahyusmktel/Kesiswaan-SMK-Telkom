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
        if (Schema::hasTable('user_digital_signatures') && ! Schema::hasColumn('user_digital_signatures', 'auto_sign_surat_tugas_spmb')) {
            Schema::table('user_digital_signatures', function (Blueprint $table) {
                $table->boolean('auto_sign_surat_tugas_spmb')->default(false)->after('auto_sign_transcript');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('user_digital_signatures') && Schema::hasColumn('user_digital_signatures', 'auto_sign_surat_tugas_spmb')) {
            Schema::table('user_digital_signatures', function (Blueprint $table) {
                $table->dropColumn('auto_sign_surat_tugas_spmb');
            });
        }
    }
};
