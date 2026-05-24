@props(['items' => []])

<nav aria-label="breadcrumb">
    <ol class="breadcrumb m-0 py-0">
        @foreach ($items as $item)
            @if ($loop->last)
                <li class="breadcrumb-item active" @if (! empty($item['aria'])) aria-current="page" @endif>{{ $item['label'] }}</li>
            @else
                <li class="breadcrumb-item">
                    @if (! empty($item['url']))
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @else
                        {{ $item['label'] }}
                    @endif
                </li>
            @endif
        @endforeach
    </ol>
</nav>
