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
        Schema::table('guru_izin_jadwal', function (Blueprint $table) {
            $table->foreignId('lms_material_id')->nullable()->constrained('lms_materials')->onDelete('set null');
            $table->foreignId('lms_assignment_id')->nullable()->constrained('lms_assignments')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_izin_jadwal', function (Blueprint $table) {
            $table->dropForeign(['lms_material_id']);
            $table->dropForeign(['lms_assignment_id']);
            $table->dropColumn(['lms_material_id', 'lms_assignment_id']);
        });
    }
};
