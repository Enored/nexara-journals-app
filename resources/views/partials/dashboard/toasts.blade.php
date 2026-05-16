<div
    id="dash-toast-host"
    class="pointer-events-none fixed bottom-4 right-4 z-[60] flex w-full max-w-sm flex-col gap-2 sm:bottom-6 sm:right-6"
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
