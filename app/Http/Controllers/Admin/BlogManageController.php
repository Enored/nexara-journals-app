<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\ReturnsDashListPartial;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Support\AdminBlogIndexFilters;
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
        return view('admin.blogs.create');
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
        return view('admin.blogs.edit', compact('blog'));
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
     * @return array{title: string, slug: string, excerpt: string|null, content: string|null, is_published: bool}
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
            'excerpt' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
        ]);

        $data['is_published'] = $request->boolean('is_published');
        $data['slug'] = filled($data['slug'] ?? null)
            ? Str::slug($data['slug'])
            : Str::slug($data['title']);

        return $data;
    }
}
