<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->string('bidang_usaha')->nullable()->after('nama_industri');
            $table->string('website')->nullable()->after('bidang_usaha');
            $table->string('jabatan_pic')->nullable()->after('nama_pic');
            $table->string('no_hp_pic', 30)->nullable()->after('nama_pic');
            $table->json('bentuk_kerjasama')->nullable()->after('catatan_mou');
            $table->string('file_mou')->nullable()->after('bentuk_kerjasama');
        });
    }

    public function down(): void
    {
        Schema::table('prakerin_industris', function (Blueprint $table) {
            $table->dropColumn([
                'bidang_usaha',
                'website',
                'jabatan_pic',
                'no_hp_pic',
                'bentuk_kerjasama',
                'file_mou',
            ]);
        });
    }
};
