@php
    use App\Enums\EditionStatus;
@endphp

@if ($status === EditionStatus::Published)
    <span class="badge badge-soft-success">Published</span>
@else
    <span class="badge badge-soft-warning">Draft</span>
@endif
