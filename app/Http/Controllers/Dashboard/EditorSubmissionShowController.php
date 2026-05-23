<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Concerns\LoadsSubmissionWorkspace;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Support\EditorRoundReviews;
use App\Support\SubmissionEditorTimeline;
use Illuminate\View\View;

class EditorSubmissionShowController extends Controller
{
    use LoadsSubmissionWorkspace;

    public function show(Submission $submission): View
    {
        $this->authorize('viewAsEditor', $submission);

        $data = $this->loadSubmissionWorkspace($submission, forEditor: true);

        return view('dashboard.editor.submission-show', [
            ...$data,
            'timeline' => SubmissionEditorTimeline::build($data['submission']),
            'roundReviews' => EditorRoundReviews::forCurrentRound($data['submission']),
        ]);
    }
}
