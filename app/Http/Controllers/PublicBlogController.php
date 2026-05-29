<?php

namespace App\Http\Controllers;

use App\Support\BlogPayload;
use Inertia\Inertia;
use Inertia\Response;

class PublicBlogController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Platform/Blogs', [
            'pageTitle' => 'Nexara Notes — '.platform_name(),
            'posts' => BlogPayload::forPublic(),
            'categories' => BlogPayload::CATEGORIES,
        ]);
    }

    public function show(string $slug): Response
    {
        $post = BlogPayload::findBySlug($slug);
        $title = $post
            ? $post['title'].' — Nexara Notes'
            : 'Post not found — Nexara Notes';

        return Inertia::render('Platform/BlogShow', [
            'pageTitle' => $title,
            'post' => $post,
            'posts' => BlogPayload::forPublic(),
        ]);
    }
}
