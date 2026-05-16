@props(['label', 'value', 'hint' => null, 'accent' => 'teal'])

@php
    $accentClasses = match ($accent) {
        'sky' => 'bg-sky-50 text-sky-700 ring-sky-100',
        'amber' => 'bg-amber-50 text-amber-700 ring-amber-100',
        'violet' => 'bg-violet-50 text-violet-700 ring-violet-100',
        'rose' => 'bg-rose-50 text-rose-700 ring-rose-100',
        default => 'bg-teal-50 text-teal-700 ring-teal-100',
    };
@endphp

<div {{ $attributes->merge(['class' => 'dash-card p-5']) }}>
    <p class="text-xs font-semibold uppercase tracking-wider text-slate-500">{{ $label }}</p>
    <p class="mt-2 text-3xl font-bold tabular-nums tracking-tight text-slate-900">{{ $value }}</p>
    @if ($hint)
        <p class="mt-1.5 text-sm text-slate-500">{{ $hint }}</p>
    @endif
</div>
