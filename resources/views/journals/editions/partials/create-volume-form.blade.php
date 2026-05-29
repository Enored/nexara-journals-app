<form
    method="POST"
    action="{{ platform_route('journal.volumes.store', $journal) }}"
    id="volume-create-form"
>
    @csrf
    <div class="mb-3">
        <label for="volume-number" class="form-label">Volume number</label>
        <input id="volume-number" type="number" name="number" value="{{ old('number', $suggestedNumber) }}" min="1" max="65535" required class="form-control @error('number') is-invalid @enderror">
        @error('number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-0">
        <label for="volume-title" class="form-label">Volume title (optional)</label>
        <input id="volume-title" type="text" name="title" value="{{ old('title') }}" class="form-control @error('title') is-invalid @enderror" placeholder="e.g. Special series on climate science">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        <div class="form-text">Create the volume first, then add one or more issues under it.</div>
    </div>
</form>
