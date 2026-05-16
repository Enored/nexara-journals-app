@props(['status'])

@php
    $v = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $classes = match ($v) {
        'submitted' => 'bg-slate-100 text-slate-800',
        'under_review' => 'bg-sky-100 text-sky-900',
        'revision_requested' => 'bg-amber-100 text-amber-900',
        'accepted' => 'bg-emerald-100 text-emerald-900',
        'rejected' => 'bg-red-100 text-red-800',
        'published' => 'bg-violet-100 text-violet-900',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$classes]) }}>
    {{ str_replace('_', ' ', $v) }}
</span>
