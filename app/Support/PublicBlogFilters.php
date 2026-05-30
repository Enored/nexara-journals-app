<?php

namespace App\Support;

use App\Models\Blog;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class PublicBlogFilters
{
    public const PER_PAGE = 9;

    public const RELATED_LIMIT = 3;

    /**
     * @return array{q: string, category: string}
     */
    public static function fromRequest(Request $request): array
    {
        $category = $request->string('category')->toString();

        if (! in_array($category, BlogPayload::CATEGORIES, true)) {
            $category = 'All';
        }

        return [
            'q' => trim($request->string('q')->toString()),
            'category' => $category,
        ];
    }

    /**
     * @param  Builder<Blog>  $query
     * @param  array{q: string, category: string}  $filters
     * @return Builder<Blog>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['category'] !== 'All') {
            $query->where('category', $filters['category']);
        }

        if ($filters['q'] !== '') {
            $like = '%'.mb_strtolower($filters['q']).'%';
            $query->where(function (Builder $inner) use ($like) {
                $inner
                    ->whereRaw('LOWER(title) LIKE ?', [$like])
                    ->orWhereRaw('LOWER(excerpt) LIKE ?', [$like])
                    ->orWhereHas('author', function (Builder $author) use ($like) {
                        $author->whereRaw('LOWER(name) LIKE ?', [$like]);
                    });
            });
        }

        return $query;
    }

    /**
     * @param  array{q: string, category: string}  $filters
     * @return LengthAwarePaginator<int, Blog>
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = Blog::query()
            ->where('is_published', true)
            ->with(['author' => fn ($q) => $q->select(BlogPayload::AUTHOR_COLUMNS)])
            ->select(BlogPayload::LIST_COLUMNS);

        self::applyToQuery($query, $filters);

        return $query
            ->orderByDesc('published_at')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * Bounded "More from Nexara Notes": same category first, then most recent,
     * excluding the current post. Single query, list columns only.
     *
     * @param  array<string, mixed>  $current
     * @return list<array<string, mixed>>
     */
    public static function related(array $current, int $limit = self::RELATED_LIMIT): array
    {
        $category = $current['category'] ?? null;

        return Blog::query()
            ->where('is_published', true)
            ->where('id', '!=', $current['id'])
            ->with(['author' => fn ($q) => $q->select(BlogPayload::AUTHOR_COLUMNS)])
            ->select(BlogPayload::LIST_COLUMNS)
            ->when($category, fn (Builder $q) => $q->orderByRaw(
                'CASE WHEN category = ? THEN 0 ELSE 1 END',
                [$category],
            ))
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get()
            ->map(fn (Blog $blog) => BlogPayload::toListItem($blog))
            ->all();
    }
}
