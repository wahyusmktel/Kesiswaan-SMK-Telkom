<?php

namespace App\Models\Erapor;

use Illuminate\Database\Eloquent\Model;

class EraporRefSubject extends Model
{
    protected $fillable = [
        'external_id',
        'name',
        'major_external_id',
        'valid_from',
        'valid_until',
        'is_active',
        'source_updated_at',
        'reference_import_id',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'source_updated_at' => 'datetime',
    ];

    public function mappings()
    {
        return $this->hasMany(EraporSubjectMapping::class);
    }
}
