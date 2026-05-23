@php
    use App\Enums\EditionStatus;
@endphp

@if ($status === EditionStatus::Published)
    <x-dash.badge class="bg-emerald-50 text-emerald-800">Published</x-dash.badge>
@else
    <x-dash.badge class="bg-amber-50 text-amber-900">Draft</x-dash.badge>
@endif
