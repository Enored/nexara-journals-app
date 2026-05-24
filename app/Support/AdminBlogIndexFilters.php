<?php

namespace App\Support;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class AdminBlogIndexFilters
{
    public const PER_PAGE = 20;

    public const STATUS_PUBLISHED = 'published';

    public const STATUS_DRAFT = 'draft';

    /**
     * @return array{q: string, status: string|null}
     */
    public static function fromRequest(Request $request): array
    {
        $status = $request->string('status')->toString();

        if (! in_array($status, [self::STATUS_PUBLISHED, self::STATUS_DRAFT], true)) {
            $status = null;
        }

        return [
            'q' => trim($request->string('q')->toString()),
            'status' => $status,
        ];
    }

    /**
     * @param  Builder<Blog>  $query
     * @param  array{q: string, status: string|null}  $filters
     * @return Builder<Blog>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['status'] === self::STATUS_PUBLISHED) {
            $query->where('is_published', true);
        } elseif ($filters['status'] === self::STATUS_DRAFT) {
            $query->where('is_published', false);
        }

        if ($filters['q'] !== '') {
            $like = '%'.mb_strtolower($filters['q']).'%';
            $query->whereRaw('LOWER(title) LIKE ?', [$like]);
        }

        return $query;
    }

    /**
     * @param  array{q: string, status: string|null}  $filters
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = Blog::query()->with('author');
        self::applyToQuery($query, $filters);

        return $query
            ->latest('updated_at')
            ->paginate(self::PER_PAGE)
            ->appends(self::queryParams($filters));
    }

    /**
     * @param  array{q: string, status: string|null}  $filters
     * @return array<int, array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(array $filters): array
    {
        $pills = [];

        if ($filters['q'] !== '') {
            $pills[] = [
                'key' => 'q',
                'label' => 'Search: '.$filters['q'],
                'url' => self::indexUrl(self::without($filters, 'q')),
            ];
        }

        if ($filters['status'] !== null) {
            $pills[] = [
                'key' => 'status',
                'label' => 'Status: '.ucfirst($filters['status']),
                'url' => self::indexUrl(self::without($filters, 'status')),
            ];
        }

        return $pills;
    }

    /**
     * @param  array{q: string, status: string|null}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== '' || $filters['status'] !== null;
    }

    /**
     * @param  array{q: string, status: string|null}  $filters
     * @return array<string, string|int>
     */
    public static function queryParams(array $filters, ?int $page = null): array
    {
        $params = [];

        if ($filters['q'] !== '') {
            $params['q'] = $filters['q'];
        }
        if ($filters['status'] !== null) {
            $params['status'] = $filters['status'];
        }
        if ($page !== null && $page > 1) {
            $params['page'] = $page;
        }

        return $params;
    }

    /**
     * @return array<string, string|int>
     */
    public static function queryParamsFromRequest(Request $request): array
    {
        return self::queryParams(self::fromRequest($request), $request->integer('page') ?: null);
    }

    /**
     * @param  array{q: string, status: string|null}  $filters
     * @return array{q: string, status: string|null}
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
     * @param  array{q: string, status: string|null}  $filters
     */
    private static function indexUrl(array $filters): string
    {
        return platform_route('admin.blogs.index', self::queryParams($filters));
    }
}
