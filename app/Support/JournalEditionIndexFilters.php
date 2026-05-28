<?php

namespace App\Support;

use App\Enums\EditionStatus;
use App\Models\Edition;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class JournalEditionIndexFilters
{
    public const PER_PAGE = 20;

    /**
     * @return array{q: string, status: EditionStatus|null}
     */
    public static function fromRequest(Request $request): array
    {
        $statusValue = $request->string('status')->toString();
        $status = $statusValue !== '' ? EditionStatus::tryFrom($statusValue) : null;

        return [
            'q' => trim($request->string('q')->toString()),
            'status' => $status,
        ];
    }

    /**
     * @param  Builder<Edition>  $query
     * @param  array{q: string, status: EditionStatus|null}  $filters
     * @return Builder<Edition>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['q'] !== '') {
            $term = $filters['q'];
            $like = '%'.mb_strtolower($term).'%';

            $query->where(function (Builder $query) use ($term, $like) {
                $query->whereRaw('LOWER(editions.title) LIKE ?', [$like]);

                if (ctype_digit($term)) {
                    $number = (int) $term;
                    $query->orWhere('editions.issue', $number)
                        ->orWhereHas('volume', fn (Builder $volumeQuery) => $volumeQuery->where('number', $number));
                }
            });
        }

        return $query;
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     */
    public static function paginate(Journal $journal, array $filters): LengthAwarePaginator
    {
        $query = Edition::query()
            ->where('editions.journal_id', $journal->id)
            ->with('volume')
            ->withCount([
                'submissions as slotted_count' => fn ($q) => $q->where('status', \App\Enums\SubmissionStatus::Accepted),
                'submissions as live_count' => fn ($q) => $q->where('status', \App\Enums\SubmissionStatus::Published),
            ]);

        self::applyToQuery($query, $filters);

        return $query
            ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
            ->select('editions.*')
            ->orderByDesc('volumes.number')
            ->orderByDesc('editions.issue')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     * @return list<array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(Journal $journal, array $filters): array
    {
        $pills = [];

        if ($filters['q'] !== '') {
            $pills[] = [
                'key' => 'q',
                'label' => 'Search: '.$filters['q'],
                'url' => self::indexUrl($journal, self::without($filters, 'q')),
            ];
        }

        if ($filters['status'] !== null) {
            $pills[] = [
                'key' => 'status',
                'label' => 'Status: '.ucfirst($filters['status']->value),
                'url' => self::indexUrl($journal, self::without($filters, 'status')),
            ];
        }

        return $pills;
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== '' || $filters['status'] !== null;
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     * @return array<string, string>
     */
    public static function queryParams(array $filters, ?int $page = null): array
    {
        $params = [];

        if ($filters['q'] !== '') {
            $params['q'] = $filters['q'];
        }
        if ($filters['status'] !== null) {
            $params['status'] = $filters['status']->value;
        }
        if ($page !== null && $page > 1) {
            $params['page'] = (string) $page;
        }

        return $params;
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     * @return array{q: string, status: EditionStatus|null}
     */
    private static function without(array $filters, string $key): array
    {
        $next = $filters;

        if ($key === 'q') {
            $next['q'] = '';
        }
        if ($key === 'status') {
            $next['status'] = null;
        }

        return $next;
    }

    /**
     * @param  array{q: string, status: EditionStatus|null}  $filters
     */
    private static function indexUrl(Journal $journal, array $filters): string
    {
        $volumeParams = collect(request()->query())->only(['vq', 'vpage'])->all();

        return platform_route('journal.editions.index', array_merge([$journal], self::queryParams($filters), $volumeParams));
    }
}
