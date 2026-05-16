<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Edition;
use App\Models\Submission;
use App\Models\WorkflowNotification;
use App\Support\SubmissionWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class SubmissionPublishController extends Controller
{
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('publish', $submission);

        $data = $request->validate([
            'edition_id' => ['required', 'uuid', 'exists:editions,id'],
        ]);

        $edition = Edition::query()->findOrFail($data['edition_id']);
        if ($edition->journal_id !== $submission->journal_id) {
            throw ValidationException::withMessages([
                'edition_id' => 'Choose an edition that belongs to this journal.',
            ]);
        }

        if ($submission->status !== SubmissionStatus::Accepted) {
            return back()->withErrors(['publish' => 'Only accepted manuscripts can be published.']);
        }

        $submission->update([
            'edition_id' => $edition->id,
            'status' => SubmissionStatus::Published,
        ]);

        WorkflowNotification::query()->create([
            'user_id' => $submission->author_id,
            'type' => 'submission_published',
            'data' => [
                'submission_id' => $submission->id,
                'edition_id' => $edition->id,
            ],
        ]);

        return redirect()
            ->away(SubmissionWorkspace::editorRoute($submission))
            ->with('status', 'The manuscript is now published in Vol. '.$edition->volume.', No. '.$edition->issue.'.');
    }
}
