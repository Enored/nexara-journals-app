@props(['status'])

@php
    $v = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $classes = match ($v) {
        'invited' => 'bg-amber-100 text-amber-900',
        'accepted' => 'bg-sky-100 text-sky-900',
        'declined' => 'bg-slate-200 text-slate-800',
        'completed' => 'bg-emerald-100 text-emerald-900',
        'expired' => 'bg-red-100 text-red-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$classes]) }}>
    {{ str_replace('_', ' ', $v) }}
</span>
