<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\Models\SpmbTpaSoal;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $soals = DB::table('spmb_tpa_soals')->get();

        foreach ($soals as $soal) {
            $updates = [];

            foreach (['pertanyaan', 'opsi_a', 'opsi_b', 'opsi_c', 'opsi_d', 'opsi_e', 'pembahasan'] as $column) {
                if (isset($soal->{$column}) && $soal->{$column} !== null) {
                    $cleaned = SpmbTpaSoal::normalizeNewlines($soal->{$column});
                    if ($cleaned !== $soal->{$column}) {
                        $updates[$column] = $cleaned;
                    }
                }
            }

            if (!empty($updates)) {
                DB::table('spmb_tpa_soals')->where('id', $soal->id)->update($updates);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reversal needed for text sanitation
    }
};
