<?php

namespace App\Support;

use App\Enums\ReviewAssignmentStatus;
use App\Models\Journal;
use App\Models\ReviewAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

final class ReviewerInboxIndexFilters
{
    public const PER_PAGE = 25;

    public const SORT_DEADLINE = 'deadline';

    public const SORT_INVITED = 'invited';

    /** Query param value: show every assignment status (incl. completed). */
    public const STATUS_PARAM_ALL = 'all';

    /**
     * @param  Collection<int, Journal>  $reviewerJournals  Journals where this user has reviewer role
     * @return array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }
     */
    public static function fromRequest(Request $request, Collection $reviewerJournals): array
    {
        $allowedSubdomains = $reviewerJournals->pluck('subdomain')->all();
        $statusParam = $request->string('status')->toString();
        $sort = $request->string('sort', self::SORT_DEADLINE)->toString();
        if (! in_array($sort, [self::SORT_DEADLINE, self::SORT_INVITED], true)) {
            $sort = self::SORT_DEADLINE;
        }

        $statusMode = 'active';
        $status = null;

        if ($statusParam === self::STATUS_PARAM_ALL) {
            $statusMode = 'all';
        } elseif ($statusParam !== '') {
            $parsed = ReviewAssignmentStatus::tryFrom($statusParam);
            if ($parsed !== null) {
                $statusMode = 'single';
                $status = $parsed;
            }
        }

        return [
            'q' => trim($request->string('q')->toString()),
            'journal' => JournalSlug::fromRequest($request, $allowedSubdomains),
            'status_mode' => $statusMode,
            'status' => $status,
            'sort' => $sort,
        ];
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     */
    public static function applyToQuery(Builder $query, array $filters, string $reviewerId): Builder
    {
        $query->where('reviewer_id', $reviewerId);

        $journalId = JournalSlug::resolveId($filters['journal']);
        if ($journalId !== null) {
            $query->whereHas('submission', fn (Builder $s) => $s->where('journal_id', $journalId));
        }

        if ($filters['status_mode'] === 'active') {
            $query->whereIn('status', [
                ReviewAssignmentStatus::Invited,
                ReviewAssignmentStatus::Accepted,
            ]);
        } elseif ($filters['status_mode'] === 'single' && $filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->whereHas('submission', fn (Builder $s) => $s->where('title', 'like', $term));
        }

        return $query;
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     */
    public static function applySort(Builder $query, array $filters): Builder
    {
        if ($filters['sort'] === self::SORT_INVITED) {
            return $query->orderByDesc('invited_at');
        }

        return $query->orderBy('deadline')->orderByDesc('invited_at');
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     */
    public static function paginate(array $filters, string $reviewerId): LengthAwarePaginator
    {
        $query = ReviewAssignment::query()->with(['submission.journal', 'review']);

        self::applyToQuery($query, $filters, $reviewerId);
        self::applySort($query, $filters);

        return $query
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     * @param  iterable<Journal>  $reviewerJournals
     * @return array<int, array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(array $filters, iterable $reviewerJournals): array
    {
        $pills = [];

        if ($filters['q'] !== '') {
            $pills[] = [
                'key' => 'q',
                'label' => 'Title: '.$filters['q'],
                'url' => self::indexUrl(self::without($filters, 'q')),
            ];
        }

        if ($filters['journal'] !== null) {
            $journalName = collect($reviewerJournals)->firstWhere('subdomain', $filters['journal'])?->name ?? $filters['journal'];
            $pills[] = [
                'key' => 'journal',
                'label' => 'Journal: '.$journalName,
                'url' => self::indexUrl(self::without($filters, 'journal')),
            ];
        }

        if ($filters['status_mode'] === 'all') {
            $pills[] = [
                'key' => 'status',
                'label' => 'Status: All',
                'url' => self::indexUrl(self::without($filters, 'status')),
            ];
        }

        if ($filters['status_mode'] === 'single' && $filters['status'] !== null) {
            $pills[] = [
                'key' => 'status',
                'label' => 'Status: '.str_replace('_', ' ', $filters['status']->value),
                'url' => self::indexUrl(self::without($filters, 'status')),
            ];
        }

        if ($filters['sort'] !== self::SORT_DEADLINE) {
            $pills[] = [
                'key' => 'sort',
                'label' => $filters['sort'] === self::SORT_INVITED
                    ? 'Sort: Recently invited'
                    : 'Sort: Deadline',
                'url' => self::indexUrl(self::without($filters, 'sort')),
            ];
        }

        return $pills;
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== ''
            || $filters['journal'] !== null
            || $filters['status_mode'] !== 'active'
            || $filters['sort'] !== self::SORT_DEADLINE;
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
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
        if ($filters['status_mode'] === 'all') {
            $params['status'] = self::STATUS_PARAM_ALL;
        }
        if ($filters['status_mode'] === 'single' && $filters['status'] !== null) {
            $params['status'] = $filters['status']->value;
        }
        if ($filters['sort'] !== self::SORT_DEADLINE) {
            $params['sort'] = $filters['sort'];
        }
        if ($page !== null && $page > 1) {
            $params['page'] = $page;
        }

        return $params;
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     * @return array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }
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
            $next['status_mode'] = 'active';
            $next['status'] = null;
        }
        if ($key === 'sort') {
            $next['sort'] = self::SORT_DEADLINE;
        }

        return $next;
    }

    /**
     * @param  array{
     *     q: string,
     *     journal: string|null,
     *     status_mode: 'active'|'all'|'single',
     *     status: ReviewAssignmentStatus|null,
     *     sort: string
     * }  $filters
     */
    private static function indexUrl(array $filters): string
    {
        return platform_route('reviewer.inbox', self::queryParams($filters));
    }
}
