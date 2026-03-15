<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassGroup extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'program_id',
        'semester_id',
        'year_level',
        'name',
        'day_profile_id',
        'student_capacity',
        'is_enrollment_open',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'year_level' => 'integer',
            'student_capacity' => 'integer',
            'is_enrollment_open' => 'boolean',
        ];
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ClassDayProfile::class, 'day_profile_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class);
    }

    public function studentAssignments(): HasMany
    {
        return $this->hasMany(ClassGroupStudent::class);
    }
}
