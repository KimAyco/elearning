<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinanceFeeSetting extends Model
{
    use HasFactory;

    protected $appends = [
        'scope_type',
        'scope_id',
    ];

    protected $fillable = [
        'school_id',
        'semester_id',
        'academic_year_id',
        'program_id',
        'enrollment_fee',
        'price_per_course_unit',
        'tuition_fee',
        'status',
        'created_by_user_id',
    ];

    protected function casts(): array
    {
        return [
            'enrollment_fee' => 'decimal:2',
            'price_per_course_unit' => 'decimal:2',
            'tuition_fee' => 'decimal:2',
        ];
    }

    public function getScopeTypeAttribute(): string
    {
        if ($this->semester_id !== null) {
            return 'semester';
        }
        if ($this->academic_year_id !== null) {
            return 'academic_year';
        }
        if ($this->program_id !== null) {
            return 'program';
        }

        return 'all';
    }

    public function getScopeIdAttribute(): ?int
    {
        return $this->semester_id
            ?? $this->academic_year_id
            ?? $this->program_id;
    }
}
