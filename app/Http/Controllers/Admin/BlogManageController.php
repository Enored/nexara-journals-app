<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Support\AdminBlogIndexFilters;
use App\Support\BlogPayload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class BlogManageController extends Controller
{
    use ReturnsDashListPartial;

    public function index(Request $request): View
    {
        $filters = AdminBlogIndexFilters::fromRequest($request);
        $blogs = AdminBlogIndexFilters::paginate($filters);

        return $this->dashListResponse(
            $request,
            'admin.blogs.partials.list',
            'admin.blogs.index',
            [
                'blogs' => $blogs,
                'filters' => $filters,
                'activeFilterPills' => AdminBlogIndexFilters::activeFilterPills($filters),
                'hasActiveFilters' => AdminBlogIndexFilters::hasActiveFilters($filters),
            ],
        );
    }

    public function create(): View
    {
        return view('admin.blogs.create', ['categories' => $this->categoryOptions()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['author_id'] = $request->user()->id;
        $data['published_at'] = $data['is_published'] ? now() : null;

        Blog::query()->create($data);

        return redirect()->route('admin.blogs.index')->with('status', 'Blog created successfully.');
    }

    public function edit(Blog $blog): View
    {
        return view('admin.blogs.edit', [
            'blog' => $blog,
            'categories' => $this->categoryOptions(),
        ]);
    }

    public function update(Request $request, Blog $blog): RedirectResponse
    {
        $data = $this->validated($request, $blog);

        if ($data['is_published'] && ! $blog->is_published) {
            $data['published_at'] = now();
        }

        if (! $data['is_published']) {
            $data['published_at'] = null;
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('status', 'Blog updated successfully.');
    }

    public function destroy(Blog $blog): RedirectResponse
    {
        $blog->delete();

        return redirect()->route('admin.blogs.index')->with('status', 'Blog removed.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validated(Request $request, ?Blog $blog = null): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('blogs', 'slug')->ignore($blog?->id),
            ],
            'category' => ['nullable', 'string', Rule::in($this->categoryOptions())],
            'cover_image' => ['nullable', 'url', 'max:2048'],
            'cover_caption' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'tags' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);
        $data['tags'] = $this->normalizeTags($data['tags'] ?? null);
        $data['read_time'] = BlogPayload::readTimeFor($data['content'] ?? null);

        return $data;
    }

    /**
     * Tagify submits its value as a JSON string of objects (e.g. [{"value":"policy"}]).
     * Accept that, a plain JSON array, or a comma-separated fallback.
     *
     * @return list<string>
     */
    private function normalizeTags(?string $raw): array
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $values = array_map(
                fn ($tag) => is_array($tag) ? ($tag['value'] ?? '') : (string) $tag,
                $decoded,
            );
        } else {
            $values = explode(',', $raw);
        }

        return collect($values)
            ->map(fn ($tag) => trim((string) $tag))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Selectable categories (everything except the "All" filter pseudo-category).
     *
     * @return list<string>
     */
    private function categoryOptions(): array
    {
        return array_values(array_filter(
            BlogPayload::CATEGORIES,
            fn (string $category) => $category !== 'All',
        ));
    }
}
