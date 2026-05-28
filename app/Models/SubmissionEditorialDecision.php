<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionEditorialDecision extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'version',
        'decision',
        'decision_letter',
        'assessment_flags',
        'recorded_by',
    ];

    protected function casts(): array
    {
        return [
            'assessment_flags' => 'array',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
