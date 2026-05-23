<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Journal;
use App\Models\Submission;
use App\Support\EditionPublisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JournalEditionManageController extends Controller
{
    public function index(Journal $journal): View
    {
        $this->authorize('manageEditions', $journal);

        $editions = $journal->editions()
            ->withCount([
                'submissions as slotted_count' => fn ($q) => $q->where('status', SubmissionStatus::Accepted),
                'submissions as live_count' => fn ($q) => $q->where('status', SubmissionStatus::Published),
            ])
            ->orderByDesc('volume')
            ->orderByDesc('issue')
            ->get();

        return view('admin.journals.editions.index', [
            'journal' => $journal,
            'editions' => $editions,
        ]);
    }

    public function show(Journal $journal, Edition $edition): View
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $edition->load(['journal']);

        $articles = $edition->submissions()
            ->with('author')
            ->orderByDesc('submitted_at')
            ->get();

        $availableToAdd = Submission::query()
            ->with('author')
            ->where('journal_id', $journal->id)
            ->where('status', SubmissionStatus::Accepted)
            ->where(function ($q) use ($edition) {
                $q->whereNull('edition_id')
                    ->orWhere('edition_id', '!=', $edition->id);
            })
            ->orderByDesc('submitted_at')
            ->limit(100)
            ->get();

        return view('admin.journals.editions.show', [
            'journal' => $journal,
            'edition' => $edition,
            'articles' => $articles,
            'availableToAdd' => $availableToAdd,
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
            'planned_date' => ['nullable', 'date'],
            'visibility' => ['required', 'string', 'in:draft,published'],
        ]);

        $this->assertUniqueVolumeIssue($journal, $data['volume'], $data['issue']);

        $publishNow = $data['visibility'] === 'published';

        $edition = Edition::query()->create([
            'journal_id' => $journal->id,
            'volume' => $data['volume'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
            'status' => $publishNow ? EditionStatus::Published : EditionStatus::Draft,
            'published_at' => $publishNow
                ? ($data['planned_date'] ? Carbon::parse($data['planned_date']) : now())
                : ($data['planned_date'] ?? null),
        ]);

        if ($publishNow) {
            return redirect()
                ->route('admin.journals.editions.show', [$journal, $edition])
                ->with('status', 'Issue created as published. Add articles, or unpublish to keep the issue off the public site until ready.');
        }

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition])
            ->with('status', 'Draft issue created. Add accepted articles, then publish when ready.');
    }

    public function update(Request $request, Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $data = $request->validate([
            'volume' => ['required', 'integer', 'min:1', 'max:65535'],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
            'planned_date' => ['nullable', 'date'],
        ]);

        $duplicate = Edition::query()
            ->where('journal_id', $journal->id)
            ->where('volume', $data['volume'])
            ->where('issue', $data['issue'])
            ->where('id', '!=', $edition->id)
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'issue' => 'An edition with this volume and issue already exists for this journal.',
            ]);
        }

        $edition->update([
            'volume' => $data['volume'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
            'published_at' => $data['planned_date'] ?? $edition->published_at,
        ]);

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition])
            ->with('status', 'Issue updated.');
    }

    public function publishIssue(Request $request, Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $data = $request->validate([
            'published_at' => ['nullable', 'date'],
        ]);

        EditionPublisher::publish(
            $edition,
            isset($data['published_at']) ? Carbon::parse($data['published_at']) : null
        );

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition->fresh()])
            ->with('status', 'Issue published. Slotted articles are now live on the journal site.');
    }

    public function unpublishIssue(Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        EditionPublisher::unpublish($edition);

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition->fresh()])
            ->with('status', 'Issue unpublished. Articles were moved back to accepted and remain slotted in this issue.');
    }

    public function assignArticle(Request $request, Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $data = $request->validate([
            'submission_id' => ['required', 'uuid', 'exists:submissions,id'],
        ]);

        $submission = Submission::query()->findOrFail($data['submission_id']);
        EditionPublisher::slotSubmission($edition, $submission);

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition])
            ->with('status', 'Article added to this issue.');
    }

    public function removeArticle(Journal $journal, Edition $edition, Submission $submission): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        EditionPublisher::removeSubmission($edition, $submission);

        return redirect()
            ->route('admin.journals.editions.show', [$journal, $edition])
            ->with('status', 'Article removed from this issue.');
    }

    public function destroy(Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $label = $edition->label();
        EditionPublisher::delete($edition);

        return redirect()
            ->route('admin.journals.editions.index', $journal)
            ->with('status', "Issue {$label} deleted. Slotted articles were returned to accepted and are no longer assigned to an issue.");
    }

    private function ensureEditionBelongsToJournal(Journal $journal, Edition $edition): void
    {
        abort_unless($edition->journal_id === $journal->id, 404);
    }

    private function assertUniqueVolumeIssue(Journal $journal, int $volume, int $issue): void
    {
        $exists = Edition::query()
            ->where('journal_id', $journal->id)
            ->where('volume', $volume)
            ->where('issue', $issue)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'issue' => 'An edition with this volume and issue already exists for this journal.',
            ]);
        }
    }
}
