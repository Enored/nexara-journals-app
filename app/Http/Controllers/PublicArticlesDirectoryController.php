<?php

namespace App\Http\Controllers;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Models\Submission;
use App\Support\ArticlesDirectoryPayload;
use Inertia\Inertia;
use Inertia\Response;

class PublicArticlesDirectoryController extends Controller
{
    public function index(): Response
    {
        $articles = Submission::query()
            ->where('status', SubmissionStatus::Published)
            ->with(['journal', 'author', 'edition.volume'])
            ->whereHas('journal', fn ($q) => $q->where('is_active', true))
            ->orderByDesc('submitted_at')
            ->get()
            ->filter(function (Submission $article) {
                if (! $article->edition) {
                    return true;
                }

                return $article->edition->status === EditionStatus::Published;
            });

        return Inertia::render('Platform/Articles', ArticlesDirectoryPayload::build($articles));
    }
}
