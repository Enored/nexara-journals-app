<?php

namespace App\Http\Controllers;

use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Support\SubmissionWorkspace;
use Illuminate\Http\RedirectResponse;

class SubmissionLegacyRedirectController extends Controller
{
    public function __invoke(Submission $submission): RedirectResponse
    {
        $user = auth()->user();

        if ($user->can('viewAsAuthor', $submission)) {
            return redirect()->away(SubmissionWorkspace::authorRoute($submission), 301);
        }

        if ($user->can('viewAsEditor', $submission)) {
            return redirect()->away(SubmissionWorkspace::editorRoute($submission), 301);
        }

        $assignment = ReviewAssignment::query()
            ->where('submission_id', $submission->id)
            ->where('reviewer_id', $user->id)
            ->orderByDesc('invited_at')
            ->first();

        if ($assignment) {
            return redirect()->away(platform_route('review-tasks.show', $assignment), 301);
        }

        abort(403);
    }
}
