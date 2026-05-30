<?php

namespace App\Enums;

enum SubmissionStatus: string
{
    case Screening = 'screening';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case RevisionRequested = 'revision_requested';
    case Accepted = 'accepted';
    case Rejected = 'rejected';
    case Published = 'published';

    public function label(): string
    {
        return match ($this) {
            self::Screening => 'In screening',
            default => str_replace('_', ' ', $this->value),
        };
    }
}
