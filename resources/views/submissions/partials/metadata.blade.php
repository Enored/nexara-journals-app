<section class="dash-card p-6">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Abstract</h2>
    <p class="mt-3 whitespace-pre-wrap text-sm leading-relaxed text-slate-800">{{ $submission->abstract }}</p>
</section>

<section class="dash-card p-6">
    <h2 class="text-sm font-semibold uppercase tracking-wide text-slate-500">Keywords</h2>
    <p class="mt-3 text-sm text-slate-800">{{ implode(', ', $submission->keywords ?? []) ?: '—' }}</p>
</section>
