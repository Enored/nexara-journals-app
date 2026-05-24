<?php

namespace App\Http\Controllers;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\View\View;

class PublicArticleController extends Controller
{
    public function show(Submission $submission): View
    {
        $journal = current_journal();
        if (! $journal || $submission->journal_id !== $journal->id || $submission->status !== SubmissionStatus::Published) {
            abort(404);
        }

        $submission->load(['author', 'edition.volume', 'journal']);

        if ($submission->edition && $submission->edition->status !== EditionStatus::Published) {
            abort(404);
        }

        return view('journal.article', [
            'journal' => $journal,
            'submission' => $submission,
        ]);
    }
}
