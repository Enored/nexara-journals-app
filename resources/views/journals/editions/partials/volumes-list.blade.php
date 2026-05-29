@php
    $volumeCreateModalUrl = platform_route('journal.volumes.create', $journal).'?modal=1';
    $volumeResetParams = collect(request()->query())->except(['vq', 'vpage'])->all();
    $volumeResetUrl = platform_route('journal.editions.index', array_merge([$journal], $volumeResetParams));
@endphp

<x-dash.list-card
    class="mb-4"
    item-label="volumes"
    :filter-action="platform_route('journal.editions.index', $journal)"
    :paginator="$volumes"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="vq"
            id="volumes-filter-q"
            :value="$volumeFilters['q'] ?? ''"
            placeholder="Search number or title…"
        />
        @if ($volumeHasActiveFilters ?? false)
            <x-dash.button variant="secondary" :href="$volumeResetUrl" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>
    <x-slot:filterEnd>
        <x-dash.button
            type="button"
            data-volume-create-open
            data-url="{{ $volumeCreateModalUrl }}"
            data-journal-name="{{ $journal->name }}"
        >
            <i data-lucide="plus" class="fs-sm me-1"></i>
            New volume
        </x-dash.button>
    </x-slot:filterEnd>
    @if ($volumeHasActiveFilters ?? false)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$volumeActiveFilterPills ?? []"
                :reset-url="$volumeResetUrl"
            />
        </x-slot:pills>
    @endif
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Volume</th>
            <th>Title</th>
            <th class="text-end">Issues</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($volumes as $volume)
            <tr>
                <td class="fw-medium">Vol. {{ $volume->number }}</td>
                <td class="text-muted">{{ $volume->title ?? '—' }}</td>
                <td class="text-end tabular-nums">{{ (int) $volume->editions_count }}</td>
                <td class="text-end text-nowrap">
                    @if ((int) $volume->editions_count === 0)
                        <form
                            method="POST"
                            action="{{ platform_route('journal.volumes.destroy', [$journal, $volume]) }}"
                            class="d-inline"
                            onsubmit="return confirm('Delete Vol. {{ $volume->number }}? This cannot be undone.');"
                        >
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-link btn-sm link-secondary p-0">Delete</button>
                        </form>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="4" class="p-0">
                    @if ($volumeHasActiveFilters ?? false)
                        <x-dash.empty
                            title="No volumes match"
                            description="Try adjusting your search."
                        />
                    @else
                        <x-dash.empty
                            title="No volumes yet"
                            description="Create a volume first, then add issues under it."
                        >
                            <x-dash.button
                                type="button"
                                data-volume-create-open
                                data-url="{{ $volumeCreateModalUrl }}"
                                data-journal-name="{{ $journal->name }}"
                            >
                                New volume
                            </x-dash.button>
                        </x-dash.empty>
                    @endif
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
