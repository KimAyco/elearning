<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'intro',
        'tag_primary',
        'tag_neutral',
        'tag_accent',
        'fact1_label',
        'fact1_value',
        'fact1_caption',
        'fact2_label',
        'fact2_value',
        'fact2_caption',
        'fact3_label',
        'fact3_value',
        'fact3_caption',
        'campus_title',
        'campus_bullet1',
        'campus_bullet2',
        'campus_bullet3',
        'campus_bullet4',
    ];

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

