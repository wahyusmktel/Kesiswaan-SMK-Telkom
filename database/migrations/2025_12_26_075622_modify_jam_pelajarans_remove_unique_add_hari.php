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
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->dropUnique(['jam_ke']);
            $table->string('hari')->nullable()->after('jam_ke')->comment('Senin, Selasa, ..., Sabtu. Null berarti semua hari.');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('jam_pelajarans', function (Blueprint $table) {
            $table->unique('jam_ke');
            $table->dropColumn('hari');
        });
    }
};
