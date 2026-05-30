<?php

namespace App\Http\Controllers;

use App\Support\JournalsDirectoryPayload;
use App\Support\PublicJournalsFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicJournalsController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = PublicJournalsFilters::fromRequest($request);
        $paginator = PublicJournalsFilters::paginate($filters);

        return Inertia::render('Platform/Journals', [
            'pageTitle' => 'Journals — '.platform_name(),
            'journals' => JournalsDirectoryPayload::mapJournals(collect($paginator->items())),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'hasMore' => $paginator->hasMorePages(),
            ],
            'filters' => $filters,
            // Lazy: computed on full visits only, skipped on load-more partial reloads.
            'press' => fn () => JournalsDirectoryPayload::press(),
        ]);
    }
}
