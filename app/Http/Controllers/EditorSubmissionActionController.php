<?php

namespace App\Http\Controllers;

use App\Enums\ReviewAssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionEditorialDecision;
use App\Models\User;
use App\Models\WorkflowNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EditorSubmissionActionController extends Controller
{
    public function assignReviewer(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('assignReviewer', $submission);

        $assignable = [
            SubmissionStatus::Submitted,
            SubmissionStatus::UnderReview,
        ];

        if (! in_array($submission->status, $assignable, true)) {
            return back()->withErrors(['assign' => 'This submission cannot receive new reviewer invitations in its current state.']);
        }

        $data = $request->validate([
            'reviewer_id' => ['required', 'uuid', 'exists:users,id'],
            'deadline' => ['required', 'date', 'after:today'],
        ]);

        $reviewer = User::query()->findOrFail($data['reviewer_id']);

        ReviewAssignment::query()->create([
            'submission_id' => $submission->id,
            'round_version' => $submission->version,
            'reviewer_id' => $reviewer->id,
            'editor_id' => $request->user()->id,
            'status' => ReviewAssignmentStatus::Assigned,
            'deadline' => $data['deadline'],
            'invited_at' => now(),
            'responded_at' => now(),
        ]);

        if ($submission->status === SubmissionStatus::Submitted) {
            $submission->update(['status' => SubmissionStatus::UnderReview]);
        }

        WorkflowNotification::query()->create([
            'user_id' => $reviewer->id,
            'type' => 'review_assigned',
            'data' => [
                'submission_id' => $submission->id,
                'message' => 'You have been assigned a new review.',
            ],
        ]);

        return back()->with('status', 'Reviewer assigned successfully.');
    }

    public function sendForReview(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('sendForReview', $submission);

        $submission->update(['status' => SubmissionStatus::Submitted]);

        return back()->with('status', 'Manuscript cleared screening and is ready for reviewer assignment.');
    }

    public function returnToAuthor(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('screenReturn', $submission);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $submission->update([
            'status' => SubmissionStatus::RevisionRequested,
            'decision_letter' => $data['note'],
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $submission->author_id,
            'type' => 'screening_returned',
            'data' => [
                'submission_id' => $submission->id,
                'message' => 'Your submission was returned before peer review. Please address the editor\'s note and resubmit.',
            ],
        ]);

        return back()->with('status', 'Submission returned to the author for changes before peer review.');
    }

    public function deskReject(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('deskReject', $submission);

        $data = $request->validate([
            'note' => ['required', 'string', 'max:5000'],
        ]);

        $submission->update([
            'status' => SubmissionStatus::Rejected,
            'decision_letter' => $data['note'],
        ]);

        SubmissionEditorialDecision::query()->create([
            'submission_id' => $submission->id,
            'version' => $submission->version,
            'decision' => 'reject',
            'decision_letter' => $data['note'],
            'assessment_flags' => [],
            'recorded_by' => $request->user()->id,
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $submission->author_id,
            'type' => 'decision_made',
            'data' => [
                'submission_id' => $submission->id,
                'status' => SubmissionStatus::Rejected->value,
            ],
        ]);

        return back()->with('status', 'Submission desk-rejected without peer review and the author has been notified.');
    }
}
