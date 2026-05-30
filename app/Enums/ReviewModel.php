<?php

namespace App\Enums;

enum ReviewModel: string
{
    case DoubleBlind = 'double_blind';
    case SingleBlind = 'single_blind';
    case OpenReview = 'open_review';

    public function label(): string
    {
        return match ($this) {
            self::DoubleBlind => 'Double-Blind',
            self::SingleBlind => 'Single-Blind',
            self::OpenReview => 'Open Review',
        };
    }

    /**
     * Whether reviewer and editor identities are hidden from the author.
     */
    public function hidesReviewersFromAuthor(): bool
    {
        return $this === self::SingleBlind || $this === self::DoubleBlind;
    }

    /**
     * Whether the author identity is hidden from reviewers.
     */
    public function hidesAuthorFromReviewer(): bool
    {
        return $this === self::DoubleBlind;
    }
}
