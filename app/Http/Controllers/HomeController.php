<?php

namespace App\Http\Controllers;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        if ($journal = current_journal()) {
            $forthcoming = Submission::query()
                ->where('journal_id', $journal->id)
                ->where('status', SubmissionStatus::Accepted)
                ->with('author')
                ->orderByDesc('submitted_at')
                ->limit(25)
                ->get();

            $published = Submission::query()
                ->where('submissions.journal_id', $journal->id)
                ->where('submissions.status', SubmissionStatus::Published)
                ->whereNotNull('submissions.edition_id')
                ->join('editions', 'submissions.edition_id', '=', 'editions.id')
                ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
                ->where('editions.status', EditionStatus::Published)
                ->with(['author', 'edition.volume'])
                ->orderByDesc('editions.published_at')
                ->orderByDesc('volumes.number')
                ->orderByDesc('editions.issue')
                ->select('submissions.*')
                ->limit(100)
                ->get();

            $publishedByEdition = $published->groupBy('edition_id');

            $publishedCount = Submission::query()
                ->where('journal_id', $journal->id)
                ->where('status', SubmissionStatus::Published)
                ->count();

            return view('journal.home', [
                'journal' => $journal,
                'forthcoming' => $forthcoming,
                'publishedByEdition' => $publishedByEdition,
                'publishedCount' => $publishedCount,
            ]);
        }

        $journals = Journal::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $latestArticles = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->with(['journal', 'author'])
            ->orderByDesc('submitted_at')
            ->limit(5)
            ->get();

        return view('home', [
            'journals' => $journals,
            'latestArticles' => $latestArticles,
        ]);
    }
}
