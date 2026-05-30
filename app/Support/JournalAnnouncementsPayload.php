<?php

namespace App\Support;

use App\Models\Announcement;
use App\Models\Journal;
use Illuminate\Support\Str;

final class JournalAnnouncementsPayload
{
    /** @var list<string> */
    public const LIST_COLUMNS = [
        'id',
        'journal_id',
        'scope',
        'category',
        'type',
        'status',
        'title',
        'body',
        'url',
        'expires_at',
        'created_at',
        'updated_at',
    ];

    /**
     * @return list<array<string, mixed>>
     */
    public static function forJournal(Journal $journal): array
    {
        return Announcement::query()
            ->select(self::LIST_COLUMNS)
            ->visibleForJournal($journal)
            ->orderByDesc('created_at')
            ->get()
            ->map(fn (Announcement $row) => self::toPublicItem($row))
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    public static function toPublicItem(Announcement $announcement): array
    {
        $body = trim($announcement->body);

        return [
            'id' => $announcement->id,
            'category' => $announcement->category->label(),
            'type' => $announcement->type->value,
            'title' => $announcement->title,
            'excerpt' => Str::limit(strip_tags($body), 220),
            'body' => self::bodyParagraphs($body),
            'url' => $announcement->url,
            'published' => $announcement->created_at?->toDateString(),
            'expiresAt' => $announcement->expires_at?->toIso8601String(),
        ];
    }

    /**
     * @return list<string>
     */
    private static function bodyParagraphs(string $body): array
    {
        $parts = preg_split("/\r\n\r\n|\n\n/", $body) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }
}
