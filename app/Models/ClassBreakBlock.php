<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ClassBreakBlock extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_day_profile_id',
        'label',
        'start_time',
        'end_time',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(ClassDayProfile::class, 'class_day_profile_id');
    }
}

