@props([
    'blog' => null,
    'action',
    'method' => 'POST',
    'submitLabel' => 'Save blog',
    'categories' => [],
])

@php
    $isEdit = $blog !== null;
    $title = old('title', $blog?->title);
    $slug = old('slug', $blog?->slug);
    $category = old('category', $blog?->category);
    $coverImage = old('cover_image', $blog?->cover_image);
    $coverCaption = old('cover_caption', $blog?->cover_caption);
    $excerpt = old('excerpt', $blog?->excerpt);
    $content = old('content', $blog?->content);
    $isPublished = old('is_published', $blog?->is_published ?? false);

    $tagsValue = old('tags');
    if ($tagsValue === null) {
        $tagsValue = ($blog?->tags ?? []);
    } else {
        $decoded = json_decode($tagsValue, true);
        $tagsValue = is_array($decoded)
            ? array_map(fn ($t) => is_array($t) ? ($t['value'] ?? '') : $t, $decoded)
            : array_map('trim', explode(',', $tagsValue));
    }
    $tagsList = array_values(array_filter(array_map('strval', (array) $tagsValue)));
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

    <x-dash.select label="Category" name="category">
        <option value="">Select a category…</option>
        @foreach ($categories as $option)
            <option value="{{ $option }}" @selected($category === $option)>{{ $option }}</option>
        @endforeach
    </x-dash.select>

    <x-dash.input
        label="Cover image URL"
        name="cover_image"
        type="url"
        :value="$coverImage"
        placeholder="https://…/cover.jpg"
    />
    <div class="form-text mb-3" style="margin-top: -0.5rem;">
        Paste an image URL for now. Direct upload is coming once cloud storage is set up.
    </div>

    <x-dash.input
        label="Cover caption / credit"
        name="cover_caption"
        :value="$coverCaption"
        placeholder="e.g. Photo: Unsplash / Jane Doe"
    />
    <div class="form-text mb-3" style="margin-top: -0.5rem;">
        Shown under the cover image, and used as the image alt text for accessibility.
    </div>

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
        <label class="form-label" for="blog-tags">Tags</label>
        <input
            type="text"
            name="tags"
            id="blog-tags"
            class="form-control{{ $errors->has('tags') ? ' is-invalid' : '' }}"
            data-blog-tags
            value="{{ implode(', ', $tagsList) }}"
            placeholder="Type a tag and press Enter"
        >
        <div class="form-text">Press Enter or comma to add each tag.</div>
        @error('tags')
            <div class="invalid-feedback d-block">{{ $message }}</div>
        @enderror
    </div>

    <div class="mb-3 pt-2 border-top">
        <label class="form-label d-block">Publication</label>
        <div class="form-check form-switch">
            <input
                type="checkbox"
                name="is_published"
                value="1"
                id="blog-is-published"
                class="form-check-input"
                role="switch"
                @checked($isPublished)
            >
            <label class="form-check-label" for="blog-is-published">
                {{ $isPublished ? 'Published' : 'Draft' }}
            </label>
        </div>
        @if ($isEdit)
            <div class="form-text">
                @if ($isPublished)
                    This post is live on the public blog.
                    @if ($blog->published_at)
                        First published {{ $blog->published_at->format('j M Y') }}.
                    @endif
                    Turn off to unpublish and hide it from visitors.
                @else
                    This post is a draft and not visible on the public blog. Turn on to publish it.
                @endif
            </div>
        @else
            <div class="form-text">
                Turn on to make this post visible on the public blog when you save. Leave off to save as a draft.
            </div>
        @endif
    </div>

    <x-dash.form-actions>
        <x-dash.button type="submit">{{ $submitLabel }}</x-dash.button>
        <x-dash.button variant="secondary" :href="platform_route('admin.blogs.index')">Cancel</x-dash.button>
    </x-dash.form-actions>
</form>
