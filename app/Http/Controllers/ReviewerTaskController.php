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

        if ($assignment->status !== ReviewAssignmentStatus::Assigned) {
            return back()->withErrors(['review' => 'This assignment is not active.']);
        }

        if ($assignment->review) {
            return back()->withErrors(['review' => 'A review has already been submitted for this assignment.']);
        }

        $data = $request->validate([
            'originality_score' => ['required', 'integer', 'min:1', 'max:5'],
            'methodology_score' => ['required', 'integer', 'min:1', 'max:5'],
            'clarity_score' => ['required', 'integer', 'min:1', 'max:5'],
            'comments_for_editor' => ['required', 'string'],
            'recommendation' => ['required', 'string', 'in:accept,minor_revision,major_revision,reject'],
        ]);

        $review = Review::query()->create([
            'assignment_id' => $assignment->id,
            'submission_id' => $assignment->submission_id,
            'reviewer_id' => $assignment->reviewer_id,
            'originality_score' => $data['originality_score'],
            'methodology_score' => $data['methodology_score'],
            'clarity_score' => $data['clarity_score'],
            'comments_for_author' => '',
            'comments_for_editor' => $data['comments_for_editor'],
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

}
