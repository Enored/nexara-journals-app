<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Support\JournalLimit;
use App\Support\JournalProfileRules;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class JournalManageController extends Controller
{
    use ReturnsDashListPartial;

    public function index(Request $request): View
    {
        $journals = Journal::query()->orderBy('name')->paginate(20);

        $data = [
            'journals' => $journals,
            'journalCount' => JournalLimit::count(),
            'journalMax' => JournalLimit::max(),
            'canCreateMoreJournals' => JournalLimit::canCreate(),
        ];

        return $this->dashListResponse(
            $request,
            'admin.journals.partials.list',
            'admin.journals.index',
            $data,
        );
    }

    public function create(): View|RedirectResponse
    {
        if (! JournalLimit::canCreate()) {
            return redirect()
                ->route('admin.journals.index')
                ->with('error', JournalLimit::reachedMessage());
        }

        return view('admin.journals.create', [
            'journalCount' => JournalLimit::count(),
            'journalMax' => JournalLimit::max(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        if (! JournalLimit::canCreate()) {
            return redirect()
                ->route('admin.journals.index')
                ->with('error', JournalLimit::reachedMessage());
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:journals,subdomain'],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
            ...JournalProfileRules::rules(),
        ]);

        Journal::query()->create(
            JournalProfileRules::withReviewModelDefault($data) + ['is_active' => true]
        );

        return redirect()->route('admin.journals.index')->with('status', 'Journal created.');
    }

    public function edit(Journal $journal): View
    {
        return view('admin.journals.edit', compact('journal'));
    }

    public function update(Request $request, Journal $journal): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'subdomain' => ['required', 'string', 'max:100', 'regex:/^[a-z0-9-]+$/', 'unique:journals,subdomain,'.$journal->id],
            'description' => ['nullable', 'string'],
            'primary_color' => ['nullable', 'string', 'max:7'],
            'submission_guidelines' => ['nullable', 'string'],
            ...JournalProfileRules::rules(),
        ]);

        $journal->update(array_merge(JournalProfileRules::withoutEmptyReviewModel($data), [
            'is_active' => $request->boolean('is_active'),
        ]));

        return redirect()->route('admin.journals.index')->with('status', 'Journal updated.');
    }
}
