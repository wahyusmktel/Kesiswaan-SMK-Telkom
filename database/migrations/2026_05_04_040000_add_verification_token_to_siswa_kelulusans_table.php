<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('siswa_kelulusans', function (Blueprint $table) {
            $table->string('verification_token', 64)->nullable()->unique()->after('catatan');
        });

        // Generate tokens for existing records
        $records = \App\Models\SiswaKelulusan::whereNull('verification_token')->get();
        foreach ($records as $record) {
            $record->update(['verification_token' => Str::random(32)]);
        }
    }

    public function down(): void
    {
        Schema::table('siswa_kelulusans', function (Blueprint $table) {
            $table->dropColumn('verification_token');
        });
    }
};
