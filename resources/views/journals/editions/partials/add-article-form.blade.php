@if ($availableToAdd->isEmpty())
    <p class="text-muted mb-0">No accepted manuscripts are available to add. Only <strong>accepted</strong> articles not already assigned to another issue can be slotted here.</p>
@else
    <form
        method="POST"
        action="{{ platform_route('journal.editions.articles.assign', [$journal, $edition]) }}"
        id="edition-add-article-form"
    >
        @csrf
        <p class="text-muted fs-sm mb-3">
            @if ($edition->isPublished())
                Choose an accepted manuscript to add to this live issue. It will be published on the journal site immediately.
            @else
                Choose an accepted manuscript to slot into this issue before publishing.
            @endif
        </p>
        <div class="mb-0">
            <label for="edition-add-submission" class="form-label">Manuscript</label>
            <select id="edition-add-submission" name="submission_id" required class="form-select @error('submission_id') is-invalid @enderror">
                <option value="">Select…</option>
                @foreach ($availableToAdd as $s)
                    <option value="{{ $s->id }}" @selected(old('submission_id') === $s->id)>{{ Str::limit($s->title, 56) }} — {{ $s->author->name }}</option>
                @endforeach
            </select>
            @error('submission_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </form>
@endif
