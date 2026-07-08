<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualSignedDocument extends Model
{
    protected $fillable = [
        'user_id',
        'digital_document_id',
        'title',
        'original_file_name',
        'original_file_path',
        'signed_file_path',
        'file_size',
        'page_count',
        'signed_page',
        'qr_x_mm',
        'qr_y_mm',
        'qr_size_mm',
    ];

    protected $casts = [
        'qr_x_mm' => 'decimal:2',
        'qr_y_mm' => 'decimal:2',
        'qr_size_mm' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function digitalDocument()
    {
        return $this->belongsTo(DigitalDocument::class);
    }

    public function steps()
    {
        return $this->hasMany(ManualSignedDocumentStep::class)->orderBy('sequence');
    }

    public function pendingStep()
    {
        return $this->hasOne(ManualSignedDocumentStep::class)->where('status', ManualSignedDocumentStep::STATUS_PENDING);
    }

    public function getWorkflowStatusLabelAttribute(): string
    {
        if (! $this->relationLoaded('steps') || $this->steps->isEmpty()) {
            return 'Selesai';
        }

        $pending = $this->steps->firstWhere('status', ManualSignedDocumentStep::STATUS_PENDING);

        if ($pending) {
            return 'Menunggu ' . ($pending->signer?->name ?? 'penanda tangan');
        }

        return $this->steps->contains('status', ManualSignedDocumentStep::STATUS_WAITING)
            ? 'Dalam antrean'
            : 'Selesai';
    }
}
