<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'subdomain',
        'issn',
        'description',
        'logo_path',
        'primary_color',
        'submission_guidelines',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function editions(): HasMany
    {
        return $this->hasMany(Edition::class);
    }

    public function volumes(): HasMany
    {
        return $this->hasMany(Volume::class);
    }

    public function journalUserRoles(): HasMany
    {
        return $this->hasMany(JournalUserRole::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }

    public function getRouteKeyName(): string
    {
        return 'subdomain';
    }
}
