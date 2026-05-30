<?php

namespace App\Models;

use App\Enums\SubmissionFileType;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubmissionFile extends Model
{
    use HasUuids;

    /**
     * Storage disk for manuscript files. Centralized here so swapping the
     * local disk for cloud/object storage later is a one-line change.
     */
    public const DISK = 'local';

    protected $fillable = [
        'submission_id',
        'file_type',
        'original_name',
        'storage_path',
        'mime_type',
        'file_size',
        'version',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'file_type' => SubmissionFileType::class,
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
