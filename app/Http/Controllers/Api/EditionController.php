<?php

namespace App\Http\Controllers\Api;

use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EditionController extends Controller
{
    public function index(Journal $journal): JsonResponse
    {
        $editions = $journal->editions()->orderByDesc('volume')->orderByDesc('issue')->get();

        return response()->json($editions);
    }

    public function store(Request $request, Journal $journal): JsonResponse
    {
        $user = $request->user();
        if (! $user->isPlatformAdmin() && ! $user->hasJournalRole($journal, JournalRole::Editor)) {
            abort(403);
        }

        $data = $request->validate([
            'volume' => ['required', 'integer', 'min:1', 'max:65535'],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
        ]);

        $edition = $journal->editions()->create($data);

        return response()->json($edition, 201);
    }
}
