<?php

namespace App\Support;

use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Support\Str;

class ArticlesDirectoryPayload
{
    /**
     * Masthead/header data for the directory.
     *
     * @return array{name: string, journals: int, articles: string}
     */
    public static function press(): array
    {
        $totalArticles = PublicArticlesFilters::visibleCount();

        return [
            'name' => platform_name(),
            'journals' => Journal::query()->where('is_active', true)->count(),
            'articles' => number_format($totalArticles),
        ];
    }

    /**
     * Map a single article to the fields the directory card renders.
     *
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

        return [
            'id' => (string) $article->id,
            'journal' => $journal?->name ?? 'Journal',
            'type' => $type,
            'title' => $article->title,
            'authors' => $authorName,
            'vol' => $volume,
            'iss' => $issue,
            'date' => $publishedDate->toDateString(),
            'oa' => true,
            'abstract' => $article->abstract ?: 'Abstract not available.',
            'url' => $journal ? journal_front_url($journal, '/articles/'.$article->id) : null,
        ];
    }
}
