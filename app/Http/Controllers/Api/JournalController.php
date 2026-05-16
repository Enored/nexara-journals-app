<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Support\JournalLimit;
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
            'issn' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
        ]);

        $journal = Journal::query()->create($data + ['is_active' => true]);

        return response()->json($journal, 201);
    }

    public function update(Request $request, Journal $journal): JsonResponse
    {
        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'subdomain' => ['sometimes', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:journals,subdomain,'.$journal->id],
            'issn' => ['nullable', 'string', 'max:20'],
            'description' => ['nullable', 'string'],
            'logo_path' => ['nullable', 'string', 'max:500'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $journal->update($data);

        return response()->json($journal->fresh());
    }
}
