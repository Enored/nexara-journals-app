<?php

namespace App\Models;

use App\Enums\SubmissionStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Submission extends Model
{
    use HasUuids;

    protected $attributes = [
        'version' => 1,
    ];

    protected $fillable = [
        'journal_id',
        'author_id',
        'title',
        'abstract',
        'keywords',
        'article_type',
        'status',
        'version',
        'edition_id',
        'decision_letter',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'status' => SubmissionStatus::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(Journal::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(Edition::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(SubmissionFile::class)
            ->orderByDesc('version')
            ->orderByDesc('created_at');
    }

    public function reviewAssignments(): HasMany
    {
        return $this->hasMany(ReviewAssignment::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function versions(): HasMany
    {
        return $this->hasMany(SubmissionVersion::class)->orderBy('version');
    }

    public function editorialDecisions(): HasMany
    {
        return $this->hasMany(SubmissionEditorialDecision::class)->orderBy('created_at');
    }
}
