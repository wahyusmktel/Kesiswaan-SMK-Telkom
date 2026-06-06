<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('fingerprint_users')) {
            return;
        }

        Schema::table('fingerprint_users', function (Blueprint $table) {
            if (!Schema::hasColumn('fingerprint_users', 'machine_registered_at')) {
                $table->timestamp('machine_registered_at')->nullable()->after('cardno');
            }
            if (!Schema::hasColumn('fingerprint_users', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('machine_registered_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('fingerprint_users')) {
            return;
        }

        Schema::table('fingerprint_users', function (Blueprint $table) {
            $columns = array_filter(
                ['machine_registered_at', 'last_synced_at'],
                fn ($column) => Schema::hasColumn('fingerprint_users', $column)
            );

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
