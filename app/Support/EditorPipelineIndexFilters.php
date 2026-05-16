<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class EditorPipelineIndexFilters
{
    public const PER_PAGE = 25;

    /**
     * @param  Collection<int, Journal>  $editorJournals
     * @return array{q: string, journal: string|null, status: SubmissionStatus|null}
     */
    public static function fromRequest(Request $request, Collection $editorJournals): array
    {
        $allowedSubdomains = $editorJournals->pluck('subdomain')->all();
        $statusValue = $request->string('status')->toString();

        return [
            'q' => trim($request->string('q')->toString()),
            'journal' => JournalSlug::fromRequest($request, $allowedSubdomains),
            'status' => $statusValue !== '' ? SubmissionStatus::tryFrom($statusValue) : null,
        ];
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     * @param  Collection<int, string>  $allowedJournalIds
     */
    public static function applyToQuery(Builder $query, array $filters, Collection $allowedJournalIds): Builder
    {
        $query->whereIn('journal_id', $allowedJournalIds);

        $journalId = JournalSlug::resolveId($filters['journal']);
        if ($journalId !== null) {
            $query->where('journal_id', $journalId);
        }

        if ($filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function (Builder $query) use ($term) {
                $query->where('title', 'like', $term)
                    ->orWhere('abstract', 'like', $term);
            });
        }

        return $query;
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     * @param  Collection<int, string>  $allowedJournalIds
     */
    public static function paginate(array $filters, Collection $allowedJournalIds): LengthAwarePaginator
    {
        $query = Submission::query()->with(['journal', 'author']);
        self::applyToQuery($query, $filters, $allowedJournalIds);

        return $query
            ->orderByDesc('submitted_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     * @param  iterable<Journal>  $editorJournals
     * @return array<int, array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(array $filters, iterable $editorJournals): array
    {
        $pills = [];

        if ($filters['q'] !== '') {
            $pills[] = [
                'key' => 'q',
                'label' => 'Search: '.$filters['q'],
                'url' => self::indexUrl(self::without($filters, 'q')),
            ];
        }

        if ($filters['journal'] !== null) {
            $journalName = collect($editorJournals)->firstWhere('subdomain', $filters['journal'])?->name ?? $filters['journal'];
            $pills[] = [
                'key' => 'journal',
                'label' => 'Journal: '.$journalName,
                'url' => self::indexUrl(self::without($filters, 'journal')),
            ];
        }

        if ($filters['status'] !== null) {
            $pills[] = [
                'key' => 'status',
                'label' => 'Status: '.str_replace('_', ' ', $filters['status']->value),
                'url' => self::indexUrl(self::without($filters, 'status')),
            ];
        }

        return $pills;
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== ''
            || $filters['journal'] !== null
            || $filters['status'] !== null;
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     * @return array<string, string|int>
     */
    public static function queryParams(array $filters, ?int $page = null): array
    {
        $params = [];

        if ($filters['q'] !== '') {
            $params['q'] = $filters['q'];
        }
        if ($filters['journal'] !== null) {
            $params[JournalSlug::QUERY_KEY] = $filters['journal'];
        }
        if ($filters['status'] !== null) {
            $params['status'] = $filters['status']->value;
        }
        if ($page !== null && $page > 1) {
            $params['page'] = $page;
        }

        return $params;
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     * @return array{q: string, journal: string|null, status: SubmissionStatus|null}
     */
    private static function without(array $filters, string $key): array
    {
        $next = $filters;
        if ($key === 'q') {
            $next['q'] = '';
        }
        if ($key === 'journal') {
            $next['journal'] = null;
        }
        if ($key === 'status') {
            $next['status'] = null;
        }

        return $next;
    }

    /**
     * @param  array{q: string, journal: string|null, status: SubmissionStatus|null}  $filters
     */
    private static function indexUrl(array $filters): string
    {
        return platform_route('editor.pipeline', self::queryParams($filters));
    }
}
