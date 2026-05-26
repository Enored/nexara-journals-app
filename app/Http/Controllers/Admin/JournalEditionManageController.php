<?php

namespace App\Http\Controllers\Admin;

use App\Enums\EditionStatus;
use App\Enums\SubmissionStatus;
use App\Http\Controllers\Concerns\ProvidesJournalEditionViews;
use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Edition;
use App\Models\Journal;
use App\Models\Submission;
use App\Models\Volume;
use App\Support\EditionPublisher;
use App\Support\JournalEditionIndexFilters;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class JournalEditionManageController extends Controller
{
    use ProvidesJournalEditionViews, ReturnsDashListPartial;

    public function index(Request $request, Journal $journal): View
    {
        $this->authorize('manageEditions', $journal);

        $filters = JournalEditionIndexFilters::fromRequest($request);
        $editions = JournalEditionIndexFilters::paginate($journal, $filters);

        $data = $this->withEditionLayout($journal, [
            'journal' => $journal,
            'volumes' => $this->journalVolumes($journal),
            'editions' => $editions,
            'filters' => $filters,
            'statuses' => EditionStatus::cases(),
            'activeFilterPills' => JournalEditionIndexFilters::activeFilterPills($journal, $filters),
            'hasActiveFilters' => JournalEditionIndexFilters::hasActiveFilters($filters),
        ]);

        return $this->dashListResponse(
            $request,
            'journals.editions.partials.list',
            'journals.editions.index',
            $data,
        );
    }

    public function show(Journal $journal, Edition $edition): View
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $edition->load(['journal', 'volume']);

        $articles = $edition->submissions()
            ->with('author')
            ->orderByDesc('submitted_at')
            ->get();

        return view('journals.editions.show', $this->withEditionLayout($journal, [
            'journal' => $journal,
            'edition' => $edition,
            'articles' => $articles,
        ]));
    }

    public function edit(Request $request, Journal $journal, Edition $edition): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        if (! $request->boolean('modal')) {
            return redirect()->route('journal.editions.show', [$journal, $edition]);
        }

        return view('journals.editions.partials.edit-form', [
            'journal' => $journal,
            'edition' => $edition,
            'volumes' => $this->journalVolumes($journal),
        ]);
    }

    public function addArticleForm(Request $request, Journal $journal, Edition $edition): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        if (! $request->boolean('modal')) {
            return redirect()->route('journal.editions.show', [$journal, $edition]);
        }

        return view('journals.editions.partials.add-article-form', [
            'journal' => $journal,
            'edition' => $edition,
            'availableToAdd' => $this->availableSubmissionsToAdd($journal, $edition),
        ]);
    }

    public function publishForm(Request $request, Journal $journal, Edition $edition): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        if (! $request->boolean('modal')) {
            return redirect()->route('journal.editions.show', [$journal, $edition]);
        }

        abort_unless($edition->isDraft(), 404);

        $slottedCount = $edition->submissions()
            ->where('status', SubmissionStatus::Accepted)
            ->count();

        return view('journals.editions.partials.publish-form', [
            'journal' => $journal,
            'edition' => $edition,
            'slottedCount' => $slottedCount,
        ]);
    }

    public function create(Request $request, Journal $journal): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        if (! $request->boolean('modal')) {
            return redirect()->route('journal.editions.index', $journal);
        }

        return view('journals.editions.partials.create-form', [
            'journal' => $journal,
            'volumes' => $this->journalVolumes($journal),
        ]);
    }

    public function createVolume(Request $request, Journal $journal): View|RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        if (! $request->boolean('modal')) {
            return redirect()->route('journal.editions.index', $journal);
        }

        return view('journals.editions.partials.create-volume-form', [
            'journal' => $journal,
            'suggestedNumber' => $this->suggestedNextVolumeNumber($journal),
        ]);
    }

    public function storeVolume(Request $request, Journal $journal): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $exists = Volume::query()
            ->where('journal_id', $journal->id)
            ->where('number', $data['number'])
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'number' => 'A volume with this number already exists for this journal.',
            ]);
        }

        Volume::query()->create([
            'journal_id' => $journal->id,
            'number' => $data['number'],
            'title' => $data['title'] ?? null,
        ]);

        return redirect()
            ->route('journal.editions.index', $journal)
            ->with('status', 'Volume '.$data['number'].' created. You can now add issues to it.');
    }

    public function destroyVolume(Journal $journal, Volume $volume): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureVolumeBelongsToJournal($journal, $volume);

        if ($volume->editions()->exists()) {
            return redirect()
                ->route('journal.editions.index', $journal)
                ->with('error', 'Remove or reassign all issues in this volume before deleting it.');
        }

        $label = $volume->label();
        $volume->delete();

        return redirect()
            ->route('journal.editions.index', $journal)
            ->with('status', "{$label} deleted.");
    }

    public function store(Request $request, Journal $journal): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);

        $data = $request->validate([
            'volume_id' => [
                'required',
                'uuid',
                Rule::exists('volumes', 'id')->where(fn ($q) => $q->where('journal_id', $journal->id)),
            ],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $this->assertUniqueVolumeIssue($journal, $data['volume_id'], $data['issue']);

        $edition = Edition::query()->create([
            'journal_id' => $journal->id,
            'volume_id' => $data['volume_id'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
            'status' => EditionStatus::Draft,
            'published_at' => null,
        ]);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition])
            ->with('status', 'Draft issue created. Add accepted articles, then publish when ready.');
    }

    public function update(Request $request, Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $data = $request->validate([
            'volume_id' => [
                'required',
                'uuid',
                Rule::exists('volumes', 'id')->where(fn ($q) => $q->where('journal_id', $journal->id)),
            ],
            'issue' => ['required', 'integer', 'min:1', 'max:65535'],
            'title' => ['nullable', 'string', 'max:255'],
        ]);

        $duplicate = Edition::query()
            ->where('journal_id', $journal->id)
            ->where('volume_id', $data['volume_id'])
            ->where('issue', $data['issue'])
            ->where('id', '!=', $edition->id)
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'issue' => 'An issue with this number already exists in the selected volume.',
            ]);
        }

        $edition->update([
            'volume_id' => $data['volume_id'],
            'issue' => $data['issue'],
            'title' => $data['title'] ?? null,
        ]);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition])
            ->with('status', 'Issue updated.');
    }

    public function publishIssue(Request $request, Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        EditionPublisher::publish($edition);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition->fresh()])
            ->with('status', 'Issue published. Slotted articles are now live on the journal site.');
    }

    public function unpublishIssue(Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        EditionPublisher::unpublish($edition);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition->fresh()])
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
        $wasPublished = $edition->isPublished();
        EditionPublisher::slotSubmission($edition, $submission);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition])
            ->with('status', $wasPublished
                ? 'Article added and published on the journal site.'
                : 'Article added to this issue.');
    }

    public function removeArticle(Journal $journal, Edition $edition, Submission $submission): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        EditionPublisher::removeSubmission($edition, $submission);

        return redirect()
            ->route('journal.editions.show', [$journal, $edition])
            ->with('status', 'Article removed from this issue.');
    }

    public function destroy(Journal $journal, Edition $edition): RedirectResponse
    {
        $this->authorize('manageEditions', $journal);
        $this->ensureEditionBelongsToJournal($journal, $edition);

        $label = $edition->label();
        EditionPublisher::delete($edition);

        return redirect()
            ->route('journal.editions.index', $journal)
            ->with('status', "Issue {$label} deleted. Slotted articles were returned to accepted and are no longer assigned to an issue.");
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection<int, Submission>
     */
    private function availableSubmissionsToAdd(Journal $journal, Edition $edition)
    {
        return Submission::query()
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
    }

    private function ensureEditionBelongsToJournal(Journal $journal, Edition $edition): void
    {
        abort_unless($edition->journal_id === $journal->id, 404);
    }

    private function journalVolumes(Journal $journal)
    {
        return Volume::query()
            ->where('journal_id', $journal->id)
            ->withCount('editions')
            ->orderByDesc('number')
            ->get();
    }

    private function suggestedNextVolumeNumber(Journal $journal): int
    {
        $max = Volume::query()->where('journal_id', $journal->id)->max('number');

        return $max ? ((int) $max + 1) : 1;
    }

    private function ensureVolumeBelongsToJournal(Journal $journal, Volume $volume): void
    {
        abort_unless($volume->journal_id === $journal->id, 404);
    }

    private function assertUniqueVolumeIssue(Journal $journal, string $volumeId, int $issue): void
    {
        $exists = Edition::query()
            ->where('journal_id', $journal->id)
            ->where('volume_id', $volumeId)
            ->where('issue', $issue)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'issue' => 'An issue with this number already exists in the selected volume.',
            ]);
        }
    }
}
