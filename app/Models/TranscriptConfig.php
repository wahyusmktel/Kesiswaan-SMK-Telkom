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
        'number_start',
        'number_end',
        'number_suffix',
        'number_date',
        'margin_top',
        'margin_right',
        'margin_bottom',
        'margin_left',
        'paper_size',
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'signature_date' => 'date',
        'number_date' => 'date',
        'margin_top' => 'decimal:2',
        'margin_right' => 'decimal:2',
        'margin_bottom' => 'decimal:2',
        'margin_left' => 'decimal:2',
    ];

    public function numberPreview(): string
    {
        $date = $this->number_date?->translatedFormat('d F Y') ?? now()->translatedFormat('d F Y');

        return trim(($this->number_start ?? '400.3.11/800.01') . ' - ' . ($this->number_end ?? '400.3.11/800.190') . ($this->number_suffix ?? '') . ' ' . $date);
    }
}
