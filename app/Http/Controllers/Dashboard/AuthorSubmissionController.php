<?php

namespace App\Http\Controllers\Dashboard;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\Submission;
use App\Support\AuthorManuscriptSubmitter;
use App\Support\AuthorSubmissionIndexFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuthorSubmissionController extends Controller
{
    use ReturnsDashListPartial;

    public function index(Request $request): View
    {
        $user = auth()->user();

        $base = Submission::query()->where('author_id', $user->id);

        $authorJournalIds = (clone $base)->distinct()->pluck('journal_id');
        $authorJournals = Journal::query()
            ->whereIn('id', $authorJournalIds)
            ->orderBy('name')
            ->get();

        $submitJournals = Journal::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $filters = AuthorSubmissionIndexFilters::fromRequest($request, $authorJournals);
        $submissions = AuthorSubmissionIndexFilters::paginate($filters, $user->id);

        $stats = [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->whereIn('status', [
                SubmissionStatus::Screening,
                SubmissionStatus::Submitted,
                SubmissionStatus::UnderReview,
                SubmissionStatus::RevisionRequested,
            ])->count(),
            'published' => (clone $base)->where('status', SubmissionStatus::Published)->count(),
            'revision' => (clone $base)->where('status', SubmissionStatus::RevisionRequested)->count(),
        ];

        $data = [
            'user' => $user,
            'submissions' => $submissions,
            'stats' => $stats,
            'authorJournals' => $authorJournals,
            'submitJournals' => $submitJournals,
            'articleTypes' => config('journal.article_types', []),
            'statuses' => SubmissionStatus::cases(),
            'filters' => $filters,
            'activeFilterPills' => AuthorSubmissionIndexFilters::activeFilterPills($filters, $authorJournals),
            'hasActiveFilters' => AuthorSubmissionIndexFilters::hasActiveFilters($filters),
        ];

        return $this->dashListResponse(
            $request,
            'dashboard.author.partials.submissions-list',
            'dashboard.author.submissions',
            $data,
        );
    }

    public function store(Request $request): RedirectResponse
    {
        $articleTypes = config('journal.article_types', []);

        $data = $request->validate([
            'journal_id' => [
                'required',
                'uuid',
                Rule::exists('journals', 'id')->where(fn ($q) => $q->where('is_active', true)),
            ],
            'title' => ['required', 'string', 'max:500'],
            'abstract' => ['required', 'string', 'max:5000'],
            'keywords' => ['required', 'string', 'max:2000'],
            'article_type' => ['required', 'string', Rule::in($articleTypes)],
            'manuscript' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
            'blinded_manuscript' => ['nullable', 'file', 'mimes:pdf,doc,docx', 'max:20480'],
        ]);

        $submission = AuthorManuscriptSubmitter::submit($data, $request->user());

        return redirect()
            ->route('author.submissions')
            ->with('status', 'Manuscript submitted to '.$submission->journal->name.'.');
    }
}
