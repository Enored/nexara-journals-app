<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PublicJournalsDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function journal(string $name, array $overrides = []): Journal
    {
        return Journal::query()->create([
            'name' => $name,
            'subdomain' => str($name)->slug()->toString(),
            'is_active' => true,
            ...$overrides,
        ]);
    }

    public function test_directory_lists_only_active_journals(): void
    {
        $this->journal('Active One');
        $this->journal('Inactive One', ['is_active' => false]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Platform/Journals')
                ->where('pagination.total', 1)
                ->has('journals', 1)
                ->where('journals.0.name', 'Active One')
                // Card link must carry the journal subdomain, not resolve to a bare apex host.
                ->where('journals.0.url', fn (string $url) => str_contains($url, 'active-one.'))
                ->where('press.journals', 1)
                ->where('filters.sort', 'az')
            );
    }

    public function test_search_narrows_journals(): void
    {
        $this->journal('Journal of Neuroscience');
        $this->journal('Cognitive Review', ['excerpt' => 'Studies in memory and attention.']);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false, parameters: ['q' => 'neuro']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 1)
                ->where('journals.0.name', 'Journal of Neuroscience')
            );

        // Matches on excerpt too.
        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false, parameters: ['q' => 'memory']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 1)
                ->where('journals.0.name', 'Cognitive Review')
            );
    }

    public function test_sort_orders_journals(): void
    {
        $this->journal('Zebra Studies');
        $this->journal('Alpha Quarterly');

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false, parameters: ['sort' => 'za']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('journals.0.name', 'Zebra Studies')
                ->where('filters.sort', 'za')
            );

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false, parameters: ['sort' => 'az']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('journals.0.name', 'Alpha Quarterly')
            );
    }

    public function test_results_are_paginated_ten_per_page(): void
    {
        foreach (range(1, 13) as $i) {
            $this->journal(sprintf('Journal %02d', $i));
        }

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 13)
                ->where('pagination.lastPage', 2)
                ->where('pagination.hasMore', true)
                ->has('journals', 10)
            );

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('journals.index', absolute: false, parameters: ['page' => 2]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.hasMore', false)
                ->has('journals', 3)
            );
    }
}
