<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionVersion extends Model
{
    use HasUuids;

    protected $fillable = [
        'submission_id',
        'version',
        'title',
        'abstract',
        'keywords',
        'submitted_at',
    ];

    protected function casts(): array
    {
        return [
            'keywords' => 'array',
            'submitted_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }
}
