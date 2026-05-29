<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class PlatformHomePayload
{
    /**
     * @return array<string, mixed>
     */
    public static function build(Collection $journals, Collection $latestArticles): array
    {
        $journalCount = $journals->count();
        $articleCount = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->count();

        $press = [
            'name' => platform_name(),
            'journals' => $journalCount,
            'articles' => $articleCount > 0 ? number_format($articleCount) : '0',
            'founded' => 2003,
            'downloads12mo' => '11.6M',
            'countries' => 168,
        ];

        $journalsPayload = $journals->values()->map(function (Journal $journal, int $index) {
            $publishedCount = (int) ($journal->published_articles_count ?? $journal->submissions()
                ->where('status', SubmissionStatus::Published)
                ->count());

            $abbr = self::journalAbbreviation($journal);

            return [
                'id' => $journal->id,
                'abbr' => $abbr,
                'name' => $journal->name,
                'field' => Str::limit($journal->description ?: 'Open-access research', 80),
                'est' => 2010,
                'impact' => '—',
                'articles' => $publishedCount,
                'url' => journal_front_url($journal),
                'flagship' => $index === 0,
            ];
        });

        $disciplines = [
            ['name' => 'Neuroscience', 'count' => max(1, (int) ceil($journalCount * 0.3))],
            ['name' => 'Cognitive science', 'count' => max(1, (int) ceil($journalCount * 0.35))],
            ['name' => 'Computational', 'count' => max(1, (int) ceil($journalCount * 0.25))],
            ['name' => 'Clinical', 'count' => max(1, (int) ceil($journalCount * 0.15))],
            ['name' => 'Methods & meta-science', 'count' => max(1, (int) ceil($journalCount * 0.2))],
        ];

        $latestPayload = $latestArticles->map(function (Submission $article) {
            $journal = $article->journal;

            return [
                'id' => $article->id,
                'jrnl' => $journal?->name ?? 'Journal',
                'subject' => $article->article_type ?: 'Research',
                'type' => $article->article_type ?: 'Research Article',
                'title' => $article->title,
                'authors' => $article->author?->name ?? 'Authors',
                'when' => $article->submitted_at?->format('j M Y') ?? '',
                'url' => $journal
                    ? journal_front_url($journal, '/articles/'.$article->id)
                    : null,
            ];
        })->values();

        $featuredArticle = $latestArticles->first();
        $featured = $featuredArticle ? [
            'journal' => $featuredArticle->journal?->name ?? 'Journal',
            'title' => $featuredArticle->title,
            'authors' => $featuredArticle->author?->name ?? '',
            'dek' => Str::limit($featuredArticle->abstract, 200),
            'altmetric' => 0,
            'citations' => 0,
            'downloads' => '—',
            'url' => $featuredArticle->journal
                ? journal_front_url($featuredArticle->journal, '/articles/'.$featuredArticle->id)
                : null,
        ] : [
            'journal' => platform_name(),
            'title' => 'Explore open research across our journals',
            'authors' => '',
            'dek' => 'Browse journals and read the latest published articles.',
            'altmetric' => 0,
            'citations' => 0,
            'downloads' => '—',
            'url' => null,
        ];

        return [
            'pageTitle' => platform_name().' — Open research in mind, brain & behaviour',
            'press' => $press,
            'journals' => $journalsPayload,
            'disciplines' => $disciplines,
            'featured' => $featured,
            'latest' => $latestPayload,
            'posts' => BlogPayload::forPublic(),
            'postCategories' => BlogPayload::CATEGORIES,
        ];
    }

    private static function journalAbbreviation(Journal $journal): string
    {
        $words = preg_split('/\s+/', trim($journal->name)) ?: [];
        if (count($words) >= 2) {
            return strtoupper(collect($words)->take(3)->map(fn ($w) => Str::substr($w, 0, 1))->implode(''));
        }

        return strtoupper(Str::substr($journal->subdomain ?: $journal->name, 0, 3));
    }
}
