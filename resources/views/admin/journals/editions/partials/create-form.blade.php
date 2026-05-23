<form
    method="POST"
    action="{{ platform_route('admin.journals.editions.store', $journal) }}"
    id="edition-create-form"
    class="space-y-4"
>
    @csrf
    <div class="grid gap-4 sm:grid-cols-2">
        <div class="dash-field">
            <label for="edition-volume" class="dash-field-label">Volume</label>
            <input id="edition-volume" type="number" name="volume" value="{{ old('volume', 1) }}" min="1" max="65535" required class="dash-input @error('volume') ring-2 ring-rose-200 @enderror">
            @error('volume')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
        <div class="dash-field">
            <label for="edition-issue" class="dash-field-label">Issue</label>
            <input id="edition-issue" type="number" name="issue" value="{{ old('issue', 1) }}" min="1" max="65535" required class="dash-input @error('issue') ring-2 ring-rose-200 @enderror">
            @error('issue')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
        </div>
    </div>
    <div class="dash-field">
        <label for="edition-title" class="dash-field-label">Issue title (optional)</label>
        <input id="edition-title" type="text" name="title" value="{{ old('title') }}" class="dash-input @error('title') ring-2 ring-rose-200 @enderror">
        @error('title')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="dash-field">
        <label for="edition-planned-date" class="dash-field-label">Planned release date (optional)</label>
        <input id="edition-planned-date" type="date" name="planned_date" value="{{ old('planned_date') }}" class="dash-input @error('planned_date') ring-2 ring-rose-200 @enderror">
        @error('planned_date')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
    <div class="dash-field">
        <label for="edition-visibility" class="dash-field-label">Visibility</label>
        <select id="edition-visibility" name="visibility" required class="dash-select @error('visibility') ring-2 ring-rose-200 @enderror">
            <option value="draft" @selected(old('visibility', 'draft') === 'draft')>Draft — not on journal site yet (recommended)</option>
            <option value="published" @selected(old('visibility') === 'published')>Published — mark issue live (add articles next)</option>
        </select>
        <p class="mt-1 text-xs text-slate-500">Draft issues let you slot accepted articles first, then publish the whole issue when ready.</p>
        @error('visibility')<p class="mt-1 text-sm text-rose-600">{{ $message }}</p>@enderror
    </div>
</form>
