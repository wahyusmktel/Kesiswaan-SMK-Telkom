<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erapor_ref_curricula', function (Blueprint $table) {
            $table->string('major_external_id', 50)->nullable()->change();
        });

        Schema::table('erapor_ref_subjects', function (Blueprint $table) {
            $table->string('major_external_id', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('erapor_ref_curricula', function (Blueprint $table) {
            $table->unsignedBigInteger('major_external_id')->nullable()->change();
        });

        Schema::table('erapor_ref_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('major_external_id')->nullable()->change();
        });
    }
};
