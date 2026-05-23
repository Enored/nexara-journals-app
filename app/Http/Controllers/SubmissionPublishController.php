<?php

namespace App\Http\Controllers;

use App\Models\Edition;
use App\Models\Submission;
use App\Support\EditionPublisher;
use App\Support\SubmissionWorkspace;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SubmissionPublishController extends Controller
{
    /**
     * Slot an accepted manuscript into a draft issue (does not make it public until the issue is published).
     */
    public function store(Request $request, Submission $submission): RedirectResponse
    {
        $this->authorize('publish', $submission);

        $data = $request->validate([
            'edition_id' => ['required', 'uuid', 'exists:editions,id'],
        ]);

        $edition = Edition::query()->findOrFail($data['edition_id']);
        EditionPublisher::slotSubmission($edition, $submission);

        return redirect()
            ->away(SubmissionWorkspace::editorRoute($submission))
            ->with('status', 'Manuscript added to '.$edition->label().'. Publish the issue when ready to release it on the journal site.');
    }
}
