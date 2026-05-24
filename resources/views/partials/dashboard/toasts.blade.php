<div
    id="dash-toast-host"
    aria-live="polite"
    aria-relevant="additions"
></div>

@php
    $dashFlashPayload = array_filter([
        'status' => session('status'),
        'error' => session('error'),
    ], fn ($value) => filled($value));

    if ($errors->any()) {
        $dashFlashPayload['errors'] = array_values($errors->all());
    }
@endphp
@if ($dashFlashPayload !== [])
    <script type="application/json" id="dash-flash-data">@json($dashFlashPayload)</script>
@endif
