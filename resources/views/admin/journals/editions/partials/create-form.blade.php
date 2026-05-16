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
            <input
                id="edition-volume"
                type="number"
                name="volume"
                value="{{ old('volume', 1) }}"
                min="1"
                max="65535"
                required
                class="dash-input @error('volume') ring-2 ring-rose-200 @enderror"
            >
            @error('volume')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
        <div class="dash-field">
            <label for="edition-issue" class="dash-field-label">Issue</label>
            <input
                id="edition-issue"
                type="number"
                name="issue"
                value="{{ old('issue', 1) }}"
                min="1"
                max="65535"
                required
                class="dash-input @error('issue') ring-2 ring-rose-200 @enderror"
            >
            @error('issue')
                <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
            @enderror
        </div>
    </div>
    <div class="dash-field">
        <label for="edition-title" class="dash-field-label">Issue title (optional)</label>
        <input
            id="edition-title"
            type="text"
            name="title"
            value="{{ old('title') }}"
            class="dash-input @error('title') ring-2 ring-rose-200 @enderror"
        >
        @error('title')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
    <div class="dash-field">
        <label for="edition-published-at" class="dash-field-label">Publication date (optional)</label>
        <input
            id="edition-published-at"
            type="date"
            name="published_at"
            value="{{ old('published_at') }}"
            class="dash-input @error('published_at') ring-2 ring-rose-200 @enderror"
        >
        @error('published_at')
            <p class="mt-1 text-sm text-rose-600">{{ $message }}</p>
        @enderror
    </div>
</form>
