<?php

namespace App\Models\Erapor;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class EraporReferenceImport extends Model
{
    protected $fillable = [
        'dataset',
        'source',
        'source_version',
        'checksum',
        'files_count',
        'records_total',
        'records_imported',
        'records_skipped',
        'records_conflicted',
        'status',
        'metadata',
        'error_message',
        'imported_by',
        'imported_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'imported_at' => 'datetime',
    ];

    public function importer()
    {
        return $this->belongsTo(User::class, 'imported_by');
    }
}
