<?php

namespace App\Support;

use App\Enums\AnnouncementCategory;
use App\Enums\AnnouncementScope;
use App\Enums\AnnouncementStatus;
use App\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class AdminAnnouncementIndexFilters
{
    public const PER_PAGE = 20;

    /**
     * @return array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}
     */
    public static function fromRequest(Request $request): array
    {
        $status = $request->string('status')->toString();
        $scope = $request->string('scope')->toString();
        $category = $request->string('category')->toString();
        $journalId = $request->string('journal_id')->toString();

        return [
            'q' => trim($request->string('q')->toString()),
            'status' => $status !== '' ? AnnouncementStatus::tryFrom($status) : null,
            'scope' => $scope !== '' ? AnnouncementScope::tryFrom($scope) : null,
            'category' => $category !== '' ? AnnouncementCategory::tryFrom($category) : null,
            'journal_id' => $journalId !== '' ? $journalId : null,
        ];
    }

    /**
     * @param  Builder<Announcement>  $query
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     * @return Builder<Announcement>
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['status'] !== null) {
            $query->where('status', $filters['status']);
        }

        if ($filters['scope'] !== null) {
            $query->where('scope', $filters['scope']);
        }

        if ($filters['category'] !== null) {
            $query->where('category', $filters['category']);
        }

        if ($filters['journal_id'] !== null) {
            $query->where('journal_id', $filters['journal_id']);
        }

        if ($filters['q'] !== '') {
            $like = '%'.mb_strtolower($filters['q']).'%';
            $query->whereRaw('LOWER(title) LIKE ?', [$like]);
        }

        return $query;
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = Announcement::query()->with('journal:id,name');
        self::applyToQuery($query, $filters);

        return $query
            ->latest('updated_at')
            ->paginate(self::PER_PAGE)
            ->appends(self::queryParams($filters));
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
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
                'label' => 'Status: '.$filters['status']->label(),
                'url' => self::indexUrl(self::without($filters, 'status')),
            ];
        }

        if ($filters['scope'] !== null) {
            $pills[] = [
                'key' => 'scope',
                'label' => 'Scope: '.$filters['scope']->label(),
                'url' => self::indexUrl(self::without($filters, 'scope')),
            ];
        }

        if ($filters['category'] !== null) {
            $pills[] = [
                'key' => 'category',
                'label' => 'Category: '.$filters['category']->label(),
                'url' => self::indexUrl(self::without($filters, 'category')),
            ];
        }

        if ($filters['journal_id'] !== null) {
            $pills[] = [
                'key' => 'journal_id',
                'label' => 'Journal filter active',
                'url' => self::indexUrl(self::without($filters, 'journal_id')),
            ];
        }

        return $pills;
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== ''
            || $filters['status'] !== null
            || $filters['scope'] !== null
            || $filters['category'] !== null
            || $filters['journal_id'] !== null;
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     * @return array<string, string>
     */
    public static function queryParams(array $filters): array
    {
        $params = [];

        if ($filters['q'] !== '') {
            $params['q'] = $filters['q'];
        }
        if ($filters['status'] !== null) {
            $params['status'] = $filters['status']->value;
        }
        if ($filters['scope'] !== null) {
            $params['scope'] = $filters['scope']->value;
        }
        if ($filters['category'] !== null) {
            $params['category'] = $filters['category']->value;
        }
        if ($filters['journal_id'] !== null) {
            $params['journal_id'] = $filters['journal_id'];
        }

        return $params;
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     * @return array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}
     */
    private static function without(array $filters, string $key): array
    {
        $next = $filters;

        return match ($key) {
            'q' => [...$next, 'q' => ''],
            'status' => [...$next, 'status' => null],
            'scope' => [...$next, 'scope' => null],
            'category' => [...$next, 'category' => null],
            'journal_id' => [...$next, 'journal_id' => null],
            default => $next,
        };
    }

    /**
     * @param  array{q: string, status: AnnouncementStatus|null, scope: AnnouncementScope|null, category: AnnouncementCategory|null, journal_id: string|null}  $filters
     */
    private static function indexUrl(array $filters): string
    {
        return platform_route('admin.announcements.index', self::queryParams($filters));
    }
}
