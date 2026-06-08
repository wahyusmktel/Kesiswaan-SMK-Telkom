<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fingerprint_attendances')) {
            return;
        }

        Schema::table('fingerprint_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('fingerprint_attendances', 'entry_source')) {
                $table->string('entry_source')->default('machine')->after('punch');
            }
            if (!Schema::hasColumn('fingerprint_attendances', 'original_timestamp')) {
                $table->dateTime('original_timestamp')->nullable()->after('entry_source');
            }
            if (!Schema::hasColumn('fingerprint_attendances', 'corrected_by')) {
                $table->foreignId('corrected_by')->nullable()->after('original_timestamp')->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('fingerprint_attendances', 'correction_note')) {
                $table->text('correction_note')->nullable()->after('corrected_by');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('fingerprint_attendances')) {
            return;
        }

        Schema::table('fingerprint_attendances', function (Blueprint $table) {
            foreach (['correction_note', 'corrected_by', 'original_timestamp', 'entry_source'] as $column) {
                if (Schema::hasColumn('fingerprint_attendances', $column)) {
                    if ($column === 'corrected_by') {
                        $table->dropConstrainedForeignId($column);
                    } else {
                        $table->dropColumn($column);
                    }
                }
            }
        });
    }
};
