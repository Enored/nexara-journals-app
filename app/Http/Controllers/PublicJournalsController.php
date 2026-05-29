<?php

namespace App\Http\Controllers;

use App\Enums\SubmissionStatus;
use App\Models\Journal;
use App\Support\JournalsDirectoryPayload;
use Inertia\Inertia;
use Inertia\Response;

class PublicJournalsController extends Controller
{
    public function index(): Response
    {
        $journals = Journal::query()
            ->where('is_active', true)
            ->withCount(['submissions as published_articles_count' => fn ($q) => $q->where('status', SubmissionStatus::Published)])
            ->orderBy('name')
            ->get();

        return Inertia::render('Platform/Journals', JournalsDirectoryPayload::build($journals));
    }
}
