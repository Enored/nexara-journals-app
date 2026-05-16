@props([
    'action',
    'method' => 'GET',
    'q' => '',
    'placeholder' => 'Search…',
    'name' => 'q',
])

<form method="{{ $method }}" action="{{ $action }}" {{ $attributes->merge(['class' => 'dash-card p-4']) }}>
    <label for="dash-search-{{ $name }}" class="dash-field-label">Search</label>
    <div class="mt-1 flex flex-col gap-2 sm:flex-row sm:items-center">
        <input
            type="search"
            name="{{ $name }}"
            id="dash-search-{{ $name }}"
            value="{{ $q }}"
            placeholder="{{ $placeholder }}"
            class="dash-input min-w-0 flex-1"
        />
        <div class="flex shrink-0 flex-wrap gap-2">
            <x-dash.button type="submit" class="w-full sm:w-auto">Search</x-dash.button>
            @isset($actions)
                {{ $actions }}
            @endisset
        </div>
    </div>
    @isset($extra)
        <div class="mt-3">{{ $extra }}</div>
    @endisset
</form>
