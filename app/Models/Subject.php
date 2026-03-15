<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subject extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'department_id',
        'code',
        'title',
        'units',
        'price_per_subject',
        'weekly_hours',
        'duration_weeks',
        'status',
    ];

    protected $casts = [
        'units' => 'decimal:1',
        'price_per_subject' => 'decimal:2',
        'weekly_hours' => 'decimal:1',
    ];

    public function offerings(): HasMany
    {
        return $this->hasMany(SubjectOffering::class);
    }

    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'subject_prerequisites',
            'subject_id',
            'prerequisite_subject_id'
        )->withPivot('school_id');
    }
}
