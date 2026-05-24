<x-dash.list-card :paginator="$journals" item-label="journals">
    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Name</th>
            <th>Subdomain</th>
            <th>ISSN</th>
            <th>Status</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>
    <x-slot:body>
        @forelse ($journals as $journal)
            <tr>
                <td class="fw-medium">{{ $journal->name }}</td>
                <td><code class="fs-xs text-muted">{{ $journal->subdomain }}</code></td>
                <td class="text-muted">{{ $journal->issn ?? '—' }}</td>
                <td>
                    @if ($journal->is_active)
                        <span class="badge badge-soft-success">Active</span>
                    @else
                        <span class="badge badge-soft-secondary">Inactive</span>
                    @endif
                </td>
                <td class="text-end text-nowrap">
                    <x-dash.link :href="platform_route('admin.journals.edit', $journal)">Edit</x-dash.link>
                    <span class="text-muted mx-1">·</span>
                    <x-dash.link :href="platform_route('admin.journals.editions.index', $journal)">Issues</x-dash.link>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-0">
                    <x-dash.empty title="No journals yet" description="Create your first journal to launch a subdomain site.">
                        @if ($canCreateMoreJournals)
                            <x-dash.button :href="platform_route('admin.journals.create')">New journal</x-dash.button>
                        @endif
                    </x-dash.empty>
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
