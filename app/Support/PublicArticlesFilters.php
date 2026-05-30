<?php

namespace App\Support;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class PublicArticlesFilters
{
    public const PER_PAGE = 10;

    /** @var list<string> */
    public const SORTS = ['newest', 'oldest'];

    /**
     * @return array{q: string, types: list<string>, years: list<int>, sort: string}
     */
    public static function fromRequest(Request $request): array
    {
        $sort = $request->string('sort')->toString();
        if (! in_array($sort, self::SORTS, true)) {
            $sort = 'newest';
        }

        $types = collect((array) $request->input('types', []))
            ->filter(fn ($type) => is_string($type) && $type !== '')
            ->map(fn (string $type) => $type)
            ->unique()
            ->values()
            ->all();

        $years = collect((array) $request->input('years', []))
            ->map(fn ($year) => (int) $year)
            ->filter(fn (int $year) => $year > 0)
            ->unique()
            ->values()
            ->all();

        return [
            'q' => trim($request->string('q')->toString()),
            'types' => $types,
            'years' => $years,
            'sort' => $sort,
        ];
    }

    /**
     * Published articles that belong to an active journal and are either
     * unassigned to an edition or sit in a published edition.
     *
     * @return Builder<Submission>
     */
    public static function baseQuery(): Builder
    {
        return Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->whereHas('journal', fn (Builder $q) => $q->where('is_active', true))
            ->where(function (Builder $q) {
                $q->whereNull('edition_id')
                    ->orWhereHas('edition', fn (Builder $e) => $e->where('status', EditionStatus::Published));
            });
    }

    /**
     * @param  Builder<Submission>  $query
     * @param  array{q: string, types: list<string>, years: list<int>, sort: string}  $filters
     * @return Builder<Submission>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['types'] !== []) {
            $query->whereIn('article_type', $filters['types']);
        }

        if ($filters['years'] !== []) {
            $query->whereIn(DB::raw(self::yearExpression()), $filters['years']);
        }

        if ($filters['q'] !== '') {
            $like = '%'.mb_strtolower($filters['q']).'%';
            $query->where(function (Builder $inner) use ($like) {
                $inner
                    ->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(abstract) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(keywords::text) LIKE ?', [$like])
                    ->orWhereHas('journal', fn (Builder $j) => $j->whereRaw('LOWER(name) LIKE ?', [$like]))
                    ->orWhereHas('author', fn (Builder $a) => $a->whereRaw('LOWER(name) LIKE ?', [$like]));
            });
        }

        return $query;
    }

    /**
     * @param  array{q: string, types: list<string>, years: list<int>, sort: string}  $filters
     * @return LengthAwarePaginator<int, Submission>
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = self::baseQuery()->with(['journal', 'author', 'edition.volume']);

        self::applyToQuery($query, $filters);

        return $query
            ->orderBy('submitted_at', $filters['sort'] === 'oldest' ? 'asc' : 'desc')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * Total number of publicly visible articles. Single indexed COUNT.
     */
    public static function visibleCount(): int
    {
        return self::baseQuery()->count();
    }

    /**
     * Facet options with counts over the full visible set. Two grouped queries,
     * computed lazily so they only run on full page visits (not paginated reloads).
     *
     * @return array{types: list<array{value: string, label: string, count: int}>, years: list<array{value: int, count: int}>}
     */
    public static function facets(): array
    {
        $typeCounts = self::baseQuery()
            ->whereNotNull('article_type')
            ->groupBy('article_type')
            ->select('article_type', DB::raw('count(*) as aggregate'))
            ->pluck('aggregate', 'article_type');

        $order = collect(config('journal.article_types', []));
        $types = $typeCounts->keys()
            ->sortBy(fn (string $type) => $order->search($type) === false ? PHP_INT_MAX : $order->search($type))
            ->map(fn (string $type) => [
                'value' => $type,
                'label' => Str::headline(str_replace('_', ' ', $type)),
                'count' => (int) $typeCounts[$type],
            ])
            ->values()
            ->all();

        $yearCounts = self::baseQuery()
            ->whereNotNull('submitted_at')
            ->selectRaw(self::yearExpression().' as yr, count(*) as aggregate')
            ->groupBy(DB::raw(self::yearExpression()))
            ->orderByDesc(DB::raw(self::yearExpression()))
            ->pluck('aggregate', 'yr');

        $years = collect($yearCounts)
            ->map(fn ($count, $year) => ['value' => (int) $year, 'count' => (int) $count])
            ->values()
            ->all();

        return [
            'types' => $types,
            'years' => $years,
        ];
    }

    /**
     * Driver-aware SQL expression that extracts the four-digit year from
     * `submitted_at`, used for both year filtering and the year facet.
     */
    private static function yearExpression(): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', submitted_at) AS INTEGER)",
            'mysql', 'mariadb' => 'YEAR(submitted_at)',
            default => 'EXTRACT(YEAR FROM submitted_at)::int',
        };
    }
}
