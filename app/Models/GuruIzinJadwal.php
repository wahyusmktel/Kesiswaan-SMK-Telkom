<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class GuruIzinJadwal extends Pivot
{
    protected $table = 'guru_izin_jadwal';

    public function material()
    {
        return $this->belongsTo(LmsMaterial::class, 'lms_material_id');
    }

    public function assignment()
    {
        return $this->belongsTo(LmsAssignment::class, 'lms_assignment_id');
    }
}
