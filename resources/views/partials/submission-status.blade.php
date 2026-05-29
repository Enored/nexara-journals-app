@props(['status'])

@php
    $v = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $classes = match ($v) {
        'submitted' => 'badge-soft-secondary',
        'under_review' => 'badge-soft-info',
        'revision_requested' => 'badge-soft-warning',
        'accepted' => 'badge-soft-success',
        'rejected' => 'badge-soft-danger',
        'published' => 'badge-soft-primary',
        default => 'badge-soft-secondary',
    };
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$classes]) }}>
    {{ str_replace('_', ' ', $v) }}
</span>
