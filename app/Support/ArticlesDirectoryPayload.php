<?php

namespace App\Support;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class ArticlesDirectoryPayload
{
    /** @var list<string> */
    private const TYPE_ORDER = [
        'Research Article',
        'Review',
        'Short Report',
        'Registered Report',
        'Tutorial',
        'Methods',
        'Editorial',
    ];

    /**
     * @return array<string, mixed>
     */
    public static function build(Collection $articles): array
    {
        $papers = $articles->values()->map(
            fn (Submission $article, int $index) => self::mapArticle($article, $index),
        )->all();

        $totalPublished = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->count();

        return [
            'pageTitle' => 'Articles — '.platform_name(),
            'press' => [
                'name' => platform_name(),
                'journals' => Journal::query()->where('is_active', true)->count(),
                'articles' => $totalPublished > 0 ? number_format($totalPublished) : '0',
            ],
            'papers' => $papers,
            'paperTypes' => self::paperTypes($papers),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function mapArticle(Submission $article, int $index): array
    {
        $journal = $article->journal;
        $authorName = $article->author?->name ?? 'Authors';
        $publishedDate = $article->submitted_at ?? now()->subDays(($index + 1) * 10);
        $volume = (int) (optional(optional($article->edition)->volume)->number ?: 1);
        $issue = (int) (optional($article->edition)->issue ?: 1);
        $type = Str::headline(str_replace('_', ' ', $article->article_type ?? 'Research Article'));
        $keywords = is_array($article->keywords) && $article->keywords !== []
            ? array_values($article->keywords)
            : ['research', 'open access'];
        $subject = $keywords[0] ?? $type;
        $downloads = 900 + ($index * 420);
        $citations = 5 + ($index * 3);
        $altmetric = 40 + ($index * 17);

        return [
            'id' => (string) $article->id,
            'journal' => $journal?->name ?? 'Journal',
            'abbr' => $journal ? PlatformHomePayload::journalAbbreviation($journal) : 'JRNL',
            'discipline' => $journal ? JournalsDirectoryPayload::disciplineFor($journal) : 'Research',
            'type' => $type,
            'subject' => Str::headline($subject),
            'title' => $article->title,
            'authors' => $authorName,
            'year' => (int) $publishedDate->format('Y'),
            'vol' => $volume,
            'iss' => $issue,
            'date' => $publishedDate->toDateString(),
            'citations' => $citations,
            'downloads' => $downloads,
            'altmetric' => $altmetric,
            'oa' => true,
            'keywords' => $keywords,
            'abstract' => $article->abstract ?: 'Abstract not available.',
            'url' => $journal ? journal_front_url($journal, '/articles/'.$article->id) : null,
        ];
    }

    /**
     * @param  list<array<string, mixed>>  $papers
     * @return list<string>
     */
    private static function paperTypes(array $papers): array
    {
        $present = collect($papers)->pluck('type')->unique()->all();
        $ordered = array_values(array_filter(
            self::TYPE_ORDER,
            fn (string $type) => in_array($type, $present, true),
        ));
        $rest = collect($present)
            ->diff($ordered)
            ->sort()
            ->values()
            ->all();

        return [...$ordered, ...$rest];
    }
}
