<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizAttempt extends Model
{
    protected $table = 'lms_quiz_attempts';

    protected $fillable = [
        'quiz_id',
        'student_user_id',
        'score',
        'max_score',
        'status',
        'started_at',
        'submitted_at',
        'graded_by_user_id',
        'graded_at',
        'teacher_feedback',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function grader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'graded_by_user_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LmsQuizAnswer::class, 'attempt_id');
    }
}
