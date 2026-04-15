<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProgramProspectus extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'program_id',
        'name',
        'status',
        'created_by_user_id',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProgramProspectusItem::class, 'program_prospectus_id');
    }
}

