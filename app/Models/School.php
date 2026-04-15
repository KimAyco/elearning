<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_code',
        'name',
        'short_description',
        'logo_path',
        'footer_logo_path',
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

    public function getFooterLogoUrlAttribute(): ?string
    {
        if (empty($this->footer_logo_path)) {
            return null;
        }

        return asset('storage/' . ltrim($this->footer_logo_path, '/'));
    }

    /**
     * Optional school seal at schools/{id}/school logo.png on the public disk (tenant login card).
     */
    public function schoolSealLogoUrl(): ?string
    {
        $relative = 'schools/' . $this->id . '/school logo.png';
        if (! Storage::disk('public')->exists($relative)) {
            return null;
        }

        $encoded = implode('/', array_map('rawurlencode', explode('/', $relative)));

        return asset('storage/' . $encoded);
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
            'admin_appearance' => 'array',
        ];
    }

    /**
     * Resolve admin dashboard appearance with sensible defaults.
     *
     * @return array{preset:string,primary:string,sidebar:string,accent:string}
     */
    public function adminAppearanceConfig(): array
    {
        $stored = (array) ($this->admin_appearance ?? []);

        $defaults = [
            'preset'  => 'default',
            'primary' => '#334155',   // slate-700 — neutral, not brand-colored
            'sidebar' => '#f8fafc',   // near-white — light sidebar for Default
            'accent'  => '#475569',   // slate-600
        ];

        return array_merge($defaults, array_intersect_key($stored, $defaults));
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

