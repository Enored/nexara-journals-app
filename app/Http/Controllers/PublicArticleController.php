<?php

namespace App\Http\Controllers;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Support\ArticlePayload;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class PublicArticleController extends Controller
{
    public function show(Submission $submission): InertiaResponse
    {
        $journal = current_journal();
        if (! $journal || $submission->journal_id !== $journal->id || $submission->status !== SubmissionStatus::Published) {
            abort(404);
        }

        $submission->load(['author', 'edition.volume', 'journal']);

        if ($submission->edition && $submission->edition->status !== EditionStatus::Published) {
            abort(404);
        }

        return Inertia::render('Journal/Article', [
            'pageTitle' => $submission->title.' - '.$journal->name,
            'journal' => [
                'name' => $journal->name,
                'short' => $journal->abbreviation ?: $journal->name,
            ],
            'article' => ArticlePayload::fromSubmission($submission),
        ]);
    }
}
