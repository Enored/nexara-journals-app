<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class JournalsDirectoryPayload
{
    /**
     * Columns needed to render directory cards/rows. Kept lean — heavy fields
     * like submission_guidelines are excluded.
     *
     * @var list<string>
     */
    public const LIST_COLUMNS = [
        'id',
        'name',
        'abbreviation',
        'subdomain',
        'excerpt',
        'description',
        'cover_image_url',
        'created_at',
    ];

    /**
     * Masthead stats. A handful of cheap aggregate queries, resolved lazily so
     * they run on full visits only (skipped on load-more partial reloads).
     *
     * @return array<string, mixed>
     */
    public static function press(): array
    {
        $journalCount = Journal::query()->where('is_active', true)->count();
        $articleCount = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->count();
        $authorCount = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->whereNotNull('author_id')
            ->distinct()
            ->count('author_id');

        return [
            'name' => platform_name(),
            'journals' => $journalCount,
            'articles' => $articleCount > 0 ? number_format($articleCount) : '0',
            'authors' => $authorCount > 0 ? number_format($authorCount) : '0',
            'downloads12mo' => '11.6M',
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function mapJournals(Collection $journals): array
    {
        return $journals->values()->map(
            fn (Journal $journal) => self::mapJournal($journal),
        )->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function mapJournal(Journal $journal): array
    {
        $publishedCount = (int) ($journal->published_articles_count ?? $journal->submissions()
            ->where('status', SubmissionStatus::Published)
            ->count());
        $excerpt = trim(strip_tags((string) $journal->excerpt));
        $description = trim(strip_tags((string) $journal->description));
        $summary = $excerpt ?: $description;
        $field = Str::limit($summary ?: 'Open-access research', 80);

        return [
            'id' => $journal->id,
            'abbr' => $journal->abbreviation ?: PlatformHomePayload::journalAbbreviation($journal),
            'name' => $journal->name,
            'field' => $field,
            'blurb' => Str::limit($summary ?: $field, 120),
            'est' => (int) ($journal->created_at?->format('Y') ?? 2010),
            'articles' => $publishedCount,
            'url' => journal_front_url($journal),
            'cover' => $journal->cover_image_url ?: null,
        ];
    }
}
