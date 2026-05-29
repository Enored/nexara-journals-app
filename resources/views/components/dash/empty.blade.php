@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'text-center py-5 px-4']) }}>
    <div class="avatar-lg mx-auto mb-3">
        <span class="avatar-title bg-light text-muted rounded-circle fs-24">
            <i data-lucide="inbox"></i>
        </span>
    </div>
    <h5 class="mb-1">{{ $title }}</h5>
    @if ($description)
        <p class="text-muted mb-0">{{ $description }}</p>
    @endif
    @if ($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
