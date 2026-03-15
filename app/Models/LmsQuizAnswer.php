<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizAnswer extends Model
{
    protected $table = 'lms_quiz_answers';

    protected $fillable = [
        'attempt_id',
        'question_id',
        'choice_id',
        'essay_answer',
        'points_awarded',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(LmsQuizAttempt::class, 'attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(LmsQuizQuestion::class, 'question_id');
    }

    public function choice(): BelongsTo
    {
        return $this->belongsTo(LmsQuizQuestionChoice::class, 'choice_id');
    }
}
