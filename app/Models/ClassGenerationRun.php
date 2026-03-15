<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClassGenerationRun extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'class_group_id',
        'initiated_by_user_id',
        'summary_json',
    ];

    protected function casts(): array
    {
        return [
            'summary_json' => 'array',
        ];
    }

    public function classGroup(): BelongsTo
    {
        return $this->belongsTo(ClassGroup::class, 'class_group_id');
    }

    public function sessions(): HasMany
    {
        return $this->hasMany(ClassSession::class, 'generation_run_id');
    }
}

