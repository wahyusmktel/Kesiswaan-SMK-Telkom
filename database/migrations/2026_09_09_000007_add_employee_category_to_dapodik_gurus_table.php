<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dapodik_gurus', function (Blueprint $table) {
            $table->string('employee_category', 20)->default('guru')->after('master_guru_id')->index();
        });
    }

    public function down(): void
    {
        Schema::table('dapodik_gurus', function (Blueprint $table) {
            $table->dropIndex(['employee_category']);
            $table->dropColumn('employee_category');
        });
    }
};
