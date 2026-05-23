<?php

namespace App\Models;

use App\Enums\JournalRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasUuids, Notifiable;

    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'password',
        'mobile',
        'bio',
        'city',
        'country',
        'orcid_id',
        'affiliation',
        'expertise',
        'is_active',
        'is_platform_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'expertise' => 'array',
            'is_active' => 'boolean',
            'is_platform_admin' => 'boolean',
        ];
    }

    public function journalUserRoles(): HasMany
    {
        return $this->hasMany(JournalUserRole::class);
    }

    /** Staff roles (reviewer, editor, journal admin) — not the implicit author role. */
    public function staffJournalRoles(): HasMany
    {
        return $this->journalUserRoles()->assignable();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class, 'author_id');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class, 'reviewer_id');
    }

    public function isPlatformAdmin(): bool
    {
        return (bool) $this->is_platform_admin;
    }

    public function syncDisplayName(): void
    {
        $this->name = trim($this->first_name.' '.$this->last_name);
    }

    public function initials(): string
    {
        $first = mb_substr(trim((string) $this->first_name), 0, 1);
        $last = mb_substr(trim((string) $this->last_name), 0, 1);

        if ($first !== '' && $last !== '') {
            return mb_strtoupper($first.$last);
        }

        if ($first !== '') {
            return mb_strtoupper($first);
        }

        return mb_strtoupper(mb_substr(trim((string) $this->name), 0, 1) ?: '?');
    }

    public function hasJournalRole(Journal $journal, JournalRole $role): bool
    {
        return $this->journalUserRoles()
            ->where('journal_id', $journal->id)
            ->where('role', $role)
            ->exists();
    }

    public function workflowNotifications(): HasMany
    {
        return $this->hasMany(WorkflowNotification::class);
    }
}
