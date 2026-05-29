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
     * @return array<string, mixed>
     */
    public static function build(Collection $journals): array
    {
        $journalCount = $journals->count();
        $articleCount = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->count();
        $authorCount = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->whereNotNull('author_id')
            ->distinct()
            ->count('author_id');

        return [
            'pageTitle' => 'Journals — '.platform_name(),
            'press' => [
                'name' => platform_name(),
                'journals' => $journalCount,
                'articles' => $articleCount > 0 ? number_format($articleCount) : '0',
                'authors' => $authorCount > 0 ? number_format($authorCount) : '0',
                'downloads12mo' => '11.6M',
            ],
            'journals' => self::mapJournals($journals),
        ];
    }

    /**
     * @return list<array<string, mixed>>
     */
    public static function mapJournals(Collection $journals): array
    {
        return $journals->values()->map(
            fn (Journal $journal, int $index) => self::mapJournal($journal, $index),
        )->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function mapJournal(Journal $journal, int $index): array
    {
        $publishedCount = (int) ($journal->published_articles_count ?? $journal->submissions()
            ->where('status', SubmissionStatus::Published)
            ->count());
        $description = trim(strip_tags((string) $journal->description));
        $field = Str::limit($description ?: 'Open-access research', 80);

        return [
            'id' => $journal->id,
            'abbr' => PlatformHomePayload::journalAbbreviation($journal),
            'name' => $journal->name,
            'field' => $field,
            'discipline' => self::inferDiscipline($journal),
            'blurb' => Str::limit($description ?: $field, 120),
            'est' => (int) ($journal->created_at?->format('Y') ?? 2010),
            'impact' => '—',
            'articles' => $publishedCount,
            'url' => journal_front_url($journal),
            'flagship' => $index === 0,
        ];
    }

    public static function disciplineFor(Journal $journal): string
    {
        return self::inferDiscipline($journal);
    }

    private static function inferDiscipline(Journal $journal): string
    {
        $text = strtolower(($journal->name ?? '').' '.($journal->description ?? ''));
        $map = [
            'neuro' => 'Neuroscience',
            'cognit' => 'Cognitive science',
            'comput' => 'Computational',
            'clinical' => 'Clinical',
            'psychiatr' => 'Clinical',
            'behaviour' => 'Behavioural',
            'behavior' => 'Behavioural',
            'language' => 'Language',
            'memory' => 'Cognitive science',
            'method' => 'Methods',
            'review' => 'Reviews',
            'perception' => 'Perception',
            'development' => 'Developmental',
        ];

        foreach ($map as $needle => $label) {
            if (str_contains($text, $needle)) {
                return $label;
            }
        }

        return 'Research';
    }
}
