<?php

namespace App\Http\Controllers\Api;

use App\Enums\EditionStatus;
use App\Enums\JournalRole;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EditionController extends Controller
{
    public function index(Journal $journal): JsonResponse
    {
        $editions = $journal->editions()
            ->with('volume')
            ->join('volumes', 'editions.volume_id', '=', 'volumes.id')
            ->select('editions.*')
            ->orderByDesc('volumes.number')
            ->orderByDesc('editions.issue')
            ->get();

        return response()->json($editions);
    }

    public function store(Request $request, Journal $journal): JsonResponse
    {
        $user = $request->user();
        if (! $user->isPlatformAdmin() && ! $user->hasJournalRole($journal, JournalRole::Editor)) {
            abort(403);
        }

        $data = $request->validate([
            'volume_id' => [
                'required',
                'uuid',
                Rule::exists('volumes', 'id')->where(fn ($q) => $q->where('journal_id', $journal->id)),
            ],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'in:draft,published'],
        ]);

        $edition = $journal->editions()->create([
            'volume_id' => $data['volume_id'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
            'published_at' => $data['published_at'] ?? null,
            'status' => ($data['status'] ?? 'draft') === 'published'
                ? EditionStatus::Published
                : EditionStatus::Draft,
        ]);

        return response()->json($edition->load('volume'), 201);
    }
}
