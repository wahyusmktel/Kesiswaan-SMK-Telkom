<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UkkNilaiBilingual extends Model
{
    protected $table = 'ukk_nilai_bilingual';

    protected $fillable = [
        'ukk_ujian_id',
        'master_siswa_id',
        'penguji_id',
        'fluency',
        'pronunciation',
        'vocabulary',
        'comprehension',
        'kesimpulan',
        'catatan',
    ];

    public function ujian()
    {
        return $this->belongsTo(UkkUjian::class, 'ukk_ujian_id');
    }

    public function siswa()
    {
        return $this->belongsTo(MasterSiswa::class, 'master_siswa_id');
    }

    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }

    /**
     * Hitung rata-rata angka pencapaian kecakapan bilingual (1: Kurang, 2: Cukup, 3: Baik, 4: Sangat Baik)
     */
    public function getRataRataScore(): ?float
    {
        $map = [
            'kurang'      => 1,
            'cukup'       => 2,
            'baik'        => 3,
            'sangat_baik' => 4,
        ];

        $scores = [];
        foreach (['fluency', 'pronunciation', 'vocabulary', 'comprehension'] as $field) {
            $val = strtolower((string)$this->$field);
            if (isset($map[$val])) {
                $scores[] = $map[$val];
            }
        }

        if (empty($scores)) {
            return null;
        }

        return round(array_sum($scores) / count($scores), 2);
    }
}
