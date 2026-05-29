@props(['status'])

@php
    $v = $status instanceof \BackedEnum ? $status->value : (string) $status;
    $label = match ($v) {
        'assigned' => 'Assigned',
        'completed' => 'Completed',
        'expired' => 'Expired',
        default => str_replace('_', ' ', $v),
    };
    $classes = match ($v) {
        'assigned' => 'bg-sky-100 text-sky-900',
        'completed' => 'bg-emerald-100 text-emerald-900',
        'expired' => 'bg-red-100 text-red-800',
        default => 'bg-slate-100 text-slate-700',
    };
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$classes]) }}>
    {{ $label }}
</span>
