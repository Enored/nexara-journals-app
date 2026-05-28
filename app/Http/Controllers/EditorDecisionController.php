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

        if (! in_array($submission->status, [SubmissionStatus::Submitted, SubmissionStatus::UnderReview], true)) {
            return back()->withErrors(['decision' => 'A decision can only be recorded for submitted or in-review manuscripts.']);
        }

        $data = $request->validate([
            'decision' => ['required', 'string', 'in:accept,minor_revision,major_revision,reject'],
            'decision_letter' => ['required', 'string'],
            'flags' => ['nullable', 'array'],
            'flags.*' => ['string', 'in:originality_verified,ai_content_detected,plagiarism_checked,ethics_reviewed,data_availability_confirmed'],
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
            'assessment_flags' => $data['flags'] ?? [],
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
