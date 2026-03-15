<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizQuestion extends Model
{
    protected $table = 'lms_quiz_questions';

    protected $fillable = [
        'quiz_id',
        'type',
        'question_text',
        'points',
        'position',
    ];

    public function quiz(): BelongsTo
    {
        return $this->belongsTo(LmsQuiz::class, 'quiz_id');
    }

    public function choices(): HasMany
    {
        return $this->hasMany(LmsQuizQuestionChoice::class, 'question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(LmsQuizAnswer::class, 'question_id');
    }
}
