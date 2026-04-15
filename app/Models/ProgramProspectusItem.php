<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramProspectusItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'program_prospectus_id',
        'semester_id',
        'subject_id',
        'year_level',
        'status',
        'created_by_user_id',
    ];

    public function prospectus(): BelongsTo
    {
        return $this->belongsTo(ProgramProspectus::class, 'program_prospectus_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(Semester::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
    }
}

