<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuiz extends Model
{
    protected $table = 'lms_quizzes';

    protected $fillable = [
        'school_id',
        'class_group_id',
        'subject_id',
        'lesson_id',
        'created_by_user_id',
        'title',
        'description',
        'time_limit_minutes',
        'due_date',
        'is_published',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'due_date' => 'datetime',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(LmsLesson::class, 'lesson_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(LmsQuizQuestion::class, 'quiz_id')->orderBy('position');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(LmsQuizAttempt::class, 'quiz_id');
    }
}
