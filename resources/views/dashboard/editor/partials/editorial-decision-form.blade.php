@php
    $roundReviews = $roundReviews ?? collect();
@endphp

<div class="card mb-3" id="editorial-decision">
    <div class="card-body">
        <h5 class="card-title">Editorial decision</h5>
        <p class="text-muted fs-sm">
            Record the outcome for <strong>version {{ $submission->version }}</strong>.
            The author receives only your <strong>decision letter</strong> below — peer-review reports stay confidential unless you quote them.
        </p>

        @if ($roundReviews->isNotEmpty())
            <div class="mt-3 border rounded">
                <div class="border-bottom px-3 py-2 bg-light">
                    <h6 class="mb-0 fs-sm fw-semibold">Peer review reports</h6>
                    <p class="mb-0 text-muted fs-xs">Confidential to the editorial office. Use <span class="fw-medium">Insert excerpt</span> to quote selected text in your letter to the author.</p>
                </div>
                <ul class="list-unstyled mb-0">
                    @foreach ($roundReviews as $index => $report)
                        <li class="p-3 {{ !$loop->last ? 'border-bottom' : '' }}" data-review-report data-assignment-id="{{ $report['assignment_id'] }}">
                            <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">
                                <div>
                                    <p class="fw-medium mb-0">
                                        {{ $report['reviewer_label'] }}
                                        <span class="fw-normal text-muted">· {{ $report['reviewer_name'] }}</span>
                                    </p>
                                    <p class="mt-1 mb-0 text-muted fs-sm">
                                        Recommends <strong>{{ $report['recommendation'] }}</strong>
                                        · Scores O/M/C: {{ $report['scores'] }}
                                        @if ($report['submitted_at'])
                                            · {{ $report['submitted_at']->format('M j, Y') }}
                                        @endif
                                    </p>
                                </div>
                                <button
                                    type="button"
                                    class="btn btn-sm btn-outline-success"
                                    data-insert-review-quote
                                    data-reviewer-label="{{ $report['reviewer_label'] }}"
                                    data-recommendation="{{ $report['recommendation'] }}"
                                >
                                    Insert excerpt
                                </button>
                            </div>
                            @if ($report['comments_for_editor'] !== '')
                                <blockquote class="mt-2 p-3 border rounded bg-white fs-sm" style="max-height: 12rem; overflow-y: auto;" data-review-quote-body>{{ $report['comments_for_editor'] }}</blockquote>
                            @else
                                <p class="mt-2 mb-0 text-muted fst-italic fs-sm">No written comments provided.</p>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @else
            <div class="alert alert-warning mt-3 fs-sm">
                No completed peer reviews for this round yet. You can still record a decision; consider waiting for reviewer reports before finalizing.
            </div>
        @endif

        <form method="POST" action="{{ platform_route('editor.submissions.decision', $submission) }}" class="mt-3" id="editorial-decision-form">
            @csrf
            <div class="mb-3">
                <label for="editorial-decision-type" class="form-label fs-sm fw-medium">Decision</label>
                <select id="editorial-decision-type" name="decision" required class="form-select" style="max-width: 20rem;">
                    <option value="accept" @selected(old('decision') === 'accept')>Accept</option>
                    <option value="minor_revision" @selected(old('decision') === 'minor_revision')>Request minor revision</option>
                    <option value="major_revision" @selected(old('decision') === 'major_revision')>Request major revision</option>
                    <option value="reject" @selected(old('decision') === 'reject')>Reject</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="decision-letter" class="form-label fs-sm fw-medium">Decision letter to author</label>
                <p class="text-muted fs-xs mb-2">This is the only feedback the author will see from this round. Summarize your decision; quote reviewers only where appropriate.</p>
                <textarea
                    id="decision-letter"
                    name="decision_letter"
                    rows="10"
                    required
                    class="form-control"
                    placeholder="Dear author,&#10;&#10;Thank you for submitting your manuscript…"
                >{{ old('decision_letter') }}</textarea>
            </div>
            <x-dash.button type="submit">Send decision to author</x-dash.button>
        </form>
    </div>
</div>

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
