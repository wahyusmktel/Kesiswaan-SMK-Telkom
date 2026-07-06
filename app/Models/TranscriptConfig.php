<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranscriptConfig extends Model
{
    protected $fillable = [
        'school_name',
        'npsn',
        'graduation_date',
        'signature_city',
        'signature_date',
        'principal_name',
        'principal_nip',
        'letterhead',
        'letterhead_path',
        'watermark_path',
        'number_start',
        'number_end',
        'number_suffix',
        'number_date',
        'margin_top',
        'margin_right',
        'margin_bottom',
        'margin_left',
        'paper_size',
        'is_borderless',
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'signature_date' => 'date',
        'number_date' => 'date',
        'margin_top' => 'decimal:2',
        'margin_right' => 'decimal:2',
        'margin_bottom' => 'decimal:2',
        'margin_left' => 'decimal:2',
        'is_borderless' => 'boolean',
    ];

    public function numberPreview(): string
    {
        return trim(($this->number_start ?? '400.3.11/800.01') . ($this->number_suffix ?? '/SMKTEL-LPG/KURL.03/V/2026'));
    }
}
