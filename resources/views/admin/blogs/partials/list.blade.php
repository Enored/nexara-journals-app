@php
    use App\Support\AdminBlogIndexFilters;
@endphp

<x-dash.list-card
    :filter-action="platform_route('admin.blogs.index')"
    :paginator="$blogs"
    item-label="blogs"
>
    <x-slot:filterStart>
        <x-dash.app-search
            type="search"
            name="q"
            id="blogs-filter-q"
            :value="$filters['q']"
            placeholder="Search by title…"
        />
        <x-dash.app-search type="select" name="status" id="blogs-filter-status" icon="circle-small">
            <option value="">All statuses</option>
            <option value="published" @selected($filters['status'] === AdminBlogIndexFilters::STATUS_PUBLISHED)>Published</option>
            <option value="draft" @selected($filters['status'] === AdminBlogIndexFilters::STATUS_DRAFT)>Draft</option>
        </x-dash.app-search>
        @if ($hasActiveFilters)
            <x-dash.button variant="secondary" :href="platform_route('admin.blogs.index')" data-dash-partial-link>Reset</x-dash.button>
        @endif
    </x-slot:filterStart>

    @if ($hasActiveFilters)
        <x-slot:pills>
            <x-dash.filter-pills
                :pills="$activeFilterPills"
                :reset-url="platform_route('admin.blogs.index')"
            />
        </x-slot:pills>
    @endif

    <x-slot:header>
        <tr class="text-uppercase fs-xxs">
            <th>Title</th>
            <th>Slug</th>
            <th>Status</th>
            <th>Updated</th>
            <th class="text-end">Actions</th>
        </tr>
    </x-slot:header>

    <x-slot:body>
        @forelse ($blogs as $blog)
            <tr>
                <td class="fw-medium">{{ $blog->title }}</td>
                <td><code class="fs-xs text-muted">{{ $blog->slug }}</code></td>
                <td>
                    @if ($blog->is_published)
                        <span class="badge badge-soft-success">Published</span>
                    @else
                        <span class="badge badge-soft-secondary">Draft</span>
                    @endif
                </td>
                <td class="text-muted">{{ optional($blog->updated_at)->diffForHumans() ?? '—' }}</td>
                <td class="text-end text-nowrap">
                    <x-dash.link :href="platform_route('admin.blogs.edit', $blog)">Edit</x-dash.link>
                    <span class="text-muted mx-1">·</span>

                    <form
                        id="blog-delete-form-{{ $blog->id }}"
                        method="POST"
                        action="{{ platform_route('admin.blogs.destroy', $blog) }}"
                        class="d-none"
                    >
                        @csrf
                        @method('DELETE')
                    </form>

                    <button
                        type="button"
                        class="btn btn-link p-0 border-0 text-danger"
                        data-admin-confirm-open
                        data-confirm-form="blog-delete-form-{{ $blog->id }}"
                        data-confirm-title="Delete blog?"
                        data-confirm-message="Delete '{{ $blog->title }}'? This action cannot be undone."
                        data-confirm-label="Delete blog"
                        data-confirm-variant="danger"
                    >
                        Delete
                    </button>
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="p-0">
                    <x-dash.empty
                        :title="$hasActiveFilters ? 'No blogs found' : 'No blogs yet'"
                        :description="$hasActiveFilters ? 'Try adjusting your search or filters.' : 'Create your first blog post to get started.'"
                    >
                        @unless ($hasActiveFilters)
                            <x-dash.button :href="platform_route('admin.blogs.create')">New blog</x-dash.button>
                        @endunless
                    </x-dash.empty>
                </td>
            </tr>
        @endforelse
    </x-slot:body>
</x-dash.list-card>
