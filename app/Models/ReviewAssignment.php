<?php

namespace App\Models;

use App\Enums\ReviewAssignmentStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ReviewAssignment extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'round_version',
        'reviewer_id',
        'editor_id',
        'status',
        'deadline',
        'invited_at',
        'responded_at',
        'completed_at',
        'decline_reason',
    ];

    protected function casts(): array
    {
        return [
            'status' => ReviewAssignmentStatus::class,
            'deadline' => 'date',
            'invited_at' => 'datetime',
            'responded_at' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function editor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'editor_id');
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class, 'assignment_id');
    }
}
