<?php

namespace App\Support;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class PublicJournalsFilters
{
    public const PER_PAGE = 10;

    /** @var list<string> */
    public const SORTS = ['az', 'za', 'newest', 'oldest'];

    /**
     * @return array{q: string, sort: string}
     */
    public static function fromRequest(Request $request): array
    {
        $sort = $request->string('sort')->toString();
        if (! in_array($sort, self::SORTS, true)) {
            $sort = 'az';
        }

        return [
            'q' => trim($request->string('q')->toString()),
            'sort' => $sort,
        ];
    }

    /**
     * @param  Builder<Journal>  $query
     * @param  array{q: string, sort: string}  $filters
     * @return Builder<Journal>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['q'] !== '') {
            $like = '%'.mb_strtolower($filters['q']).'%';
            $query->where(function (Builder $inner) use ($like) {
                $inner
                    ->whereRaw('LOWER(name) LIKE ?', [$like])
                    ->orWhereRaw("LOWER(COALESCE(excerpt, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(description, '')) LIKE ?", [$like])
                    ->orWhereRaw("LOWER(COALESCE(abbreviation, '')) LIKE ?", [$like]);
            });
        }

        return $query;
    }

    /**
     * @param  Builder<Journal>  $query
     * @return Builder<Journal>
     */
    public static function applySort(Builder $query, string $sort): Builder
    {
        return match ($sort) {
            'za' => $query->orderBy('name', 'desc'),
            'newest' => $query->orderByDesc('created_at')->orderBy('name'),
            'oldest' => $query->orderBy('created_at')->orderBy('name'),
            default => $query->orderBy('name', 'asc'),
        };
    }

    /**
     * @param  array{q: string, sort: string}  $filters
     * @return LengthAwarePaginator<int, Journal>
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = Journal::query()
            ->where('is_active', true)
            ->select(JournalsDirectoryPayload::LIST_COLUMNS)
            ->withCount(['submissions as published_articles_count' => fn (Builder $q) => $q->where('status', SubmissionStatus::Published)]);

        self::applyToQuery($query, $filters);
        self::applySort($query, $filters['sort']);

        return $query
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }
}
