<?php

namespace App\Models;

use App\Enums\ReviewModel;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Journal extends Model
{
    use HasUuids;

    protected $fillable = [
        'name',
        'abbreviation',
        'subdomain',
        'e_issn',
        'p_issn',
        'doi_prefix',
        'excerpt',
        'description',
        'cover_image_url',
        'primary_color',
        'submission_guidelines',
        'review_model',
        'frequency',
        'license_type',
        'contact_email',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'review_model' => ReviewModel::class,
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
