<?php

namespace Tests\Unit;

use App\Models\Blog;
use App\Models\User;
use App\Support\AdminBlogIndexFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminBlogIndexFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_by_title_and_status(): void
    {
        $author = User::factory()->create();

        Blog::query()->create([
            'title' => 'Published Announcement',
            'slug' => 'published-announcement',
            'is_published' => true,
            'published_at' => now(),
            'author_id' => $author->id,
        ]);

        Blog::query()->create([
            'title' => 'Draft Notes',
            'slug' => 'draft-notes',
            'is_published' => false,
            'author_id' => $author->id,
        ]);

        $filters = [
            'q' => 'draft',
            'status' => AdminBlogIndexFilters::STATUS_DRAFT,
        ];

        $query = Blog::query();
        AdminBlogIndexFilters::applyToQuery($query, $filters);
        $results = $query->get();

        $this->assertCount(1, $results);
        $this->assertSame('Draft Notes', $results->first()->title);
    }

    public function test_pagination_preserves_query_string(): void
    {
        $author = User::factory()->create();

        for ($i = 1; $i <= AdminBlogIndexFilters::PER_PAGE + 1; $i++) {
            Blog::query()->create([
                'title' => "Blog {$i}",
                'slug' => "blog-{$i}",
                'is_published' => true,
                'published_at' => now(),
                'author_id' => $author->id,
            ]);
        }

        $paginator = AdminBlogIndexFilters::paginate([
            'q' => '',
            'status' => AdminBlogIndexFilters::STATUS_PUBLISHED,
        ]);

        $this->assertTrue($paginator->hasMorePages());
        $this->assertStringContainsString('status=published', $paginator->nextPageUrl() ?? '');
    }
}
