<?php

namespace Tests\Feature;

use App\Enums\SubmissionStatus;
use App\Http\Middleware\RedirectPlatformRoutesToApex;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PublicArticlesDirectoryTest extends TestCase
{
    use RefreshDatabase;

    private function publishArticle(Journal $journal, User $author, array $overrides = []): Submission
    {
        return Submission::query()->create([
            'journal_id' => $journal->id,
            'author_id' => $author->id,
            'title' => 'Untitled',
            'abstract' => 'An abstract.',
            'keywords' => ['alpha'],
            'article_type' => 'Research Article',
            'status' => SubmissionStatus::Published,
            'submitted_at' => now(),
            ...$overrides,
        ]);
    }

    public function test_directory_lists_only_published_articles_from_active_journals(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Active Journal',
            'subdomain' => 'active',
            'is_active' => true,
        ]);
        $inactive = Journal::query()->create([
            'name' => 'Inactive Journal',
            'subdomain' => 'inactive',
            'is_active' => false,
        ]);
        $author = User::factory()->create();

        $this->publishArticle($journal, $author, ['title' => 'Visible Paper']);
        $this->publishArticle($inactive, $author, ['title' => 'Hidden — inactive journal']);
        $this->publishArticle($journal, $author, [
            'title' => 'Hidden — not published',
            'status' => SubmissionStatus::UnderReview,
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('articles.index', absolute: false))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Platform/Articles')
                ->where('pagination.total', 1)
                ->has('papers', 1)
                ->where('papers.0.title', 'Visible Paper')
                ->where('press.articles', '1')
                ->has('facets.types')
            );
    }

    public function test_search_and_type_filters_narrow_results(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Journal of Tests',
            'subdomain' => 'tests',
            'is_active' => true,
        ]);
        $author = User::factory()->create();

        $this->publishArticle($journal, $author, [
            'title' => 'Quantum entanglement in cold atoms',
            'article_type' => 'Research Article',
        ]);
        $this->publishArticle($journal, $author, [
            'title' => 'A review of editorial workflows',
            'article_type' => 'Review Article',
        ]);

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('articles.index', absolute: false, parameters: ['q' => 'quantum']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 1)
                ->where('papers.0.title', 'Quantum entanglement in cold atoms')
                ->where('filters.q', 'quantum')
            );

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('articles.index', absolute: false, parameters: ['types' => ['Review Article']]))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 1)
                ->where('papers.0.title', 'A review of editorial workflows')
            );
    }

    public function test_results_are_paginated(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Busy Journal',
            'subdomain' => 'busy',
            'is_active' => true,
        ]);
        $author = User::factory()->create();

        foreach (range(1, 12) as $i) {
            $this->publishArticle($journal, $author, [
                'title' => "Paper {$i}",
                'submitted_at' => now()->subDays($i),
            ]);
        }

        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('articles.index', absolute: false))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('pagination.total', 12)
                ->where('pagination.lastPage', 2)
                ->where('pagination.page', 1)
                ->has('papers', 10)
            );
    }
}
