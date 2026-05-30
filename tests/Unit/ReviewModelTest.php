<?php

namespace Tests\Unit;

use App\Enums\ReviewModel;
use PHPUnit\Framework\TestCase;

class ReviewModelTest extends TestCase
{
    public function test_labels_are_human_readable(): void
    {
        $this->assertSame('Double-Blind', ReviewModel::DoubleBlind->label());
        $this->assertSame('Single-Blind', ReviewModel::SingleBlind->label());
        $this->assertSame('Open Review', ReviewModel::OpenReview->label());
    }

    public function test_reviewers_are_hidden_from_author_for_single_and_double_blind(): void
    {
        $this->assertTrue(ReviewModel::SingleBlind->hidesReviewersFromAuthor());
        $this->assertTrue(ReviewModel::DoubleBlind->hidesReviewersFromAuthor());
        $this->assertFalse(ReviewModel::OpenReview->hidesReviewersFromAuthor());
    }

    public function test_author_is_hidden_from_reviewer_only_for_double_blind(): void
    {
        $this->assertTrue(ReviewModel::DoubleBlind->hidesAuthorFromReviewer());
        $this->assertFalse(ReviewModel::SingleBlind->hidesAuthorFromReviewer());
        $this->assertFalse(ReviewModel::OpenReview->hidesAuthorFromReviewer());
    }
}
