<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubjectOffering extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'subject_id',
        'semester_id',
        'assigned_teacher_user_id',
        'schedule_summary',
        'status',
        'created_by_user_id',
    ];

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_teacher_user_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }
}
