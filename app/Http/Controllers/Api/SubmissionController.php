<?php

namespace App\Http\Controllers\Api;

use App\Enums\JournalRole;
use App\Enums\ReviewAssignmentStatus;
use App\Enums\SubmissionFileType;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\ReviewAssignment;
use App\Models\Submission;
use App\Models\SubmissionFile;
use App\Models\User;
use App\Models\WorkflowNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubmissionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = Submission::query()->with(['journal', 'author']);

        if ($user->isPlatformAdmin()) {
            if ($request->filled('journal_id')) {
                $query->where('journal_id', $request->string('journal_id'));
            }
        } elseif ($journal = current_journal()) {
            $query->where('journal_id', $journal->id);
            if (! $user->hasJournalRole($journal, JournalRole::Editor)
                && ! $user->hasJournalRole($journal, JournalRole::Admin)) {
                $query->where('author_id', $user->id);
            }
        } else {
            $query->where('author_id', $user->id);
        }

        return response()->json($query->orderByDesc('submitted_at')->paginate(25));
    }

    public function store(Request $request): JsonResponse
    {
        $journal = current_journal();
        if (! $journal) {
            return response()->json(['message' => 'Submissions must be created from a journal subdomain.'], 422);
        }

        $user = $request->user();

        $data = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'max:5000'],
            'keywords' => ['required', 'array', 'min:1'],
            'keywords.*' => ['string', 'max:100'],
            'article_type' => ['required', 'string', 'max:100'],
        ]);

        $submission = Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $user->id,
            'title' => $data['title'],
            'abstract' => $data['abstract'],
            'keywords' => $data['keywords'],
            'article_type' => $data['article_type'],
            'status' => SubmissionStatus::Submitted,
            'version' => 1,
            'submitted_at' => now(),
        ]);

        \App\Support\SubmissionVersionRecorder::record($submission->fresh());

        return response()->json($submission->load('journal'), 201);
    }

    public function show(Request $request, Submission $submission): JsonResponse
    {
        $this->authorizeView($request->user(), $submission);

        return response()->json($submission->load(['journal', 'author', 'files', 'reviewAssignments.reviewer', 'reviews']));
    }

    public function update(Request $request, Submission $submission): JsonResponse
    {
        $user = $request->user();
        if (! $user->isPlatformAdmin()
            && ! $user->hasJournalRole($submission->journal, JournalRole::Editor)) {
            abort(403);
        }

        $data = $request->validate([
            'title' => ['sometimes', 'string', 'max:500'],
            'edition_id' => ['nullable', 'uuid', 'exists:editions,id'],
        ]);

        $submission->update($data);

        return response()->json($submission->fresh()->load('edition'));
    }

    public function uploadFile(Request $request, Submission $submission): JsonResponse
    {
        $user = $request->user();
        $isAuthor = $submission->author_id === $user->id;
        $isEditor = $user->isPlatformAdmin()
            || $user->hasJournalRole($submission->journal, JournalRole::Editor)
            || $user->hasJournalRole($submission->journal, JournalRole::Admin);
        if (! $isAuthor && ! $isEditor) {
            abort(403);
        }

        if ($isAuthor && ! in_array($submission->status, [SubmissionStatus::Submitted, SubmissionStatus::RevisionRequested], true)) {
            abort(403, 'Authors may upload files only while the manuscript is submitted or awaiting your revision.');
        }

        $data = $request->validate([
            'file' => ['required', 'file', 'max:20480'],
            'file_type' => ['required', 'string', 'in:manuscript,supplementary,revision,review_attachment'],
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions/'.$submission->id, 'local');

        $record = SubmissionFile::query()->create([
            'submission_id' => $submission->id,
            'file_type' => SubmissionFileType::from($data['file_type']),
            'original_name' => $file->getClientOriginalName(),
            'storage_path' => $path,
            'mime_type' => $file->getClientMimeType() ?? 'application/octet-stream',
            'file_size' => $file->getSize(),
            'version' => $submission->version,
            'uploaded_by' => $user->id,
        ]);

        return response()->json($record, 201);
    }

    public function assignReviewer(Request $request, Submission $submission): JsonResponse
    {
        $this->authorize('assignReviewer', $submission);

        $user = $request->user();

        $data = $request->validate([
            'reviewer_id' => ['required', 'uuid', 'exists:users,id'],
            'deadline' => ['required', 'date', 'after:today'],
        ]);

        $reviewer = User::query()->findOrFail($data['reviewer_id']);

        $assignment = ReviewAssignment::query()->create([
            'submission_id' => $submission->id,
            'round_version' => $submission->version,
            'reviewer_id' => $reviewer->id,
            'editor_id' => $user->id,
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
                'assignment_id' => $assignment->id,
                'journal' => $submission->journal->name,
            ],
        ]);

        return response()->json($assignment->load('reviewer'), 201);
    }

    public function decision(Request $request, Submission $submission): JsonResponse
    {
        $user = $request->user();
        if (! $user->isPlatformAdmin()
            && ! $user->hasJournalRole($submission->journal, JournalRole::Editor)) {
            abort(403);
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

        WorkflowNotification::query()->create([
            'user_id' => $submission->author_id,
            'type' => 'decision_made',
            'data' => [
                'submission_id' => $submission->id,
                'status' => $status->value,
            ],
        ]);

        return response()->json($submission->fresh());
    }

    private function authorizeView(User $user, Submission $submission): void
    {
        if ($user->isPlatformAdmin()) {
            return;
        }
        if ($submission->author_id === $user->id) {
            return;
        }
        if ($user->hasJournalRole($submission->journal, JournalRole::Editor)) {
            return;
        }
        if ($submission->reviewAssignments()->where('reviewer_id', $user->id)->exists()) {
            return;
        }

        abort(403);
    }
}
