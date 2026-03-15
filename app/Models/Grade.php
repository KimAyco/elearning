<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Grade extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'enrollment_id',
        'student_user_id',
        'subject_offering_id',
        'section_id',
        'grade_value',
        'status',
        'teacher_user_id',
        'submitted_at',
        'submitted_remarks',
        'dean_user_id',
        'dean_decision_remarks',
        'dean_decided_at',
        'registrar_user_id',
        'finalized_at',
        'released_at',
    ];

    protected function casts(): array
    {
        return [
            'submitted_at' => 'datetime',
            'dean_decided_at' => 'datetime',
            'finalized_at' => 'datetime',
            'released_at' => 'datetime',
        ];
    }

    public function enrollment(): BelongsTo
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class, 'subject_offering_id');
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(SubjectOffering::class, 'subject_offering_id');
    }
}

