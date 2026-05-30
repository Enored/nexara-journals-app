<?php

namespace App\Http\Controllers;

use App\Support\BlogPayload;
use App\Support\PublicBlogFilters;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\Response as HttpResponse;

class PublicBlogController extends Controller
{
    public function index(Request $request): Response
    {
        $filters = PublicBlogFilters::fromRequest($request);
        $paginator = PublicBlogFilters::paginate($filters);

        return Inertia::render('Platform/Blogs', [
            'pageTitle' => 'Nexara Notes — '.platform_name(),
            'posts' => collect($paginator->items())
                ->map(fn ($blog) => BlogPayload::toListItem($blog))
                ->all(),
            'pagination' => [
                'page' => $paginator->currentPage(),
                'lastPage' => $paginator->lastPage(),
                'total' => $paginator->total(),
                'hasMore' => $paginator->hasMorePages(),
            ],
            'filters' => $filters,
            'categories' => BlogPayload::CATEGORIES,
            // Lazy: computed on full visits only, skipped on load-more partial reloads.
            'counts' => fn () => BlogPayload::categoryCounts(),
        ]);
    }

    public function show(Request $request, string $slug): HttpResponse
    {
        $post = BlogPayload::findPublishedBySlug($slug);

        $response = Inertia::render('Platform/BlogShow', [
            'pageTitle' => $post
                ? $post['title'].' — Nexara Notes'
                : 'Post not found — Nexara Notes',
            'post' => $post,
            'related' => $post ? PublicBlogFilters::related($post) : [],
        ])->toResponse($request);

        // Styled Inertia page, but with the correct HTTP status for a missing post.
        return $post ? $response : $response->setStatusCode(404);
    }
}
