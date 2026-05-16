<?php

namespace App\Http\Controllers;

use App\Enums\ReviewAssignmentStatus;
use App\Enums\SubmissionStatus;
use App\Models\ReviewAssignment;
use App\Models\Submission;
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
            'status' => ReviewAssignmentStatus::Invited,
            'deadline' => $data['deadline'],
            'invited_at' => now(),
        ]);

        if ($submission->status === SubmissionStatus::Submitted) {
            $submission->update(['status' => SubmissionStatus::UnderReview]);
        }

        WorkflowNotification::query()->create([
            'user_id' => $reviewer->id,
            'type' => 'review_invited',
            'data' => [
                'submission_id' => $submission->id,
                'message' => 'You have a new review invitation.',
            ],
        ]);

        return back()->with('status', 'Reviewer invited successfully.');
    }
}
