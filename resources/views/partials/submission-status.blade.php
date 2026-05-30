@props(['status'])

@php
    $v = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $classes = match ($v) {
        'screening' => 'badge-soft-secondary',
        'submitted' => 'badge-soft-info',
        'under_review' => 'badge-soft-info',
        'revision_requested' => 'badge-soft-warning',
        'accepted' => 'badge-soft-success',
        'rejected' => 'badge-soft-danger',
        'published' => 'badge-soft-primary',
        default => 'badge-soft-secondary',
    };
    $text = $status instanceof \App\Enums\SubmissionStatus ? $status->label() : str_replace('_', ' ', $v);
@endphp
<span {{ $attributes->merge(['class' => 'badge '.$classes]) }}>
    {{ $text }}
</span>
