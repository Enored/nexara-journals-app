<?php

namespace App\Support;

use App\Models\Journal;
use App\Models\Volume;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

final class VolumeIndexFilters
{
    public const PER_PAGE = 15;

    /**
     * @return array{q: string}
     */
    public static function fromRequest(Request $request): array
    {
        return [
            'q' => trim($request->string('vq')->toString()),
        ];
    }

    /**
     * @param  array{q: string}  $filters
     */
    public static function paginate(Journal $journal, array $filters): LengthAwarePaginator
    {
        $query = Volume::query()
            ->where('journal_id', $journal->id)
            ->withCount('editions');

        self::applySearch($query, $filters);

        return $query
            ->orderByDesc('number')
            ->paginate(self::PER_PAGE, ['*'], 'vpage')
            ->withQueryString();
    }

    /**
     * @param  array{q: string}  $filters
     */
    public static function hasActiveFilters(array $filters): bool
    {
        return $filters['q'] !== '';
    }

    /**
     * @param  array{q: string}  $filters
     * @return list<array{key: string, label: string, url: string}>
     */
    public static function activeFilterPills(Journal $journal, array $filters): array
    {
        $pills = [];

        if ($filters['q'] !== '') {
            $otherParams = collect(request()->query())->except(['vq', 'vpage'])->all();
            $pills[] = [
                'key' => 'vq',
                'label' => 'Search: '.$filters['q'],
                'url' => platform_route('journal.editions.index', array_merge([$journal], $otherParams)),
            ];
        }

        return $pills;
    }

    /**
     * @param  Builder<Volume>  $query
     * @param  array{q: string}  $filters
     */
    private static function applySearch(Builder $query, array $filters): void
    {
        if ($filters['q'] === '') {
            return;
        }

        $term = $filters['q'];
        $like = '%'.mb_strtolower($term).'%';

        $query->where(function (Builder $q) use ($term, $like) {
            $q->whereRaw('LOWER(title) LIKE ?', [$like]);

            if (ctype_digit($term)) {
                $q->orWhere('number', (int) $term);
            }
        });
    }
}
