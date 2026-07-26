<?php

namespace App\Models\Erapor;

use App\Models\Rombel;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EraporRombelCurriculum extends Model
{
    protected $fillable = [
        'erapor_period_setting_id',
        'rombel_id',
        'erapor_ref_curriculum_id',
        'configured_by',
        'configured_at',
        'notes',
    ];

    protected $casts = [
        'configured_at' => 'datetime',
    ];

    public function periodSetting()
    {
        return $this->belongsTo(EraporPeriodSetting::class, 'erapor_period_setting_id');
    }

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }

    public function referenceCurriculum()
    {
        return $this->belongsTo(EraporRefCurriculum::class, 'erapor_ref_curriculum_id');
    }

    public function configurator()
    {
        return $this->belongsTo(User::class, 'configured_by');
    }
}
