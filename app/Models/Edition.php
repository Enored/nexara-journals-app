<?php

namespace App\Models;

use App\Enums\EditionStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Edition extends Model
{
    use HasUuids;

    protected $fillable = [
        'journal_id',
        'volume_id',
        'issue',
        'title',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'status' => EditionStatus::class,
            'published_at' => 'date',
        ];
    }

    public function isPublished(): bool
    {
        return $this->status === EditionStatus::Published;
    }

    public function isDraft(): bool
    {
        return $this->status === EditionStatus::Draft;
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', EditionStatus::Published);
    }

    /**
     * @param  Builder<self>  $query
     * @return Builder<self>
     */
    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', EditionStatus::Draft);
    }

    public function label(): string
    {
        $volumeNumber = $this->volume?->number ?? '?';
        $base = 'Vol. '.$volumeNumber.', No. '.$this->issue;

        return $this->title ? $base.' — '.$this->title : $base;
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function volume(): BelongsTo
    {
        return $this->belongsTo(Volume::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(Submission::class);
    }
}
