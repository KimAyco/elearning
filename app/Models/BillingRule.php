<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingRule extends Model
{
    use HasFactory;

    protected $appends = [
        'scope_type',
        'scope_id',
    ];

    protected $fillable = [
        'school_id',
        'semester_id',
        'charge_type',
        'billing_category_id',
        'year_level',
        'program_id',
        'department_id',
        'section_id',
        'scope_student_user_id',
        'description',
        'amount',
        'is_required',
        'status',
        'created_by_finance_user_id',
    ];

    public function category()
    {
        return $this->belongsTo(BillingCategory::class, 'billing_category_id');
    }

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'is_required' => 'boolean',
            'year_level' => 'integer',
        ];
    }

    public function getScopeTypeAttribute(): string
    {
        if ($this->program_id !== null) {
            return 'program';
        }
        if ($this->department_id !== null) {
            return 'department';
        }
        if ($this->section_id !== null) {
            return 'section';
        }
        if ($this->scope_student_user_id !== null) {
            return 'student';
        }

        return 'all';
    }

    public function getScopeIdAttribute(): ?int
    {
        return $this->program_id
            ?? $this->department_id
            ?? $this->section_id
            ?? $this->scope_student_user_id;
    }
}
