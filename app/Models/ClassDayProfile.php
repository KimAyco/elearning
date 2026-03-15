<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassDayProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'name',
        'class_start_time',
        'class_end_time',
        'slot_minutes',
        'days_mask',
    ];

    protected function casts(): array
    {
        return [
            'days_mask' => 'array',
            'slot_minutes' => 'integer',
        ];
    }

    public function breaks(): HasMany
    {
        return $this->hasMany(ClassBreakBlock::class, 'class_day_profile_id');
    }
}

