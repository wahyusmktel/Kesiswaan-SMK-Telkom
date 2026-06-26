<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->string('provinsi_code', 20)->nullable()->after('alamat');
            $table->string('provinsi_name')->nullable()->after('provinsi_code');
            $table->string('kabupaten_code', 20)->nullable()->after('provinsi_name');
            $table->string('kabupaten_name')->nullable()->after('kabupaten_code');
            $table->string('kecamatan_code', 20)->nullable()->after('kabupaten_name');
            $table->string('kecamatan_name')->nullable()->after('kecamatan_code');
            $table->string('desa_code', 20)->nullable()->after('kecamatan_name');
            $table->string('desa_name')->nullable()->after('desa_code');
        });
    }

    public function down(): void
    {
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->dropColumn([
                'provinsi_code',
                'provinsi_name',
                'kabupaten_code',
                'kabupaten_name',
                'kecamatan_code',
                'kecamatan_name',
                'desa_code',
                'desa_name',
            ]);
        });
    }
};
