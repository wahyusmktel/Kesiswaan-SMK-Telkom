<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManualSignedDocumentStep extends Model
{
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING = 'pending';
    public const STATUS_WAITING = 'waiting';

    protected $fillable = [
        'manual_signed_document_id',
        'signer_user_id',
        'digital_document_id',
        'sequence',
        'status',
        'signed_page',
        'qr_x_mm',
        'qr_y_mm',
        'qr_size_mm',
        'signed_at',
    ];

    protected $casts = [
        'qr_x_mm' => 'decimal:2',
        'qr_y_mm' => 'decimal:2',
        'qr_size_mm' => 'decimal:2',
        'signed_at' => 'datetime',
    ];

    public function manualDocument()
    {
        return $this->belongsTo(ManualSignedDocument::class, 'manual_signed_document_id');
    }

    public function signer()
    {
        return $this->belongsTo(User::class, 'signer_user_id');
    }

    public function digitalDocument()
    {
        return $this->belongsTo(DigitalDocument::class);
    }
}
