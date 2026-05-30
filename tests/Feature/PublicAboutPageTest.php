<?php

namespace Tests\Feature;

use App\Http\Middleware\RedirectPlatformRoutesToApex;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class PublicAboutPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_about_page_renders_full_payload(): void
    {
        $this->withoutMiddleware(RedirectPlatformRoutesToApex::class)
            ->get(route('about', absolute: false))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Platform/About')
                ->has('about.hero', fn (AssertableInertia $hero) => $hero
                    ->has('eyebrow')
                    ->has('title')
                    ->has('lead')
                )
                ->has('about.mission.paragraphs', 3)
                ->has('about.stats', 6)
                ->has('about.leadership', 6)
                ->has('about.timeline', 7)
                ->has('about.offices', 2)
                ->has('about.contact.items', 4)
            );
    }
}
