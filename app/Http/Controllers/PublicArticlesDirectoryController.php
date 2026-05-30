<?php

namespace App\Http\Controllers;

use App\Models\Submission;
use App\Support\ArticlesDirectoryPayload;
use App\Support\PublicArticlesFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PublicArticlesDirectoryController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = PublicArticlesFilters::fromRequest($request);
        $paginator = PublicArticlesFilters::paginate($filters);

        return Inertia::render('Platform/Articles', [
            'pageTitle' => 'Articles — '.platform_name(),
            'press' => ArticlesDirectoryPayload::press(),
            'papers' => collect($paginator->items())
                ->values()
                ->map(fn (Submission $article, int $index) => ArticlesDirectoryPayload::mapArticle($article, $index))
                ->all(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'perPage' => $paginator->perPage(),
                'total' => $paginator->total(),
            ],
            'filters' => $filters,
            // Lazy: computed on full visits only, retained across paginated reloads.
            'facets' => fn () => PublicArticlesFilters::facets(),
        ]);
    }
}
