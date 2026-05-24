<form
    method="POST"
    action="{{ platform_route('admin.journals.editions.store', $journal) }}"
    id="edition-create-form"
>
    @csrf
    @if ($volumes->isEmpty())
        <div class="alert alert-warning mb-0">
            Create a <strong>volume</strong> first using <strong>New volume</strong> above, then return here to add an issue.
        </div>
        <div data-ajax-modal-block-submit></div>
    @else
        <div class="row g-3">
            <div class="col-sm-6">
                <label for="edition-volume-id" class="form-label">Volume</label>
                <select id="edition-volume-id" name="volume_id" required class="form-select @error('volume_id') is-invalid @enderror">
                    <option value="">Select volume…</option>
                    @foreach ($volumes as $volume)
                        <option value="{{ $volume->id }}" @selected(old('volume_id') === $volume->id)>
                            Vol. {{ $volume->number }}@if ($volume->title) — {{ $volume->title }}@endif
                        </option>
                    @endforeach
                </select>
                @error('volume_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-sm-6">
                <label for="edition-issue" class="form-label">Issue number</label>
                <input id="edition-issue" type="number" name="issue" value="{{ old('issue', 1) }}" min="1" max="65535" required class="form-control @error('issue') is-invalid @enderror">
                @error('issue')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <div class="mb-0 mt-3">
            <label for="edition-title" class="form-label">Issue title (optional)</label>
            <input id="edition-title" type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror">
            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <p class="text-muted mb-0 mt-3 fs-sm">Issues are created as drafts. Add articles, then use <strong>Publish issue</strong> when ready.</p>
    @endif
</form>
