<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LessonPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id', 'class_id', 'subject_id', 'teach_date', 'topic',
        'learning_objectives', 'pre_assessment', 'methods', 'activities',
        'resources', 'final_assessment', 'differentiation',
        'duration_minutes', 'status', 'reflection',
    ];

    protected $casts = [
        'teach_date'      => 'date',
        'methods'         => 'array',
        'activities'      => 'array',
        'resources'       => 'array',
        'differentiation' => 'array',
    ];

    public function teacher()  { return $this->belongsTo(User::class,    'teacher_id'); }
    public function class()    { return $this->belongsTo(Kelas::class, 'class_id'); }
    public function subject()  { return $this->belongsTo(MataPelajaran::class, 'subject_id'); }
    public function todos()    { return $this->hasMany(LessonTodo::class)->orderBy('sort_order'); }

    public function pendingTodosCount(): int
    {
        return $this->todos()->where('is_done', 0)->count();
    }

    public function completionPercent(): int
    {
        $total = $this->todos()->count();
        if ($total === 0) return 100;
        return (int) round(($this->todos()->where('is_done', 1)->count() / $total) * 100);
    }

    public function statusBadge(): string
    {
        return match($this->status) {
            'draft'     => '<span class="badge bg-secondary">Draft</span>',
            'published' => '<span class="badge bg-primary">Siap Mengajar</span>',
            'done'      => '<span class="badge bg-success">Selesai ✓</span>',
            default     => ''
        };
    }
}
