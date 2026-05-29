@props(['bare' => false])

<div {{ $attributes->class($bare ? [] : ['card']) }}>
    <div class="table-responsive">
        <table class="table table-custom table-centered table-hover w-100 mb-0">
            @isset($header)
                <thead class="bg-light align-middle bg-opacity-25 thead-sm">
                    {{ $header }}
                </thead>
            @endisset
            <tbody>{{ $body ?? $slot }}</tbody>
        </table>
    </div>
    @isset($footer)
        <div class="card-footer border-0">{{ $footer }}</div>
    @endisset
</div>
