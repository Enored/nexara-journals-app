<?php

namespace App\Support;

use App\Enums\JournalRole;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class AdminUserIndexFilters
{
    public const PER_PAGE = 25;

    /**
     * @return array{q: string, journal: string|null, role: JournalRole|null}
     */
    public static function fromRequest(Request $request): array
    {
        $roleValue = $request->string('role')->toString();

        $role = $roleValue !== '' ? JournalRole::tryFrom($roleValue) : null;
        if ($role !== null && ! $role->isAssignable()) {
            $role = null;
        }

        return [
            'q' => trim($request->string('q')->toString()),
            'journal' => JournalSlug::fromRequest($request),
            'role' => $role,
        ];
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
     */
    public static function applyToQuery(Builder $query, array $filters): Builder
    {
        if ($filters['q'] !== '') {
            $term = '%'.$filters['q'].'%';
            $query->where(function (Builder $query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        $journalId = JournalSlug::resolveId($filters['journal']);

        if ($journalId !== null || $filters['role'] !== null) {
            $query->whereHas('journalUserRoles', function (Builder $query) use ($filters, $journalId) {
                if ($journalId !== null) {
                    $query->where('journal_id', $journalId);
                }
                if ($filters['role'] !== null) {
                    $query->where('role', $filters['role']);
                }
            });
        }

        return $query;
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
     */
    public static function paginate(array $filters): LengthAwarePaginator
    {
        $query = User::query()->with(['staffJournalRoles' => fn ($q) => $q->with('journal')]);
        self::applyToQuery($query, $filters);

        return $query
            ->orderBy('name')
            ->paginate(self::PER_PAGE)
            ->withQueryString();
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
     * @param  iterable<Journal>  $journals
     * @return array<int, array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(array $filters, iterable $journals): array
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
            $journalName = collect($journals)->firstWhere('subdomain', $filters['journal'])?->name ?? $filters['journal'];
            $pills[] = [
                'key' => 'journal',
                'label' => 'Journal: '.$journalName,
                'url' => self::indexUrl(self::without($filters, 'journal')),
            ];
        }

        if ($filters['role'] !== null) {
            $pills[] = [
                'key' => 'role',
                'label' => 'Role: '.$filters['role']->label(),
                'url' => self::indexUrl(self::without($filters, 'role')),
            ];
        }

        return $pills;
    }

    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== ''
            || $filters['journal'] !== null
            || $filters['role'] !== null;
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
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
        if ($filters['role'] !== null) {
            $params['role'] = $filters['role']->value;
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
     * @return array<string, string|int>
     */
    public static function returnQueryFromRequest(Request $request): array
    {
        $return = $request->input('return', []);

        if (! is_array($return)) {
            return [];
        }

        $allowed = ['q', JournalSlug::QUERY_KEY, 'journal_id', 'role', 'page'];
        $params = array_intersect_key($return, array_flip($allowed));

        if (isset($params['journal_id']) && ! isset($params[JournalSlug::QUERY_KEY])) {
            $legacy = Journal::query()->find($params['journal_id']);
            if ($legacy) {
                $params[JournalSlug::QUERY_KEY] = $legacy->subdomain;
            }
            unset($params['journal_id']);
        }

        return array_filter($params, fn ($value) => filled($value));
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
     * @return array{q: string, journal: string|null, role: JournalRole|null}
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
        if ($key === 'role') {
            $next['role'] = null;
        }

        return $next;
    }

    /**
     * @param  array{q: string, journal: string|null, role: JournalRole|null}  $filters
     */
    private static function indexUrl(array $filters): string
    {
        return platform_route('admin.users.index', self::queryParams($filters));
    }
}
