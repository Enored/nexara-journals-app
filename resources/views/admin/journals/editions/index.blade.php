@extends('layouts.dashboard', ['activeNav' => 'admin-journals'])

@section('title', 'Issues & volumes')
@section('pageTitle', 'Issues & volumes')
@section('pageDescription', $journal->name)

@php($editionCreateModalUrl = platform_route('admin.journals.editions.create', $journal).'?modal=1')

@section('content')
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div class="flex flex-wrap items-center gap-2 text-sm text-slate-600">
            <x-dash.link :href="platform_route('admin.journals.index')">Journals</x-dash.link>
            <span class="text-slate-300">/</span>
            <span class="font-medium text-slate-900">{{ $journal->name }}</span>
        </div>
        <div class="flex flex-wrap gap-2">
            <x-dash.button
                type="button"
                data-edition-create-open
                data-url="{{ $editionCreateModalUrl }}"
                data-journal-name="{{ $journal->name }}"
            >New issue</x-dash.button>
        </div>
    </div>

    <x-dash.table>
        <x-slot:header>
            <tr>
                <th>Volume / Issue</th>
                <th>Title</th>
                <th>Publication date</th>
                <th class="text-right">Published articles</th>
            </tr>
        </x-slot:header>
        <x-slot:body>
            @forelse ($editions as $e)
                <tr>
                    <td class="font-medium text-slate-900">{{ $e->volume }} / {{ $e->issue }}</td>
                    <td class="text-slate-600">{{ $e->title ?? '—' }}</td>
                    <td class="text-slate-600">{{ $e->published_at?->format('Y-m-d') ?? '—' }}</td>
                    <td class="text-right text-slate-600">{{ $e->submissions_count }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="!p-0">
                        <x-dash.empty title="No issues yet" description="Create an issue before assigning accepted manuscripts to a volume.">
                            <x-dash.button
                                type="button"
                                data-edition-create-open
                                data-url="{{ $editionCreateModalUrl }}"
                                data-journal-name="{{ $journal->name }}"
                            >New issue</x-dash.button>
                        </x-dash.empty>
                    </td>
                </tr>
            @endforelse
        </x-slot:body>
    </x-dash.table>

    <div
        id="edition-create-modal"
        class="dash-modal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="edition-create-modal-title"
        aria-hidden="true"
    >
        <button type="button" class="dash-modal-backdrop" data-edition-create-close aria-label="Close dialog"></button>
        <div class="dash-modal-panel sm:max-w-lg">
            <div class="dash-modal-header">
                <div class="min-w-0">
                    <h2 id="edition-create-modal-title" class="text-base font-semibold text-slate-900">New issue</h2>
                    <p id="edition-create-modal-subtitle" class="mt-0.5 truncate text-sm text-slate-500"></p>
                </div>
                <button
                    type="button"
                    class="rounded-lg p-1.5 text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
                    data-edition-create-close
                    aria-label="Close"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
            <div id="edition-create-modal-body" class="dash-modal-body">
                <p class="text-sm text-slate-500">Loading…</p>
            </div>
            <div class="dash-modal-footer">
                <x-dash.button variant="secondary" type="button" data-edition-create-close>Cancel</x-dash.button>
                <x-dash.button type="submit" form="edition-create-form">Create issue</x-dash.button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
(function () {
    const modal = document.getElementById('edition-create-modal');
    const body = document.getElementById('edition-create-modal-body');
    const subtitle = document.getElementById('edition-create-modal-subtitle');
    if (!modal || !body || !subtitle) {
        return;
    }

    let lastTrigger = null;

    const open = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('overflow-hidden');
    };

    const close = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('overflow-hidden');
        body.innerHTML = '<p class="text-sm text-slate-500">Loading…</p>';
        if (lastTrigger) {
            lastTrigger.focus();
            lastTrigger = null;
        }
    };

    const load = async (url, journalName) => {
        subtitle.textContent = journalName;
        body.innerHTML = '<p class="text-sm text-slate-500">Loading…</p>';
        open();

        try {
            const response = await fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', Accept: 'text/html' },
                credentials: 'same-origin',
            });
            if (!response.ok) {
                throw new Error('Failed to load');
            }
            body.innerHTML = await response.text();
        } catch {
            body.innerHTML = '<p class="text-sm text-red-600">Could not load form. Please try again.</p>';
        }
    };

    document.querySelectorAll('[data-edition-create-open]').forEach((btn) => {
        btn.addEventListener('click', () => {
            lastTrigger = btn;
            load(btn.dataset.url, btn.dataset.journalName || '');
        });
    });

    modal.querySelectorAll('[data-edition-create-close]').forEach((el) => {
        el.addEventListener('click', close);
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && modal.classList.contains('is-open')) {
            close();
        }
    });
})();
</script>
@endpush
