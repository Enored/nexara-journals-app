<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Journal;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JournalEditionManageController extends Controller
{
    public function index(Journal $journal): View
    {
        $this->authorize('manageEditions', $journal);

        $editions = $journal->editions()
            ->withCount(['submissions' => fn ($q) => $q->where('status', SubmissionStatus::Published)])
            ->orderByDesc('volume')
            ->orderByDesc('issue')
            ->get();

        return view('admin.journals.editions.index', [
            'journal' => $journal,
            'editions' => $editions,
        ]);
    }

    public function create(Request $request, Journal $journal): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        if (! $request->boolean('modal')) {
            return redirect()->route('admin.journals.editions.index', $journal);
        }

        return view('admin.journals.editions.partials.create-form', [
            'journal' => $journal,
        ]);
    }

    public function store(Request $request, Journal $journal): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        $data = $request->validate([
            'volume' => ['required', 'integer', 'min:1', 'max:65535'],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
        ]);

        $exists = Edition::query()
            ->where('journal_id', $journal->id)
            ->where('volume', $data['volume'])
            ->where('issue', $data['issue'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'issue' => 'An edition with this volume and issue already exists for this journal.',
            ]);
        }

        Edition::query()->create([
            'journal_id' => $journal->id,
            'volume' => $data['volume'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
            'published_at' => $data['published_at'] ?? null,
        ]);

        return redirect()
            ->route('admin.journals.editions.index', $journal)
            ->with('status', 'Edition created.');
    }
}
