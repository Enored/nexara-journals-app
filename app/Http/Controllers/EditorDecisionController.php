<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Models\SubmissionEditorialDecision;
use App\Models\WorkflowNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditorDecisionController extends Controller
{
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('recordDecision', $submission);

        if ($submission->status !== SubmissionStatus::UnderReview) {
            return back()->withErrors(['decision' => 'A decision can be recorded while the manuscript is under review.']);
        }

        $data = $request->validate([
            'decision' => ['required', 'string', 'in:accept,minor_revision,major_revision,reject'],
            'decision_letter' => ['required', 'string'],
        ]);

        $status = match ($data['decision']) {
            'accept' => SubmissionStatus::Accepted,
            'minor_revision', 'major_revision' => SubmissionStatus::RevisionRequested,
            'reject' => SubmissionStatus::Rejected,
        };

        $submission->update([
            'status' => $status,
            'decision_letter' => $data['decision_letter'],
        ]);

        SubmissionEditorialDecision::query()->create([
            'submission_id' => $submission->id,
            'version' => $submission->version,
            'decision' => $data['decision'],
            'decision_letter' => $data['decision_letter'],
            'recorded_by' => $request->user()->id,
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $submission->author_id,
            'type' => 'decision_made',
            'data' => [
                'submission_id' => $submission->id,
                'status' => $status->value,
            ],
        ]);

        return back()->with('status', 'Decision saved and the author has been notified in-app.');
    }
}
