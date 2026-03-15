<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LmsLesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_group_id',
        'subject_id',
        'title',
        'position',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function modules(): HasMany
    {
        return $this->hasMany(LmsModule::class, 'lesson_id');
    }

    public function quizzes(): HasMany
    {
        return $this->hasMany(LmsQuiz::class, 'lesson_id');
    }
}

