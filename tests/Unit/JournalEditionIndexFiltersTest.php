<?php

namespace Tests\Unit;

use App\Enums\EditionStatus;
use App\Models\Edition;
use App\Models\Journal;
use App\Models\Volume;
use App\Support\JournalEditionIndexFilters;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class JournalEditionIndexFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_filters_by_status_and_search_term(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Test Journal',
            'subdomain' => 'test-journal',
        ]);

        $volumeOne = Volume::query()->create([
            'journal_id' => $journal->id,
            'number' => 1,
        ]);

        $volumeThree = Volume::query()->create([
            'journal_id' => $journal->id,
            'number' => 3,
        ]);

        Edition::query()->create([
            'journal_id' => $journal->id,
            'volume_id' => $volumeOne->id,
            'issue' => 2,
            'title' => 'Spring special',
            'status' => EditionStatus::Draft,
        ]);

        Edition::query()->create([
            'journal_id' => $journal->id,
            'volume_id' => $volumeThree->id,
            'issue' => 1,
            'title' => 'Annual review',
            'status' => EditionStatus::Published,
        ]);

        $filters = JournalEditionIndexFilters::fromRequest(Request::create('/', 'GET', [
            'q' => 'spring',
            'status' => EditionStatus::Draft->value,
        ]));

        $results = JournalEditionIndexFilters::paginate($journal, $filters);

        $this->assertSame(1, $results->total());
        $this->assertSame('Spring special', $results->first()->title);
    }

    public function test_search_matches_volume_or_issue_number(): void
    {
        $journal = Journal::query()->create([
            'name' => 'Filter Journal',
            'subdomain' => 'filter-journal',
        ]);

        $volumeFive = Volume::query()->create([
            'journal_id' => $journal->id,
            'number' => 5,
        ]);

        $volumeTwo = Volume::query()->create([
            'journal_id' => $journal->id,
            'number' => 2,
        ]);

        Edition::query()->create([
            'journal_id' => $journal->id,
            'volume_id' => $volumeFive->id,
            'issue' => 9,
            'title' => 'Other',
            'status' => EditionStatus::Draft,
        ]);

        Edition::query()->create([
            'journal_id' => $journal->id,
            'volume_id' => $volumeTwo->id,
            'issue' => 5,
            'title' => 'Target',
            'status' => EditionStatus::Draft,
        ]);

        $filters = JournalEditionIndexFilters::fromRequest(Request::create('/', 'GET', ['q' => '5']));
        $results = JournalEditionIndexFilters::paginate($journal, $filters);

        $this->assertSame(2, $results->total());
    }
}
