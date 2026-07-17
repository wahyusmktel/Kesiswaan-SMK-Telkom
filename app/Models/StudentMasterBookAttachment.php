<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentMasterBookAttachment extends Model
{
    protected $fillable = [
        'student_master_book_id',
        'category',
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'file_size',
        'uploaded_by',
    ];

    public function masterBook()
    {
        return $this->belongsTo(StudentMasterBook::class, 'student_master_book_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
