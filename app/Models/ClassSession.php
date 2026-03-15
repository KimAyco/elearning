<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_group_id',
        'subject_id',
        'session_type',
        'duration_minutes',
        'day_of_week',
        'start_time',
        'end_time',
        'teacher_user_id',
        'generation_run_id',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'duration_minutes' => 'integer',
            'day_of_week' => 'integer',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_user_id');
    }

    public function generationRun(): BelongsTo
    {
        return $this->belongsTo(ClassGenerationRun::class, 'generation_run_id');
    }
}

