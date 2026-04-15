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
        'footer_title',
        'footer_description',
        'footer_address',
        'footer_email',
        'footer_phone',
        'footer_copyright',
        'footer_quick_links',
        'footer_social_facebook',
        'footer_social_instagram',
        'footer_social_x',
        'footer_social_youtube',
        'footer_social_website',
    ];

    protected function casts(): array
    {
        return [
            'footer_quick_links' => 'array',
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }
}

