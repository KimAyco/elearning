<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_code',
        'name',
        'short_description',
        'logo_path',
        'cover_image_path',
        'theme',
        'status',
        'subscription_state',
        'suspended_at',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo_path)) {
            return null;
        }
        return asset('storage/' . ltrim($this->logo_path, '/'));
    }

    public function getCoverImageUrlAttribute(): ?string
    {
        if (empty($this->cover_image_path)) {
            return null;
        }
        return asset('storage/' . ltrim($this->cover_image_path, '/'));
    }

    protected function casts(): array
    {
        return [
            'suspended_at' => 'datetime',
        ];
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function profile(): HasOne
    {
        return $this->hasOne(SchoolProfile::class);
    }
}

