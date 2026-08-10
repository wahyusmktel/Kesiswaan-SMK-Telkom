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
        'manual_signature_enabled',
        'manual_signature_path',
        'manual_signature_x',
        'manual_signature_y',
        'manual_signature_width',
        'scan_color_mode',
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
        'manual_signature_enabled' => 'boolean',
        'manual_signature_x' => 'decimal:2',
        'manual_signature_y' => 'decimal:2',
        'manual_signature_width' => 'decimal:2',
    ];

    public function numberPreview(): string
    {
        return trim(($this->number_start ?? '400.3.11/800.01').($this->number_suffix ?? '/SMKTEL-LPG/KURL.03/V/2026'));
    }
}
