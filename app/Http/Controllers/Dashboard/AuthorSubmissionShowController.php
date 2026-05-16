<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Concerns\LoadsSubmissionWorkspace;
use App\Http\Controllers\Controller;
use App\Models\Submission;
use App\Support\SubmissionEditorTimeline;
use Illuminate\View\View;

class AuthorSubmissionShowController extends Controller
{
    use LoadsSubmissionWorkspace;

    public function show(Submission $submission): View
    {
        $this->authorize('viewAsAuthor', $submission);

        $data = $this->loadSubmissionWorkspace($submission, forEditor: false);

        return view('dashboard.author.submission-show', [
            'submission' => $data['submission'],
            'timeline' => SubmissionEditorTimeline::build($data['submission'], forAuthor: true),
        ]);
    }
}
