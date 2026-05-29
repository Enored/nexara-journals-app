<?php

namespace App\Support;

use App\Models\Submission;
use Illuminate\Support\Str;

class ArticlePayload
{
    public static function fromSubmission(Submission $article, int $index = 0): array
    {
        $authorName = optional($article->author)->name ?: 'Unknown Author';
        $initials = collect(explode(' ', $authorName))
            ->filter()
            ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
            ->take(2)
            ->join('');

        $publishedDate = $article->submitted_at ?: now()->subDays(($index + 1) * 10);
        $receivedDate = (clone $publishedDate)->subDays(35 + $index);
        $volume = optional(optional($article->edition)->volume)->number ?: 18;
        $issue = optional($article->edition)->issue ?: 2;

        return [
            'id' => (string) $article->id,
            'type' => Str::headline(str_replace('_', ' ', $article->article_type ?? 'Research Article')),
            'oa' => true,
            'subject' => 'Computational Cognition',
            'title' => $article->title,
            'authors' => [
                ['name' => $authorName, 'corresp' => true, 'aff' => 1],
            ],
            'affiliations' => ['Nexara Journal Network'],
            'abstract' => $article->abstract ?: 'This article preview is shown using placeholder text because the complete abstract is not currently available in the system.',
            'doi' => '10.31472/nexara.'.str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT),
            'pages' => (10 + ($index * 6)).'-'.(15 + ($index * 6)),
            'volume' => (int) $volume,
            'issue' => (int) $issue,
            'year' => (int) $publishedDate->format('Y'),
            'publishedOn' => $publishedDate->format('M j, Y'),
            'receivedOn' => $receivedDate->format('M j, Y'),
            'citations' => 5 + ($index * 3),
            'downloads' => number_format(900 + ($index * 420)),
            'altmetric' => 40 + ($index * 17),
            'altmetricBreakdown' => ['news' => 2, 'twitter' => 18, 'blogs' => 3, 'policy' => 0],
            'keywords' => $article->keywords ?: ['journal', 'research', 'open access'],
            'initials' => $initials ?: 'NA',
        ];
    }
}
