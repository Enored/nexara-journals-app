<?php

namespace App\Models;

use App\Enums\AnnouncementCategory;
use App\Enums\AnnouncementScope;
use App\Enums\AnnouncementStatus;
use App\Enums\AnnouncementType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Announcement extends Model
{
    use HasUuids;

    protected $fillable = [
        'journal_id',
        'scope',
        'category',
        'type',
        'status',
        'title',
        'body',
        'url',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'scope' => AnnouncementScope::class,
            'category' => AnnouncementCategory::class,
            'type' => AnnouncementType::class,
            'status' => AnnouncementStatus::class,
            'expires_at' => 'datetime',
        ];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function isExpired(): bool
    {
        return $this->expires_at !== null && $this->expires_at->lte(now());
    }

    /**
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    /**
     * Published announcements visible on a journal home page (global + journal-specific, not expired).
     *
     * @param  Builder<Announcement>  $query
     * @return Builder<Announcement>
     */
    public function scopeVisibleForJournal(Builder $query, Journal $journal): Builder
    {
        return $query
            ->where('status', AnnouncementStatus::Published)
            ->where(function (Builder $query) use ($journal) {
                $query->where('scope', AnnouncementScope::Global)
                    ->orWhere('journal_id', $journal->id);
            })
            ->notExpired();
    }
}
