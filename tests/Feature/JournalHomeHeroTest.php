<?php

namespace Tests\Feature;

use App\Models\Journal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class JournalHomeHeroTest extends TestCase
{
    use RefreshDatabase;

    private function base(): string
    {
        return (string) config('journal.base_domain');
    }

    public function test_hero_exposes_real_journal_profile_fields(): void
    {
        Journal::query()->create([
            'name' => 'Journal of Cognition',
            'subdomain' => 'cognition',
            'is_active' => true,
            'description' => 'A peer-reviewed open-access journal.',
            'e_issn' => '2845-1739',
            'p_issn' => '2845-1720',
            'doi_prefix' => '10.31472',
            'frequency' => 'Quarterly',
            'license_type' => 'CC BY 4.0',
            'review_model' => 'double_blind',
            'contact_email' => 'editor@cognition.test',
        ]);

        $this->get("http://cognition.{$this->base()}/")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Journal/Home')
                ->where('journal.name', 'Journal of Cognition')
                ->where('journal.tagline', 'A peer-reviewed open-access journal.')
                ->where('journal.issn_online', '2845-1739')
                ->where('journal.issn_print', '2845-1720')
                ->where('journal.doiPrefix', '10.31472')
                ->where('journal.frequency', 'Quarterly')
                ->where('journal.license', 'CC BY 4.0')
                ->where('journal.contactEmail', 'editor@cognition.test')
                ->missing('journal.reviewType')
                ->has('announcements', 0)
            );
    }

    public function test_hero_omits_missing_optional_fields(): void
    {
        Journal::query()->create([
            'name' => 'Sparse Journal',
            'subdomain' => 'sparse',
            'is_active' => true,
            // No description, ISSNs, DOI prefix, frequency, license, or contact.
        ]);

        $this->get("http://sparse.{$this->base()}/")
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('Journal/Home')
                ->where('journal.name', 'Sparse Journal')
                ->where('journal.tagline', null)
                ->where('journal.issn_online', null)
                ->where('journal.issn_print', null)
                ->where('journal.doiPrefix', null)
                ->where('journal.frequency', null)
                ->where('journal.license', null)
                ->where('journal.contactEmail', null)
                ->missing('journal.reviewType')
            );
    }
}
