<?php

namespace App\Http\Controllers;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Edition;
use App\Models\Journal;
use App\Models\Submission;
use App\Support\ArticlePayload;
use App\Support\PlatformHomePayload;
use Illuminate\Http\Response;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class HomeController extends Controller
{
    public function index(): InertiaResponse|Response
    {
        if ($journal = current_journal()) {
            return $this->renderJournalHome($journal);
        }

        $journals = Journal::query()
            ->where('is_active', true)
            ->withCount(['submissions as published_articles_count' => fn ($q) => $q->where('status', SubmissionStatus::Published)])
            ->orderBy('name')
            ->get();

        $latestArticles = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->with(['journal', 'author'])
            ->orderByDesc('submitted_at')
            ->limit(6)
            ->get();

        return Inertia::render('Platform/Home', PlatformHomePayload::build($journals, $latestArticles));
    }

    private function renderJournalHome(Journal $journal): InertiaResponse
    {
        // Single query: every published edition for this journal, eager-loading its
        // volume and the count of published submissions. The join on `volumes` lets
        // us order by volume number deterministically when published_at ties.
        $publishedEditions = Edition::query()
            ->select('editions.*')
            ->where('editions.journal_id', $journal->id)
            ->where('editions.status', EditionStatus::Published)
            ->whereNotNull('editions.published_at')
            ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
            ->with(['volume:id,number,title'])
            ->withCount(['submissions as articles_count' => fn ($query) => $query->where('status', SubmissionStatus::Published),
            ])
            ->orderByDesc('editions.published_at')
            ->orderByDesc('volumes.number')
            ->orderByDesc('editions.issue')
            ->get();

        $totalIssues = $publishedEditions->count();
        $currentEdition = $publishedEditions->first();

        $foundedYear = $publishedEditions
            ->map(fn (Edition $edition) => (int) $edition->published_at->format('Y'))
            ->min() ?: (int) now()->format('Y');

        $currentArticles = $currentEdition
            ? Submission::query()
                ->where('edition_id', $currentEdition->id)
                ->where('status', SubmissionStatus::Published)
                ->with(['author:id,name', 'edition.volume:id,number'])
                ->orderByDesc('submitted_at')
                ->get()
            : collect();

        $articlePayload = $currentArticles->values()->map(
            fn (Submission $article, int $index) => ArticlePayload::fromSubmission($article, $index),
        );

        if ($articlePayload->isEmpty()) {
            $articlePayload = collect([
                [
                    'id' => 'sample-1',
                    'type' => 'Research Article',
                    'oa' => true,
                    'subject' => 'Computational Cognition',
                    'title' => 'Sample article title for journal UI preview',
                    'authors' => [['name' => 'Nexara Editorial Team', 'corresp' => true, 'aff' => 1]],
                    'affiliations' => ['Nexara Journal Network'],
                    'abstract' => 'Dummy article content is shown because there are no published submissions yet.',
                    'doi' => '10.31472/nexara.0001',
                    'pages' => '1-12',
                    'volume' => 18,
                    'issue' => 2,
                    'year' => (int) now()->format('Y'),
                    'publishedOn' => now()->format('M j, Y'),
                    'receivedOn' => now()->subDays(40)->format('M j, Y'),
                    'citations' => 3,
                    'downloads' => '1,204',
                    'altmetric' => 51,
                    'altmetricBreakdown' => ['news' => 2, 'twitter' => 20, 'blogs' => 5, 'policy' => 0],
                    'keywords' => ['journal', 'research', 'open access'],
                    'initials' => 'NA',
                ],
            ]);
        }

        $firstArticle = $articlePayload->first();

        $currentEditionPublishedAt = $currentEdition?->published_at;

        $journalPayload = [
            'name' => $journal->name ?: 'Journal of Computational Cognition',
            'short' => $journal->abbreviation ?: 'J. Comput. Cognition',
            'founded' => (int) $foundedYear,
            'totalIssues' => $totalIssues,
            'issn_online' => $journal->issn ?: '2845-1739',
            'issn_print' => '2845-1720',
            'doiPrefix' => '10.31472/jcc',
            'frequency' => 'Quarterly · 4 issues / year',
            'impact' => '6.42',
            'acceptance' => '18%',
            'citeScore' => '9.1',
            'timeToFirstDecision' => '31 days',
            'tagline' => $journal->description ?: 'A peer-reviewed, fully open-access venue for theoretical, computational, and empirical work at the intersection of cognition, learning systems, and neural computation.',
            'currentVolume' => $currentEdition?->volume?->number ?? $firstArticle['volume'],
            'currentIssue' => $currentEdition?->issue ?? $firstArticle['issue'],
            'currentDate' => $currentEditionPublishedAt?->format('M Y') ?? now()->format('M Y'),
            'currentArticlesCount' => $currentEdition ? (int) $currentEdition->articles_count : $articlePayload->count(),
            'editorChief' => [
                'name' => optional($journal->owner)->name ?: 'Helena Vasquez',
                'aff' => 'Nexara Research Network',
                'init' => 'HV',
            ],
            'editors' => [
                ['name' => 'Marek Toth', 'role' => 'Deputy Editor', 'aff' => 'ETH Zurich', 'init' => 'MT'],
                ['name' => 'Ayanna Okafor', 'role' => 'Methods Editor', 'aff' => 'UCL', 'init' => 'AO'],
                ['name' => 'Rohan Iyer', 'role' => 'Statistics Editor', 'aff' => 'Stanford University', 'init' => 'RI'],
                ['name' => 'Sofia Castellanos', 'role' => 'Reviews Editor', 'aff' => 'Max Planck Inst.', 'init' => 'SC'],
            ],
        ];

        $currentEditionId = $currentEdition?->id;

        $issuesPayload = $publishedEditions->take(6)->map(function (Edition $edition) use ($currentEditionId) {
            $publishedAt = $edition->published_at;
            $isCurrent = $edition->id === $currentEditionId;

            return [
                'id' => $edition->id,
                'v' => (int) ($edition->volume?->number ?? 0),
                'i' => (int) $edition->issue,
                'year' => (int) $publishedAt->format('Y'),
                'month' => $publishedAt->format('M'),
                'topic' => $edition->title ?: ($isCurrent ? 'Current journal issue' : 'Archive issue'),
                'current' => $isCurrent,
                'articles' => (int) $edition->articles_count,
            ];
        })->values();

        $subjectsPayload = [
            'Reinforcement Learning',
            'Neural Computation',
            'Memory and Recall',
            'Decision Making',
            'Bayesian Cognition',
            'Language and Symbols',
            'Computational Psychiatry',
            'Reviews and Tutorials',
        ];

        return Inertia::render('Journal/Home', [
            'pageTitle' => $journal->name.' - '.platform_name(),
            'journal' => $journalPayload,
            'articles' => $articlePayload->values(),
            'issues' => $issuesPayload,
            'subjects' => $subjectsPayload,
        ]);
    }
}
