<form
    method="POST"
    action="{{ platform_route('journal.editions.update', [$journal, $edition]) }}"
    id="edition-edit-form"
>
    @csrf
    @method('PUT')
    <div class="row g-3">
        <div class="col-sm-6">
            <label for="edition-edit-volume-id" class="form-label">Volume</label>
            <select id="edition-edit-volume-id" name="volume_id" required class="form-select @error('volume_id') is-invalid @enderror">
                @foreach ($volumes as $volume)
                    <option value="{{ $volume->id }}" @selected(old('volume_id', $edition->volume_id) === $volume->id)>
                        Vol. {{ $volume->number }}@if ($volume->title) — {{ $volume->title }}@endif
                    </option>
                @endforeach
            </select>
            @error('volume_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <div class="col-sm-6">
            <label for="edition-edit-issue" class="form-label">Issue number</label>
            <input id="edition-edit-issue" type="number" name="issue" value="{{ old('issue', $edition->issue) }}" min="1" max="65535" required class="form-control @error('issue') is-invalid @enderror">
            @error('issue')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
    </div>
    <div class="mb-0 mt-3">
        <label for="edition-edit-title" class="form-label">Title (optional)</label>
        <input id="edition-edit-title" type="text" name="title" value="{{ old('title', $edition->title) }}" class="form-control @error('title') is-invalid @enderror">
        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</form>
