<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Enrollment extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'semester_id',
        'student_user_id',
        'subject_offering_id',
        'section_id',
        'status',
        'validation_remarks',
        'confirmed_by_registrar_user_id',
        'confirmed_at',
        'enrolled_at',
        'dropped_at',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'enrolled_at' => 'datetime',
            'dropped_at' => 'datetime',
        ];
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function offering(): BelongsTo
    {
        return $this->belongsTo(SubjectOffering::class, 'subject_offering_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_user_id');
    }
}
