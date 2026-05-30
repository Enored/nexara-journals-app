<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Support\JournalLimit;
use App\Support\JournalProfileRules;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JournalController extends Controller
{
    public function index(): JsonResponse
    {
        $journals = Journal::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return response()->json($journals);
    }

    public function show(Journal $journal): JsonResponse
    {
        return response()->json($journal);
    }

    public function store(Request $request): JsonResponse
    {
        if (! JournalLimit::canCreate()) {
            return response()->json([
                'message' => JournalLimit::reachedMessage(),
            ], 422);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:journals,subdomain'],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
            ...JournalProfileRules::rules(),
        ]);

        $journal = Journal::query()->create(
            JournalProfileRules::withReviewModelDefault($data) + ['is_active' => true]
        );

        return response()->json($journal, 201);
    }

    public function update(Request $request, Journal $journal): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'subdomain' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:journals,subdomain,'.$journal->id],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            ...JournalProfileRules::rules(),
        ]);

        $journal->update(JournalProfileRules::withoutEmptyReviewModel($data));

        return response()->json($journal->fresh());
    }
}
