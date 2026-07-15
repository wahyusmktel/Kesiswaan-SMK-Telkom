<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('ujian_semester_mapels', 'nama_mapel')) {
            Schema::table('ujian_semester_mapels', function (Blueprint $table) {
                $table->string('nama_mapel')->nullable()->after('mata_pelajaran_id');
            });
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement(<<<'SQL'
                UPDATE ujian_semester_mapels
                SET nama_mapel = (
                    SELECT mata_pelajarans.nama_mapel
                    FROM mata_pelajarans
                    WHERE mata_pelajarans.id = ujian_semester_mapels.mata_pelajaran_id
                )
            SQL);
        } else {
            DB::table('ujian_semester_mapels')
                ->join('mata_pelajarans', 'ujian_semester_mapels.mata_pelajaran_id', '=', 'mata_pelajarans.id')
                ->update(['ujian_semester_mapels.nama_mapel' => DB::raw('mata_pelajarans.nama_mapel')]);
        }

        try {
            Schema::table('ujian_semester_mapels', function (Blueprint $table) {
                $table->dropForeign(['mata_pelajaran_id']);
            });
        } catch (\Throwable $e) {
            // The previous failed migration attempt may already have removed this FK.
        }

        Schema::table('ujian_semester_mapels', function (Blueprint $table) {
            $table->index('ujian_semester_id', 'ujian_semester_mapels_ujian_id_index');
            $table->dropUnique('ujian_semester_mapel_unique');
            $table->foreignId('mata_pelajaran_id')->nullable()->change();
            $table->unique(['ujian_semester_id', 'nama_mapel'], 'ujian_semester_nama_mapel_unique');
        });

        if (!Schema::hasColumn('nilai_ujian_semesters', 'ujian_semester_mapel_id')) {
            Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
                $table->foreignId('ujian_semester_mapel_id')->nullable()->after('ujian_semester_id')->constrained('ujian_semester_mapels')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('nilai_ujian_semesters', 'nama_mapel')) {
            Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
                $table->string('nama_mapel')->nullable()->after('mata_pelajaran_id');
            });
        }

        if (DB::getDriverName() === 'sqlite') {
            DB::statement(<<<'SQL'
                UPDATE nilai_ujian_semesters
                SET ujian_semester_mapel_id = (
                        SELECT ujian_semester_mapels.id
                        FROM ujian_semester_mapels
                        WHERE ujian_semester_mapels.ujian_semester_id = nilai_ujian_semesters.ujian_semester_id
                          AND ujian_semester_mapels.mata_pelajaran_id = nilai_ujian_semesters.mata_pelajaran_id
                        LIMIT 1
                    ),
                    nama_mapel = (
                        SELECT ujian_semester_mapels.nama_mapel
                        FROM ujian_semester_mapels
                        WHERE ujian_semester_mapels.ujian_semester_id = nilai_ujian_semesters.ujian_semester_id
                          AND ujian_semester_mapels.mata_pelajaran_id = nilai_ujian_semesters.mata_pelajaran_id
                        LIMIT 1
                    )
            SQL);
        } else {
            DB::table('nilai_ujian_semesters')
                ->join('ujian_semester_mapels', function ($join) {
                    $join->on('nilai_ujian_semesters.ujian_semester_id', '=', 'ujian_semester_mapels.ujian_semester_id')
                        ->on('nilai_ujian_semesters.mata_pelajaran_id', '=', 'ujian_semester_mapels.mata_pelajaran_id');
                })
                ->update([
                    'nilai_ujian_semesters.ujian_semester_mapel_id' => DB::raw('ujian_semester_mapels.id'),
                    'nilai_ujian_semesters.nama_mapel' => DB::raw('ujian_semester_mapels.nama_mapel'),
                ]);
        }

        Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
            $table->dropForeign(['mata_pelajaran_id']);
            $table->foreignId('mata_pelajaran_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('nilai_ujian_semesters', function (Blueprint $table) {
            $table->dropForeign(['ujian_semester_mapel_id']);
            $table->dropColumn(['ujian_semester_mapel_id', 'nama_mapel']);
            $table->foreignId('mata_pelajaran_id')->nullable(false)->change();
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->cascadeOnDelete();
        });

        Schema::table('ujian_semester_mapels', function (Blueprint $table) {
            $table->dropUnique('ujian_semester_nama_mapel_unique');
            $table->dropColumn('nama_mapel');
            $table->foreignId('mata_pelajaran_id')->nullable(false)->change();
            $table->foreign('mata_pelajaran_id')->references('id')->on('mata_pelajarans')->cascadeOnDelete();
            $table->unique(['ujian_semester_id', 'mata_pelajaran_id'], 'ujian_semester_mapel_unique');
        });
    }
};
