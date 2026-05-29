@props([
    'blog' => null,
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save blog',
])

@php
    $isEdit = $blog !== null;
    $title = old('title', $blog?->title);
    $slug = old('slug', $blog?->slug);
    $excerpt = old('excerpt', $blog?->excerpt);
    $content = old('content', $blog?->content);
    $isPublished = old('is_published', $blog?->is_published ?? false);
@endphp

<form method="POST" action="{{ $action }}" id="blog-form" data-blog-editor-form>
    @csrf
    @if ($method !== 'POST')
        @method($method)
    @endif

    <x-dash.input label="Title" name="title" :value="$title" required />
    <x-dash.input
        label="Slug (optional)"
        name="slug"
        :value="$slug"
        placeholder="auto-generated-from-title"
        pattern="[a-z0-9-]+"
    />
    <x-dash.textarea label="Excerpt" name="excerpt" rows="3" maxlength="500">{{ $excerpt }}</x-dash.textarea>

    <div class="mb-3">
        <label class="form-label">Content</label>

        <ul class="nav nav-tabs nav-bordered mb-2" role="tablist">
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link active"
                    data-blog-tab="editor"
                    aria-selected="true"
                >
                    Editor
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button
                    type="button"
                    class="nav-link"
                    data-blog-tab="preview"
                    aria-selected="false"
                >
                    Preview
                </button>
            </li>
        </ul>

        <div data-blog-pane="editor">
            <div class="border rounded blog-editor-wrap bg-white">
                <div id="blog-editor" data-blog-editor style="min-height: 340px"></div>
            </div>
            <textarea
                name="content"
                id="blog-content-input"
                class="d-none"
                data-blog-content-input
                rows="1"
            >{{ $content }}</textarea>
            @error('content')
                <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
        </div>

        <div data-blog-pane="preview" class="d-none">
            <div class="border rounded p-3 bg-light blog-rich-content" data-blog-preview style="min-height: 340px"></div>
        </div>

        <div class="form-text">Rich text editor supports formatting, links, and lists.</div>
    </div>

    <div class="mb-3">
        <div class="form-check">
            <input
                type="checkbox"
                name="is_published"
                value="1"
                id="blog-is-published"
                class="form-check-input"
                @checked($isPublished)
            >
            <label class="form-check-label" for="blog-is-published">Publish immediately</label>
        </div>
    </div>

    <x-dash.form-actions>
        <x-dash.button type="submit">{{ $submitLabel }}</x-dash.button>
        <x-dash.button variant="secondary" :href="platform_route('admin.blogs.index')">Cancel</x-dash.button>
    </x-dash.form-actions>
</form>
