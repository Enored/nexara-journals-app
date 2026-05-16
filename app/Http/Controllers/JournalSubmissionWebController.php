<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Support\SubmissionVersionRecorder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalSubmissionWebController extends Controller
{
    public function create(): View
    {
        return view('journal.submit', [
            'journal' => current_journal(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $journal = current_journal();
        $data = $request->validate([
            'title' => ['required', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'max:5000'],
            'keywords' => ['required', 'string', 'max:2000'],
            'article_type' => ['required', 'string', 'max:100'],
        ]);

        $keywords = array_values(array_filter(array_map('trim', preg_split('/[,;]+/', $data['keywords']))));

        $submission = Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $request->user()->id,
            'title' => $data['title'],
            'abstract' => $data['abstract'],
            'keywords' => $keywords,
            'article_type' => $data['article_type'],
            'status' => SubmissionStatus::Submitted,
            'version' => 1,
            'submitted_at' => now(),
        ]);

        SubmissionVersionRecorder::record($submission->fresh());

        return redirect()->route('home')->with('status', 'Your manuscript was submitted.');
    }
}
