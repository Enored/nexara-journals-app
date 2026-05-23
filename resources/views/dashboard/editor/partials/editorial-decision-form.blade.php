@php
    $roundReviews = $roundReviews ?? collect();
@endphp

<section class="dash-card p-6" id="editorial-decision">
    <h2 class="text-lg font-semibold text-slate-900">Editorial decision</h2>
    <p class="mt-1 text-sm text-slate-600">
        Record the outcome for <strong>version {{ $submission->version }}</strong>.
        The author receives only your <strong>decision letter</strong> below — peer-review reports stay confidential unless you quote them.
    </p>

    @if ($roundReviews->isNotEmpty())
        <div class="mt-6 rounded-xl border border-slate-200 bg-slate-50/80">
            <div class="border-b border-slate-200 px-4 py-3">
                <h3 class="text-sm font-semibold text-slate-900">Peer review reports</h3>
                <p class="mt-0.5 text-xs text-slate-600">Confidential to the editorial office. Use <span class="font-medium">Insert excerpt</span> to quote selected text in your letter to the author.</p>
            </div>
            <ul class="divide-y divide-slate-200">
                @foreach ($roundReviews as $index => $report)
                    <li class="p-4" data-review-report data-assignment-id="{{ $report['assignment_id'] }}">
                        <div class="flex flex-wrap items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-medium text-slate-900">
                                    {{ $report['reviewer_label'] }}
                                    <span class="font-normal text-slate-500">· {{ $report['reviewer_name'] }}</span>
                                </p>
                                <p class="mt-1 text-sm text-slate-600">
                                    Recommends <strong>{{ $report['recommendation'] }}</strong>
                                    · Scores O/M/C: {{ $report['scores'] }}
                                    @if ($report['submitted_at'])
                                        · {{ $report['submitted_at']->format('M j, Y') }}
                                    @endif
                                </p>
                            </div>
                            <button
                                type="button"
                                class="shrink-0 rounded-md border border-teal-200 bg-white px-3 py-1.5 text-xs font-semibold text-teal-800 shadow-sm hover:bg-teal-50"
                                data-insert-review-quote
                                data-reviewer-label="{{ $report['reviewer_label'] }}"
                                data-recommendation="{{ $report['recommendation'] }}"
                            >
                                Insert excerpt
                            </button>
                        </div>
                        @if ($report['comments_for_editor'] !== '')
                            <blockquote class="mt-3 max-h-48 overflow-y-auto rounded-lg border border-slate-200 bg-white px-4 py-3 text-sm leading-relaxed text-slate-800" data-review-quote-body>
                                {{ $report['comments_for_editor'] }}
                            </blockquote>
                        @else
                            <p class="mt-3 text-sm italic text-slate-500">No written comments provided.</p>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @else
        <p class="mt-4 rounded-lg border border-amber-200 bg-amber-50/80 px-4 py-3 text-sm text-amber-950">
            No completed peer reviews for this round yet. You can still record a decision; consider waiting for reviewer reports before finalizing.
        </p>
    @endif

    <form method="POST" action="{{ platform_route('editor.submissions.decision', $submission) }}" class="mt-6 space-y-4" id="editorial-decision-form">
        @csrf
        <div>
            <label for="editorial-decision-type" class="dash-field-label">Decision</label>
            <select id="editorial-decision-type" name="decision" required class="dash-select mt-1 w-full max-w-md">
                <option value="accept" @selected(old('decision') === 'accept')>Accept</option>
                <option value="minor_revision" @selected(old('decision') === 'minor_revision')>Request minor revision</option>
                <option value="major_revision" @selected(old('decision') === 'major_revision')>Request major revision</option>
                <option value="reject" @selected(old('decision') === 'reject')>Reject</option>
            </select>
        </div>
        <div>
            <label for="decision-letter" class="dash-field-label">Decision letter to author</label>
            <p class="mt-0.5 text-xs text-slate-500">This is the only feedback the author will see from this round. Summarize your decision; quote reviewers only where appropriate.</p>
            <textarea
                id="decision-letter"
                name="decision_letter"
                rows="10"
                required
                class="dash-textarea mt-2 w-full"
                placeholder="Dear author,&#10;&#10;Thank you for submitting your manuscript…"
            >{{ old('decision_letter') }}</textarea>
        </div>
        <x-dash.button type="submit" variant="secondary" class="!bg-slate-900 !text-white hover:!bg-slate-800">Send decision to author</x-dash.button>
    </form>
</section>

@push('scripts')
<script>
(function () {
    const letter = document.getElementById('decision-letter');
    if (!letter) {
        return;
    }

    const insertAtCursor = (text) => {
        const start = letter.selectionStart ?? letter.value.length;
        const end = letter.selectionEnd ?? letter.value.length;
        const before = letter.value.slice(0, start);
        const after = letter.value.slice(end);
        const needsGap = before.length > 0 && !before.endsWith('\n\n');
        const prefix = needsGap ? '\n\n' : '';
        const gapAfter = after.length > 0 ? '\n\n' : '';
        letter.value = before + prefix + text + gapAfter + after;
        letter.focus();
        const pos = (before + prefix + text).length;
        letter.setSelectionRange(pos, pos);
    };

    document.querySelectorAll('[data-insert-review-quote]').forEach((btn) => {
        btn.addEventListener('click', () => {
            const card = btn.closest('[data-review-report]');
            const body = card?.querySelector('[data-review-quote-body]');
            const label = btn.dataset.reviewerLabel || 'Reviewer';
            const recommendation = btn.dataset.recommendation || '';
            const quote = body?.textContent?.trim() || '';

            if (!quote) {
                window.showDashToast?.('This review has no comments to quote.', 'error', 5000);
                return;
            }

            const block = [
                `[Excerpt from ${label}'s confidential review — recommendation: ${recommendation}]`,
                quote,
                '[End of excerpt]',
            ].join('\n');

            insertAtCursor(block);
            window.showDashToast?.('Excerpt added to the decision letter. Edit as needed before sending.', 'success');
        });
    });
})();
</script>
@endpush
