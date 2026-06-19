<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonTodo extends Model
{
    protected $fillable = [
        'lesson_plan_id', 'todo_text', 'category', 'is_done', 'due_before', 'sort_order'
    ];

    protected $casts = ['is_done' => 'boolean', 'due_before' => 'datetime'];

    public function lessonPlan() { return $this->belongsTo(LessonPlan::class); }
}
