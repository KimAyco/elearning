<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsQuizQuestionChoice extends Model
{
    protected $table = 'lms_quiz_question_choices';

    protected $fillable = [
        'question_id',
        'choice_text',
        'is_correct',
    ];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function question(): BelongsTo
    {
        return $this->belongsTo(LmsQuizQuestion::class, 'question_id');
    }
}
