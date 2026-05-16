<?php

namespace App\Http\Controllers;

use App\Enums\ReviewAssignmentStatus;
use App\Enums\ReviewRecommendation;
use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\WorkflowNotification;
use App\Support\SubmissionEditorTimeline;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewerTaskController extends Controller
{
    public function show(ReviewAssignment $assignment): View
    {
        $this->authorize('view', $assignment);

        $assignment->load(['submission.journal', 'submission.files', 'review']);

        $submission = $assignment->submission;

        return view('dashboard.reviewer.review-task-show', [
            'assignment' => $assignment,
            'submission' => $submission,
            'timeline' => SubmissionEditorTimeline::buildForReviewer($assignment),
        ]);
    }

    public function store(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        $this->authorize('submit', $assignment);

        if ($assignment->status !== ReviewAssignmentStatus::Accepted) {
            return back()->withErrors(['review' => 'Accept the invitation before submitting a review.']);
        }

        if ($assignment->review) {
            return back()->withErrors(['review' => 'A review has already been submitted for this assignment.']);
        }

        $data = $request->validate([
            'originality_score' => ['required', 'integer', 'min:1', 'max:5'],
            'methodology_score' => ['required', 'integer', 'min:1', 'max:5'],
            'clarity_score' => ['required', 'integer', 'min:1', 'max:5'],
            'comments_for_author' => ['required', 'string'],
            'comments_for_editor' => ['nullable', 'string'],
            'recommendation' => ['required', 'string', 'in:accept,minor_revision,major_revision,reject'],
        ]);

        $review = Review::query()->create([
            'assignment_id' => $assignment->id,
            'submission_id' => $assignment->submission_id,
            'reviewer_id' => $assignment->reviewer_id,
            'originality_score' => $data['originality_score'],
            'methodology_score' => $data['methodology_score'],
            'clarity_score' => $data['clarity_score'],
            'comments_for_author' => $data['comments_for_author'],
            'comments_for_editor' => $data['comments_for_editor'] ?? null,
            'recommendation' => ReviewRecommendation::from($data['recommendation']),
            'attachment_file_id' => null,
            'submitted_at' => now(),
        ]);

        $assignment->update([
            'status' => ReviewAssignmentStatus::Completed,
            'completed_at' => now(),
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $assignment->editor_id,
            'type' => 'review_submitted',
            'data' => [
                'submission_id' => $assignment->submission_id,
                'review_id' => $review->id,
            ],
        ]);

        return redirect()
            ->away(platform_route('review-tasks.show', $assignment))
            ->with('status', 'Review submitted. Thank you.');
    }

    public function accept(ReviewAssignment $assignment): RedirectResponse
    {
        $this->authorize('respond', $assignment);

        if ($assignment->status !== ReviewAssignmentStatus::Invited) {
            return back()->withErrors(['invite' => 'This invitation is no longer open.']);
        }

        $assignment->update([
            'status' => ReviewAssignmentStatus::Accepted,
            'responded_at' => now(),
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $assignment->editor_id,
            'type' => 'reviewer_accepted',
            'data' => [
                'submission_id' => $assignment->submission_id,
                'assignment_id' => $assignment->id,
            ],
        ]);

        return back()->with('status', 'You accepted this review.');
    }

    public function decline(Request $request, ReviewAssignment $assignment): RedirectResponse
    {
        $this->authorize('respond', $assignment);

        if ($assignment->status !== ReviewAssignmentStatus::Invited) {
            return back()->withErrors(['invite' => 'This invitation is no longer open.']);
        }

        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:5000'],
        ]);

        $assignment->update([
            'status' => ReviewAssignmentStatus::Declined,
            'responded_at' => now(),
            'decline_reason' => $data['reason'] ?? null,
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $assignment->editor_id,
            'type' => 'reviewer_declined',
            'data' => [
                'submission_id' => $assignment->submission_id,
                'assignment_id' => $assignment->id,
            ],
        ]);

        return redirect()
            ->away(platform_route('reviewer.inbox'))
            ->with('status', 'You declined this review.');
    }
}
