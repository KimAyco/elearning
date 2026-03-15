<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolRegistration extends Model
{
    use HasFactory;

    protected $table = 'school_registrations';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'subdomain',
        'plan_months',
        'status',
        'auto_approved',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'plan_months' => 'integer',
            'auto_approved' => 'boolean',
            'metadata' => 'array',
        ];
    }
}
