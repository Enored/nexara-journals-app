<?php

namespace App\Http\Controllers\Api;

use App\Enums\ReviewAssignmentStatus;
use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Models\WorkflowNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $assignments = ReviewAssignment::query()
            ->with(['submission.journal'])
            ->where('reviewer_id', $request->user()->id)
            ->orderBy('deadline')
            ->paginate(25);

        return response()->json($assignments);
    }

    public function show(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($request->user()->id, $assignment);

        return response()->json($assignment->load(['submission.journal', 'review']));
    }

    public function accept(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        return response()->json(['message' => 'Assignments are mandatory and do not require acceptance.'], 422);
    }

    public function decline(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        return response()->json(['message' => 'Review assignments cannot be declined.'], 422);
    }

    public function submit(Request $request, ReviewAssignment $assignment): JsonResponse
    {
        $this->authorizeAssignment($request->user()->id, $assignment);

        if ($assignment->status !== ReviewAssignmentStatus::Assigned) {
            return response()->json(['message' => 'This assignment is not active.'], 422);
        }

        if ($assignment->review) {
            return response()->json(['message' => 'Review already submitted.'], 422);
        }

        $data = $request->validate([
            'originality_score' => ['required', 'integer', 'min:1', 'max:5'],
            'methodology_score' => ['required', 'integer', 'min:1', 'max:5'],
            'clarity_score' => ['required', 'integer', 'min:1', 'max:5'],
            'comments_for_editor' => ['required', 'string'],
            'recommendation' => ['required', 'string', 'in:accept,minor_revision,major_revision,reject'],
            'attachment_file_id' => ['nullable', 'uuid', 'exists:submission_files,id'],
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
            'attachment_file_id' => $data['attachment_file_id'] ?? null,
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

        return response()->json($review->load('assignment'), 201);
    }

    private function authorizeAssignment(string $userId, ReviewAssignment $assignment): void
    {
        if ($assignment->reviewer_id !== $userId) {
            abort(403);
        }
    }
}
