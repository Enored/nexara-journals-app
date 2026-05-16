<?php

namespace App\Models;

use App\Enums\ReviewRecommendation;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasUuids;

    protected $fillable = [
        'assignment_id',
        'submission_id',
        'reviewer_id',
        'originality_score',
        'methodology_score',
        'clarity_score',
        'comments_for_author',
        'comments_for_editor',
        'recommendation',
        'attachment_file_id',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'recommendation' => ReviewRecommendation::class,
            'submitted_at' => 'datetime',
        ];
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(ReviewAssignment::class, 'assignment_id');
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function attachmentFile(): BelongsTo
    {
        return $this->belongsTo(SubmissionFile::class, 'attachment_file_id');
    }
}
